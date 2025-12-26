<?php
// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);
/**
 * Get User Profile API
 * Document Tracking System - Magallanes National High School
 * 
 * Fetches current user's profile information
 */

// Suppress error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get the root directory (three levels up from api/v1/shared/)
$rootDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'session.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auth.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

// Require login
requireLogin();

try {
    $pdo = getDBConnection();
    $userId = getCurrentUserId();
    
    // Get user profile with role information
    $sql = "SELECT 
                u.user_id,
                u.username,
                u.email,
                u.first_name,
                u.last_name,
                u.role_id,
                u.created_at,
                r.role_code,
                r.role_name
            FROM users u
            INNER JOIN roles r ON u.role_id = r.role_id
            WHERE u.user_id = :user_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get phone separately (column may not exist in all schema versions)
    $phone = null;
    try {
        $checkPhone = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone'");
        if ($checkPhone->rowCount() > 0) {
            $phoneStmt = $pdo->prepare("SELECT phone FROM users WHERE user_id = :user_id");
            $phoneStmt->execute(['user_id' => $userId]);
            $phoneRow = $phoneStmt->fetch(PDO::FETCH_ASSOC);
            $phone = $phoneRow['phone'] ?? null;
        }
    } catch (PDOException $e) {
        // Phone column doesn't exist, that's okay
        $phone = null;
    }

    // Get profile_image separately (column may not exist in all schema versions)
    $profileImage = null;
    try {
        $checkImage = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
        if ($checkImage->rowCount() > 0) {
            $imageStmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_id = :user_id");
            $imageStmt->execute(['user_id' => $userId]);
            $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);
            $profileImage = $imageRow['profile_image'] ?? null;
        }
    } catch (PDOException $e) {
        // Profile image column doesn't exist, that's okay
        $profileImage = null;
    }
    
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User not found.'
        ]);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'profile' => [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'] ?? '',
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'phone' => $phone ?? '',
            'profile_image' => $profileImage ?? '',
            'role' => $user['role_code'] ?? '',
            'role_code' => $user['role_code'] ?? '',
            'role_name' => $user['role_name'] ?? '',
            'created_at' => $user['created_at']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_user_profile.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching profile information.'
    ]);
} catch (Exception $e) {
    error_log("Error in get_user_profile.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

