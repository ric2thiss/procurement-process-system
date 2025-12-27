<?php
/**
 * Forward to PPMP API
 * Document Tracking System - Magallanes National High School
 * 
 * Forwards a supply request to PPMP Management when item is not available
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/supply/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Require login
requireLogin();

// Check if user has SUPPLY role
if (getCurrentUserRole() !== 'SUPPLY') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only supply office staff can forward requests.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    $supplyRequestId = isset($input['supply_request_id']) ? (int)$input['supply_request_id'] : 0;
    
    if (!$supplyRequestId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Supply request ID is required.'
        ]);
        exit();
    }
    
    // Update supply request status to "Not Available"
    $sql = "UPDATE supply_requests 
            SET status = 'Not Available',
                updated_at = CURRENT_TIMESTAMP
            WHERE supply_request_id = :request_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':request_id' => $supplyRequestId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Request forwarded to PPMP Management successfully.'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Supply request not found.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Database error in forward_to_ppmp.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while forwarding request.'
    ]);
} catch (Exception $e) {
    error_log("Error in forward_to_ppmp.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

