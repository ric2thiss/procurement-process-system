<?php
/**
 * Add Inventory Item API
 * Document Tracking System - Magallanes National High School
 * 
 * Adds a new item to the inventory
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

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        'message' => 'Access denied. Only supply office staff can add inventory items.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get request data
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Check if JSON decoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data. ' . json_last_error_msg()
        ]);
        exit();
    }
    
    // Validate required fields
    $itemCode = isset($input['item_code']) ? trim($input['item_code']) : '';
    $itemDescription = isset($input['item_description']) ? trim($input['item_description']) : '';
    $unitOfMeasure = isset($input['unit_of_measure']) ? trim($input['unit_of_measure']) : '';
    
    if (empty($itemCode)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item code is required.'
        ]);
        exit();
    }
    
    if (empty($itemDescription)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item description is required.'
        ]);
        exit();
    }
    
    if (empty($unitOfMeasure)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Unit of measure is required.'
        ]);
        exit();
    }
    
    // Check if item code already exists
    $checkSql = "SELECT item_id FROM inventory_items WHERE item_code = :item_code";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':item_code' => $itemCode]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item code already exists. Please use a different item code.'
        ]);
        exit();
    }
    
    // Prepare data for insertion
    $category = (isset($input['category']) && $input['category'] !== '') ? trim($input['category']) : null;
    
    $standardUnitPrice = null;
    if (isset($input['standard_unit_price']) && $input['standard_unit_price'] !== '' && $input['standard_unit_price'] !== null) {
        $standardUnitPrice = (float)$input['standard_unit_price'];
    }
    
    $reorderLevel = (isset($input['reorder_level']) && $input['reorder_level'] !== '' && $input['reorder_level'] !== null) 
        ? (int)$input['reorder_level'] 
        : 0;
    
    $reorderQuantity = (isset($input['reorder_quantity']) && $input['reorder_quantity'] !== '' && $input['reorder_quantity'] !== null) 
        ? (int)$input['reorder_quantity'] 
        : 0;
    
    $stockOnHand = (isset($input['stock_on_hand']) && $input['stock_on_hand'] !== '' && $input['stock_on_hand'] !== null) 
        ? (int)$input['stock_on_hand'] 
        : 0;
    
    $location = (isset($input['location']) && $input['location'] !== '') ? trim($input['location']) : null;
    $notes = (isset($input['notes']) && $input['notes'] !== '') ? trim($input['notes']) : null;
    
    // Insert new inventory item
    $sql = "INSERT INTO inventory_items (
                item_code,
                item_description,
                category,
                unit_of_measure,
                standard_unit_price,
                reorder_level,
                reorder_quantity,
                stock_on_hand,
                location,
                notes,
                is_active,
                created_by
            ) VALUES (
                :item_code,
                :item_description,
                :category,
                :unit_of_measure,
                :standard_unit_price,
                :reorder_level,
                :reorder_quantity,
                :stock_on_hand,
                :location,
                :notes,
                1,
                :created_by
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':item_code' => $itemCode,
        ':item_description' => $itemDescription,
        ':category' => $category,
        ':unit_of_measure' => $unitOfMeasure,
        ':standard_unit_price' => $standardUnitPrice,
        ':reorder_level' => $reorderLevel,
        ':reorder_quantity' => $reorderQuantity,
        ':stock_on_hand' => $stockOnHand,
        ':location' => $location,
        ':notes' => $notes,
        ':created_by' => $userId
    ]);
    
    $itemId = $pdo->lastInsertId();
    
    // If initial stock was added, create a stock movement record
    if ($stockOnHand > 0) {
        $movementSql = "INSERT INTO inventory_movements (
            item_id,
            movement_type,
            quantity,
            reference_type,
            reference_id,
            stock_before,
            stock_after,
            notes,
            created_by
        ) VALUES (
            :item_id,
            'IN',
            :quantity,
            'ADJUSTMENT',
            NULL,
            0,
            :stock_after,
            'Initial stock entry',
            :created_by
        )";
        
        $movementStmt = $pdo->prepare($movementSql);
        $movementStmt->execute([
            ':item_id' => $itemId,
            ':quantity' => $stockOnHand,
            ':stock_after' => $stockOnHand,
            ':created_by' => $userId
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Inventory item added successfully!',
        'item_id' => $itemId
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in add_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while adding the inventory item.'
    ]);
} catch (Exception $e) {
    error_log("Error in add_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

