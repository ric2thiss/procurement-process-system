<?php
/**
 * Get Recent Requests API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches recent supply requests for the logged-in teacher
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/teacher/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Require login
requireLogin();

// Check if user has TEACHER role
if (getCurrentUserRole() !== 'TEACHER') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only teachers can view requests.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get limit from query parameter (default 5)
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $limit = max(1, min($limit, 50)); // Between 1 and 50
    
    // Fetch recent requests with item details
    $sql = "SELECT 
                sr.supply_request_id,
                sr.tracking_id,
                sr.status,
                sr.request_date,
                sr.priority,
                sri.item_description,
                sri.quantity,
                sri.unit_of_measure
            FROM supply_requests sr
            LEFT JOIN supply_request_items sri ON sr.supply_request_id = sri.supply_request_id
            WHERE sr.requester_id = :user_id
            ORDER BY sr.request_date DESC, sr.supply_request_id DESC
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $requests = $stmt->fetchAll();
    
    // Format the data
    $formattedRequests = [];
    foreach ($requests as $request) {
        $formattedRequests[] = [
            'supply_request_id' => $request['supply_request_id'],
            'tracking_id' => $request['tracking_id'],
            'item_description' => $request['item_description'] ?? 'N/A',
            'quantity' => $request['quantity'] ?? 0,
            'unit_of_measure' => $request['unit_of_measure'] ?? '',
            'status' => $request['status'],
            'request_date' => $request['request_date'],
            'priority' => $request['priority']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'requests' => $formattedRequests
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_recent_requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching requests.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_recent_requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

