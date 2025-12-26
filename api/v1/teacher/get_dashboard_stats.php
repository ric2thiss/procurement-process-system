<?php
/**
 * Get Dashboard Statistics API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches statistics for the teacher dashboard
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/teacher/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Require login
requireLogin();

// Check if user has TEACHER role
if (getCurrentUserRole() !== 'TEACHER') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only teachers can view statistics.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get total requests count
    $sqlTotal = "SELECT COUNT(*) as total FROM supply_requests WHERE requester_id = :user_id";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute(['user_id' => $userId]);
    $totalRequests = $stmtTotal->fetchColumn();
    
    // Get pending count
    $sqlPending = "SELECT COUNT(*) as pending FROM supply_requests 
                   WHERE requester_id = :user_id 
                   AND status IN ('Submitted', 'Pending PPMP', 'For Approval')";
    $stmtPending = $pdo->prepare($sqlPending);
    $stmtPending->execute(['user_id' => $userId]);
    $pendingRequests = $stmtPending->fetchColumn();
    
    // Get approved count
    $sqlApproved = "SELECT COUNT(*) as approved FROM supply_requests 
                    WHERE requester_id = :user_id 
                    AND status IN ('Approved', 'Under Procurement', 'DV Processing')";
    $stmtApproved = $pdo->prepare($sqlApproved);
    $stmtApproved->execute(['user_id' => $userId]);
    $approvedRequests = $stmtApproved->fetchColumn();
    
    // Get completed count
    $sqlCompleted = "SELECT COUNT(*) as completed FROM supply_requests 
                     WHERE requester_id = :user_id 
                     AND status = 'Completed'";
    $stmtCompleted = $pdo->prepare($sqlCompleted);
    $stmtCompleted->execute(['user_id' => $userId]);
    $completedRequests = $stmtCompleted->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$totalRequests,
            'pending' => (int)$pendingRequests,
            'approved' => (int)$approvedRequests,
            'completed' => (int)$completedRequests
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

