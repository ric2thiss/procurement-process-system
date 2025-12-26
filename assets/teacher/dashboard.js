/**
 * Dashboard Module
 * Handles dashboard statistics and recent requests
 */

import { escapeHtml, getStatusBadge } from './utils.js';
import { showSection } from './sections.js';

/**
 * Initialize Dashboard
 */
export function initializeDashboard() {
    showSection('dashboard');
}

/**
 * Load Dashboard Data
 * Fetches statistics and recent requests
 */
export function loadDashboardData() {
    loadDashboardStats();
    loadRecentRequests();
}

/**
 * Load Dashboard Statistics
 */
function loadDashboardStats() {
    fetch('../../api/v1/teacher/get_dashboard_stats.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.stats) {
                // Update statistics cards
                const totalEl = document.getElementById('statTotalRequests');
                const pendingEl = document.getElementById('statPendingRequests');
                const approvedEl = document.getElementById('statApprovedRequests');
                const completedEl = document.getElementById('statCompletedRequests');
                
                if (totalEl) totalEl.textContent = data.stats.total || 0;
                if (pendingEl) pendingEl.textContent = data.stats.pending || 0;
                if (approvedEl) approvedEl.textContent = data.stats.approved || 0;
                if (completedEl) completedEl.textContent = data.stats.completed || 0;
            } else {
                console.error('Failed to load dashboard stats:', data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

/**
 * Load Recent Requests
 */
function loadRecentRequests() {
    fetch('../../api/v1/teacher/get_recent_requests.php?limit=5')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('recentRequestsTableBody');
            const loadingRow = document.getElementById('recentRequestsLoading');
            const emptyRow = document.getElementById('recentRequestsEmpty');
            
            if (!tbody) return;
            
            // Hide loading row
            if (loadingRow) {
                loadingRow.classList.add('hidden');
            }
            
            if (data.success && data.requests && data.requests.length > 0) {
                // Hide empty state
                if (emptyRow) {
                    emptyRow.classList.add('hidden');
                }
                
                // Clear existing rows (except loading and empty)
                const existingRows = tbody.querySelectorAll('tr:not(#recentRequestsLoading):not(#recentRequestsEmpty)');
                existingRows.forEach(row => row.remove());
                
                // Add request rows
                data.requests.forEach(request => {
                    const row = createRequestRow(request);
                    tbody.appendChild(row);
                });
            } else {
                // Show empty state
                if (emptyRow) {
                    emptyRow.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error loading recent requests:', error);
            const tbody = document.getElementById('recentRequestsTableBody');
            const loadingRow = document.getElementById('recentRequestsLoading');
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="5" class="px-4 py-8 text-center text-red-600">Error loading requests. Please refresh the page.</td>';
            }
        });
}

/**
 * Create Request Row for Recent Requests Table
 * @param {object} request - Request data
 * @returns {HTMLElement} Table row element
 */
function createRequestRow(request) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    const requestDate = new Date(request.request_date);
    const formattedDate = requestDate.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const statusBadge = getStatusBadge(request.status);
    
    row.innerHTML = `
        <td class="px-3 lg:px-4 py-3 text-sm font-semibold text-blue-600">${escapeHtml(request.tracking_id)}</td>
        <td class="px-3 lg:px-4 py-3 text-sm text-gray-900">${escapeHtml(request.item_description || 'N/A')}</td>
        <td class="px-3 lg:px-4 py-3">${statusBadge}</td>
        <td class="px-3 lg:px-4 py-3 text-sm text-gray-600 hidden md:table-cell">${escapeHtml(formattedDate)}</td>
        <td class="px-3 lg:px-4 py-3">
            <button class="text-blue-600 hover:text-blue-800 mr-3" onclick="viewRequestDetails('${escapeHtml(request.tracking_id)}')" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <button class="text-green-600 hover:text-green-800" onclick="trackRequest('${escapeHtml(request.tracking_id)}')" title="Track Request">
                <i class="fas fa-route"></i>
            </button>
        </td>
    `;
    
    return row;
}

