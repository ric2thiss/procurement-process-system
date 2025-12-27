<?php
/**
 * Get Inventory Items API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches inventory items with pagination, search, and filters
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
        'message' => 'Access denied. Only supply office staff can view inventory.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 100)) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $stockStatus = isset($_GET['stock_status']) ? trim($_GET['stock_status']) : '';
    
    // Build WHERE clause
    $whereConditions = ['ii.is_active = 1'];
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $whereConditions[] = '(ii.item_code LIKE :search OR ii.item_description LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }
    
    // Category filter
    if (!empty($category)) {
        $whereConditions[] = 'ii.category = :category';
        $params[':category'] = $category;
    }
    
    // Stock status filter
    if (!empty($stockStatus)) {
        if ($stockStatus === 'in-stock') {
            $whereConditions[] = 'ii.stock_on_hand > ii.reorder_level';
        } elseif ($stockStatus === 'low-stock') {
            $whereConditions[] = 'ii.stock_on_hand > 0 AND ii.stock_on_hand <= ii.reorder_level';
        } elseif ($stockStatus === 'out-of-stock') {
            $whereConditions[] = 'ii.stock_on_hand = 0';
        }
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total
                 FROM inventory_items ii
                 WHERE $whereClause";
    
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
    
    // Get items
    $sql = "SELECT 
                ii.item_id,
                ii.item_code,
                ii.item_description,
                ii.category,
                ii.unit_of_measure,
                ii.stock_on_hand,
                ii.reorder_level,
                ii.location,
                ii.standard_unit_price
            FROM inventory_items ii
            WHERE $whereClause
            ORDER BY ii.item_code ASC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => (int)$totalRecords,
            'per_page' => $limit,
            'from' => $offset + 1,
            'to' => min($offset + $limit, $totalRecords)
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_inventory_items.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching inventory items.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_inventory_items.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

