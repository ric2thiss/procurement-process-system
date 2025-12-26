<?php
/**
 * Admin Dashboard
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require login and check role
requireLogin();

// Check if user has ADMIN role
if (getCurrentUserRole() !== 'ADMIN') {
    header('Location: /dts/auth/login.php');
    exit();
}

// Get user info from session
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$userEmail = $_SESSION['email'] ?? 'admin@school.edu';
$userRole = $_SESSION['role_name'] ?? 'Super Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Document Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/global/main.css">
    <link rel="stylesheet" href="../../assets/css/pages/admin-dashboard.css">
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
                        <h1 class="text-base lg:text-lg font-bold text-white">DTS Admin</h1>
                        <p class="text-xs text-green-200">Super Administrator</p>
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
                        <a href="#users" data-section="users" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-users w-5"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#roles" data-section="roles" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-user-shield w-5"></i>
                            <span>Role Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#ppmp" data-section="ppmp" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-contract w-5"></i>
                            <span>PPMP Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#transactions" data-section="transactions" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-invoice w-5"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="#audit" data-section="audit" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-clipboard-list w-5"></i>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reports" data-section="reports" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Reports & Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="#signatories" data-section="signatories" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-signature w-5"></i>
                            <span>Document Signatories</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config" data-section="config" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>System Configuration</span>
                        </a>
                    </li>
                    <li>
                        <a href="#maintenance" data-section="maintenance" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-tools w-5"></i>
                            <span>Maintenance</span>
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
                        <p class="text-xs text-green-200"><?php echo htmlspecialchars($userEmail); ?></p>
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
                <div class="flex items-center space-x-2 lg:space-x-4 flex-1 min-w-0">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 truncate" id="pageTitle">Dashboard Overview</h2>
                        <p class="text-xs lg:text-sm text-gray-600 truncate" id="pageSubtitle">System statistics and monitoring</p>
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
                                <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">1,247</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>12% from last month</p>
                            </div>
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-invoice text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">156</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>8% from last month</p>
                            </div>
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">43</p>
                                <p class="text-xs text-yellow-600 mt-1">Requires attention</p>
                            </div>
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Completed This Month</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">892</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>15% from last month</p>
                            </div>
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-red-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Transaction Status Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Transaction Status Distribution</h3>
                        <canvas id="statusChart" height="250"></canvas>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-plus text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">New user registered</p>
                                    <p class="text-xs text-gray-600">John Doe registered as Teacher</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">PR Approved</p>
                                    <p class="text-xs text-gray-600">PR-2025-001 approved by Principal</p>
                                    <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation text-yellow-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Budget Alert</p>
                                    <p class="text-xs text-gray-600">Budget allocation running low</p>
                                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file text-purple-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">New transaction</p>
                                    <p class="text-xs text-gray-600">Supply Request SR-2025-045 created</p>
                                    <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Monthly Transaction Trend -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Transaction Trend</h3>
                        <canvas id="monthlyTrendChart" height="200"></canvas>
                    </div>

                    <!-- Processing Time Analysis -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Average Processing Time by Stage</h3>
                        <canvas id="processingTimeChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Budget and Approval Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Budget Utilization -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Budget Utilization</h3>
                        <canvas id="budgetChart" height="200"></canvas>
                    </div>

                    <!-- Approval Rate -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Approval vs Rejection Rate</h3>
                        <canvas id="approvalChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Office Performance Chart -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Transactions by Office</h3>
                        <canvas id="officeChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button class="quick-action-btn p-4 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                            <i class="fas fa-user-plus text-2xl text-gray-600 mb-2"></i>
                            <p class="text-sm font-semibold text-gray-900">Add User</p>
                        </button>
                        <button class="quick-action-btn p-4 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                            <i class="fas fa-cog text-2xl text-gray-600 mb-2"></i>
                            <p class="text-sm font-semibold text-gray-900">System Config</p>
                        </button>
                        <button class="quick-action-btn p-4 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                            <i class="fas fa-download text-2xl text-gray-600 mb-2"></i>
                            <p class="text-sm font-semibold text-gray-900">Export Report</p>
                        </button>
                        <button class="quick-action-btn p-4 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                            <i class="fas fa-database text-2xl text-gray-600 mb-2"></i>
                            <p class="text-sm font-semibold text-gray-900">Backup Data</p>
                        </button>
                    </div>
                </div>
            </section>

            <!-- User Management Section -->
            <section id="users-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">User Management</h3>
                        <button class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add New User
                        </button>
                    </div>

                    <!-- Search and Filters -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" placeholder="Search users..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option>All Roles</option>
                            <option>Teacher</option>
                            <option>Supply Office</option>
                            <option>Principal</option>
                            <option>Budget Office</option>
                            <option>Procurement</option>
                            <option>Bookkeeper</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>Suspended</option>
                        </select>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Login</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">USR-001</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">John Doe</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">john.doe@school.edu</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Teacher</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 10:30</td>
                                    <td class="px-4 py-3">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                            <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">USR-002</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Jane Smith</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">jane.smith@school.edu</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Supply Office</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 09:15</td>
                                    <td class="px-4 py-3">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                            <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">USR-003</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Robert Johnson</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">robert.j@school.edu</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Principal</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 08:00</td>
                                    <td class="px-4 py-3">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                            <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Showing 1-10 of 156 users</p>
                        <div class="flex space-x-2">
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                            <button class="px-3 py-2 bg-[#103D1C] text-white rounded-lg">1</button>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Role Management Section -->
            <section id="roles-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Role Management</h3>
                        <button class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create New Role
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Teacher</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">156 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Can create supply requests and track status</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Supply Office</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">12 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Manages inventory and creates purchase requests</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Principal</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">3 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Approves purchase requests and signs DVs</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Budget Office</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">8 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Manages budget and creates ORS</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Procurement</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">5 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Executes procurement activities</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Bookkeeper</h4>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">4 users</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Creates disbursement vouchers</p>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PPMP Management Section -->
            <section id="ppmp-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">PPMP Management</h3>
                            <p class="text-gray-600">Manage Project Procurement Management Plans</p>
                        </div>
                        <a href="../../module/ppmp-management/ppmp_dashboard.html" target="_blank" class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>Open PPMP Module
                        </a>
                    </div>

                    <!-- PPMP Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Active PPMP</p>
                                    <p class="text-2xl font-bold text-gray-900">1</p>
                                </div>
                                <i class="fas fa-file-contract text-blue-600 text-3xl"></i>
                            </div>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Items</p>
                                    <p class="text-2xl font-bold text-gray-900">127</p>
                                </div>
                                <i class="fas fa-boxes text-green-600 text-3xl"></i>
                            </div>
                        </div>
                        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Budget</p>
                                    <p class="text-2xl font-bold text-gray-900">₱2.5M</p>
                                </div>
                                <i class="fas fa-dollar-sign text-purple-600 text-3xl"></i>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Pending Amendments</p>
                                    <p class="text-2xl font-bold text-gray-900">3</p>
                                </div>
                                <i class="fas fa-edit text-yellow-600 text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active PPMP Card -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Active PPMP</h4>
                        <div class="border-2 border-green-500 rounded-lg p-6 bg-green-50">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h5 class="text-xl font-bold text-gray-900">PPMP 2025</h5>
                                    <p class="text-sm text-gray-600">Active • Approved</p>
                                </div>
                                <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full font-medium">Active</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Items</p>
                                    <p class="font-semibold text-gray-900">127</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Budget</p>
                                    <p class="font-semibold text-gray-900">₱2,500,000</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Created</p>
                                    <p class="font-semibold text-gray-900">Jan 1, 2025</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Year</p>
                                    <p class="font-semibold text-gray-900">2025</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="../../module/ppmp-management/ppmp_dashboard.html#items-management" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm text-center transition-colors">
                                    <i class="fas fa-boxes mr-2"></i>Manage Items
                                </a>
                                <a href="../../module/ppmp-management/ppmp_dashboard.html#amendments" target="_blank" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm text-center transition-colors">
                                    <i class="fas fa-edit mr-2"></i>View Amendments
                                </a>
                                <a href="../../module/ppmp-management/ppmp_dashboard.html#reports" target="_blank" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm text-center transition-colors">
                                    <i class="fas fa-chart-bar mr-2"></i>Reports
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="../../module/ppmp-management/ppmp_dashboard.html#create-ppmp" target="_blank" class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                                <i class="fas fa-plus-circle text-3xl text-green-600 mb-2"></i>
                                <p class="font-semibold text-gray-900">Create New PPMP</p>
                                <p class="text-xs text-gray-600 mt-1">Start new annual PPMP</p>
                            </a>
                            <a href="../../module/ppmp-management/ppmp_dashboard.html#ppmp-list" target="_blank" class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-600 hover:bg-blue-50 transition-colors text-center">
                                <i class="fas fa-list text-3xl text-blue-600 mb-2"></i>
                                <p class="font-semibold text-gray-900">View All PPMP</p>
                                <p class="text-xs text-gray-600 mt-1">Browse PPMP history</p>
                            </a>
                            <a href="../../module/ppmp-management/ppmp_dashboard.html#reports" target="_blank" class="p-4 border-2 border-gray-200 rounded-lg hover:border-purple-600 hover:bg-purple-50 transition-colors text-center">
                                <i class="fas fa-chart-bar text-3xl text-purple-600 mb-2"></i>
                                <p class="font-semibold text-gray-900">PPMP Reports</p>
                                <p class="text-xs text-gray-600 mt-1">Generate analytics</p>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Transactions Section -->
            <section id="transactions-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Transaction Monitoring</h3>
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
                        <input type="text" placeholder="Search by Tracking ID..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option>All Status</option>
                            <option>Submitted</option>
                            <option>For Approval</option>
                            <option>Approved</option>
                            <option>Under Procurement</option>
                            <option>Completed</option>
                        </select>
                        <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Transactions Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tracking ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Requestor</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Document Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Updated</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">2025-SR-001</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">John Doe</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Supply Request</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Submitted</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 10:30</td>
                                    <td class="px-4 py-3">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">2025-PR-045</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Jane Smith</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Purchase Request</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">For Approval</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-14</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-15 09:15</td>
                                    <td class="px-4 py-3">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">2025-PR-044</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Robert Johnson</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Purchase Request</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Approved</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-13</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-14 14:20</td>
                                    <td class="px-4 py-3">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Audit Logs Section -->
            <section id="audit-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Audit Logs</h3>
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
                        <input type="text" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option>All Actions</option>
                            <option>Create</option>
                            <option>Update</option>
                            <option>Approve</option>
                            <option>Reject</option>
                            <option>Delete</option>
                        </select>
                        <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
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
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">2025-01-14 14:20:33</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Jane Smith</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Update</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">PR-2025-044</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Updated Purchase Request status</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">192.168.1.102</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports-section" class="content-section hidden">
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Reports & Analytics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Transaction Status</h4>
                                        <p class="text-xs text-gray-600">Status distribution report</p>
                                    </div>
                                </div>
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Processing Time</h4>
                                        <p class="text-xs text-gray-600">Average processing times</p>
                                    </div>
                                </div>
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-check-circle text-yellow-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Approval Report</h4>
                                        <p class="text-xs text-gray-600">Approval/rejection rates</p>
                                    </div>
                                </div>
                                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Budget Utilization</h4>
                                        <p class="text-xs text-gray-600">Budget allocation status</p>
                                    </div>
                                </div>
                                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-cart text-red-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Procurement Activity</h4>
                                        <p class="text-xs text-gray-600">Procurement activities report</p>
                                    </div>
                                </div>
                                <button class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-600 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clipboard-list text-indigo-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Audit Trail</h4>
                                        <p class="text-xs text-gray-600">Complete audit log report</p>
                                    </div>
                                </div>
                                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Document Signatory Management Section -->
            <section id="signatories-section" class="content-section hidden">
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Document Signatory Management</h3>
                            <p class="text-gray-600">Configure signatories for each document type in the procurement process</p>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Note: This system is designed to track the document process. Signatory configuration ensures proper workflow tracking.
                            </p>
                        </div>

                        <!-- Document Types -->
                        <div class="space-y-6">
                            <!-- PPMP Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Project Procurement Management Plan (PPMP)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for PPMP documents</p>
                                    </div>
                                    <button onclick="editSignatories('PPMP')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Principal / School Head</p>
                                            <p class="text-xs text-gray-600">SCHOOL PRINCIPAL II</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">BAC Chairperson</p>
                                            <p class="text-xs text-gray-600">Bids and Awards Committee</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PR Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Purchase Request (PR)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for PR documents</p>
                                    </div>
                                    <button onclick="editSignatories('PR')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Principal / School Head</p>
                                            <p class="text-xs text-gray-600">Approval Signatory</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- RIS Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Requisition and Issue Slip (RIS)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for RIS documents</p>
                                    </div>
                                    <button onclick="editSignatories('RIS')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Principal / School Head</p>
                                            <p class="text-xs text-gray-600">Approval Signatory</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- RFQ Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Request for Quotation (RFQ)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for RFQ documents</p>
                                    </div>
                                    <button onclick="editSignatories('RFQ')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">BAC Chairperson</p>
                                            <p class="text-xs text-gray-600">Bids and Awards Committee</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Abstract of Quotation Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Abstract of Quotation</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for Abstract of Quotation documents</p>
                                    </div>
                                    <button onclick="editSignatories('Abstract')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">BAC Chairperson</p>
                                            <p class="text-xs text-gray-600">Bids and Awards Committee</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Head of Procuring Entity (HoPE)</p>
                                            <p class="text-xs text-gray-600">Principal / School Head</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PO Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Purchase Order (PO)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for PO documents</p>
                                    </div>
                                    <button onclick="editSignatories('PO')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">BAC Chairperson</p>
                                            <p class="text-xs text-gray-600">Bids and Awards Committee</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Head of Procuring Entity (HoPE)</p>
                                            <p class="text-xs text-gray-600">Principal / School Head</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- IAR Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Inspection and Acceptance Report (IAR)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for IAR documents</p>
                                    </div>
                                    <button onclick="editSignatories('IAR')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">IAC Chairperson</p>
                                            <p class="text-xs text-gray-600">Inspection and Acceptance Committee</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Principal / School Head</p>
                                            <p class="text-xs text-gray-600">Approval Signatory</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- DV Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Disbursement Voucher (DV)</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for DV documents</p>
                                    </div>
                                    <button onclick="editSignatories('DV')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Bookkeeper</p>
                                            <p class="text-xs text-gray-600">Certified by</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Principal / School Head</p>
                                            <p class="text-xs text-gray-600">Approved for Payment</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cheque Signatories -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Cheque</h4>
                                        <p class="text-sm text-gray-600">Configure signatories for cheque documents</p>
                                    </div>
                                    <button onclick="editSignatories('Cheque')" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Signatory 1</p>
                                            <p class="text-xs text-gray-600">Primary Signatory</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900">Signatory 2</p>
                                            <p class="text-xs text-gray-600">Secondary Signatory</p>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Edit Signatories Modal -->
            <div id="editSignatoriesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900" id="modalDocumentType">Edit Signatories</h3>
                            <button onclick="closeSignatoriesModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-6">Configure signatories for this document type. The system will track when signatures are required and completed.</p>
                        
                        <!-- Current Signatories List -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Current Signatories</h4>
                            <div id="signatoriesList" class="space-y-3">
                                <!-- Signatories will be dynamically loaded here -->
                            </div>
                        </div>

                        <!-- Add New Signatory -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Add Signatory</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select User/Role</label>
                                    <select id="signatoryUser" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                        <option value="">Select User or Role...</option>
                                        <option value="principal">Principal / School Head</option>
                                        <option value="bac-chair">BAC Chairperson</option>
                                        <option value="iac-chair">IAC Chairperson</option>
                                        <option value="bookkeeper">Bookkeeper</option>
                                        <option value="budget-officer">Budget Officer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Signatory Position/Title</label>
                                    <input type="text" id="signatoryTitle" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="e.g., SCHOOL PRINCIPAL II">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Signatory Label/Purpose</label>
                                <input type="text" id="signatoryLabel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="e.g., Approved by, Certified by, etc.">
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Signatory Order (1 = First to sign)</label>
                                <input type="number" id="signatoryOrder" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="1">
                            </div>
                            <button onclick="addSignatory()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Signatory
                            </button>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 pt-6 mt-6 flex items-center justify-end space-x-4">
                            <button onclick="closeSignatoriesModal()" class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button onclick="saveSignatories()" class="px-6 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Signatories
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Configuration Section -->
            <section id="config-section" class="content-section hidden">
                <div class="space-y-6">
                    <!-- General Settings -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">General Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">System Name</label>
                                <input type="text" value="Document Tracking System" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Institution Name</label>
                                <input type="text" value="Magallanes National High School" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Session Timeout (minutes)</label>
                                <input type="number" value="30" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="maintenance" class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <label for="maintenance" class="text-sm text-gray-700">Enable Maintenance Mode</label>
                            </div>
                        </div>
                        <button class="mt-6 bg-[#103D1C] hover:bg-[#14532d] text-white px-6 py-2 rounded-lg transition-colors">
                            Save Settings
                        </button>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Notification Settings</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Email Notifications</label>
                                    <p class="text-xs text-gray-600">Send email notifications for status changes</p>
                                </div>
                                <input type="checkbox" checked class="w-4 h-4 text-green-600 focus:ring-green-500">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">SMS Notifications</label>
                                    <p class="text-xs text-gray-600">Send SMS notifications (requires SMS gateway)</p>
                                </div>
                                <input type="checkbox" class="w-4 h-4 text-green-600 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Server</label>
                                <input type="text" placeholder="smtp.example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        <button class="mt-6 bg-[#103D1C] hover:bg-[#14532d] text-white px-6 py-2 rounded-lg transition-colors">
                            Save Settings
                        </button>
                    </div>

                    <!-- Backup Settings -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Backup & Recovery</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Backup Frequency</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option>Daily</option>
                                    <option>Weekly</option>
                                    <option>Monthly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Backup Location</label>
                                <input type="text" value="/backups" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div class="flex space-x-4">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-database mr-2"></i>Create Backup Now
                                </button>
                                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-upload mr-2"></i>Restore Backup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Maintenance Section -->
            <section id="maintenance-section" class="content-section hidden">
                <div class="space-y-6">
                    <!-- System Health -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">System Health</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 border-2 border-green-200 rounded-lg bg-green-50">
                                <i class="fas fa-server text-3xl text-green-600 mb-2"></i>
                                <p class="text-sm font-semibold text-gray-700">Server Status</p>
                                <p class="text-lg font-bold text-green-600">Online</p>
                            </div>
                            <div class="text-center p-4 border-2 border-blue-200 rounded-lg bg-blue-50">
                                <i class="fas fa-database text-3xl text-blue-600 mb-2"></i>
                                <p class="text-sm font-semibold text-gray-700">Database</p>
                                <p class="text-lg font-bold text-blue-600">Connected</p>
                            </div>
                            <div class="text-center p-4 border-2 border-purple-200 rounded-lg bg-purple-50">
                                <i class="fas fa-hdd text-3xl text-purple-600 mb-2"></i>
                                <p class="text-sm font-semibold text-gray-700">Storage</p>
                                <p class="text-lg font-bold text-purple-600">45% Used</p>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Tasks -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Maintenance Tasks</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Database Optimization</h4>
                                    <p class="text-sm text-gray-600">Optimize database tables and indexes</p>
                                </div>
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Run Now
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Clear Cache</h4>
                                    <p class="text-sm text-gray-600">Clear system cache files</p>
                                </div>
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Run Now
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Log Rotation</h4>
                                    <p class="text-sm text-gray-600">Archive old log files</p>
                                </div>
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Run Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    <script src="../../assets/js/admin-dashboard.js"></script>
</body>
</html>

