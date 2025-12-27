<?php
/**
 * Get Supply Requests API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches supply requests for the supply office with pagination, search, and filters
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
        'message' => 'Access denied. Only supply office staff can view requests.'
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
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $fromDate = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
    $toDate = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    // Search filter - search in tracking_id, requester name, or item descriptions
    if (!empty($search)) {
        $searchPattern = '%' . $search . '%';
        $whereConditions[] = '(sr.tracking_id LIKE :search1 
            OR CONCAT(u.first_name, " ", u.last_name) LIKE :search2
            OR u.first_name LIKE :search3
            OR u.last_name LIKE :search4
            OR EXISTS (
                SELECT 1 FROM supply_request_items sri 
                WHERE sri.supply_request_id = sr.supply_request_id 
                AND (sri.item_description LIKE :search5 OR sri.specifications LIKE :search6)
            ))';
        $params[':search1'] = $searchPattern;
        $params[':search2'] = $searchPattern;
        $params[':search3'] = $searchPattern;
        $params[':search4'] = $searchPattern;
        $params[':search5'] = $searchPattern;
        $params[':search6'] = $searchPattern;
    }
    
    // Status filter
    if (!empty($status)) {
        $statusMap = [
            'pending' => 'Submitted',
            'checked' => 'Available',
            'ris-generated' => 'Available', // When RIS is generated, status is 'Available'
            'pr-created' => 'Not Available',
            'submitted' => 'Submitted',
            'available' => 'Available',
            'not-available' => 'Not Available',
            'pending-ppmp' => 'Pending PPMP',
            'for-approval' => 'For Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'under-procurement' => 'Under Procurement',
            'completed' => 'Completed'
        ];
        
        if (isset($statusMap[$status])) {
            $whereConditions[] = 'sr.status = :status';
            $params[':status'] = $statusMap[$status];
        }
    }
    
    // Date filters
    if (!empty($fromDate)) {
        $whereConditions[] = 'DATE(sr.request_date) >= :from_date';
        $params[':from_date'] = $fromDate;
    }
    
    if (!empty($toDate)) {
        $whereConditions[] = 'DATE(sr.request_date) <= :to_date';
        $params[':to_date'] = $toDate;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total
                 FROM supply_requests sr
                 INNER JOIN users u ON sr.requester_id = u.user_id
                 $whereClause";
    
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
    
    // Get requests
    $sql = "SELECT 
                sr.supply_request_id,
                sr.tracking_id,
                sr.status,
                sr.request_date,
                sr.updated_at,
                sr.priority,
                CONCAT(u.first_name, ' ', u.last_name) as requester_name,
                COALESCE(
                    (SELECT GROUP_CONCAT(sri.item_description SEPARATOR ', ') 
                     FROM supply_request_items sri 
                     WHERE sri.supply_request_id = sr.supply_request_id),
                    'N/A'
                ) as item_description,
                COALESCE(
                    (SELECT SUM(sri.quantity) 
                     FROM supply_request_items sri 
                     WHERE sri.supply_request_id = sr.supply_request_id),
                    0
                ) as quantity,
                COALESCE(
                    (SELECT sri.unit_of_measure 
                     FROM supply_request_items sri 
                     WHERE sri.supply_request_id = sr.supply_request_id 
                     LIMIT 1),
                    ''
                ) as unit_of_measure
            FROM supply_requests sr
            INNER JOIN users u ON sr.requester_id = u.user_id
            $whereClause
            ORDER BY sr.request_date DESC, sr.supply_request_id DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data
    $formattedRequests = [];
    foreach ($requests as $request) {
        $formattedRequests[] = [
            'supply_request_id' => $request['supply_request_id'],
            'tracking_id' => $request['tracking_id'],
            'requester_name' => $request['requester_name'],
            'item_description' => $request['item_description'] ?? 'N/A',
            'quantity' => $request['quantity'] ?? 0,
            'unit_of_measure' => $request['unit_of_measure'] ?? '',
            'status' => $request['status'],
            'request_date' => $request['request_date'],
            'updated_at' => $request['updated_at'],
            'priority' => $request['priority']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'requests' => $formattedRequests,
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
    error_log("Database error in get_supply_requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching requests.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_supply_requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

