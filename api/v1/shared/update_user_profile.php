<?php
/**
 * Update User Profile API
 * Document Tracking System - Magallanes National High School
 * 
 * Updates current user's profile information
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
    
    // Validate and sanitize input
    $firstName = isset($input['first_name']) ? trim($input['first_name']) : '';
    $lastName = isset($input['last_name']) ? trim($input['last_name']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'First name is required.';
    } elseif (strlen($firstName) > 100) {
        $errors[] = 'First name must not exceed 100 characters.';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required.';
    } elseif (strlen($lastName) > 100) {
        $errors[] = 'Last name must not exceed 100 characters.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } elseif (strlen($email) > 255) {
        $errors[] = 'Email must not exceed 255 characters.';
    }
    
    if (!empty($phone) && strlen($phone) > 20) {
        $errors[] = 'Phone number must not exceed 20 characters.';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit();
    }
    
    // Check if email is already taken by another user
    $checkEmailSql = "SELECT user_id FROM users WHERE email = :email AND user_id != :user_id";
    $checkEmailStmt = $pdo->prepare($checkEmailSql);
    $checkEmailStmt->execute(['email' => $email, 'user_id' => $userId]);
    if ($checkEmailStmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email is already taken by another user.'
        ]);
        exit();
    }
    
    // Update user profile
    $updateSql = "UPDATE users 
                  SET first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      phone = :phone,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE user_id = :user_id";
    
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'user_id' => $userId
    ]);
    
    // Update session
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['email'] = $email;
    if ($phone) {
        $_SESSION['phone'] = $phone;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully.'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in update_user_profile.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating profile.'
    ]);
} catch (Exception $e) {
    error_log("Error in update_user_profile.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred.'
    ]);
}

