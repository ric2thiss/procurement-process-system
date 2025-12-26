/**
 * History Module
 * Handles request history functionality with search and filters
 */

import { escapeHtml, getStatusBadge, formatDate } from './utils.js';
import { viewRequestDetails } from './modals.js';

// State management
let searchTimeout = null;

/**
 * Initialize Request History Section
 */
export function initializeRequestHistory() {
    // Setup filter event listeners
    const searchInput = document.getElementById('historySearch');
    const statusFilter = document.getElementById('historyStatusFilter');
    const dateFilter = document.getElementById('historyDateFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadRequestHistory();
            }, 500); // Debounce search
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            loadRequestHistory();
        });
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            loadRequestHistory();
        });
    }
}

/**
 * Load Request History
 */
export function loadRequestHistory() {
    const search = document.getElementById('historySearch')?.value || '';
    const status = document.getElementById('historyStatusFilter')?.value || '';
    const date = document.getElementById('historyDateFilter')?.value || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (date) {
        params.append('from_date', date);
        params.append('to_date', date);
    }
    
    const tbody = document.getElementById('historyTableBody');
    const loadingRow = document.getElementById('historyLoading');
    const emptyRow = document.getElementById('historyEmpty');
    
    if (!tbody) return;
    
    // Show loading
    if (loadingRow) loadingRow.classList.remove('hidden');
    if (emptyRow) emptyRow.classList.add('hidden');
    
    // Clear existing rows
    const existingRows = tbody.querySelectorAll('tr:not(#historyLoading):not(#historyEmpty)');
    existingRows.forEach(row => row.remove());
    
    const url = `../../api/v1/teacher/get_request_history.php${params.toString() ? '?' + params.toString() : ''}`;
    
    fetch(url)
        .then(response => {
            // Always try to parse as JSON (API returns JSON even for errors)
            return response.json().then(data => {
                if (!response.ok) {
                    // Response was not OK, use error message from JSON
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                return data;
            });
        })
        .then(data => {
            // Hide loading
            if (loadingRow) loadingRow.classList.add('hidden');
            
            if (data.success && data.requests && data.requests.length > 0) {
                // Hide empty state
                if (emptyRow) emptyRow.classList.add('hidden');
                
                // Add request rows
                data.requests.forEach(request => {
                    const row = createHistoryRow(request);
                    tbody.appendChild(row);
                });
            } else {
                // Show empty state
                if (emptyRow) emptyRow.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading request history:', error);
            if (loadingRow) {
                loadingRow.innerHTML = `<td colspan="6" class="px-4 py-8 text-center text-red-600">Error loading request history: ${error.message || 'Please refresh the page.'}</td>`;
            }
        });
}

/**
 * Create History Row
 * @param {object} request - Request data
 * @returns {HTMLElement} Table row element
 */
function createHistoryRow(request) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    // Format dates
    const completedDate = new Date(request.completed_at || request.updated_at);
    const formattedCompletedDate = formatDate(completedDate);
    
    // Get status badge
    let statusBadge = '';
    const status = request.status;
    switch (status) {
        case 'Completed':
            statusBadge = '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-medium">Completed</span>';
            break;
        case 'Cancelled':
            statusBadge = '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">Cancelled</span>';
            break;
        case 'Rejected':
            statusBadge = '<span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">Rejected</span>';
            break;
        default:
            statusBadge = `<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-medium">${escapeHtml(status)}</span>`;
    }
    
    // Format quantity
    const quantityText = request.quantity && request.unit_of_measure 
        ? `${request.quantity} ${request.unit_of_measure}`
        : 'N/A';
    
    row.innerHTML = `
        <td class="px-4 py-3 text-sm font-semibold text-blue-600">${escapeHtml(request.tracking_id)}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(request.item_description)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(quantityText)}</td>
        <td class="px-4 py-3">${statusBadge}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(formattedCompletedDate)}</td>
        <td class="px-4 py-3">
            <button class="text-blue-600 hover:text-blue-800" onclick="viewRequestDetails('${escapeHtml(request.tracking_id)}')" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
        </td>
    `;
    
    return row;
}

