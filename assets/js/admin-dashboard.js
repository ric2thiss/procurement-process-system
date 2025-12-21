/**
 * Admin Dashboard JavaScript
 * Handles interactivity for Super Admin Dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initDashboard();
    initNavigation();
    initSidebarToggle();
    initDateTime();
    initQuickActions();
    initTables();
    initModals();
    initCharts();
});

/**
 * Initialize Dashboard
 */
function initDashboard() {
    console.log('Admin Dashboard initialized');
    // Set default active section
    showSection('dashboard');
}

/**
 * Navigation Management
 */
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding section
            showSection(section);
            
            // Update page title
            updatePageTitle(section);
            
            // Close mobile sidebar if open
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    });
}

/**
 * Show Section
 */
function showSection(sectionName) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.add('hidden');
    });
    
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }
}

/**
 * Update Page Title
 */
function updatePageTitle(section) {
    const titles = {
        'dashboard': { title: 'Dashboard Overview', subtitle: 'System statistics and monitoring' },
        'users': { title: 'User Management', subtitle: 'Manage system users and permissions' },
        'roles': { title: 'Role Management', subtitle: 'Configure user roles and permissions' },
        'ppmp': { title: 'PPMP Management', subtitle: 'Manage Project Procurement Management Plans' },
        'transactions': { title: 'Transaction Monitoring', subtitle: 'Monitor all system transactions' },
        'audit': { title: 'Audit Logs', subtitle: 'View system activity and audit trail' },
        'reports': { title: 'Reports & Analytics', subtitle: 'Generate and view system reports' },
        'config': { title: 'System Configuration', subtitle: 'Configure system settings' },
        'maintenance': { title: 'System Maintenance', subtitle: 'System health and maintenance tasks' }
    };
    
    const pageInfo = titles[section] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

/**
 * Sidebar Toggle for Mobile
 */
function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            toggleSidebar();
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    // Close sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.add('hidden');
        }
    });
}

/**
 * Toggle Sidebar
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('hidden');
}

/**
 * Close Sidebar
 */
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.remove('open');
    sidebarOverlay.classList.add('hidden');
}

/**
 * Initialize Date and Time
 */
function initDateTime() {
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

/**
 * Update Date and Time
 */
function updateDateTime() {
    const now = new Date();
    const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    
    const dateElement = document.getElementById('currentDate');
    const timeElement = document.getElementById('currentTime');
    
    if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
    }
    
    if (timeElement) {
        timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }
}

/**
 * Initialize Quick Actions
 */
function initQuickActions() {
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.querySelector('p').textContent;
            handleQuickAction(action);
        });
    });
}

/**
 * Handle Quick Action
 */
function handleQuickAction(action) {
    console.log(`Quick action: ${action}`);
    
    switch(action) {
        case 'Add User':
            // Navigate to users section
            document.querySelector('[data-section="users"]').click();
            // Could trigger add user modal here
            break;
        case 'System Config':
            document.querySelector('[data-section="config"]').click();
            break;
        case 'Export Report':
            document.querySelector('[data-section="reports"]').click();
            break;
        case 'Backup Data':
            document.querySelector('[data-section="maintenance"]').click();
            break;
    }
}

/**
 * Initialize Tables
 */
function initTables() {
    // Add row click handlers
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons
            if (!e.target.closest('button')) {
                // Handle row click (e.g., view details)
                console.log('Row clicked:', this);
            }
        });
    });
    
    // Initialize search functionality
    const searchInputs = document.querySelectorAll('input[type="text"][placeholder*="Search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
}

/**
 * Filter Table
 */
function filterTable(input) {
    const searchTerm = input.value.toLowerCase();
    const table = input.closest('.bg-white').querySelector('table');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

/**
 * Initialize Modals (for future use)
 */
function initModals() {
    // Placeholder for modal functionality
    // This can be expanded when backend is integrated
}

/**
 * Export Functions (for use in other scripts)
 */
window.AdminDashboard = {
    showSection: showSection,
    updatePageTitle: updatePageTitle,
    toggleSidebar: toggleSidebar,
    closeSidebar: closeSidebar
};

/**
 * Utility Functions
 */

// Format Date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Format DateTime
function formatDateTime(date) {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Show Notification (placeholder)
function showNotification(message, type = 'info') {
    // This can be implemented with a toast notification library
    console.log(`[${type.toUpperCase()}] ${message}`);
}

// Confirm Action
function confirmAction(message) {
    return confirm(message);
}

// Show Loading State
function showLoading(element) {
    element.classList.add('loading');
}

// Hide Loading State
function hideLoading(element) {
    element.classList.remove('loading');
}

/**
 * Event Listeners for Dynamic Content
 */

// Handle form submissions
document.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        showLoading(submitBtn);
    }
    
    // Simulate form submission (replace with actual API call)
    setTimeout(() => {
        if (submitBtn) {
            hideLoading(submitBtn);
        }
        showNotification('Settings saved successfully', 'success');
    }, 1000);
});

// Handle button clicks
document.addEventListener('click', function(e) {
    // Handle delete buttons
    if (e.target.closest('.fa-trash')) {
        if (confirmAction('Are you sure you want to delete this item?')) {
            // Handle deletion
            console.log('Delete confirmed');
        }
    }
    
    // Handle edit buttons
    if (e.target.closest('.fa-edit')) {
        // Handle edit action
        console.log('Edit clicked');
    }
    
    // Handle view buttons
    if (e.target.closest('.fa-eye')) {
        // Handle view action
        console.log('View clicked');
    }
});

/**
 * Initialize Charts using Chart.js
 */
function initCharts() {
    initStatusChart();
    initMonthlyTrendChart();
    initProcessingTimeChart();
    initBudgetChart();
    initApprovalChart();
    initOfficeChart();
}

/**
 * Transaction Status Distribution Chart (Doughnut)
 */
function initStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Submitted', 'Approved', 'Under Procurement', 'DV Processing', 'Completed', 'Rejected'],
            datasets: [{
                label: 'Transaction Status',
                data: [245, 189, 156, 98, 559, 12],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',   // Blue - Submitted
                    'rgba(34, 197, 94, 0.8)',    // Green - Approved
                    'rgba(234, 179, 8, 0.8)',    // Yellow - Under Procurement
                    'rgba(168, 85, 247, 0.8)',    // Purple - DV Processing
                    'rgba(239, 68, 68, 0.8)',    // Red - Completed
                    'rgba(156, 163, 175, 0.8)'   // Gray - Rejected
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(156, 163, 175, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Monthly Transaction Trend Chart (Line)
 */
function initMonthlyTrendChart() {
    const ctx = document.getElementById('monthlyTrendChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
            datasets: [{
                label: 'Transactions',
                data: [120, 145, 132, 168, 189, 201, 247],
                borderColor: 'rgba(16, 61, 28, 1)',
                backgroundColor: 'rgba(16, 61, 28, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: 'rgba(16, 61, 28, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 50
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Average Processing Time by Stage Chart (Bar)
 */
function initProcessingTimeChart() {
    const ctx = document.getElementById('processingTimeChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Supply Office', 'PPMP Check', 'Accounting', 'Principal', 'Procurement', 'Bookkeeper'],
            datasets: [{
                label: 'Average Days',
                data: [2.5, 1.5, 3.2, 1.8, 5.5, 2.0],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(16, 61, 28, 0.8)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(16, 61, 28, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Average: ${context.parsed.y} days`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' days';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Budget Utilization Chart (Pie)
 */
function initBudgetChart() {
    const ctx = document.getElementById('budgetChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Allocated', 'Obligated', 'Available', 'Pending'],
            datasets: [{
                label: 'Budget Status',
                data: [5000000, 3200000, 1500000, 300000],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',   // Allocated - Blue
                    'rgba(234, 179, 8, 0.8)',    // Obligated - Yellow
                    'rgba(34, 197, 94, 0.8)',    // Available - Green
                    'rgba(239, 68, 68, 0.8)'     // Pending - Red
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const formattedValue = new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP'
                            }).format(value);
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${formattedValue} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Approval vs Rejection Rate Chart (Bar - Horizontal)
 */
function initApprovalChart() {
    const ctx = document.getElementById('approvalChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Approved', 'Rejected'],
            datasets: [{
                label: 'Count',
                data: [189, 12],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',    // Approved - Green
                    'rgba(239, 68, 68, 0.8)'     // Rejected - Red
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed.x / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed.x} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Transactions by Office Chart (Bar)
 */
function initOfficeChart() {
    const ctx = document.getElementById('officeChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Supply Office', 'PPMP Module', 'Accounting', 'Principal', 'Procurement', 'Bookkeeper', 'Payment'],
            datasets: [{
                label: 'Active Transactions',
                data: [45, 32, 28, 15, 38, 22, 18],
                backgroundColor: 'rgba(16, 61, 28, 0.8)',
                borderColor: 'rgba(16, 61, 28, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Active: ${context.parsed.y} transactions`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

/**
 * Export Data Functions
 */
function exportToCSV(data, filename) {
    // Placeholder for CSV export functionality
    console.log('Exporting to CSV:', filename);
}

function exportToPDF(data, filename) {
    // Placeholder for PDF export functionality
    console.log('Exporting to PDF:', filename);
}

/**
 * API Integration Placeholders
 * These functions will be used when backend is integrated
 */
const API = {
    getUsers: async () => {
        // Placeholder for API call
        return [];
    },
    
    createUser: async (userData) => {
        // Placeholder for API call
        return { success: true };
    },
    
    updateUser: async (userId, userData) => {
        // Placeholder for API call
        return { success: true };
    },
    
    deleteUser: async (userId) => {
        // Placeholder for API call
        return { success: true };
    },
    
    getTransactions: async (filters) => {
        // Placeholder for API call
        return [];
    },
    
    getAuditLogs: async (filters) => {
        // Placeholder for API call
        return [];
    },
    
    getReports: async (reportType, params) => {
        // Placeholder for API call
        return {};
    },
    
    updateSettings: async (settings) => {
        // Placeholder for API call
        return { success: true };
    },
    
    runMaintenance: async (task) => {
        // Placeholder for API call
        return { success: true };
    }
};

// Export API object
window.API = API;

