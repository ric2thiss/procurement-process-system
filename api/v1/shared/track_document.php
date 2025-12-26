<?php
/**
 * Track Document API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches tracking information for a given tracking ID
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

// Require login
requireLogin();

// Check if user has TEACHER role
if (getCurrentUserRole() !== 'TEACHER') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only teachers can track documents.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
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
    
    // Get supply request by tracking ID
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
                sri.item_description,
                sri.quantity,
                sri.unit_of_measure,
                sri.specifications,
                u.first_name,
                u.last_name
            FROM supply_requests sr
            LEFT JOIN supply_request_items sri ON sr.supply_request_id = sri.supply_request_id
            LEFT JOIN users u ON sr.requester_id = u.user_id
            WHERE sr.tracking_id = :tracking_id
            AND sr.requester_id = :user_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'tracking_id' => $trackingId,
        'user_id' => $userId
    ]);
    
    $request = $stmt->fetch();
    
    if (!$request) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Request not found. Please check the tracking ID.'
        ]);
        exit();
    }
    
    // Get tracking history from document_tracking table
    $trackingSql = "SELECT 
                        dt.tracking_id,
                        dt.current_status,
                        dt.previous_status,
                        dt.remarks,
                        dt.tracked_at,
                        u.first_name,
                        u.last_name,
                        o.office_name
                    FROM document_tracking dt
                    LEFT JOIN users u ON dt.tracked_by = u.user_id
                    LEFT JOIN offices o ON dt.current_office_id = o.office_id
                    WHERE dt.document_type = 'Supply Request'
                    AND dt.document_id = :document_id
                    ORDER BY dt.tracked_at ASC";
    
    $trackingStmt = $pdo->prepare($trackingSql);
    $trackingStmt->execute(['document_id' => $request['supply_request_id']]);
    $trackingHistory = $trackingStmt->fetchAll();
    
    // Format tracking history
    $formattedHistory = [];
    foreach ($trackingHistory as $history) {
        $formattedHistory[] = [
            'status' => $history['current_status'],
            'previous_status' => $history['previous_status'],
            'remarks' => $history['remarks'],
            'tracked_at' => $history['tracked_at'],
            'tracked_by' => ($history['first_name'] && $history['last_name']) 
                ? $history['first_name'] . ' ' . $history['last_name'] 
                : 'System',
            'office_name' => $history['office_name'] ?? 'N/A'
        ];
    }
    
    // Get related documents if any
    $relatedDocs = [];
    
    // Check for PR
    $prSql = "SELECT pr_number, status, created_at 
              FROM purchase_requests 
              WHERE supply_request_id = :supply_request_id 
              LIMIT 1";
    $prStmt = $pdo->prepare($prSql);
    $prStmt->execute(['supply_request_id' => $request['supply_request_id']]);
    $pr = $prStmt->fetch();
    if ($pr) {
        $relatedDocs[] = [
            'type' => 'Purchase Request',
            'number' => $pr['pr_number'],
            'status' => $pr['status'],
            'created_at' => $pr['created_at']
        ];
    }
    
    // Check for RIS
    $risSql = "SELECT ris_number, status, issue_date 
               FROM ris 
               WHERE supply_request_id = :supply_request_id 
               LIMIT 1";
    $risStmt = $pdo->prepare($risSql);
    $risStmt->execute(['supply_request_id' => $request['supply_request_id']]);
    $ris = $risStmt->fetch();
    if ($ris) {
        $relatedDocs[] = [
            'type' => 'Requisition and Issue Slip',
            'number' => $ris['ris_number'],
            'status' => $ris['status'],
            'created_at' => $ris['issue_date']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'request' => [
            'supply_request_id' => $request['supply_request_id'],
            'tracking_id' => $request['tracking_id'],
            'item_description' => $request['item_description'] ?? 'N/A',
            'quantity' => $request['quantity'] ?? 0,
            'unit_of_measure' => $request['unit_of_measure'] ?? '',
            'status' => $request['status'],
            'request_date' => $request['request_date'],
            'updated_at' => $request['updated_at'],
            'priority' => $request['priority'],
            'justification' => $request['justification'],
            'expected_delivery_date' => $request['expected_delivery_date'],
            'requester_name' => ($request['first_name'] && $request['last_name']) 
                ? $request['first_name'] . ' ' . $request['last_name'] 
                : 'N/A',
            'specifications' => $request['specifications'] ?? ''
        ],
        'tracking_history' => $formattedHistory,
        'related_documents' => $relatedDocs
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in track_document.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching tracking information.'
    ]);
} catch (Exception $e) {
    error_log("Error in track_document.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

