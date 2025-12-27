<?php
/**
 * Teacher Dashboard
 * Document Tracking System - Magallanes National High School
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require login and check role
requireLogin();

// Check if user has TEACHER role
if (getCurrentUserRole() !== 'TEACHER') {
    header('Location: /dts/auth/login.php');
    exit();
}

// Get user info from session
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$userRole = $_SESSION['role_name'] ?? 'Teacher';
$userRoleCode = $_SESSION['role'] ?? 'TEACHER';

// Format role for portal title (e.g., "TEACHER" -> "Teacher Portal", "STAFF" -> "Staff Portal")
$portalTitle = ucfirst(strtolower($userRoleCode)) . ' Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Document Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/global/main.css">
    <link rel="stylesheet" href="../../assets/css/pages/teacher-dashboard.css">
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
                        <p class="text-xs text-green-200"><?php echo htmlspecialchars($portalTitle); ?></p>
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
                        <a href="#new-request" data-section="new-request" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>New Supply Request</span>
                        </a>
                    </li>
                    <li>
                        <a href="#my-requests" data-section="my-requests" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-file-alt w-5"></i>
                            <span>My Requests</span>
                        </a>
                    </li>
                    <li>
                        <a href="#tracking" data-section="tracking" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-route w-5"></i>
                            <span>Track Document</span>
                        </a>
                    </li>
                    <li>
                        <a href="#issued-items" data-section="issued-items" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-box-check w-5"></i>
                            <span>Issued Items</span>
                        </a>
                    </li>
                    <li>
                        <a href="#history" data-section="history" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-green-800 transition-colors">
                            <i class="fas fa-history w-5"></i>
                            <span>Request History</span>
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
                <div class="flex items-center space-x-2 lg:space-x-4 flex-1 min-w-0">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 truncate" id="pageTitle">Dashboard Overview</h2>
                        <p class="text-xs lg:text-sm text-gray-600 truncate" id="pageSubtitle">Your supply request statistics</p>
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
        <main class="p-4 lg:p-6">
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Total Requests</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statTotalRequests">0</p>
                                <p class="text-xs text-blue-600 mt-1">All time</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-file-alt text-blue-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Pending</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statPendingRequests">0</p>
                                <p class="text-xs text-yellow-600 mt-1">Awaiting action</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-clock text-yellow-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Approved</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statApprovedRequests">0</p>
                                <p class="text-xs text-green-600 mt-1">In progress</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-check-circle text-green-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-lg shadow-md p-4 lg:p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm font-medium text-gray-600">Completed</p>
                                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2" id="statCompletedRequests">0</p>
                                <p class="text-xs text-purple-600 mt-1">Items received</p>
                            </div>
                            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                                <i class="fas fa-box-check text-purple-600 text-lg lg:text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
                    <div class="flex items-center justify-between mb-4 lg:mb-6">
                        <h3 class="text-base lg:text-lg font-bold text-gray-900">Recent Requests</h3>
                        <a href="#my-requests" class="text-blue-600 hover:text-blue-800 text-xs lg:text-sm font-medium">View All</a>
                    </div>
                    <div class="overflow-x-auto -mx-4 lg:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Tracking ID</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Item Description</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Status</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap hidden md:table-cell">Date Submitted</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-gray-700 uppercase whitespace-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRequestsTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Loading state -->
                                    <tr id="recentRequestsLoading">
                                        <td colspan="5" class="px-3 lg:px-4 py-8 text-center">
                                            <div class="flex items-center justify-center">
                                                <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                                <span class="text-gray-600">Loading recent requests...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Empty state (hidden by default) -->
                                    <tr id="recentRequestsEmpty" class="hidden">
                                        <td colspan="5" class="px-3 lg:px-4 py-8 text-center text-gray-600">
                                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                            <p>No recent requests found.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                    <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="#new-request" class="quick-action-btn p-4 lg:p-6 border-2 border-gray-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition-colors text-center">
                            <i class="fas fa-plus-circle text-2xl lg:text-3xl text-green-600 mb-2 lg:mb-3"></i>
                            <p class="text-xs lg:text-sm font-semibold text-gray-900">Create New Request</p>
                            <p class="text-xs text-gray-600 mt-1">Submit a supply request</p>
                        </a>
                        <a href="#tracking" class="quick-action-btn p-4 lg:p-6 border-2 border-gray-200 rounded-lg hover:border-blue-600 hover:bg-blue-50 transition-colors text-center">
                            <i class="fas fa-route text-2xl lg:text-3xl text-blue-600 mb-2 lg:mb-3"></i>
                            <p class="text-xs lg:text-sm font-semibold text-gray-900">Track Document</p>
                            <p class="text-xs text-gray-600 mt-1">Check request status</p>
                        </a>
                        <a href="#issued-items" class="quick-action-btn p-4 lg:p-6 border-2 border-gray-200 rounded-lg hover:border-purple-600 hover:bg-purple-50 transition-colors text-center sm:col-span-2 lg:col-span-1">
                            <i class="fas fa-box-check text-2xl lg:text-3xl text-purple-600 mb-2 lg:mb-3"></i>
                            <p class="text-xs lg:text-sm font-semibold text-gray-900">Issued Items</p>
                            <p class="text-xs text-gray-600 mt-1">View received items</p>
                        </a>
                    </div>
                </div>
            </section>

            <!-- New Supply Request Section -->
            <section id="new-request-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                    <div class="mb-4 lg:mb-6">
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Create New Request</h3>
                        <p class="text-sm lg:text-base text-gray-600">Select the type of request and fill out the required information</p>
                    </div>

                    <form id="supplyRequestForm" class="space-y-6">
                        <!-- Request Type Selection -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-list text-blue-600 mr-2"></i>Request Type <span class="text-red-500">*</span>
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors request-type-option">
                                    <input type="radio" name="requestType" value="equipment" class="mr-3" checked onchange="toggleRequestType()">
                                    <div>
                                        <div class="font-semibold text-gray-900">Equipment</div>
                                        <div class="text-sm text-gray-600">Physical equipment and devices</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors request-type-option">
                                    <input type="radio" name="requestType" value="food" class="mr-3" onchange="toggleRequestType()">
                                    <div>
                                        <div class="font-semibold text-gray-900">Food</div>
                                        <div class="text-sm text-gray-600">Meals, snacks, and catering</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors request-type-option">
                                    <input type="radio" name="requestType" value="supplies" class="mr-3" onchange="toggleRequestType()">
                                    <div>
                                        <div class="font-semibold text-gray-900">Supplies</div>
                                        <div class="text-sm text-gray-600">Office supplies and materials</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors request-type-option">
                                    <input type="radio" name="requestType" value="services" class="mr-3" onchange="toggleRequestType()">
                                    <div>
                                        <div class="font-semibold text-gray-900">Services</div>
                                        <div class="text-sm text-gray-600">Maintenance, repairs, professional services</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors request-type-option">
                                    <input type="radio" name="requestType" value="other" class="mr-3" onchange="toggleRequestType()">
                                    <div>
                                        <div class="font-semibold text-gray-900">Other</div>
                                        <div class="text-sm text-gray-600">Other requests not listed above</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-box text-green-600 mr-2"></i>Request Details
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Item Description / Event Name -->
                                <div class="md:col-span-2" id="itemDescriptionContainer">
                                    <label for="itemDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span id="itemDescriptionLabel">Item Description</span> <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        id="itemDescription" 
                                        name="itemDescription" 
                                        rows="3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Describe the item or equipment you need (e.g., Whiteboard Markers, Projector Lamp, A4 Bond Paper)"
                                        required
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1" id="itemDescriptionHelp">Provide a clear and detailed description of the item</p>
                                </div>

                                <!-- Quantity (for Supply) -->
                                <div id="quantityContainer">
                                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Quantity <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        id="quantity" 
                                        name="quantity" 
                                        min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter quantity"
                                    >
                                </div>

                                <!-- Unit of Measure (for Supply) -->
                                <div id="unitOfMeasureContainer">
                                    <label for="unitOfMeasure" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Unit of Measure <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="unitOfMeasure" 
                                        name="unitOfMeasure"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="">Select unit</option>
                                        <option value="piece">Piece</option>
                                        <option value="set">Set</option>
                                        <option value="box">Box</option>
                                        <option value="ream">Ream</option>
                                        <option value="pack">Pack</option>
                                        <option value="bottle">Bottle</option>
                                        <option value="roll">Roll</option>
                                        <option value="dozen">Dozen</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Event Name (for Budget) -->
                                <div id="eventNameContainer" class="hidden">
                                    <label for="eventName" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Event Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="eventName" 
                                        name="eventName" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., Buwan ng Wika Culmination"
                                    >
                                </div>

                                <!-- Event Date (for Budget) -->
                                <div id="eventDateContainer" class="hidden">
                                    <label for="eventDate" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Event Date (Optional)
                                    </label>
                                    <input 
                                        type="date" 
                                        id="eventDate" 
                                        name="eventDate"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                </div>

                                <!-- Number of Participants (for Budget) -->
                                <div id="numberOfParticipantsContainer" class="hidden">
                                    <label for="numberOfParticipants" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Number of Participants <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        id="numberOfParticipants" 
                                        name="numberOfParticipants" 
                                        min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter number of participants"
                                    >
                                </div>

                                <!-- Estimated Budget (for Budget) -->
                                <div id="estimatedBudgetContainer" class="hidden">
                                    <label for="estimatedBudget" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Estimated Budget (₱) <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        id="estimatedBudget" 
                                        name="estimatedBudget" 
                                        min="0.01"
                                        step="0.01"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="0.00"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Total estimated cost for snacks/meals</p>
                                </div>

                                <!-- Item Specifications (Optional) -->
                                <div class="md:col-span-2">
                                    <label for="specifications" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Additional Details / Specifications (Optional)
                                    </label>
                                    <textarea 
                                        id="specifications" 
                                        name="specifications" 
                                        rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Provide additional details, specifications, or special requirements"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Purpose and Justification -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Purpose & Justification
                            </h4>
                            
                            <div>
                                <label for="justification" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Justification / Purpose <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="justification" 
                                    name="justification" 
                                    rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Explain why this item is needed and how it will be used (e.g., For classroom instruction, For laboratory activities, For office use)"
                                    required
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">Please provide a clear justification for this request</p>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>Additional Information
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Priority Level -->
                                <div>
                                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Priority Level
                                    </label>
                                    <select 
                                        id="priority" 
                                        name="priority"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                </div>

                                <!-- Expected Delivery Date -->
                                <div>
                                    <label for="expectedDate" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Expected Delivery Date (Optional)
                                    </label>
                                    <input 
                                        type="date" 
                                        id="expectedDate" 
                                        name="expectedDate"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">When do you need this item?</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-4 lg:pt-6">
                            <button 
                                type="button" 
                                onclick="resetForm()"
                                class="w-full sm:w-auto px-4 lg:px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm lg:text-base"
                            >
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto px-4 lg:px-6 py-2 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors text-sm lg:text-base"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- My Requests Section -->
            <section id="my-requests-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">My Requests</h3>
                            <p class="text-gray-600">View and manage all your supply requests</p>
                        </div>
                        <button onclick="navigateToNewRequest()" class="bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>New Request
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input 
                            type="text" 
                            id="myRequestsSearch"
                            placeholder="Search by tracking ID or description..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select id="myRequestsStatusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="submitted">Submitted</option>
                            <option value="available">Available (Issued)</option>
                            <option value="not-available">Not Available</option>
                            <option value="pending-ppmp">Pending PPMP</option>
                            <option value="for-approval">For Approval</option>
                            <option value="approved">Approved</option>
                            <option value="under-procurement">Under Procurement</option>
                            <option value="completed">Completed</option>
                        </select>
                        <input 
                            type="date" 
                            id="myRequestsFromDate"
                            placeholder="From Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <input 
                            type="date" 
                            id="myRequestsToDate"
                            placeholder="To Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>

                    <!-- Requests Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tracking ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Item Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date Submitted</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Updated</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="myRequestsTableBody" class="divide-y divide-gray-200">
                                <!-- Loading state -->
                                <tr id="myRequestsLoading">
                                    <td colspan="7" class="px-4 py-8 text-center">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                            <span class="text-gray-600">Loading requests...</span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Empty state (hidden by default) -->
                                <tr id="myRequestsEmpty" class="hidden">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-600">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No requests found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="myRequestsPagination" class="mt-6 flex items-center justify-between hidden">
                        <p class="text-sm text-gray-600" id="myRequestsPaginationInfo">Showing 0-0 of 0 requests</p>
                        <div class="flex space-x-2" id="myRequestsPaginationButtons">
                            <!-- Pagination buttons will be inserted here -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tracking Section -->
            <section id="tracking-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Track Document</h3>
                        <p class="text-gray-600">Enter a tracking ID to view the status and progress of your request</p>
                    </div>

                    <!-- Search Form -->
                    <div class="mb-8">
                        <form class="flex gap-4" onsubmit="trackDocument(event)">
                            <input 
                                type="text" 
                                id="trackingIdInput"
                                placeholder="Enter Tracking ID (e.g., 2025-SR-024)"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                            <button 
                                type="submit"
                                class="px-6 py-3 bg-[#103D1C] hover:bg-[#14532d] text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-search mr-2"></i>Track
                            </button>
                        </form>
                    </div>

                    <!-- Tracking Results -->
                    <div id="trackingResults" class="hidden">
                        <!-- Request Info Card -->
                        <div id="trackingInfoCard" class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
                            <!-- Content will be dynamically inserted -->
                        </div>

                        <!-- Progress Timeline -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Document Progress</h4>
                            <div class="relative">
                                <!-- Timeline Line -->
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                                
                                <!-- Timeline Steps -->
                                <div id="trackingTimeline" class="space-y-6">
                                    <!-- Timeline steps will be dynamically inserted -->
                                </div>
                            </div>
                        </div>

                        <!-- Status History -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Status History</h4>
                            <div id="trackingStatusHistory" class="space-y-3">
                                <!-- Status history will be dynamically inserted -->
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="trackingEmpty" class="text-center py-12">
                        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-600">Enter a tracking ID above to view document status</p>
                    </div>
                </div>
            </section>

            <!-- Issued Items Section -->
            <section id="issued-items-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Issued Items</h3>
                        <p class="text-gray-600">View items that have been issued to you from inventory</p>
                    </div>

                    <!-- Issued Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">RIS Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Item Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date Issued</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="issuedItemsTableBody" class="divide-y divide-gray-200">
                                <!-- Loading state -->
                                <tr id="issuedItemsLoading">
                                    <td colspan="6" class="px-4 py-8 text-center">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                            <span class="text-gray-600">Loading issued items...</span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Empty state (hidden by default) -->
                                <tr id="issuedItemsEmpty" class="hidden">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-600">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No issued items found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Request History Section -->
            <section id="history-section" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Request History</h3>
                        <p class="text-gray-600">View all your past supply requests including completed and archived transactions</p>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input 
                            type="text" 
                            id="historySearch"
                            placeholder="Search by tracking ID or item description..." 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <select id="historyStatusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <input 
                            type="date" 
                            id="historyDateFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>

                    <!-- History Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tracking ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Item Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date Completed</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody" class="divide-y divide-gray-200">
                                <!-- Loading state -->
                                <tr id="historyLoading">
                                    <td colspan="6" class="px-4 py-8 text-center">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-spinner fa-spin text-gray-400 mr-2"></i>
                                            <span class="text-gray-600">Loading request history...</span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Empty state (hidden by default) -->
                                <tr id="historyEmpty" class="hidden">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-600">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No request history found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    <!-- Request Details Modal -->
    <div id="requestDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeRequestDetailsModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-3 lg:px-6 lg:py-4 flex items-center justify-between">
                <h3 class="text-lg lg:text-xl font-bold text-gray-900">Request Details</h3>
                <button onclick="closeRequestDetailsModal()" class="text-gray-400 hover:text-gray-600 p-1 w-fit lg:w-auto" style="width: fit-content;">
                    <i class="fas fa-times text-lg lg:text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Loading State -->
                <div id="requestDetailsLoading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-600">Loading request details...</p>
                </div>
                
                <!-- Error State -->
                <div id="requestDetailsError" class="hidden text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-4"></i>
                    <p class="text-red-600 font-semibold mb-2">Error loading request details</p>
                    <p class="text-gray-600 text-sm" id="requestDetailsErrorMessage"></p>
                </div>
                
                <!-- Request Details Content -->
                <div id="requestDetailsContent" class="hidden">
                    <!-- Brief Request Info -->
                    <div class="space-y-4">
                        <!-- Tracking ID and Status -->
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tracking ID</p>
                                <p class="text-xl font-bold text-gray-900" id="detailTrackingId">-</p>
                            </div>
                            <div id="detailStatus">-</div>
                        </div>
                        
                        <!-- Item Description -->
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Item Description</p>
                            <p class="text-base font-semibold text-gray-900" id="detailItemDescription">-</p>
                            <p class="text-sm text-gray-600 mt-1" id="detailQuantity">-</p>
                        </div>
                        
                        <!-- Key Details Grid -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Request Date</p>
                                <p class="text-sm font-medium text-gray-900" id="detailRequestDate">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Status</p>
                                <p class="text-sm font-medium text-gray-900" id="detailStatusText">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Priority</p>
                                <p class="text-sm font-medium text-gray-900" id="detailPriority">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Last Updated</p>
                                <p class="text-sm font-medium text-gray-900" id="detailUpdatedAt">-</p>
                            </div>
                        </div>
                        
                        <!-- Justification (Brief) -->
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-2">Justification</p>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg line-clamp-3" id="detailJustification">-</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-3 pt-6 mt-6 border-t border-gray-200">
                        <button onclick="trackRequestFromDetails()" class="flex-1 bg-[#103D1C] hover:bg-[#14532d] text-white px-4 py-2 rounded-lg transition-colors text-sm">
                            <i class="fas fa-route mr-2"></i>Track Request
                        </button>
                        <button onclick="closeRequestDetailsModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="../../assets/teacher/main.js"></script>
</body>
</html>

