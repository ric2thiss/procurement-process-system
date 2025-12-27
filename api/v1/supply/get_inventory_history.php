<?php
/**
 * Get Inventory Item History API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches movement history for an inventory item
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
        'message' => 'Access denied. Only supply office staff can view inventory history.'
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
    
    // First, get item info
    $itemSql = "SELECT item_id, item_code, item_description, unit_of_measure 
                FROM inventory_items 
                WHERE item_id = :item_id 
                LIMIT 1";
    $itemStmt = $pdo->prepare($itemSql);
    $itemStmt->execute([':item_id' => $itemId]);
    $item = $itemStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Inventory item not found.'
        ]);
        exit();
    }
    
    // Get inventory movements with user information
    $sql = "SELECT 
                im.movement_id,
                im.movement_type,
                im.quantity,
                im.reference_type,
                im.reference_id,
                im.stock_before,
                im.stock_after,
                im.unit_price,
                im.notes,
                im.movement_date,
                im.created_by,
                u.first_name,
                u.last_name,
                u.username
            FROM inventory_movements im
            LEFT JOIN users u ON im.created_by = u.user_id
            WHERE im.item_id = :item_id
            ORDER BY im.movement_date DESC, im.movement_id DESC
            LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':item_id' => $itemId]);
    $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'item' => $item,
        'movements' => $movements
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_inventory_history.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching inventory history.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_inventory_history.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

