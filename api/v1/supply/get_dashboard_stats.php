<?php
/**
 * Get Dashboard Statistics API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches statistics for the supply office dashboard
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
        'message' => 'Access denied. Only supply office staff can view statistics.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get pending requests count (Submitted status)
    $sqlPending = "SELECT COUNT(*) as pending FROM supply_requests WHERE status = 'Submitted'";
    $stmtPending = $pdo->prepare($sqlPending);
    $stmtPending->execute();
    $pendingRequests = $stmtPending->fetchColumn();
    
    // Get count of items that have stock available (stock_on_hand > 0)
    $sqlItems = "SELECT COUNT(*) as total FROM inventory_items WHERE is_active = 1 AND stock_on_hand > 0";
    $stmtItems = $pdo->prepare($sqlItems);
    $stmtItems->execute();
    $itemsAvailable = $stmtItems->fetchColumn() ?: 0;
    
    // Get low stock items count (stock_on_hand <= reorder_level)
    $sqlLowStock = "SELECT COUNT(*) as low_stock FROM inventory_items 
                   WHERE is_active = 1 AND stock_on_hand > 0 AND stock_on_hand <= reorder_level";
    $stmtLowStock = $pdo->prepare($sqlLowStock);
    $stmtLowStock->execute();
    $lowStockItems = $stmtLowStock->fetchColumn();
    
    // Get RIS generated this month
    $sqlRIS = "SELECT COUNT(*) as ris_count FROM ris 
              WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $stmtRIS = $pdo->prepare($sqlRIS);
    $stmtRIS->execute();
    $risGenerated = $stmtRIS->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'pending_requests' => (int)$pendingRequests,
            'items_available' => (int)$itemsAvailable,
            'low_stock_items' => (int)$lowStockItems,
            'ris_generated' => (int)$risGenerated
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_dashboard_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching statistics.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_dashboard_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

