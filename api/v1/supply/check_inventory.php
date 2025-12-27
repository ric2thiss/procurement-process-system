<?php
/**
 * Check Inventory Availability API
 * Document Tracking System - Magallanes National High School
 * 
 * Checks if requested items are available in inventory
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/supply/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required. Please log in.'
    ]);
    exit();
}

// Check if user has SUPPLY role
$userRole = getCurrentUserRole();
if ($userRole !== 'SUPPLY') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only supply office staff can check inventory.'
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
    
    // Get request items
    $sql = "SELECT 
                sri.item_description,
                sri.quantity,
                sri.unit_of_measure
            FROM supply_request_items sri
            WHERE sri.supply_request_id = :request_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':request_id' => $supplyRequestId]);
    $requestItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$requestItem) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Request item not found.'
        ]);
        exit();
    }
    
    // Try to find matching inventory item by description
    $searchSql = "SELECT 
                    item_id,
                    item_code,
                    item_description,
                    stock_on_hand,
                    reorder_level,
                    location,
                    unit_of_measure
                 FROM inventory_items
                 WHERE is_active = 1
                 AND (item_description LIKE :search1 OR item_code LIKE :search2)
                 ORDER BY stock_on_hand DESC
                 LIMIT 1";
    
    $searchStmt = $pdo->prepare($searchSql);
    $itemDescription = $requestItem['item_description'] ?? '';
    $searchTerm = '%' . $itemDescription . '%';
    $searchStmt->execute([':search1' => $searchTerm, ':search2' => $searchTerm]);
    $inventoryItem = $searchStmt->fetch(PDO::FETCH_ASSOC);
    
    $requestedQuantity = (float)($requestItem['quantity'] ?? 0);
    $stockOnHand = $inventoryItem ? (float)($inventoryItem['stock_on_hand'] ?? 0) : 0;
    
    if ($inventoryItem && $stockOnHand >= $requestedQuantity) {
        // Item is available
        echo json_encode([
            'success' => true,
            'available' => true,
            'item_id' => $inventoryItem['item_id'],
            'item_code' => $inventoryItem['item_code'] ?? '',
            'stock_on_hand' => $stockOnHand,
            'unit_of_measure' => $inventoryItem['unit_of_measure'] ?? '',
            'location' => $inventoryItem['location'] ?? ''
        ]);
    } else {
        // Item is not available
        echo json_encode([
            'success' => true,
            'available' => false,
            'stock_on_hand' => $stockOnHand,
            'unit_of_measure' => $requestItem['unit_of_measure'] ?? ''
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Database error in check_inventory.php: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking inventory.'
    ]);
} catch (Exception $e) {
    error_log("Error in check_inventory.php: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

