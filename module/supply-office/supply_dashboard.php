<?php
/**
 * Supply Office Dashboard
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require login and check role
requireLogin();

// Check if user has SUPPLY role
if (getCurrentUserRole() !== 'SUPPLY') {
    header('Location: /dts/auth/login.php');
    exit();
}

// Get user info from session
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$userRole = $_SESSION['role_name'] ?? 'Supply Officer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Office Dashboard - Document Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/global/main.css">
    <link rel="stylesheet" href="../../assets/css/pages/supply-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Sidebar open state for mobile */
        #sidebar.open {
            transform: translateX(0) !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-[#103D1C] text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-50">
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div class="p-4 lg:p-6 border-b border-green-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 flex items-center justify-center bg-white rounded-lg">
                        <img src="../../assets/img/logo.png" alt="Logo" class="logo-img">
                    </div>
                    <div>
                        <h1 class="text-base lg:text-lg font-bold text-white">DTS</h1>
                        <p class="text-xs text-green-200">Supply Office</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="#dashboard" data-section="dashboard" class="nav-item active flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#supply-requests" data-section="supply-requests" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-clipboard-list w-5"></i>
                            <span>Supply Requests</span>
                            <span id="supplyRequestsBadge" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="#inventory" data-section="inventory" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-boxes w-5"></i>
                            <span>Inventory</span>
                        </a>
                    </li>
                    <li>
                        <a href="#ris-management" data-section="ris-management" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-invoice w-5"></i>
                            <span>RIS Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reports" data-section="reports" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Inventory Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="#profile" data-section="profile" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-user-cog w-5"></i>
                            <span>Profile & Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-green-800">
                <a href="#profile" data-section="profile" class="nav-item flex items-center space-x-3 mb-3 p-2 rounded-lg hover:bg-green-800 transition-colors cursor-pointer">
                    <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0 relative">
                        <img 
                            id="sidebarProfileImage" 
                            src="" 
                            alt="Profile" 
                            class="w-full h-full object-cover hidden"
                        >
                        <div id="sidebarProfileImagePlaceholder" class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate"><?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-xs text-green-200 truncate"><?php echo htmlspecialchars($userRole); ?></p>
                    </div>
                </a>
                <a href="../../auth/logout.php" class="w-full bg-green-700 hover:bg-green-600 px-4 py-2 rounded-lg text-sm transition-colors block text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="flex items-center justify-between px-4 py-3 lg:px-6 lg:py-4"> 
                <!-- Left Side: Hamburger Menu + Title/Subtitle -->
                <div class="flex items-center justify-around">
                    <!-- "flex items-center gap-4 flex-1 min-w-0" -->
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900 p-2 flex-shrink-0 w-fit">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex flex-col">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900" id="pageTitle">Dashboard Overview</h2>
                        <p class="text-xs lg:text-sm text-gray-600" id="pageSubtitle">Supply office statistics and monitoring</p>
                    </div>
                </div>
                <!-- Right Side: Notification and Date/Time -->
                <div class="flex items-center space-x-2 lg:space-x-4 flex-shrink-0">
                    <div class="relative">
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bell text-base lg:text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs lg:text-sm font-semibold text-gray-900" id="currentDate"></p>
                        <p class="text-xs text-gray-600" id="currentTime"></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-4 lg:p-6">
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Pending Requests</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statPendingRequests">0</p>
                                <p class="text-xs text-yellow-600 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Requires action</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-clipboard-list text-blue-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Items Available</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statItemsAvailable">0</p>
                                <p class="text-xs text-green-600 mt-1">In stock</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-boxes text-green-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Low Stock Items</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statLowStock">0</p>
                                <p class="text-xs text-yellow-600 mt-1">Below threshold</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-exclamation-circle text-yellow-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">RIS Generated</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statRISGenerated">0</p>
                                <p class="text-xs text-purple-600 mt-1">This month</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-file-invoice text-purple-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-6 lg:mb-8">
                    <!-- Inventory Status Chart -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Inventory Status</h3>
                        <div class="chart-container" style="position: relative; height: 250px;">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>

                    <!-- Request Processing Chart -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Request Processing (This Month)</h3>
                        <div class="chart-container" style="position: relative; height: 250px;">
                            <canvas id="requestChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity and Low Stock Alerts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-4 max-h-96 overflow-y-auto" id="recentActivityContainer">
                            <!-- Loading state -->
                            <div class="flex items-center justify-center py-8">
                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Loading recent activity...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Low Stock Alerts</h3>
                        <div class="space-y-3" id="lowStockAlertsContainer">
                            <!-- Loading state -->
                            <div class="flex items-center justify-center py-8">
                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Loading low stock alerts...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Supply Requests Section -->
            <section id="supply-requests-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 lg:mb-6">
                        <div>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Supply Requests</h3>
                            <p class="text-sm lg:text-base text-gray-600">Review and process supply requests from teachers</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm lg:text-base">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <button class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm lg:text-base">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="supplyRequestsSearch"
                                placeholder="Search by tracking ID or requester..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 pr-10"
                            >
                            <button 
                                id="clearSearchBtn"
                                onclick="clearSearch()"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                                title="Clear search"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <select id="supplyRequestsStatusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="submitted">Submitted</option>
                            <option value="available">Available</option>
                            <option value="not-available">Not Available</option>
                            <option value="pending-ppmp">Pending PPMP</option>
                            <option value="for-approval">For Approval</option>
                            <option value="approved">Approved</option>
                            <option value="under-procurement">Under Procurement</option>
                            <option value="completed">Completed</option>
                        </select>
                        <input 
                            type="date" 
                            id="supplyRequestsFromDate"
                            placeholder="From Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <input 
                            type="date" 
                            id="supplyRequestsToDate"
                            placeholder="To Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>

                    <!-- Requests Table -->
                    <div class="overflow-x-auto -mx-4 lg:mx-0 lg:overflow-x-visible">
                        <div class="inline-block min-w-full align-middle lg:w-full">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[12%] lg:min-w-[120px]">Tracking ID</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[15%] lg:min-w-[120px]">Requester</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[30%] lg:min-w-[200px] break-words">Item Description</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden md:table-cell lg:w-[10%] lg:min-w-[100px]">Quantity</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden lg:table-cell lg:w-[12%] lg:min-w-[120px]">Date Requested</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[12%] lg:min-w-[100px]">Status</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[7%] lg:min-w-[100px]">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="supplyRequestsTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Loading state -->
                                    <tr id="supplyRequestsLoading">
                                        <td colspan="7" class="px-3 lg:px-4 py-8 text-center">
                                            <div class="flex items-center justify-center">
                                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                                <span class="text-gray-600">Loading requests...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Empty state (hidden by default) -->
                                    <tr id="supplyRequestsEmpty" class="hidden">
                                        <td colspan="7" class="px-3 lg:px-4 py-8 text-center text-gray-600">
                                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                            <p>No requests found.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="supplyRequestsPagination" class="mt-6 flex items-center justify-between hidden">
                        <p class="text-sm text-gray-600" id="supplyRequestsPaginationInfo">Showing 0-0 of 0 requests</p>
                        <div class="flex space-x-2" id="supplyRequestsPaginationButtons">
                            <!-- Pagination buttons will be inserted here -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Request Processing Modal -->
            <div id="processModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-4 lg:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg lg:text-xl font-bold text-gray-900">Process Supply Request</h3>
                            <button onclick="closeProcessModal()" class="text-gray-400 hover:text-gray-600 p-1 w-fit lg:w-auto" style="width: fit-content;">
                                <i class="fas fa-times text-lg lg:text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 lg:p-6">
                        <!-- Request Details -->
                        <div class="mb-4 lg:mb-6 bg-gray-50 p-3 lg:p-4 rounded-lg">
                            <h4 class="text-sm lg:text-base font-semibold text-gray-900 mb-3">Request Details</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Tracking ID</p>
                                    <p class="font-semibold text-gray-900" id="modalTrackingId">2025-SR-024</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Requester</p>
                                    <p class="font-semibold text-gray-900" id="modalRequester">John Doe</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Item Description</p>
                                    <p class="font-semibold text-gray-900" id="modalItem">Whiteboard Markers</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantity Requested</p>
                                    <p class="font-semibold text-gray-900" id="modalQuantity">2 sets</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Check -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">
                                <i class="fas fa-boxes text-green-600 mr-2"></i>Inventory Check
                            </h4>
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 lg:p-4 rounded-lg mb-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Current Stock</p>
                                        <p class="text-2xl font-bold text-gray-900" id="currentStock">25 sets</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Stock Location</p>
                                        <p class="font-semibold text-gray-900" id="stockLocation">Warehouse A, Shelf 3</p>
                                    </div>
                                </div>
                            </div>
                            <button onclick="checkInventory(event)" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-search mr-2"></i>Check Inventory Availability
                            </button>
                        </div>

                        <!-- Decision Section -->
                        <div id="decisionSection" class="hidden">
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Processing Decision</h4>
                                
                                <!-- If Available -->
                                <div id="availableSection" class="hidden">
                                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                                        <p class="font-semibold text-green-900">
                                            <i class="fas fa-check-circle mr-2"></i>Item is Available in Stock
                                        </p>
                                        <p class="text-sm text-green-700 mt-1">You can proceed to generate RIS and issue the item.</p>
                                    </div>
                                    <button onclick="showRISForm()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors mb-4">
                                        <i class="fas fa-file-invoice mr-2"></i>Generate Requisition and Issue Slip (RIS)
                                    </button>
                                </div>

                                <!-- If Not Available -->
                                <div id="notAvailableSection" class="hidden">
                                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                                        <p class="font-semibold text-red-900">
                                            <i class="fas fa-times-circle mr-2"></i>Item is Not Available in Stock
                                        </p>
                                        <p class="text-sm text-red-700 mt-1">Forward to Procurement / BAC Secretariat. Proceed to create or update PPMP (STEP 2).</p>
                                        <p class="text-xs text-red-600 mt-2">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Note:</strong> PR cannot be created until PPMP is created, submitted to Budget Office, consolidated into APP, and APP is approved (STEP 3-6).
                                        </p>
                                    </div>
                                    <button onclick="forwardToPPMP()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-file-contract mr-2"></i>Forward to PPMP Management (STEP 3: Create/Update PPMP)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Request Details Modal -->
            <div id="viewDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-4 lg:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg lg:text-xl font-bold text-gray-900">Request Details</h3>
                            <button onclick="closeViewDetailsModal()" class="text-gray-400 hover:text-gray-600 p-1 w-fit lg:w-auto" style="width: fit-content;">
                                <i class="fas fa-times text-lg lg:text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 lg:p-6">
                        <!-- Request Details -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Tracking ID</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsTrackingId">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p id="viewDetailsStatus">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Requester</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsRequester">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Request Date</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsRequestDate">-</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-gray-600">Item Description</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsItem">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Quantity Requested</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsQuantity">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Unit of Measure</p>
                                    <p class="font-semibold text-gray-900" id="viewDetailsUnit">-</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-gray-600">Justification</p>
                                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg" id="viewDetailsJustification">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Inventory Item Modal -->
            <div id="addInventoryItemModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" onclick="if(event.target === this) closeAddInventoryItemModal()">
                <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="p-4 lg:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg lg:text-xl font-bold text-gray-900">Add New Inventory Item</h3>
                            <button onclick="closeAddInventoryItemModal()" class="text-gray-400 hover:text-gray-600 w-fit lg:w-auto" style="width: fit-content;">
                                <i class="fas fa-times text-lg lg:text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <form id="addInventoryItemForm" class="p-4 lg:p-6">
                        <!-- Error Message Container -->
                        <div id="addInventoryItemError" class="hidden mb-4">
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold">Error</p>
                                        <p id="addInventoryItemErrorText" class="text-sm mt-1"></p>
                                    </div>
                                    <button type="button" onclick="closeAddInventoryItemError()" class="text-red-500 hover:text-red-700 ml-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Message Container -->
                        <div id="addInventoryItemSuccess" class="hidden mb-4">
                            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold">Success</p>
                                        <p id="addInventoryItemSuccessText" class="text-sm mt-1"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4 lg:space-y-6">
                            <!-- Item Code -->
                            <div>
                                <label for="addItemCode" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Item Code <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="addItemCode" 
                                    name="item_code"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Enter item code (e.g., ITEM-001)"
                                    required
                                >
                                <p class="text-xs text-gray-500 mt-1">Must be unique</p>
                            </div>

                            <!-- Item Description -->
                            <div>
                                <label for="addItemDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Item Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="addItemDescription" 
                                    name="item_description"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Enter item description"
                                    required
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                                <!-- Category -->
                                <div>
                                    <label for="addItemCategory" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Category
                                    </label>
                                    <select 
                                        id="addItemCategory" 
                                        name="category"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="">Select Category</option>
                                        <option value="Office Supplies">Office Supplies</option>
                                        <option value="Teaching Materials">Teaching Materials</option>
                                        <option value="Equipment">Equipment</option>
                                        <option value="Consumables">Consumables</option>
                                    </select>
                                </div>

                                <!-- Unit of Measure -->
                                <div>
                                    <label for="addItemUnitOfMeasure" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Unit of Measure <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="addItemUnitOfMeasure" 
                                        name="unit_of_measure"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., Pieces, Ream, Box"
                                        required
                                    >
                                </div>

                                <!-- Standard Unit Price -->
                                <div>
                                    <label for="addItemStandardPrice" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Standard Unit Price
                                    </label>
                                    <input 
                                        type="number" 
                                        id="addItemStandardPrice" 
                                        name="standard_unit_price"
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="0.00"
                                    >
                                </div>

                                <!-- Initial Stock On Hand -->
                                <div>
                                    <label for="addItemStockOnHand" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Initial Stock On Hand
                                    </label>
                                    <input 
                                        type="number" 
                                        id="addItemStockOnHand" 
                                        name="stock_on_hand"
                                        min="0"
                                        value="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                </div>

                                <!-- Reorder Level -->
                                <div>
                                    <label for="addItemReorderLevel" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Reorder Level
                                    </label>
                                    <input 
                                        type="number" 
                                        id="addItemReorderLevel" 
                                        name="reorder_level"
                                        min="0"
                                        value="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Alert when stock reaches this level</p>
                                </div>

                                <!-- Reorder Quantity -->
                                <div>
                                    <label for="addItemReorderQuantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Reorder Quantity
                                    </label>
                                    <input 
                                        type="number" 
                                        id="addItemReorderQuantity" 
                                        name="reorder_quantity"
                                        min="0"
                                        value="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Recommended order quantity</p>
                                </div>

                                <!-- Location -->
                                <div class="md:col-span-2">
                                    <label for="addItemLocation" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Location
                                    </label>
                                    <input 
                                        type="text" 
                                        id="addItemLocation" 
                                        name="location"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., Warehouse A, Shelf 1"
                                    >
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="addItemNotes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Notes
                                </label>
                                <textarea 
                                    id="addItemNotes" 
                                    name="notes"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Additional notes (optional)"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <button 
                                type="button" 
                                onclick="closeAddInventoryItemModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm lg:text-base"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base"
                            >
                                <i class="fas fa-plus mr-2"></i>Add Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Inventory Item Modal -->
            <div id="editInventoryItemModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" onclick="if(event.target === this) closeEditInventoryItemModal()">
                <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="p-4 lg:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg lg:text-xl font-bold text-gray-900">Edit Inventory Item</h3>
                            <button onclick="closeEditInventoryItemModal()" class="text-gray-400 hover:text-gray-600 w-fit lg:w-auto" style="width: fit-content;">
                                <i class="fas fa-times text-lg lg:text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <form id="editInventoryItemForm" class="p-4 lg:p-6">
                        <input type="hidden" id="editItemId" name="item_id">
                        
                        <!-- Error Message Container -->
                        <div id="editInventoryItemError" class="hidden mb-4">
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold">Error</p>
                                        <p id="editInventoryItemErrorText" class="text-sm mt-1"></p>
                                    </div>
                                    <button type="button" onclick="closeEditInventoryItemError()" class="text-red-500 hover:text-red-700 ml-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Message Container -->
                        <div id="editInventoryItemSuccess" class="hidden mb-4">
                            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold">Success</p>
                                        <p id="editInventoryItemSuccessText" class="text-sm mt-1"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4 lg:space-y-6">
                            <!-- Item Code -->
                            <div>
                                <label for="editItemCode" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Item Code <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="editItemCode" 
                                    name="item_code"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Enter item code (e.g., ITEM-001)"
                                    required
                                >
                                <p class="text-xs text-gray-500 mt-1">Must be unique</p>
                            </div>

                            <!-- Item Description -->
                            <div>
                                <label for="editItemDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Item Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="editItemDescription" 
                                    name="item_description"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Enter item description"
                                    required
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                                <!-- Category -->
                                <div>
                                    <label for="editItemCategory" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Category
                                    </label>
                                    <select 
                                        id="editItemCategory" 
                                        name="category"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="">Select Category</option>
                                        <option value="Office Supplies">Office Supplies</option>
                                        <option value="Teaching Materials">Teaching Materials</option>
                                        <option value="Equipment">Equipment</option>
                                        <option value="Consumables">Consumables</option>
                                    </select>
                                </div>

                                <!-- Unit of Measure -->
                                <div>
                                    <label for="editItemUnitOfMeasure" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Unit of Measure <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="editItemUnitOfMeasure" 
                                        name="unit_of_measure"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., Pieces, Ream, Box"
                                        required
                                    >
                                </div>

                                <!-- Standard Unit Price -->
                                <div>
                                    <label for="editItemStandardPrice" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Standard Unit Price
                                    </label>
                                    <input 
                                        type="number" 
                                        id="editItemStandardPrice" 
                                        name="standard_unit_price"
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="0.00"
                                    >
                                </div>

                                <!-- Stock On Hand (Read-only display) -->
                                <div>
                                    <label for="editItemStockOnHand" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Current Stock On Hand
                                    </label>
                                    <input 
                                        type="number" 
                                        id="editItemStockOnHand"
                                        name="stock_on_hand"
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Changing stock will create a history entry.</p>
                                </div>

                                <!-- Reorder Level -->
                                <div>
                                    <label for="editItemReorderLevel" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Reorder Level
                                    </label>
                                    <input 
                                        type="number" 
                                        id="editItemReorderLevel" 
                                        name="reorder_level"
                                        min="0"
                                        value="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Alert when stock reaches this level</p>
                                </div>

                                <!-- Reorder Quantity -->
                                <div>
                                    <label for="editItemReorderQuantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Reorder Quantity
                                    </label>
                                    <input 
                                        type="number" 
                                        id="editItemReorderQuantity" 
                                        name="reorder_quantity"
                                        min="0"
                                        value="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Recommended order quantity</p>
                                </div>

                                <!-- Location -->
                                <div class="md:col-span-2">
                                    <label for="editItemLocation" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Location
                                    </label>
                                    <input 
                                        type="text" 
                                        id="editItemLocation" 
                                        name="location"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., Warehouse A, Shelf 1"
                                    >
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="editItemNotes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Notes
                                </label>
                                <textarea 
                                    id="editItemNotes" 
                                    name="notes"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Additional notes (optional)"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <button 
                                type="button" 
                                onclick="closeEditInventoryItemModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm lg:text-base"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base"
                            >
                                <i class="fas fa-save mr-2"></i>Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory History Modal -->
            <div id="inventoryHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-start justify-center p-4 pt-6" onclick="if(event.target === this) closeInventoryHistoryModal()">
                <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto mt-6" onclick="event.stopPropagation()">
                    <div class="p-4 lg:p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg lg:text-xl font-bold text-gray-900">Inventory History</h3>
                                <p class="text-xs lg:text-sm text-gray-600 mt-1" id="historyItemInfo">Loading...</p>
                            </div>
                            <button onclick="closeInventoryHistoryModal()" class="text-gray-400 hover:text-gray-600 w-fit lg:w-auto" style="width: fit-content;">
                                <i class="fas fa-times text-lg lg:text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 lg:p-6">
                        <!-- Loading State -->
                        <div id="historyLoading" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-600">Loading history...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="historyEmpty" class="hidden text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-600">No movement history found for this item.</p>
                        </div>

                        <!-- History Table -->
                        <div id="historyContent" class="hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Date & Time</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Type</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Quantity</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Stock Before</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Stock After</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Reference</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Performed By</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- History rows will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Section -->
            <section id="inventory-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 lg:mb-6">
                        <div>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Inventory Management</h3>
                            <p class="text-sm lg:text-base text-gray-600">Manage inventory items and stock levels</p>
                        </div>
                        <button onclick="showAddItemModal()" class="w-full sm:w-auto bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors text-sm lg:text-base">
                            <i class="fas fa-plus mr-2"></i>Add New Item
                        </button>
                    </div>

                    <!-- Filters and Search -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input 
                            type="text" 
                            id="inventorySearch"
                            placeholder="Search items..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select id="inventoryCategoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Categories</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Teaching Materials">Teaching Materials</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Consumables">Consumables</option>
                        </select>
                        <select id="inventoryStockStatusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Stock Status</option>
                            <option value="in-stock">In Stock</option>
                            <option value="low-stock">Low Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Inventory Table -->
                    <div class="overflow-x-auto -mx-4 lg:mx-0 lg:overflow-x-visible">
                        <div class="inline-block min-w-full align-middle lg:w-full">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[10%] lg:min-w-[100px]">Item Code</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[25%] lg:min-w-[200px] break-words">Item Description</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden md:table-cell lg:w-[12%] lg:min-w-[120px]">Category</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden lg:table-cell lg:w-[8%] lg:min-w-[80px]">Unit</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[15%] lg:min-w-[120px]">Stock On Hand</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden lg:table-cell lg:w-[15%] lg:min-w-[150px] break-words">Location</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[10%] lg:min-w-[100px]">Status</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[5%] lg:min-w-[100px]">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inventoryTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Loading state -->
                                    <tr id="inventoryLoading">
                                        <td colspan="8" class="px-3 lg:px-4 py-8 text-center">
                                            <div class="flex items-center justify-center">
                                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                                <span class="text-gray-600">Loading inventory...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Empty state (hidden by default) -->
                                    <tr id="inventoryEmpty" class="hidden">
                                        <td colspan="8" class="px-3 lg:px-4 py-8 text-center text-gray-600">
                                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                            <p>No inventory items found.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RIS Management Section -->
            <section id="ris-management-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                    <div class="mb-4 lg:mb-6">
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">RIS Management</h3>
                        <p class="text-sm lg:text-base text-gray-600">Manage Requisition and Issue Slips</p>
                    </div>

                    <!-- RIS List -->
                    <div class="overflow-x-auto -mx-4 lg:mx-0 lg:overflow-x-visible">
                        <div class="inline-block min-w-full align-middle lg:w-full">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[12%] lg:min-w-[120px]">RIS Number</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[12%] lg:min-w-[120px]">Supply Request ID</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[15%] lg:min-w-[120px]">Requester</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[25%] lg:min-w-[200px] break-words">Item Description</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden md:table-cell lg:w-[10%] lg:min-w-[100px]">Quantity</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase hidden lg:table-cell lg:w-[12%] lg:min-w-[120px]">Date Generated</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[10%] lg:min-w-[100px]">Status</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase lg:w-[4%] lg:min-w-[100px]">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="risTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Loading state -->
                                    <tr id="risLoading">
                                        <td colspan="8" class="px-3 lg:px-4 py-8 text-center">
                                            <div class="flex items-center justify-center">
                                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                                <span class="text-gray-600">Loading RIS list...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Empty state (hidden by default) -->
                                    <tr id="risEmpty" class="hidden">
                                        <td colspan="8" class="px-3 lg:px-4 py-8 text-center text-gray-600">
                                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                            <p>No RIS found.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Note: PR Creation Removed from Supply Office -->
            <!-- 
                IMPORTANT PROCESS NOTE:
                Purchase Requests (PR) should NOT be created directly from Supply Office.
                
                Correct Process Flow (per documentation-2.md):
                1. STEP 2: Item Not Available → Forward to Procurement/BAC Secretariat → Create/Update PPMP
                2. STEP 3: Create or Update PPMP
                3. STEP 4: Submit PPMP to Budget Office
                4. STEP 5: BAC consolidates PPMP into APP → HoPE approves APP
                5. STEP 6: Create PR referencing approved APP (in PR & PPMP Module)
                
                PR creation is handled by the Purchase Request & PPMP Module (STEP 6)
                after PPMP is created, submitted to Budget Office, consolidated into APP,
                and APP is approved by HoPE.
            -->

            <!-- Reports Section -->
            <section id="reports-section" class="content-section hidden">
                <div class="space-y-4 lg:space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4 lg:mb-6">Inventory Reports</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                            <div class="border-2 border-gray-200 rounded-lg p-4 lg:p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-3 lg:space-x-4 mb-3 lg:mb-4">
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-boxes text-blue-600 text-lg lg:text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm lg:text-base font-bold text-gray-900">Inventory Status Report</h4>
                                        <p class="text-xs text-gray-600">Current stock levels</p>
                                    </div>
                                </div>
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs lg:text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-4 lg:p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-3 lg:space-x-4 mb-3 lg:mb-4">
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 text-lg lg:text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm lg:text-base font-bold text-gray-900">Low Stock Report</h4>
                                        <p class="text-xs text-gray-600">Items below threshold</p>
                                    </div>
                                </div>
                                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-xs lg:text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-4 lg:p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-3 lg:space-x-4 mb-3 lg:mb-4">
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-invoice text-green-600 text-lg lg:text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm lg:text-base font-bold text-gray-900">RIS Report</h4>
                                        <p class="text-xs text-gray-600">Issuance history</p>
                                    </div>
                                </div>
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs lg:text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-4 lg:p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-3 lg:space-x-4 mb-3 lg:mb-4">
                                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-line text-purple-600 text-lg lg:text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm lg:text-base font-bold text-gray-900">Stock Movement</h4>
                                        <p class="text-xs text-gray-600">Stock movement history</p>
                                    </div>
                                </div>
                                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-xs lg:text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Profile & Settings Section -->
            <section id="profile-section" class="content-section hidden">
                <div class="space-y-6">
                    <!-- Profile Image Card -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <div class="mb-4 lg:mb-6">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Profile Picture</h3>
                            <p class="text-sm lg:text-base text-gray-600">Upload or change your profile image</p>
                        </div>

                        <div class="flex flex-col items-center space-y-4">
                            <!-- Profile Image Display -->
                            <div class="relative">
                                <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-full overflow-hidden bg-gray-200 border-4 border-gray-300 flex items-center justify-center">
                                    <img 
                                        id="profileImageDisplay" 
                                        src="" 
                                        alt="Profile Image" 
                                        class="w-full h-full object-cover hidden"
                                    >
                                    <div id="profileImagePlaceholder" class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400 text-5xl lg:text-6xl"></i>
                                    </div>
                                </div>
                                <div id="profileImageLoading" class="absolute inset-0 bg-white bg-opacity-75 rounded-full flex items-center justify-center hidden">
                                    <i class="fas fa-spinner fa-spin text-green-600 text-2xl"></i>
                                </div>
                            </div>

                            <!-- Upload Button -->
                            <div class="flex flex-col items-center space-y-2">
                                <label for="profileImageInput" class="cursor-pointer">
                                    <input 
                                        type="file" 
                                        id="profileImageInput" 
                                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                        class="hidden"
                                    >
                                    <span class="inline-flex items-center px-4 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base">
                                        <i class="fas fa-camera mr-2"></i>Upload Image
                                    </span>
                                </label>
                                <p class="text-xs text-gray-500 text-center max-w-xs">
                                    JPG, PNG, GIF or WebP. Max size 5MB
                                </p>
                                <button 
                                    type="button" 
                                    id="removeProfileImageBtn" 
                                    class="text-red-600 hover:text-red-800 text-sm hidden"
                                    onclick="removeProfileImage()"
                                >
                                    <i class="fas fa-trash mr-1"></i>Remove Image
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Information Card -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <div class="mb-4 lg:mb-6">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Profile Information</h3>
                            <p class="text-sm lg:text-base text-gray-600">Update your personal information</p>
                        </div>

                        <form id="profileForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- First Name -->
                                <div>
                                    <label for="profileFirstName" class="block text-sm font-semibold text-gray-700 mb-2">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="profileFirstName" 
                                        name="first_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter first name"
                                        required
                                    >
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label for="profileLastName" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="profileLastName" 
                                        name="last_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter last name"
                                        required
                                    >
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="profileEmail" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        id="profileEmail" 
                                        name="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter email address"
                                        required
                                    >
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="profilePhone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="profilePhone" 
                                        name="phone"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter phone number"
                                    >
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base"
                                >
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Information Card -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <div class="mb-4 lg:mb-6">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Account Information</h3>
                            <p class="text-sm lg:text-base text-gray-600">Your account details (read-only)</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">User ID</label>
                                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-900" id="profileUserId">-</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-900" id="profileRole">-</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Created</label>
                                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-900" id="profileCreatedAt">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <div class="mb-4 lg:mb-6">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Change Password</h3>
                            <p class="text-sm lg:text-base text-gray-600">Update your account password</p>
                        </div>

                        <form id="passwordForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Current Password -->
                                <div>
                                    <label for="currentPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Current Password <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        id="currentPassword" 
                                        name="current_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter current password"
                                        required
                                    >
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="newPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                                        New Password <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        id="newPassword" 
                                        name="new_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter new password"
                                        required
                                        minlength="8"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="confirmPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        id="confirmPassword" 
                                        name="confirm_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Confirm new password"
                                        required
                                        minlength="8"
                                    >
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base"
                                >
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity duration-300"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script type="module" src="../../assets/supply/main.js"></script>
</body>
</html>

