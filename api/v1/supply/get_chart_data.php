<?php
/**
 * Get Chart Data API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches data for dashboard charts
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
    
    // Inventory status counts
    $inventorySql = "SELECT 
                        SUM(CASE WHEN stock_on_hand > reorder_level THEN 1 ELSE 0 END) as in_stock,
                        SUM(CASE WHEN stock_on_hand > 0 AND stock_on_hand <= reorder_level THEN 1 ELSE 0 END) as low_stock,
                        SUM(CASE WHEN stock_on_hand = 0 THEN 1 ELSE 0 END) as out_of_stock
                     FROM inventory_items
                     WHERE is_active = 1";
    
    $inventoryStmt = $pdo->prepare($inventorySql);
    $inventoryStmt->execute();
    $inventoryData = $inventoryStmt->fetch(PDO::FETCH_ASSOC);
    
    // Request processing data (last 4 weeks)
    $weeks = [];
    $risGenerated = [];
    $prCreated = [];
    
    for ($i = 3; $i >= 0; $i--) {
        $weekStart = date('Y-m-d', strtotime("-$i weeks monday"));
        $weekEnd = date('Y-m-d', strtotime("-$i weeks sunday"));
        $weeks[] = 'Week ' . (4 - $i);
        
        // RIS generated this week
        $risSql = "SELECT COUNT(*) as count FROM ris 
                   WHERE DATE(created_at) BETWEEN :start AND :end";
        $risStmt = $pdo->prepare($risSql);
        $risStmt->execute([':start' => $weekStart, ':end' => $weekEnd]);
        $risGenerated[] = (int)$risStmt->fetchColumn();
        
        // PR created this week (if PR table exists)
        $prSql = "SELECT COUNT(*) as count FROM purchase_requests 
                  WHERE DATE(created_at) BETWEEN :start AND :end";
        $prStmt = $pdo->prepare($prSql);
        $prStmt->execute([':start' => $weekStart, ':end' => $weekEnd]);
        $prCreated[] = (int)$prStmt->fetchColumn();
    }
    
    echo json_encode([
        'success' => true,
        'inventory' => [
            'in_stock' => (int)($inventoryData['in_stock'] ?? 0),
            'low_stock' => (int)($inventoryData['low_stock'] ?? 0),
            'out_of_stock' => (int)($inventoryData['out_of_stock'] ?? 0)
        ],
        'request_processing' => [
            'weeks' => $weeks,
            'ris_generated' => $risGenerated,
            'pr_created' => $prCreated
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_chart_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching chart data.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_chart_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

