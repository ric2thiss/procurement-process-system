<?php
/**
 * Change Password API
 * Document Tracking System - Magallanes National High School
 * 
 * Changes the current user's password
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
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request data.'
        ]);
        exit();
    }
    
    $currentPassword = isset($input['current_password']) ? $input['current_password'] : '';
    $newPassword = isset($input['new_password']) ? $input['new_password'] : '';
    $confirmPassword = isset($input['confirm_password']) ? $input['confirm_password'] : '';
    
    $errors = [];
    
    if (empty($currentPassword)) {
        $errors[] = 'Current password is required.';
    }
    
    if (empty($newPassword)) {
        $errors[] = 'New password is required.';
    } elseif (strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters long.';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirm password do not match.';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit();
    }
    
    // Get current user's password hash
    $userSql = "SELECT password_hash FROM users WHERE user_id = :user_id";
    $userStmt = $pdo->prepare($userSql);
    $userStmt->execute(['user_id' => $userId]);
    $user = $userStmt->fetch();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User not found.'
        ]);
        exit();
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password_hash'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Current password is incorrect.'
        ]);
        exit();
    }
    
    // Hash new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password
    $updateSql = "UPDATE users 
                  SET password_hash = :password_hash,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE user_id = :user_id";
    
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        'password_hash' => $newPasswordHash,
        'user_id' => $userId
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully.'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in change_password.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while changing password.'
    ]);
} catch (Exception $e) {
    error_log("Error in change_password.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

