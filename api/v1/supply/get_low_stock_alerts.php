<?php
/**
 * Get Low Stock Alerts API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches low stock alerts for the dashboard
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
        'message' => 'Access denied.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get low stock items
    $sql = "SELECT 
                item_id,
                item_code,
                item_description,
                stock_on_hand,
                reorder_level,
                unit_of_measure
            FROM inventory_items
            WHERE is_active = 1
            AND stock_on_hand > 0
            AND stock_on_hand <= reorder_level
            ORDER BY stock_on_hand ASC
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'alerts' => $alerts
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_low_stock_alerts.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching low stock alerts.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_low_stock_alerts.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

