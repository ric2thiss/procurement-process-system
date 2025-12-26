<?php
/**
 * Document Tracking & Audit Dashboard
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require login and check role
requireLogin();

// Check if user has AUDITOR role
if (getCurrentUserRole() !== 'AUDITOR') {
    header('Location: /dts/auth/login.php');
    exit();
}

// Get user info from session
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$userRole = $_SESSION['role_name'] ?? 'Document Tracking & Audit';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Tracking & Audit Dashboard - Document Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/global/main.css">
    <link rel="stylesheet" href="../../assets/css/pages/audit-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-[#103D1C] text-white transform transition-transform duration-300 z-50">
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div class="p-6 border-b border-green-800">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 flex items-center justify-center bg-white rounded-lg">
                        <img src="../../assets/img/logo.png" alt="Logo" class="logo-img">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">DTS</h1>
                        <p class="text-xs text-green-200">Document Tracking & Audit</p>
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
                        <a href="#document-tracking" data-section="document-tracking" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-search w-5"></i>
                            <span>Document Tracking</span>
                        </a>
                    </li>
                    <li>
                        <a href="#audit-logs" data-section="audit-logs" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-clipboard-list w-5"></i>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="#transaction-history" data-section="transaction-history" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-history w-5"></i>
                            <span>Transaction History</span>
                        </a>
                    </li>
                    <li>
                        <a href="#document-version" data-section="document-version" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-code-branch w-5"></i>
                            <span>Document Version History</span>
                        </a>
                    </li>
                    <li>
                        <a href="#advanced-queries" data-section="advanced-queries" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-search-plus w-5"></i>
                            <span>Advanced Queries</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reports" data-section="reports" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Audit Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-green-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-xs text-green-200"><?php echo htmlspecialchars($userRole); ?></p>
                    </div>
                </div>
                <a href="../../auth/logout.php" class="w-full bg-green-700 hover:bg-green-600 px-4 py-2 rounded-lg text-sm transition-colors block text-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-4">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900" id="pageTitle">Dashboard Overview</h2>
                        <p class="text-sm text-gray-600" id="pageSubtitle">Document tracking and audit statistics</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900" id="currentDate"></p>
                        <p class="text-xs text-gray-600" id="currentTime"></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">1,247</p>
                                <p class="text-xs text-blue-600 mt-1">All time</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-invoice text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Transactions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">156</p>
                                <p class="text-xs text-green-600 mt-1">In progress</p>
                            </div>
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-sync-alt text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Audit Logs</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">8,542</p>
                                <p class="text-xs text-yellow-600 mt-1">Total entries</p>
                            </div>
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Completed</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">1,091</p>
                                <p class="text-xs text-purple-600 mt-1">Archived</p>
                            </div>
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Transaction Status Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Transaction Status Distribution</h3>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="transactionStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity Timeline</h3>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Transaction Completed</p>
                                    <p class="text-xs text-gray-600">2025-SR-001 - Status: Completed</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">User Action</p>
                                    <p class="text-xs text-gray-600">John Doe - Approved PR-2025-090</p>
                                    <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Document Tracking Section -->
            <section id="document-tracking-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Document Tracking</h3>
                            <p class="text-gray-600">Track documents by tracking ID or document number</p>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tracking ID</label>
                                <input 
                                    type="text" 
                                    placeholder="Enter tracking ID..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                    id="trackingIdSearch"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Number</label>
                                <input 
                                    type="text" 
                                    placeholder="Enter document number..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                    id="documentNumberSearch"
                                >
                            </div>
                            <div class="flex items-end">
                                <button onclick="searchDocument()" class="w-full bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Results -->
                    <div id="trackingResults" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Tracking Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Tracking ID</p>
                                    <p class="font-semibold text-gray-900" id="resultTrackingId">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Current Status</p>
                                    <p class="font-semibold text-gray-900" id="resultStatus">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Current Office</p>
                                    <p class="font-semibold text-gray-900" id="resultOffice">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Last Updated</p>
                                    <p class="font-semibold text-gray-900" id="resultLastUpdated">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Audit Logs Section -->
            <section id="audit-logs-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Audit Logs</h3>
                            <p class="text-gray-600">View all system actions and changes</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input 
                            type="text" 
                            placeholder="Search by user or action..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Actions</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                        </select>
                        <input 
                            type="date" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <input 
                            type="date" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>

                    <!-- Audit Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Timestamp</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Document</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Details</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 10:30:25</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">John Doe</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Create</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">SR-2025-001</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Created Supply Request</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">192.168.1.100</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 09:15:10</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Robert Johnson</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Approve</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">PR-2025-045</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Approved Purchase Request</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">192.168.1.105</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Transaction History Section -->
            <section id="transaction-history-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Transaction History</h3>
                            <p class="text-gray-600">View complete transaction history and status changes</p>
                        </div>
                    </div>

                    <!-- Transaction History Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tracking ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Document Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Requester</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Updated</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600">2025-SR-001</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Supply Request</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">John Doe</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Completed</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-10</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15</td>
                                    <td class="px-4 py-3">
                                        <button onclick="viewTransactionHistory('2025-SR-001')" class="text-blue-600 hover:text-blue-800" title="View History">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Document Version History Section -->
            <section id="document-version-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Document Version History</h3>
                            <p class="text-gray-600">View complete version history of documents</p>
                        </div>
                    </div>

                    <!-- Document Search -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Number</label>
                                <input 
                                    type="text" 
                                    placeholder="Enter document number..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                    id="versionDocNumber"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Type</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">All Types</option>
                                    <option value="PR">Purchase Request</option>
                                    <option value="ORS">Obligation Request Status</option>
                                    <option value="PO">Purchase Order</option>
                                    <option value="DV">Disbursement Voucher</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button onclick="loadVersionHistory()" class="w-full bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>Load History
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Version History Timeline -->
                    <div id="versionHistory" class="space-y-4">
                        <div class="border-l-4 border-blue-500 pl-4 py-4 bg-blue-50 rounded-r-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 bg-blue-600 text-white text-xs rounded-full font-semibold">Version 3</span>
                                        <span class="text-sm font-semibold text-gray-900">Current Version</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-1">Document updated by: <span class="font-semibold">Jane Smith</span></p>
                                    <p class="text-xs text-gray-600">Status changed: Approved → Under Procurement</p>
                                    <p class="text-xs text-gray-500 mt-2">2025-01-15 14:30:25</p>
                                </div>
                                <button onclick="viewVersion(3)" class="text-blue-600 hover:text-blue-800" title="View Version">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="border-l-4 border-gray-300 pl-4 py-4 bg-gray-50 rounded-r-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 bg-gray-600 text-white text-xs rounded-full font-semibold">Version 2</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-1">Document updated by: <span class="font-semibold">Principal Name</span></p>
                                    <p class="text-xs text-gray-600">Status changed: For Approval → Approved</p>
                                    <p class="text-xs text-gray-500 mt-2">2025-01-14 10:15:10</p>
                                </div>
                                <button onclick="viewVersion(2)" class="text-blue-600 hover:text-blue-800" title="View Version">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="border-l-4 border-gray-300 pl-4 py-4 bg-gray-50 rounded-r-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 bg-gray-600 text-white text-xs rounded-full font-semibold">Version 1</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-1">Document created by: <span class="font-semibold">John Doe</span></p>
                                    <p class="text-xs text-gray-600">Initial creation: Status - Submitted</p>
                                    <p class="text-xs text-gray-500 mt-2">2025-01-10 09:00:00</p>
                                </div>
                                <button onclick="viewVersion(1)" class="text-blue-600 hover:text-blue-800" title="View Version">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Advanced Queries Section -->
            <section id="advanced-queries-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Advanced Queries</h3>
                            <p class="text-gray-600">Query audit trail by time, user, or document</p>
                        </div>
                    </div>

                    <!-- Query Type Selection -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button onclick="setQueryType('time')" class="query-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-green-600 transition-colors text-center">
                                <i class="fas fa-clock text-3xl text-blue-600 mb-3"></i>
                                <h4 class="font-bold text-gray-900 mb-2">Time-Based Query</h4>
                                <p class="text-sm text-gray-600">Query by date range and time period</p>
                            </button>
                            <button onclick="setQueryType('user')" class="query-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-green-600 transition-colors text-center">
                                <i class="fas fa-user text-3xl text-green-600 mb-3"></i>
                                <h4 class="font-bold text-gray-900 mb-2">User-Based Query</h4>
                                <p class="text-sm text-gray-600">Query actions by specific user</p>
                            </button>
                            <button onclick="setQueryType('document')" class="query-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-green-600 transition-colors text-center">
                                <i class="fas fa-file text-3xl text-purple-600 mb-3"></i>
                                <h4 class="font-bold text-gray-900 mb-2">Document-Based Query</h4>
                                <p class="text-sm text-gray-600">Query all actions for a document</p>
                            </button>
                        </div>
                    </div>

                    <!-- Time-Based Query Form -->
                    <div id="timeQueryForm" class="hidden mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Time-Based Query</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Action Type</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">All Actions</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="forward">Forward</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button onclick="executeTimeQuery()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>Query
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- User-Based Query Form -->
                    <div id="userQueryForm" class="hidden mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">User-Based Query</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">User Name</label>
                                <input type="text" placeholder="Enter user name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">All Roles</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="principal">Principal</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="procurement">Procurement</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div class="flex items-end">
                                <button onclick="executeUserQuery()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>Query
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Document-Based Query Form -->
                    <div id="documentQueryForm" class="hidden mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Document-Based Query</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Number</label>
                                <input type="text" placeholder="Enter document number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Document Type</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">All Types</option>
                                    <option value="PR">Purchase Request</option>
                                    <option value="ORS">Obligation Request Status</option>
                                    <option value="PO">Purchase Order</option>
                                    <option value="DV">Disbursement Voucher</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tracking ID</label>
                                <input type="text" placeholder="Enter tracking ID..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div class="flex items-end">
                                <button onclick="executeDocumentQuery()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>Query
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Query Results -->
                    <div id="queryResults" class="hidden">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Query Results</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Timestamp</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Document</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 14:30:25</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">Jane Smith</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Update</span></td>
                                        <td class="px-4 py-3 text-sm text-gray-900">PR-2025-090</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Status changed: Approved → Under Procurement</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports-section" class="content-section hidden">
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Audit Reports</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Audit Trail Report</h4>
                                        <p class="text-xs text-gray-600">Complete audit trail</p>
                                    </div>
                                </div>
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">User Activity Report</h4>
                                        <p class="text-xs text-gray-600">User action logs</p>
                                    </div>
                                </div>
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-invoice text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Document Status Report</h4>
                                        <p class="text-xs text-gray-600">Document status tracking</p>
                                    </div>
                                </div>
                                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
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
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="../../assets/js/audit-dashboard.js"></script>
</body>
</html>

