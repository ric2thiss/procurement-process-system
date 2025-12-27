<?php
/**
 * Get Recent Activity API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches recent activity for the dashboard
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
    $limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 20)) : 5;
    
    $activities = [];
    
    // Get recent RIS generated
    $risSql = "SELECT 
                    r.ris_number,
                    CONCAT(u.first_name, ' ', u.last_name) as requester_name,
                    r.created_at
               FROM ris r
               INNER JOIN users u ON r.requester_id = u.user_id
               ORDER BY r.created_at DESC
               LIMIT :limit";
    
    $risStmt = $pdo->prepare($risSql);
    $risStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $risStmt->execute();
    $risList = $risStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($risList as $ris) {
        $activities[] = [
            'type' => 'RIS Generated',
            'description' => $ris['ris_number'] . ' for ' . $ris['requester_name'],
            'timestamp' => $ris['created_at'],
            'time_ago' => getTimeAgo($ris['created_at'])
        ];
    }
    
    // Get recent low stock alerts
    $lowStockSql = "SELECT 
                        item_description,
                        stock_on_hand,
                        unit_of_measure,
                        updated_at
                    FROM inventory_items
                    WHERE is_active = 1
                    AND stock_on_hand > 0
                    AND stock_on_hand <= reorder_level
                    ORDER BY updated_at DESC
                    LIMIT 3";
    
    $lowStockStmt = $pdo->prepare($lowStockSql);
    $lowStockStmt->execute();
    $lowStockList = $lowStockStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($lowStockList as $item) {
        $activities[] = [
            'type' => 'Low Stock Alert',
            'description' => $item['item_description'] . ' - ' . $item['stock_on_hand'] . ' ' . $item['unit_of_measure'] . ' remaining',
            'timestamp' => $item['updated_at'],
            'time_ago' => getTimeAgo($item['updated_at'])
        ];
    }
    
    // Sort by timestamp (most recent first) and limit
    usort($activities, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    $activities = array_slice($activities, 0, $limit);
    
    // Remove timestamp from final output (only keep time_ago for display)
    foreach ($activities as &$activity) {
        unset($activity['timestamp']);
    }
    
    echo json_encode([
        'success' => true,
        'activities' => $activities
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_recent_activity.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching recent activity.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_recent_activity.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

/**
 * Get time ago string
 */
function getTimeAgo($datetime) {
    if (empty($datetime)) {
        return 'Just now';
    }
    
    // Create DateTime objects for proper timezone handling
    try {
        $activityTime = new DateTime($datetime);
        $now = new DateTime();
        
        // Calculate difference
        $diff = $now->getTimestamp() - $activityTime->getTimestamp();
        
        // Handle future dates (shouldn't happen, but just in case)
        if ($diff < 0) {
            return 'Just now';
        }
        
        // Less than a minute
        if ($diff < 60) {
            return 'Just now';
        }
        // Less than an hour
        elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ($minutes == 1 ? ' minute' : ' minutes') . ' ago';
        }
        // Less than a day
        elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ($hours == 1 ? ' hour' : ' hours') . ' ago';
        }
        // Less than a week
        elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ($days == 1 ? ' day' : ' days') . ' ago';
        }
        // More than a week - show date
        else {
            return $activityTime->format('M d, Y');
        }
    } catch (Exception $e) {
        // If date parsing fails, return a safe default
        return 'Recently';
    }
}

