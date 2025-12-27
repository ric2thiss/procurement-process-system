<?php
/**
 * Generate RIS API
 * Document Tracking System - Magallanes National High School
 * 
 * Generates a Requisition and Issue Slip (RIS) for a supply request
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
        'message' => 'Access denied. Only supply office staff can generate RIS.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
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
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Get supply request details
    $sql = "SELECT 
                sr.supply_request_id,
                sr.requester_id,
                sr.tracking_id,
                sri.item_description,
                sri.quantity,
                sri.unit_of_measure
            FROM supply_requests sr
            INNER JOIN supply_request_items sri ON sr.supply_request_id = sri.supply_request_id
            WHERE sr.supply_request_id = :request_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':request_id' => $supplyRequestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Supply request not found.'
        ]);
        exit();
    }
    
    // Generate RIS number (format: RIS-YYYY-XXX)
    $year = date('Y');
    $countSql = "SELECT COUNT(*) as count FROM ris WHERE YEAR(created_at) = :year";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([':year' => $year]);
    $count = $countStmt->fetchColumn();
    $risNumber = 'RIS-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    
    // Get inventory item (if available)
    $inventorySql = "SELECT 
                        item_id,
                        standard_unit_price
                     FROM inventory_items
                     WHERE is_active = 1
                     AND item_description LIKE :search
                     LIMIT 1";
    
    $inventoryStmt = $pdo->prepare($inventorySql);
    $inventoryStmt->execute([':search' => '%' . $request['item_description'] . '%']);
    $inventoryItem = $inventoryStmt->fetch(PDO::FETCH_ASSOC);
    
    $unitPrice = $inventoryItem ? $inventoryItem['standard_unit_price'] : 0.00;
    $totalAmount = $unitPrice * $request['quantity'];
    
    // Create RIS
    $risSql = "INSERT INTO ris (
                    ris_number,
                    supply_request_id,
                    requester_id,
                    issued_to_id,
                    issue_date,
                    total_amount,
                    status,
                    created_by
                ) VALUES (
                    :ris_number,
                    :supply_request_id,
                    :requester_id,
                    :issued_to_id,
                    CURDATE(),
                    :total_amount,
                    'Generated',
                    :created_by
                )";
    
    $risStmt = $pdo->prepare($risSql);
    $risStmt->execute([
        ':ris_number' => $risNumber,
        ':supply_request_id' => $supplyRequestId,
        ':requester_id' => $request['requester_id'],
        ':issued_to_id' => $request['requester_id'],
        ':total_amount' => $totalAmount,
        ':created_by' => $userId
    ]);
    
    $risId = $pdo->lastInsertId();
    
    // Create RIS item
    $risItemSql = "INSERT INTO ris_items (
                        ris_id,
                        inventory_item_id,
                        item_description,
                        quantity,
                        unit_of_measure,
                        unit_price,
                        total_amount
                    ) VALUES (
                        :ris_id,
                        :inventory_item_id,
                        :item_description,
                        :quantity,
                        :unit_of_measure,
                        :unit_price,
                        :total_amount
                    )";
    
    $risItemStmt = $pdo->prepare($risItemSql);
    $risItemStmt->execute([
        ':ris_id' => $risId,
        ':inventory_item_id' => $inventoryItem ? $inventoryItem['item_id'] : null,
        ':item_description' => $request['item_description'],
        ':quantity' => $request['quantity'],
        ':unit_of_measure' => $request['unit_of_measure'],
        ':unit_price' => $unitPrice,
        ':total_amount' => $totalAmount
    ]);
    
    // Update supply request status
    $updateRequestSql = "UPDATE supply_requests 
                        SET status = 'Available',
                            updated_at = CURRENT_TIMESTAMP
                        WHERE supply_request_id = :request_id";
    
    $updateRequestStmt = $pdo->prepare($updateRequestSql);
    $updateRequestStmt->execute([':request_id' => $supplyRequestId]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'ris_number' => $risNumber,
        'ris_id' => $risId,
        'message' => 'RIS generated successfully.'
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database error in generate_ris.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while generating RIS.'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in generate_ris.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

