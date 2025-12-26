<?php
/**
 * Submit Supply/Budget Request API
 * Document Tracking System - Magallanes National High School
 * 
 * Handles submission of supply requests and budget requests (e.g., snacks)
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
        'message' => 'Access denied. Only teachers can submit requests.'
    ]);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    
    // Get user ID
    $userId = getCurrentUserId();
    
    // Get and validate input
    $requestType = trim($_POST['requestType'] ?? '');
    $itemDescription = trim($_POST['itemDescription'] ?? '');
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unitOfMeasure = trim($_POST['unitOfMeasure'] ?? '');
    $specifications = trim($_POST['specifications'] ?? '');
    $justification = trim($_POST['justification'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Normal');
    $expectedDate = !empty($_POST['expectedDate']) ? $_POST['expectedDate'] : null;
    
    // Additional fields for budget/snack requests
    $eventName = trim($_POST['eventName'] ?? '');
    $eventDate = !empty($_POST['eventDate']) ? $_POST['eventDate'] : null;
    $numberOfParticipants = isset($_POST['numberOfParticipants']) ? (int)$_POST['numberOfParticipants'] : 0;
    $estimatedBudget = isset($_POST['estimatedBudget']) ? (float)$_POST['estimatedBudget'] : 0;
    
    // Validation
    $errors = [];
    
    // Validate request type
    $validRequestTypes = ['equipment', 'food', 'supplies', 'services', 'other'];
    if (!in_array($requestType, $validRequestTypes)) {
        $errors[] = 'Invalid request type. Must be one of: ' . implode(', ', $validRequestTypes) . '.';
    }
    
    // Determine if this is a quantity-based request or food request
    $isQuantityBased = in_array($requestType, ['equipment', 'supplies', 'services', 'other']);
    $isFoodRequest = ($requestType === 'food');
    
    // Validate item description
    if (empty($itemDescription)) {
        $errors[] = 'Item description is required.';
    } elseif (strlen($itemDescription) < 5) {
        $errors[] = 'Item description must be at least 5 characters.';
    } elseif (strlen($itemDescription) > 500) {
        $errors[] = 'Item description must not exceed 500 characters.';
    }
    
    // Validate quantity for quantity-based requests
    if ($isQuantityBased) {
        if ($quantity <= 0) {
            $errors[] = 'Quantity must be greater than 0.';
        } elseif ($quantity > 10000) {
            $errors[] = 'Quantity must not exceed 10,000.';
        }
        
        if (empty($unitOfMeasure)) {
            $errors[] = 'Unit of measure is required.';
        }
    }
    
    // Validate food request fields
    if ($isFoodRequest) {
        if ($numberOfParticipants <= 0) {
            $errors[] = 'Number of participants must be greater than 0.';
        } elseif ($numberOfParticipants > 10000) {
            $errors[] = 'Number of participants must not exceed 10,000.';
        }
    }
    
    // Validate justification
    if (empty($justification)) {
        $errors[] = 'Justification/purpose is required.';
    } elseif (strlen($justification) < 10) {
        $errors[] = 'Justification must be at least 10 characters.';
    } elseif (strlen($justification) > 1000) {
        $errors[] = 'Justification must not exceed 1000 characters.';
    }
    
    // Validate priority
    if (!in_array($priority, ['Normal', 'High', 'Urgent'])) {
        $errors[] = 'Invalid priority level.';
    }
    
    // Validate expected date
    if ($expectedDate) {
        $expectedDateObj = DateTime::createFromFormat('Y-m-d', $expectedDate);
        if (!$expectedDateObj || $expectedDateObj->format('Y-m-d') !== $expectedDate) {
            $errors[] = 'Invalid expected delivery date format.';
        } else {
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            if ($expectedDateObj < $today) {
                $errors[] = 'Expected delivery date cannot be in the past.';
            }
        }
    }
    
    // Additional validation for food requests
    if ($isFoodRequest) {
        if (empty($eventName)) {
            $errors[] = 'Event name is required for budget requests.';
        } elseif (strlen($eventName) > 255) {
            $errors[] = 'Event name must not exceed 255 characters.';
        }
        
        if ($eventDate) {
            $eventDateObj = DateTime::createFromFormat('Y-m-d', $eventDate);
            if (!$eventDateObj || $eventDateObj->format('Y-m-d') !== $eventDate) {
                $errors[] = 'Invalid event date format.';
            }
        }
        
        if ($estimatedBudget <= 0) {
            $errors[] = 'Estimated budget must be greater than 0.';
        } elseif ($estimatedBudget > 1000000) {
            $errors[] = 'Estimated budget must not exceed ₱1,000,000.';
        }
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $errors
        ]);
        exit();
    }
    
    // Generate tracking ID
    $trackingId = generateTrackingId($pdo);
    
    // Insert supply request
    $sql = "INSERT INTO supply_requests (
        tracking_id, 
        requester_id, 
        request_date, 
        priority, 
        justification, 
        expected_delivery_date, 
        status,
        remarks
    ) VALUES (
        :tracking_id,
        :requester_id,
        NOW(),
        :priority,
        :justification,
        :expected_delivery_date,
        'Submitted',
        :remarks
    )";
    
    // Build remarks based on request type
    $requestTypeLabels = [
        'equipment' => 'Equipment',
        'food' => 'Food',
        'supplies' => 'Supplies',
        'services' => 'Services',
        'other' => 'Other'
    ];
    
    $remarks = "Request Type: " . ($requestTypeLabels[$requestType] ?? ucfirst($requestType));
    
    if ($isFoodRequest) {
        $remarks .= "\nEvent: " . $eventName;
        if ($eventDate) {
            $remarks .= "\nEvent Date: " . $eventDate;
        }
        $remarks .= "\nNumber of Participants: " . $numberOfParticipants;
        $remarks .= "\nEstimated Budget: ₱" . number_format($estimatedBudget, 2);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'tracking_id' => $trackingId,
        'requester_id' => $userId,
        'priority' => $priority,
        'justification' => $justification,
        'expected_delivery_date' => $expectedDate,
        'remarks' => $remarks
    ]);
    
    $supplyRequestId = $pdo->lastInsertId();
    
    // Insert supply request item
    // For food requests, we'll use the event name and estimated budget
    if ($isFoodRequest) {
        $itemDesc = "Food - " . $eventName;
        $qty = $numberOfParticipants;
        $unit = 'pax'; // persons/participants
        $specs = "Estimated Budget: ₱" . number_format($estimatedBudget, 2) . 
                 ($eventDate ? "\nEvent Date: " . $eventDate : '') .
                 ($specifications ? "\n\nAdditional Details:\n" . $specifications : '');
    } else {
        // For equipment, supplies, services, and other
        $itemDesc = $itemDescription;
        $qty = $quantity;
        $unit = $unitOfMeasure;
        $specs = $specifications;
    }
    
    $itemSql = "INSERT INTO supply_request_items (
        supply_request_id,
        item_description,
        quantity,
        unit_of_measure,
        specifications
    ) VALUES (
        :supply_request_id,
        :item_description,
        :quantity,
        :unit_of_measure,
        :specifications
    )";
    
    $itemStmt = $pdo->prepare($itemSql);
    $itemStmt->execute([
        'supply_request_id' => $supplyRequestId,
        'item_description' => $itemDesc,
        'quantity' => $qty,
        'unit_of_measure' => $unit,
        'specifications' => $specs
    ]);
    
    // Create document tracking entry
    $trackingSql = "INSERT INTO document_tracking (
        document_type,
        document_id,
        document_number,
        current_status,
        current_office_id,
        tracked_at,
        tracked_by
    ) VALUES (
        'Supply Request',
        :document_id,
        :document_number,
        'Submitted',
        NULL,
        NOW(),
        :tracked_by
    )";
    
    $trackingStmt = $pdo->prepare($trackingSql);
    $trackingStmt->execute([
        'document_id' => $supplyRequestId,
        'document_number' => $trackingId,
        'tracked_by' => $userId
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Request submitted successfully!',
        'tracking_id' => $trackingId,
        'supply_request_id' => $supplyRequestId
    ]);
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database error in submit_request.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your request. Please try again.'
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in submit_request.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
}

/**
 * Generate unique tracking ID
 * Format: YYYY-SR-XXX (e.g., 2025-SR-001)
 * 
 * @param PDO $pdo Database connection
 * @return string Tracking ID
 */
function generateTrackingId($pdo) {
    $year = date('Y');
    $prefix = $year . '-SR-';
    
    // Get the highest number for this year
    $sql = "SELECT tracking_id FROM supply_requests 
            WHERE tracking_id LIKE :prefix 
            ORDER BY tracking_id DESC 
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['prefix' => $prefix . '%']);
    $lastTrackingId = $stmt->fetchColumn();
    
    if ($lastTrackingId) {
        // Extract the number part
        $lastNumber = (int)substr($lastTrackingId, strlen($prefix));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    // Format with leading zeros (3 digits)
    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

