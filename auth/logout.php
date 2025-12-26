<?php
/**
 * Logout Handler
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../includes/session.php';

// Logout user
logout();

// Redirect to login page
header('Location: login.php');
exit();

