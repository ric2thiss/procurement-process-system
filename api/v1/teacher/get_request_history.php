<?php
/**
 * Get Request History API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches completed/archived supply requests for the logged-in teacher with filters
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
        'message' => 'Access denied. Only teachers can view request history.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $fromDate = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
    $toDate = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
    
    // Build WHERE clause
    $whereConditions = ['sr.requester_id = :user_id'];
    $params = [':user_id' => $userId];
    
    // Filter for completed/archived requests (completed, cancelled, rejected)
    $whereConditions[] = "sr.status IN ('Completed', 'Cancelled', 'Rejected')";
    
    // Search filter
    if (!empty($search)) {
        $whereConditions[] = '(sr.tracking_id LIKE :search OR EXISTS (
            SELECT 1 FROM supply_request_items sri 
            WHERE sri.supply_request_id = sr.supply_request_id 
            AND sri.item_description LIKE :search
        ))';
        $params[':search'] = '%' . $search . '%';
    }
    
    // Status filter
    if (!empty($status)) {
        $statusMap = [
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected'
        ];
        
        if (isset($statusMap[$status])) {
            $whereConditions[] = 'sr.status = :status';
            $params[':status'] = $statusMap[$status];
        }
    }
    
    // Date filters
    if (!empty($fromDate)) {
        $whereConditions[] = 'DATE(sr.request_date) >= :from_date';
        $params[':from_date'] = $fromDate;
    }
    
    if (!empty($toDate)) {
        $whereConditions[] = 'DATE(sr.request_date) <= :to_date';
        $params[':to_date'] = $toDate;
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Get requests - use subquery to get first item only to avoid duplicates
    $sql = "SELECT 
                sr.supply_request_id,
                sr.tracking_id,
                sr.status,
                sr.request_date,
                sr.updated_at,
                (SELECT sri.item_description 
                 FROM supply_request_items sri 
                 WHERE sri.supply_request_id = sr.supply_request_id 
                 LIMIT 1) as item_description,
                (SELECT sri.quantity 
                 FROM supply_request_items sri 
                 WHERE sri.supply_request_id = sr.supply_request_id 
                 LIMIT 1) as quantity,
                (SELECT sri.unit_of_measure 
                 FROM supply_request_items sri 
                 WHERE sri.supply_request_id = sr.supply_request_id 
                 LIMIT 1) as unit_of_measure
            FROM supply_requests sr
            WHERE $whereClause
            ORDER BY sr.updated_at DESC, sr.supply_request_id DESC";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
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
            'updated_at' => $request['updated_at'],
            'completed_at' => $request['updated_at'] // Use updated_at as completed_at since completed_at column may not exist
        ];
    }
    
    echo json_encode([
        'success' => true,
        'requests' => $formattedRequests
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_request_history.php: " . $e->getMessage());
    error_log("SQL: " . (isset($sql) ? $sql : 'N/A'));
    error_log("Params: " . print_r($params ?? [], true));
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching request history: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in get_request_history.php: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
}

