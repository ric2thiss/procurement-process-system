<?php
/**
 * Get Single Inventory Item API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches a single inventory item by ID
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
        'message' => 'Access denied. Only supply office staff can view inventory items.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get item ID from request
    $itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
    
    if (!$itemId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Item ID is required.'
        ]);
        exit();
    }
    
    // Get inventory item
    $sql = "SELECT 
                ii.item_id,
                ii.item_code,
                ii.item_description,
                ii.category,
                ii.unit_of_measure,
                ii.standard_unit_price,
                ii.reorder_level,
                ii.reorder_quantity,
                ii.stock_on_hand,
                ii.location,
                ii.notes,
                ii.is_active
            FROM inventory_items ii
            WHERE ii.item_id = :item_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':item_id' => $itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Inventory item not found.'
        ]);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'item' => $item
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching inventory item.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_inventory_item.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

