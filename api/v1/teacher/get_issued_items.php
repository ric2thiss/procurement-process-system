<?php
/**
 * Get Issued Items API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches all RIS items issued to the logged-in teacher
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
        'message' => 'Access denied. Only teachers can view issued items.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get issued items from RIS
    $sql = "SELECT 
                r.ris_id,
                r.ris_number,
                r.issue_date,
                r.status as ris_status,
                r.acknowledged_at,
                ri.ris_item_id,
                ri.item_description,
                ri.quantity,
                ri.unit_of_measure,
                ri.unit_price,
                ri.total_amount,
                sr.tracking_id
            FROM ris r
            INNER JOIN ris_items ri ON r.ris_id = ri.ris_id
            LEFT JOIN supply_requests sr ON r.supply_request_id = sr.supply_request_id
            WHERE r.issued_to_id = :user_id
            ORDER BY r.issue_date DESC, r.ris_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $items = $stmt->fetchAll();
    
    // Format the data
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'ris_id' => $item['ris_id'],
            'ris_number' => $item['ris_number'],
            'ris_item_id' => $item['ris_item_id'],
            'item_description' => $item['item_description'],
            'quantity' => $item['quantity'],
            'unit_of_measure' => $item['unit_of_measure'],
            'unit_price' => $item['unit_price'],
            'total_amount' => $item['total_amount'],
            'issue_date' => $item['issue_date'],
            'status' => $item['ris_status'],
            'acknowledged_at' => $item['acknowledged_at'],
            'tracking_id' => $item['tracking_id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $formattedItems
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_issued_items.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching issued items.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_issued_items.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

