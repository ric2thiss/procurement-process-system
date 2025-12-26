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
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">5</span>
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
                </ul>
            </nav>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-green-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-xs text-green-200"><?php echo htmlspecialchars($userRole); ?></p>
                    </div>
                </div>
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
                <!-- Right Side: Notification -->
                <div class="flex items-center flex-shrink-0">
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
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
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">15</p>
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
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">1,247</p>
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
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">8</p>
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
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">42</p>
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
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">RIS Generated</p>
                                    <p class="text-xs text-gray-600">RIS-2025-042 for John Doe</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shopping-cart text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">PR Created</p>
                                    <p class="text-xs text-gray-600">PR-2025-089 for Projector Lamp</p>
                                    <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation text-yellow-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Low Stock Alert</p>
                                    <p class="text-xs text-gray-600">A4 Bond Paper - 5 reams remaining</p>
                                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Low Stock Alerts</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                                <div>
                                    <p class="font-semibold text-gray-900">A4 Bond Paper</p>
                                    <p class="text-sm text-gray-600">5 reams remaining (Threshold: 10)</p>
                                </div>
                                <span class="text-yellow-600 font-bold">5</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                                <div>
                                    <p class="font-semibold text-gray-900">Whiteboard Markers</p>
                                    <p class="text-sm text-gray-600">3 boxes remaining (Threshold: 5)</p>
                                </div>
                                <span class="text-yellow-600 font-bold">3</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                                <div>
                                    <p class="font-semibold text-gray-900">Ballpoint Pens</p>
                                    <p class="text-sm text-gray-600">8 boxes remaining (Threshold: 10)</p>
                                </div>
                                <span class="text-yellow-600 font-bold">8</span>
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
                        <input 
                            type="text" 
                            placeholder="Search by tracking ID or requester..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="pending">Pending Review</option>
                            <option value="checked">Inventory Checked</option>
                            <option value="ris-generated">RIS Generated</option>
                            <option value="pr-created">PR Created</option>
                        </select>
                        <input 
                            type="date" 
                            placeholder="From Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <input 
                            type="date" 
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
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600">2025-SR-024</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">John Doe</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">Whiteboard Markers (Set of 10)</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">2 sets</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">2025-01-15 10:30</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Pending Review</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="processRequest('2025-SR-024')" class="text-green-600 hover:text-green-800 mr-2 p-1" title="Process Request">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button onclick="viewRequestDetails('2025-SR-024')" class="text-blue-600 hover:text-blue-800 p-1" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600">2025-SR-023</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">Jane Smith</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">Projector Lamp</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">1 piece</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">2025-01-14 14:20</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Inventory Checked</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="processRequest('2025-SR-023')" class="text-green-600 hover:text-green-800 mr-2 p-1" title="Process Request">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button onclick="viewRequestDetails('2025-SR-023')" class="text-blue-600 hover:text-blue-800 p-1" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600">2025-SR-022</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">Robert Johnson</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">A4 Bond Paper (Ream)</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">10 reams</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">2025-01-13 09:15</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">RIS Generated</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="viewRIS('RIS-2025-042')" class="text-purple-600 hover:text-purple-800 mr-2 p-1" title="View RIS">
                                                <i class="fas fa-file-invoice"></i>
                                            </button>
                                            <button onclick="viewRequestDetails('2025-SR-022')" class="text-blue-600 hover:text-blue-800 p-1" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Showing 1-3 of 15 requests</p>
                        <div class="flex space-x-2">
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                            <button class="px-3 py-2 bg-[#103D1C] text-white rounded-lg">1</button>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
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
                            <button onclick="closeProcessModal()" class="text-gray-400 hover:text-gray-600 p-1">
                                <i class="fas fa-times text-xl"></i>
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
                            <button onclick="checkInventory()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
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
                            placeholder="Search items..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Categories</option>
                            <option value="office">Office Supplies</option>
                            <option value="teaching">Teaching Materials</option>
                            <option value="equipment">Equipment</option>
                            <option value="consumables">Consumables</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Stock Status</option>
                            <option value="in-stock">In Stock</option>
                            <option value="low-stock">Low Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
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
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-gray-900">ITEM-001</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">Whiteboard Markers (Set of 10)</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">Teaching Materials</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">Set</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <span class="text-xs lg:text-sm font-semibold text-gray-900">25</span>
                                            <span class="text-xs text-gray-500"> (Threshold: 5)</span>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell break-words">Warehouse A, Shelf 3</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">In Stock</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="editInventoryItem('ITEM-001')" class="text-blue-600 hover:text-blue-800 mr-2 p-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="viewInventoryHistory('ITEM-001')" class="text-purple-600 hover:text-purple-800 p-1">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-gray-900">ITEM-002</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">A4 Bond Paper (Ream)</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">Office Supplies</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">Ream</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <span class="text-xs lg:text-sm font-semibold text-yellow-600">5</span>
                                            <span class="text-xs text-gray-500"> (Threshold: 10)</span>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell break-words">Warehouse B, Shelf 1</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Low Stock</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="editInventoryItem('ITEM-002')" class="text-blue-600 hover:text-blue-800 mr-2 p-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="viewInventoryHistory('ITEM-002')" class="text-purple-600 hover:text-purple-800 p-1">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-gray-900">ITEM-003</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">Projector Lamp</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">Equipment</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">Piece</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <span class="text-xs lg:text-sm font-semibold text-red-600">0</span>
                                            <span class="text-xs text-gray-500"> (Threshold: 2)</span>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell break-words">Warehouse A, Shelf 5</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">Out of Stock</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="editInventoryItem('ITEM-003')" class="text-blue-600 hover:text-blue-800 mr-2 p-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="viewInventoryHistory('ITEM-003')" class="text-purple-600 hover:text-purple-800 p-1">
                                                <i class="fas fa-history"></i>
                                            </button>
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
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600">RIS-2025-042</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">2025-SR-022</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">Robert Johnson</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">A4 Bond Paper (Ream)</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">10 reams</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">2025-01-13</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Issued</span></td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <button onclick="viewRIS('RIS-2025-042')" class="text-blue-600 hover:text-blue-800 mr-2 p-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="printRIS('RIS-2025-042')" class="text-green-600 hover:text-green-800 p-1">
                                                <i class="fas fa-print"></i>
                                            </button>
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
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity duration-300"></div>

    <script src="../../assets/js/supply-dashboard.js"></script>
</body>
</html>

