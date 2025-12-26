<?php
/**
 * PPMP Management Dashboard
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require login and check role
requireLogin();

// Check if user has PPMP_MGR role
if (getCurrentUserRole() !== 'PPMP_MGR') {
    header('Location: /dts/auth/login.php');
    exit();
}

// Get user info from session
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$userRole = $_SESSION['role_name'] ?? 'PPMP Management';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPMP Management - Document Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/global/main.css">
    <link rel="stylesheet" href="../../assets/css/pages/ppmp-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                        <p class="text-xs text-green-200">PPMP Management</p>
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
                        <a href="#ppmp-list" data-section="ppmp-list" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-list w-5"></i>
                            <span>PPMP List</span>
                        </a>
                    </li>
                    <li>
                        <a href="#create-ppmp" data-section="create-ppmp" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Create New PPMP</span>
                        </a>
                    </li>
                    <li>
                        <a href="#items-management" data-section="items-management" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-boxes w-5"></i>
                            <span>Items Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#amendments" data-section="amendments" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-edit w-5"></i>
                            <span>PPMP Amendments</span>
                        </a>
                    </li>
                    <li>
                        <a href="#app-consolidation" data-section="app-consolidation" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-alt w-5"></i>
                            <span>APP Consolidation</span>
                            <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full" id="appConsolidationCount">2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#ppmp-approvals" data-section="ppmp-approvals" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-user-check w-5"></i>
                            <span>PPMP Pending Approvals</span>
                            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full" id="ppmpApprovalCount">2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reports" data-section="reports" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Reports & Analytics</span>
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
                <a href="../../auth/logout.php" class="w-full bg-green-700 hover:bg-green-600 px-4 py-2 rounded-lg text-sm transition-colors block text-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="flex items-center justify-between px-4 py-3 lg:px-6 lg:py-4">
                <div class="flex items-center space-x-2 lg:space-x-4 flex-1 min-w-0">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 truncate" id="pageTitle">Dashboard Overview</h2>
                        <p class="text-xs lg:text-sm text-gray-600 truncate" id="pageSubtitle">PPMP management and monitoring</p>
                    </div>
                </div>
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
        <main class="p-6">
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active PPMP</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">1</p>
                                <p class="text-xs text-blue-600 mt-1">Year 2025</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Items</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">127</p>
                                <p class="text-xs text-green-600 mt-1">In PPMP 2025</p>
                            </div>
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-boxes text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Budget</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₱2.5M</p>
                                <p class="text-xs text-purple-600 mt-1">Allocated</p>
                            </div>
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Amendments</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">3</p>
                                <p class="text-xs text-yellow-600 mt-1">Awaiting approval</p>
                            </div>
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-edit text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Budget Utilization Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Budget Utilization</h3>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="budgetChart"></canvas>
                        </div>
                    </div>

                    <!-- Items by Category Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Items by Category</h3>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">New item added to PPMP 2025</p>
                                <p class="text-xs text-gray-600">Projector Lamp added by Admin User</p>
                                <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-edit text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">PPMP amendment submitted</p>
                                <p class="text-xs text-gray-600">Amendment #3 for PPMP 2025</p>
                                <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">PPMP 2025 approved</p>
                                <p class="text-xs text-gray-600">Annual PPMP approved and activated</p>
                                <p class="text-xs text-gray-500 mt-1">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PPMP List Section -->
            <section id="ppmp-list-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">PPMP List</h3>
                            <p class="text-gray-600">View and manage all PPMP documents</p>
                        </div>
                        <a href="#create-ppmp" class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create New PPMP
                        </a>
                    </div>

                    <!-- PPMP Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Active PPMP -->
                        <div class="border-2 border-green-500 rounded-lg p-6 bg-green-50">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">PPMP 2025</h4>
                                    <p class="text-sm text-gray-600">Active</p>
                                </div>
                                <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full font-medium">Active</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Items:</span>
                                    <span class="font-semibold text-gray-900">127</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Budget:</span>
                                    <span class="font-semibold text-gray-900">₱2,500,000</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="font-semibold text-green-600">Approved</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <span class="font-semibold text-gray-900">Jan 1, 2025</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewPPMP('2025')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View
                                </button>
                                <button onclick="editPPMP('2025')" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button onclick="printPPMP('2025')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Previous Year PPMP -->
                        <div class="border-2 border-gray-300 rounded-lg p-6 bg-white">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">PPMP 2024</h4>
                                    <p class="text-sm text-gray-600">Completed</p>
                                </div>
                                <span class="px-3 py-1 bg-gray-500 text-white text-xs rounded-full font-medium">Archived</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Items:</span>
                                    <span class="font-semibold text-gray-900">98</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Budget:</span>
                                    <span class="font-semibold text-gray-900">₱2,100,000</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="font-semibold text-gray-600">Completed</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <span class="font-semibold text-gray-900">Jan 1, 2024</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewPPMP('2024')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View
                                </button>
                                <button onclick="printPPMP('2024')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Create PPMP Section -->
            <section id="create-ppmp-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Create New PPMP</h3>
                        <p class="text-gray-600">Create a new annual Project Procurement Management Plan</p>
                    </div>

                    <form id="ppmpForm" class="space-y-6">
                        <!-- PPMP Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Basic Information
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        PPMP Year <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        id="ppmpYear"
                                        min="2020"
                                        max="2099"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                        value="2026"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        End User/Unit <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                        placeholder="e.g., Magallanes National High School"
                                        required
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Project, Programs & Activities (PAPs) <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                        placeholder="Describe the project, programs, and activities"
                                        required
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Fund Source
                                    </label>
                                    <input 
                                        type="text" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                        placeholder="e.g., MOOE 2026"
                                        value="MOOE 2026"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4">
                            <button 
                                type="button" 
                                onclick="resetPPMPForm()"
                                class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-6 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-save mr-2"></i>Create PPMP
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Items Management Section -->
            <section id="items-management-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">PPMP Items Management</h3>
                            <p class="text-gray-600">Manage items in PPMP 2025</p>
                        </div>
                        <button onclick="showAddItemModal()" class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>

                    <!-- Filters -->
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
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Modes</option>
                            <option value="shopping">Shopping</option>
                            <option value="bidding">Competitive Bidding</option>
                            <option value="svp">Small Value Procurement</option>
                        </select>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            <i class="fas fa-filter mr-2"></i>Apply
                        </button>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Estimated Budget</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Mode</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">001</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Whiteboard Markers (Set of 10)</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">25</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Set</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">₱250.00</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱6,250.00</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Shopping</td>
                                    <td class="px-4 py-3">
                                        <button onclick="editPPMPItem('001')" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePPMPItem('001')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">002</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">A4 Bond Paper (Ream)</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">100</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Ream</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">₱180.00</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱18,000.00</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Shopping</td>
                                    <td class="px-4 py-3">
                                        <button onclick="editPPMPItem('002')" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePPMPItem('002')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">003</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Projector Lamp</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">5</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Piece</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">₱3,500.00</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱17,500.00</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Small Value Procurement</td>
                                    <td class="px-4 py-3">
                                        <button onclick="editPPMPItem('003')" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePPMPItem('003')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Items</p>
                                <p class="text-2xl font-bold text-gray-900">127</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Estimated Budget</p>
                                <p class="text-2xl font-bold text-gray-900">₱2,500,000.00</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Average Item Budget</p>
                                <p class="text-2xl font-bold text-gray-900">₱19,685.04</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Amendments Section -->
            <section id="amendments-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">PPMP Amendments</h3>
                            <p class="text-gray-600">Manage amendments and revisions to PPMP</p>
                        </div>
                        <button onclick="createAmendment()" class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Amendment
                        </button>
                    </div>

                    <!-- Amendments Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Amendment #</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">PPMP Year</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date Created</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">AMEND-003</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Added new items for Q2 procurement</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-14</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Pending Approval</span></td>
                                    <td class="px-4 py-3">
                                        <button onclick="viewAmendment('AMEND-003')" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="approveAmendment('AMEND-003')" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">AMEND-002</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Budget adjustment for equipment items</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-10</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Approved</span></td>
                                    <td class="px-4 py-3">
                                        <button onclick="viewAmendment('AMEND-002')" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- PPMP Pending Approvals Section -->
            <section id="ppmp-approvals-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">PPMP Pending Approvals</h3>
                            <p class="text-gray-600">PPMPs forwarded to Principal for approval - Track approval status</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                These PPMPs are pending Principal signature/approval. Once approved, they will proceed to APP consolidation.
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </div>

                    <!-- PPMP Approvals Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">PPMP Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit/Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Fiscal Year</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Budget</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date Forwarded</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600">PPMP-2025-001</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Science Department</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">2025</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱500,000.00</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-10</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Pending Principal Approval</span></td>
                                    <td class="px-4 py-3">
                                        <button onclick="viewPPMPStatus('PPMP-2025-001')" class="text-blue-600 hover:text-blue-800" title="View Status">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600">PPMP-2025-002</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Math Department</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">2025</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱450,000.00</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-12</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Pending Principal Approval</span></td>
                                    <td class="px-4 py-3">
                                        <button onclick="viewPPMPStatus('PPMP-2025-002')" class="text-blue-600 hover:text-blue-800" title="View Status">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- APP Consolidation Section -->
            <section id="app-consolidation-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">APP Consolidation</h3>
                            <p class="text-gray-600">BAC Secretariat - Consolidate PPMPs into Annual Procurement Plan (APP) - STEP 5</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="createAPP()" class="px-4 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create APP
                            </button>
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <!-- APP List -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-900">APP 2025 (Draft)</h4>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Draft</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">PPMPs Consolidated:</span>
                                    <span class="font-semibold">5</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Items:</span>
                                    <span class="font-semibold">127</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Budget:</span>
                                    <span class="font-semibold">₱2,500,000.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-semibold">Jan 15, 2025</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewAPP('2025')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Review
                                </button>
                                <button onclick="submitAPPForApproval('2025')" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit to HoPE
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-500 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-900">APP 2025 (For Approval)</h4>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">For HoPE Approval</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">PPMPs Consolidated:</span>
                                    <span class="font-semibold">5</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Items:</span>
                                    <span class="font-semibold">125</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Budget:</span>
                                    <span class="font-semibold">₱2,450,000.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Submitted:</span>
                                    <span class="font-semibold">Jan 20, 2025</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewAPP('2025-pending')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PPMPs Pending Consolidation -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">PPMPs Pending Consolidation (Budget Cleared)</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">PPMP Number</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">End User/Unit</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Items Count</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Budget</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Budget Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-semibold text-blue-600">PPMP-2025-001</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">Science Department</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">25 items</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">₱500,000.00</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Budget Available</span></td>
                                        <td class="px-4 py-3">
                                            <button onclick="viewPPMPDetails('PPMP-2025-001')" class="text-blue-600 hover:text-blue-800 mr-2" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="addToAPP('PPMP-2025-001')" class="text-green-600 hover:text-green-800" title="Add to APP">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-semibold text-blue-600">PPMP-2025-002</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">IT Department</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">30 items</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">₱600,000.00</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Budget Available</span></td>
                                        <td class="px-4 py-3">
                                            <button onclick="viewPPMPDetails('PPMP-2025-002')" class="text-blue-600 hover:text-blue-800 mr-2" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="addToAPP('PPMP-2025-002')" class="text-green-600 hover:text-green-800" title="Add to APP">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- APP Approval Status -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">APP Approval Status</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">APP 2024</p>
                                            <p class="text-sm text-gray-600">Approved by HoPE on Dec 15, 2024</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">APP Approved - Items Eligible for Procurement</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">APP 2025</p>
                                            <p class="text-sm text-gray-600">Pending HoPE approval - Submitted on Jan 20, 2025</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">Pending HoPE Approval</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">PPMP Reports & Analytics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">PPMP Summary Report</h4>
                                    <p class="text-xs text-gray-600">Complete PPMP overview</p>
                                </div>
                            </div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Generate Report
                            </button>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Budget Analysis</h4>
                                    <p class="text-xs text-gray-600">Budget allocation report</p>
                                </div>
                            </div>
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Generate Report
                            </button>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Procurement Schedule</h4>
                                    <p class="text-xs text-gray-600">Monthly schedule report</p>
                                </div>
                            </div>
                            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script src="../../assets/js/ppmp-dashboard.js"></script>
</body>
</html>

