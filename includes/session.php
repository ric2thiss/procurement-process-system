<?php
/**
 * Session Management
 * Document Tracking System - Magallanes National High School
 * 
 * Handles secure session management
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Configure session security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
    
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Require user to be logged in, redirect to login if not
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /dts/auth/login.php');
        exit();
    }
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * 
 * @return string|null Role code or null if not logged in
 */
function getCurrentUserRole() {
    return $_SESSION['role_code'] ?? null;
}

/**
 * Logout user and destroy session
 */
function logout() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Set session data for logged in user
 * 
 * @param array $userData User data from database
 */
function setUserSession($userData) {
    $_SESSION['user_id'] = $userData['user_id'];
    $_SESSION['username'] = $userData['username'];
    $_SESSION['email'] = $userData['email'] ?? null;
    $_SESSION['first_name'] = $userData['first_name'];
    $_SESSION['last_name'] = $userData['last_name'];
    $_SESSION['role_id'] = $userData['role_id'];
    $_SESSION['role_code'] = $userData['role_code'];
    $_SESSION['role_name'] = $userData['role_name'];
    $_SESSION['office_id'] = $userData['office_id'] ?? null;
    $_SESSION['logged_in_at'] = time();
}

