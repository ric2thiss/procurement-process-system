<?php
/**
 * Upload Profile Image API
 * Document Tracking System - Magallanes National High School
 * 
 * Handles profile image uploads
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

// Handle DELETE request to remove profile image
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $pdo = getDBConnection();
        $userId = getCurrentUserId();

        // Get existing profile image path
        try {
            $checkImage = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
            if ($checkImage->rowCount() > 0) {
                $getImageSql = "SELECT profile_image FROM users WHERE user_id = :user_id";
                $getImageStmt = $pdo->prepare($getImageSql);
                $getImageStmt->execute(['user_id' => $userId]);
                $existingImage = $getImageStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existingImage && !empty($existingImage['profile_image'])) {
                    $rootDir = dirname(dirname(dirname(dirname(__FILE__))));
                    $oldImagePath = $rootDir . DIRECTORY_SEPARATOR . $existingImage['profile_image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Update database to remove profile image
                $updateSql = "UPDATE users SET profile_image = NULL, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute(['user_id' => $userId]);
            }
        } catch (PDOException $e) {
            // Column doesn't exist, that's okay
        }

        echo json_encode([
            'success' => true,
            'message' => 'Profile image removed successfully.'
        ]);
        exit();
    } catch (Exception $e) {
        error_log("Error removing profile image: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while removing profile image.'
        ]);
        exit();
    }
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['profile_image'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded. Please select an image file.'
        ]);
        exit();
    }

    $file = $_FILES['profile_image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = $errorMessages[$file['error']] ?? 'Unknown upload error';
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Upload error: ' . $errorMsg
        ]);
        exit();
    }

    $userId = getCurrentUserId();

    // Validate file type - try multiple methods
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if file has an extension
    if (empty($fileExtension)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'File must have a valid extension (jpg, jpeg, png, gif, or webp).'
        ]);
        exit();
    }
    
    // First check by extension
    if (!in_array($fileExtension, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit();
    }
    
    // Then check MIME type if available
    $mimeType = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else if (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($file['tmp_name']);
    }
    
    if ($mimeType && !in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit();
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'File size exceeds maximum allowed size of 5MB.'
        ]);
        exit();
    }

    // Create uploads directory if it doesn't exist
    $rootDir = dirname(dirname(dirname(dirname(__FILE__))));
    $uploadDir = $rootDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile_images' . DIRECTORY_SEPARATOR;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Failed to create upload directory: " . $uploadDir);
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create upload directory. Please contact administrator.'
            ]);
            exit();
        }
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        error_log("Upload directory is not writable: " . $uploadDir);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Upload directory is not writable. Please contact administrator.'
        ]);
        exit();
    }

    // Generate unique filename
    $fileName = 'user_' . $userId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    // Get existing profile image to delete it later
    $pdo = getDBConnection();
    $oldImagePath = null;
    
    // Check if profile_image column exists first
    try {
        $checkColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
        if ($checkColumn->rowCount() > 0) {
            $getImageSql = "SELECT profile_image FROM users WHERE user_id = :user_id";
            $getImageStmt = $pdo->prepare($getImageSql);
            $getImageStmt->execute(['user_id' => $userId]);
            $existingImage = $getImageStmt->fetch(PDO::FETCH_ASSOC);
            if ($existingImage && !empty($existingImage['profile_image'])) {
                $rootDir = dirname(dirname(dirname(dirname(__FILE__))));
                $oldImagePath = $rootDir . DIRECTORY_SEPARATOR . $existingImage['profile_image'];
            }
        }
    } catch (PDOException $e) {
        // Column doesn't exist yet, that's okay - we'll create it later
        error_log("Could not check existing image: " . $e->getMessage());
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        error_log("Failed to move uploaded file from " . $file['tmp_name'] . " to " . $filePath);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save uploaded file. Please check file permissions or contact administrator.'
        ]);
        exit();
    }
    
    // Verify the file was actually saved
    if (!file_exists($filePath)) {
        error_log("File does not exist after move_uploaded_file: " . $filePath);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to verify uploaded file. Please try again.'
        ]);
        exit();
    }

    // Update database with relative path
    $relativePath = 'uploads/profile_images/' . $fileName;
    
    // Check if profile_image column exists, if not, we'll handle it gracefully
    try {
        $updateSql = "UPDATE users SET profile_image = :profile_image, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'profile_image' => $relativePath,
            'user_id' => $userId
        ]);
    } catch (PDOException $e) {
        // If column doesn't exist, try to add it
        if (strpos($e->getMessage(), "Unknown column 'profile_image'") !== false) {
            try {
                $alterSql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL";
                $pdo->exec($alterSql);
                // Retry the update
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([
                    'profile_image' => $relativePath,
                    'user_id' => $userId
                ]);
            } catch (PDOException $alterError) {
                // If we can't add the column, delete the uploaded file and return error
                unlink($filePath);
                error_log("Failed to add profile_image column: " . $alterError->getMessage());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database configuration error. Please contact administrator.'
                ]);
                exit();
            }
        } else {
            throw $e;
        }
    }

    // Delete old image if it exists
    if ($oldImagePath && file_exists($oldImagePath) && $oldImagePath !== $filePath) {
        unlink($oldImagePath);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile image uploaded successfully.',
        'image_path' => $relativePath
    ]);

} catch (PDOException $e) {
    error_log("Database error in upload_profile_image.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again or contact administrator.',
        'error_details' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getMessage() : null
    ]);
} catch (Exception $e) {
    error_log("Error in upload_profile_image.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
}

