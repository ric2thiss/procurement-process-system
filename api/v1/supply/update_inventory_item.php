<?php
/**
 * Update Inventory Item API
 * Document Tracking System - Magallanes National High School
 * 
 * Updates an existing inventory item
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
        'message' => 'Access denied. Only supply office staff can update inventory items.'
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
    $itemId = isset($input['item_id']) ? (int)$input['item_id'] : 0;
    $itemCode = isset($input['item_code']) ? trim($input['item_code']) : '';
    $itemDescription = isset($input['item_description']) ? trim($input['item_description']) : '';
    $unitOfMeasure = isset($input['unit_of_measure']) ? trim($input['unit_of_measure']) : '';
    
    if (!$itemId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item ID is required.'
        ]);
        exit();
    }
    
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
    
    // Check if item exists and get current stock
    $checkSql = "SELECT item_id, item_code, stock_on_hand FROM inventory_items WHERE item_id = :item_id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':item_id' => $itemId]);
    $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingItem) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Inventory item not found.'
        ]);
        exit();
    }
    
    // Get current stock before update
    $currentStock = (int)$existingItem['stock_on_hand'];
    
    // Check if item code already exists (excluding current item)
    $duplicateSql = "SELECT item_id FROM inventory_items WHERE item_code = :item_code AND item_id != :item_id";
    $duplicateStmt = $pdo->prepare($duplicateSql);
    $duplicateStmt->execute([
        ':item_code' => $itemCode,
        ':item_id' => $itemId
    ]);
    if ($duplicateStmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item code already exists. Please use a different item code.'
        ]);
        exit();
    }
    
    // Prepare data for update
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
    
    // Get new stock value if provided
    $newStock = null;
    if (isset($input['stock_on_hand']) && $input['stock_on_hand'] !== '' && $input['stock_on_hand'] !== null) {
        $newStock = (int)$input['stock_on_hand'];
        if ($newStock < 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Stock on hand cannot be negative.'
            ]);
            exit();
        }
    }
    
    $location = (isset($input['location']) && $input['location'] !== '') ? trim($input['location']) : null;
    $notes = (isset($input['notes']) && $input['notes'] !== '') ? trim($input['notes']) : null;
    
    // Start transaction for atomic update
    $pdo->beginTransaction();
    
    try {
        // Update inventory item
        if ($newStock !== null) {
            $sql = "UPDATE inventory_items SET
                        item_code = :item_code,
                        item_description = :item_description,
                        category = :category,
                        unit_of_measure = :unit_of_measure,
                        standard_unit_price = :standard_unit_price,
                        reorder_level = :reorder_level,
                        reorder_quantity = :reorder_quantity,
                        stock_on_hand = :stock_on_hand,
                        location = :location,
                        notes = :notes,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE item_id = :item_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':item_code' => $itemCode,
                ':item_description' => $itemDescription,
                ':category' => $category,
                ':unit_of_measure' => $unitOfMeasure,
                ':standard_unit_price' => $standardUnitPrice,
                ':reorder_level' => $reorderLevel,
                ':reorder_quantity' => $reorderQuantity,
                ':stock_on_hand' => $newStock,
                ':location' => $location,
                ':notes' => $notes,
                ':item_id' => $itemId
            ]);
        } else {
            $sql = "UPDATE inventory_items SET
                        item_code = :item_code,
                        item_description = :item_description,
                        category = :category,
                        unit_of_measure = :unit_of_measure,
                        standard_unit_price = :standard_unit_price,
                        reorder_level = :reorder_level,
                        reorder_quantity = :reorder_quantity,
                        location = :location,
                        notes = :notes,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE item_id = :item_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':item_code' => $itemCode,
                ':item_description' => $itemDescription,
                ':category' => $category,
                ':unit_of_measure' => $unitOfMeasure,
                ':standard_unit_price' => $standardUnitPrice,
                ':reorder_level' => $reorderLevel,
                ':reorder_quantity' => $reorderQuantity,
                ':location' => $location,
                ':notes' => $notes,
                ':item_id' => $itemId
            ]);
        }
        
        // If stock changed, create inventory movement record
        if ($newStock !== null && $newStock != $currentStock) {
            $stockDifference = $newStock - $currentStock;
            $movementType = $stockDifference > 0 ? 'IN' : ($stockDifference < 0 ? 'OUT' : 'ADJUSTMENT');
            $quantity = abs($stockDifference);
            
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
                :movement_type,
                :quantity,
                'ADJUSTMENT',
                NULL,
                :stock_before,
                :stock_after,
                :notes,
                :created_by
            )";
            
            $movementNotes = $stockDifference > 0 
                ? "Stock adjustment: Increased by {$quantity} units"
                : ($stockDifference < 0 
                    ? "Stock adjustment: Decreased by {$quantity} units"
                    : "Stock adjustment: No change");
            
            // If there are notes from the form, append them
            if ($notes) {
                $movementNotes .= " - " . $notes;
            }
            
            $movementStmt = $pdo->prepare($movementSql);
            $movementStmt->execute([
                ':item_id' => $itemId,
                ':movement_type' => $movementType,
                ':quantity' => $quantity,
                ':stock_before' => $currentStock,
                ':stock_after' => $newStock,
                ':notes' => $movementNotes,
                ':created_by' => $userId
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Inventory item updated successfully!',
            'item_id' => $itemId
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Database error in update_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating the inventory item.'
    ]);
} catch (Exception $e) {
    error_log("Error in update_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

