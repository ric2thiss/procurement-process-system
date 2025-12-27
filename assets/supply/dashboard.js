/**
 * Dashboard Module
 * Handles dashboard statistics, charts, and recent activity
 */

import { escapeHtml } from './utils.js';
import { showSection } from './sections.js';

// Store chart instances
let chartInstances = {
    inventoryChart: null,
    requestChart: null
};

/**
 * Initialize Dashboard
 */
export function initializeDashboard() {
    showSection('dashboard');
    // loadDashboardData() will be called by showSection() in sections.js
}

/**
 * Load Dashboard Data
 * Fetches statistics, charts data, and recent activity
 */
export function loadDashboardData() {
    loadDashboardStats();
    loadRecentActivity();
    loadLowStockAlerts();
    loadSidebarBadge();
    initializeCharts();
}

/**
 * Load Dashboard Statistics
 */
function loadDashboardStats() {
    fetch('../../api/v1/supply/get_dashboard_stats.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.stats) {
                // Update statistics cards
                const pendingEl = document.getElementById('statPendingRequests');
                const itemsAvailableEl = document.getElementById('statItemsAvailable');
                const lowStockEl = document.getElementById('statLowStock');
                const risGeneratedEl = document.getElementById('statRISGenerated');
                
                if (pendingEl) pendingEl.textContent = data.stats.pending_requests || 0;
                if (itemsAvailableEl) itemsAvailableEl.textContent = data.stats.items_available || 0;
                if (lowStockEl) lowStockEl.textContent = data.stats.low_stock_items || 0;
                if (risGeneratedEl) risGeneratedEl.textContent = data.stats.ris_generated || 0;
                
                // Update sidebar badge
                updateSidebarBadge(data.stats.pending_requests || 0);
            } else {
                console.error('Failed to load dashboard stats:', data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

/**
 * Load Sidebar Badge
 * Updates the pending requests badge in the sidebar
 */
export function loadSidebarBadge() {
    fetch('../../api/v1/supply/get_dashboard_stats.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.stats) {
                updateSidebarBadge(data.stats.pending_requests || 0);
            }
        })
        .catch(error => {
            console.error('Error loading sidebar badge:', error);
        });
}

/**
 * Update Sidebar Badge
 * @param {number} count - Number of pending requests
 */
function updateSidebarBadge(count) {
    const badge = document.getElementById('supplyRequestsBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

/**
 * Load Recent Activity
 */
function loadRecentActivity() {
    fetch('../../api/v1/supply/get_recent_activity.php?limit=5')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('recentActivityContainer');
            if (!container) return;
            
            if (data.success && data.activities && data.activities.length > 0) {
                container.innerHTML = data.activities.map(activity => createActivityItem(activity)).join('');
            } else {
                container.innerHTML = '<p class="text-gray-600 text-sm">No recent activity</p>';
            }
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
        });
}

/**
 * Create Activity Item HTML
 */
function createActivityItem(activity) {
    const iconMap = {
        'RIS Generated': { icon: 'fa-check', color: 'bg-green-100 text-green-600' },
        'PR Created': { icon: 'fa-shopping-cart', color: 'bg-blue-100 text-blue-600' },
        'Low Stock Alert': { icon: 'fa-exclamation', color: 'bg-yellow-100 text-yellow-600' },
        'Request Processed': { icon: 'fa-cog', color: 'bg-purple-100 text-purple-600' }
    };
    
    const config = iconMap[activity.type] || { icon: 'fa-info-circle', color: 'bg-gray-100 text-gray-600' };
    
    return `
        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
            <div class="w-10 h-10 ${config.color} rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas ${config.icon}"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">${escapeHtml(activity.type)}</p>
                <p class="text-xs text-gray-600">${escapeHtml(activity.description)}</p>
                <p class="text-xs text-gray-500 mt-1">${escapeHtml(activity.time_ago)}</p>
            </div>
        </div>
    `;
}

/**
 * Initialize Charts
 */
export function initializeCharts() {
    // Destroy existing charts if they exist
    if (chartInstances.inventoryChart) {
        chartInstances.inventoryChart.destroy();
        chartInstances.inventoryChart = null;
    }
    if (chartInstances.requestChart) {
        chartInstances.requestChart.destroy();
        chartInstances.requestChart = null;
    }

    // Load chart data from API
    fetch('../../api/v1/supply/get_chart_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Inventory Status Chart
                const inventoryCtx = document.getElementById('inventoryChart');
                if (inventoryCtx && !chartInstances.inventoryChart) {
                    chartInstances.inventoryChart = new Chart(inventoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                            datasets: [{
                                data: [
                                    data.inventory.in_stock || 0,
                                    data.inventory.low_stock || 0,
                                    data.inventory.out_of_stock || 0
                                ],
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 2,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // Request Processing Chart
                const requestCtx = document.getElementById('requestChart');
                if (requestCtx && !chartInstances.requestChart) {
                    chartInstances.requestChart = new Chart(requestCtx, {
                        type: 'bar',
                        data: {
                            labels: data.request_processing.weeks || [],
                            datasets: [{
                                label: 'RIS Generated',
                                data: data.request_processing.ris_generated || [],
                                backgroundColor: '#10b981'
                            }, {
                                label: 'PR Created',
                                data: data.request_processing.pr_created || [],
                                backgroundColor: '#3b82f6'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 2,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
}

/**
 * Load Low Stock Alerts
 */
export function loadLowStockAlerts() {
    fetch('../../api/v1/supply/get_low_stock_alerts.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('lowStockAlertsContainer');
            if (!container) return;
            
            if (data.success && data.alerts && data.alerts.length > 0) {
                container.innerHTML = data.alerts.map(alert => createLowStockAlert(alert)).join('');
            } else {
                container.innerHTML = '<p class="text-gray-600 text-sm">No low stock alerts</p>';
            }
        })
        .catch(error => {
            console.error('Error loading low stock alerts:', error);
        });
}

/**
 * Create Low Stock Alert HTML
 */
function createLowStockAlert(alert) {
    return `
        <div class="flex items-center justify-between p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
            <div>
                <p class="font-semibold text-gray-900">${escapeHtml(alert.item_description)}</p>
                <p class="text-sm text-gray-600">${escapeHtml(alert.stock_on_hand)} ${escapeHtml(alert.unit_of_measure)} remaining (Threshold: ${escapeHtml(alert.reorder_level)})</p>
            </div>
            <span class="text-yellow-600 font-bold">${escapeHtml(alert.stock_on_hand)}</span>
        </div>
    `;
}

