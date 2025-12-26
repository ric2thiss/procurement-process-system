<?php
/**
 * Authentication Handler
 * Document Tracking System - Magallanes National High School
 * 
 * Handles user authentication logic
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';

/**
 * Role to Dashboard Mapping
 * Maps role codes to their respective dashboard paths
 */
function getDashboardPath($roleCode) {
    $roleDashboards = [
        'TEACHER' => '../module/teacher/teacher_dashboard.php',
        'SUPPLY' => '../module/supply-office/supply_dashboard.php',
        'PPMP_MGR' => '../module/ppmp-management/ppmp_dashboard.php',
        'PR_PPMP_MGR' => '../module/purchase-request-ppmp/pr_ppmp_dashboard.php',
        'PRINCIPAL' => '../module/principal/principal_dashboard.php',
        'BUDGET' => '../module/budgeting-accounting/budgeting_dashboard.php',
        'PROCUREMENT' => '../module/procurement-office/procurement_dashboard.php',
        'BOOKKEEPER' => '../module/bookkeeper/bookkeeper_dashboard.php',
        'PAYMENT' => '../module/payment-disbursement/payment_dashboard.php',
        'ADMIN' => '../module/admin/admin_dashboard.php',
        'AUDITOR' => '../module/document-tracking-audit/audit_dashboard.php'
    ];
    
    return $roleDashboards[$roleCode] ?? '../module/admin/admin_dashboard.php';
}

/**
 * Authenticate user with username/email and password
 * 
 * @param string $username Username or email
 * @param string $password Plain text password
 * @return array|false User data array on success, false on failure
 */
function authenticateUser($username, $password) {
    try {
        $pdo = getDBConnection();
        
        // Query to find user by username or email
        $sql = "SELECT u.user_id, u.username, u.password_hash, u.email, 
                       u.first_name, u.last_name, u.middle_name,
                       u.role_id, u.office_id, u.is_active,
                       r.role_code, r.role_name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.role_id
                WHERE (u.username = :username OR u.email = :email)
                AND u.is_active = 1
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'email' => $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        // Update last login timestamp
        $updateSql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute(['user_id' => $user['user_id']]);
        
        // Remove password_hash from returned data
        unset($user['password_hash']);
        
        return $user;
        
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate login input
 * 
 * @param string $username Username or email
 * @param string $password Password
 * @return array Array with 'valid' (bool) and 'errors' (array)
 */
function validateLoginInput($username, $password) {
    $errors = [];
    
    // Validate username/email
    if (empty($username)) {
        $errors[] = "Username or email is required.";
    } elseif (strlen($username) > 255) {
        $errors[] = "Username or email is too long.";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Process login request
 * 
 * @return array Result array with 'success' (bool), 'message' (string), and 'redirect' (string)
 */
function processLogin() {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [
            'success' => false,
            'message' => 'Invalid request method.',
            'redirect' => null
        ];
    }
    
    // Get and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    $validation = validateLoginInput($username, $password);
    if (!$validation['valid']) {
        return [
            'success' => false,
            'message' => implode(' ', $validation['errors']),
            'redirect' => null
        ];
    }
    
    // Authenticate user
    $user = authenticateUser($username, $password);
    
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid username/email or password.',
            'redirect' => null
        ];
    }
    
    // Set user session
    setUserSession($user);
    
    // Get dashboard path based on role
    $dashboardPath = getDashboardPath($user['role_code']);
    
    return [
        'success' => true,
        'message' => 'Login successful!',
        'redirect' => $dashboardPath
    ];
}

