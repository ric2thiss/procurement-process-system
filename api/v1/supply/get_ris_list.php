<?php
/**
 * Get RIS List API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches Requisition and Issue Slip (RIS) list
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
        'message' => 'Access denied. Only supply office staff can view RIS list.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get RIS list with related information
    $sql = "SELECT 
                r.ris_id,
                r.ris_number,
                r.supply_request_id,
                sr.tracking_id as supply_request_tracking_id,
                r.issue_date,
                r.status,
                r.total_amount,
                CONCAT(u.first_name, ' ', u.last_name) as requester_name,
                (SELECT ri.item_description 
                 FROM ris_items ri 
                 WHERE ri.ris_id = r.ris_id 
                 LIMIT 1) as item_description,
                (SELECT ri.quantity 
                 FROM ris_items ri 
                 WHERE ri.ris_id = r.ris_id 
                 LIMIT 1) as quantity,
                (SELECT ri.unit_of_measure 
                 FROM ris_items ri 
                 WHERE ri.ris_id = r.ris_id 
                 LIMIT 1) as unit_of_measure
            FROM ris r
            LEFT JOIN supply_requests sr ON r.supply_request_id = sr.supply_request_id
            INNER JOIN users u ON r.requester_id = u.user_id
            ORDER BY r.created_at DESC
            LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $risList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'ris_list' => $risList
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_ris_list.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching RIS list.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_ris_list.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

