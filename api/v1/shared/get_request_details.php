<?php
/**
 * Get Request Details API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches detailed information for a specific supply request
 * 
 * Updated: 2025-01-XX - Added SUPPLY role support
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/shared/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in - don't use requireLogin() as it might redirect
if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required. Please log in.'
    ]);
    exit();
}

// Get user role - allow multiple roles to view request details
$userRole = getCurrentUserRole();
$userId = getCurrentUserId();

// If role is null, check session directly
if (empty($userRole) && isset($_SESSION['role_code'])) {
    $userRole = $_SESSION['role_code'];
}

// This is a shared API - allow any authenticated user
// Data access is restricted based on role (TEACHER can only see own requests)

try {
    $pdo = getDBConnection();
    
    // Ensure we have userId and userRole
    if (empty($userId)) {
        $userId = getCurrentUserId();
    }
    if (empty($userRole)) {
        $userRole = getCurrentUserRole();
        if (empty($userRole) && isset($_SESSION['role_code'])) {
            $userRole = $_SESSION['role_code'];
        }
    }
    
    // Get tracking ID
    $trackingId = isset($_GET['tracking_id']) ? trim($_GET['tracking_id']) : '';
    
    if (empty($trackingId)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Tracking ID is required.'
        ]);
        exit();
    }
    
    // Get supply request details
    // Teachers can only see their own requests, other roles (SUPPLY, ADMIN, etc.) can see all requests
    $sql = "SELECT 
                sr.supply_request_id,
                sr.tracking_id,
                sr.status,
                sr.request_date,
                sr.updated_at,
                sr.priority,
                sr.justification,
                sr.expected_delivery_date,
                sr.remarks,
                u.first_name,
                u.last_name,
                u.email
            FROM supply_requests sr
            LEFT JOIN users u ON sr.requester_id = u.user_id
            WHERE sr.tracking_id = :tracking_id";
    
    // Add requester restriction only for TEACHER role
    if ($userRole === 'TEACHER' && !empty($userId)) {
        $sql .= " AND sr.requester_id = :user_id";
    }
    
    $sql .= " LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $params = ['tracking_id' => $trackingId];
    
    // Only add user_id parameter for TEACHER role
    if ($userRole === 'TEACHER' && !empty($userId)) {
        $params['user_id'] = $userId;
    }
    
    $stmt->execute($params);
    
    $request = $stmt->fetch();
    
    if (!$request) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Request not found.'
        ]);
        exit();
    }
    
    // Get request items
    $itemsSql = "SELECT 
                    item_description,
                    quantity,
                    unit_of_measure,
                    specifications
                 FROM supply_request_items
                 WHERE supply_request_id = :supply_request_id";
    
    $itemsStmt = $pdo->prepare($itemsSql);
    $itemsStmt->execute(['supply_request_id' => $request['supply_request_id']]);
    $items = $itemsStmt->fetchAll();
    
    // Get the first item for backward compatibility (most requests have single item)
    $firstItem = !empty($items) ? $items[0] : null;
    
    // Get tracking history count
    $historySql = "SELECT COUNT(*) as count
                   FROM document_tracking
                   WHERE document_type = 'Supply Request'
                   AND document_id = :document_id";
    
    $historyStmt = $pdo->prepare($historySql);
    $historyStmt->execute(['document_id' => $request['supply_request_id']]);
    $historyCount = $historyStmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'request' => [
            'supply_request_id' => $request['supply_request_id'],
            'tracking_id' => $request['tracking_id'],
            'status' => $request['status'],
            'request_date' => $request['request_date'],
            'updated_at' => $request['updated_at'],
            'priority' => $request['priority'],
            'justification' => $request['justification'],
            'expected_delivery_date' => $request['expected_delivery_date'],
            'remarks' => $request['remarks'],
            'requester_name' => ($request['first_name'] && $request['last_name']) 
                ? $request['first_name'] . ' ' . $request['last_name'] 
                : 'N/A',
            'requester_email' => $request['email'] ?? 'N/A',
            // Include first item details for backward compatibility
            'item_description' => $firstItem['item_description'] ?? null,
            'quantity' => $firstItem['quantity'] ?? null,
            'unit_of_measure' => $firstItem['unit_of_measure'] ?? null
        ],
        'items' => $items,
        'history_count' => (int)$historyCount
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_request_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching request details.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_request_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

