/**
 * Requests Module
 * Handles "My Requests" functionality with search, filters, and pagination
 */

import { escapeHtml, getStatusBadge } from './utils.js';
import { viewRequestDetails } from './modals.js';
import { trackRequest } from './tracking.js';

// State management
let currentPage = 1;
let totalPages = 1;
let searchTimeout = null;
let isLoading = false;

/**
 * Initialize My Requests Section
 */
export function initializeMyRequests() {
    // Setup filter event listeners
    const searchInput = document.getElementById('myRequestsSearch');
    const statusFilter = document.getElementById('myRequestsStatusFilter');
    const fromDate = document.getElementById('myRequestsFromDate');
    const toDate = document.getElementById('myRequestsToDate');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadMyRequests();
            }, 500); // Debounce search
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentPage = 1;
            loadMyRequests();
        });
    }
    
    if (fromDate) {
        fromDate.addEventListener('change', function() {
            currentPage = 1;
            loadMyRequests();
        });
    }
    
    if (toDate) {
        toDate.addEventListener('change', function() {
            currentPage = 1;
            loadMyRequests();
        });
    }
}

/**
 * Load My Requests
 * @param {number|null} page - Page number to load (null to use current page)
 */
export function loadMyRequests(page = null) {
    // Prevent multiple simultaneous loads
    if (isLoading) {
        return;
    }
    
    if (page !== null) {
        currentPage = page;
    }
    
    const search = document.getElementById('myRequestsSearch')?.value || '';
    const status = document.getElementById('myRequestsStatusFilter')?.value || '';
    const fromDate = document.getElementById('myRequestsFromDate')?.value || '';
    const toDate = document.getElementById('myRequestsToDate')?.value || '';
    
    // Build query string
    const params = new URLSearchParams({
        page: currentPage,
        limit: 10
    });
    
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);
    
    const tbody = document.getElementById('myRequestsTableBody');
    const loadingRow = document.getElementById('myRequestsLoading');
    const emptyRow = document.getElementById('myRequestsEmpty');
    
    if (!tbody) return;
    
    // Set loading flag
    isLoading = true;
    
    // Show loading
    if (loadingRow) loadingRow.classList.remove('hidden');
    if (emptyRow) emptyRow.classList.add('hidden');
    
    // Clear existing rows
    const allRows = tbody.querySelectorAll('tr');
    allRows.forEach(row => {
        if (row.id !== 'myRequestsLoading' && row.id !== 'myRequestsEmpty') {
            row.remove();
        }
    });
    
    fetch(`../../api/v1/teacher/get_my_requests.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Hide loading
            if (loadingRow) loadingRow.classList.add('hidden');
            
            // Final clear of any remaining rows (safety check)
            const remainingRows = tbody.querySelectorAll('tr:not(#myRequestsLoading):not(#myRequestsEmpty)');
            remainingRows.forEach(row => row.remove());
            
            if (data.success && data.requests && data.requests.length > 0) {
                // Hide empty state
                if (emptyRow) emptyRow.classList.add('hidden');
                
                // Add request rows - use a Set to track IDs and prevent duplicates
                const addedIds = new Set();
                data.requests.forEach(request => {
                    if (!addedIds.has(request.supply_request_id)) {
                        const row = createMyRequestRow(request);
                        tbody.appendChild(row);
                        addedIds.add(request.supply_request_id);
                    }
                });
                
                // Update pagination
                totalPages = data.pagination.total_pages;
                updateMyRequestsPagination(data.pagination);
            } else {
                // Show empty state
                if (emptyRow) emptyRow.classList.remove('hidden');
                updateMyRequestsPagination({ current_page: 1, total_pages: 0, total_records: 0, from: 0, to: 0 });
            }
        })
        .catch(error => {
            console.error('Error loading my requests:', error);
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="7" class="px-4 py-8 text-center text-red-600">Error loading requests. Please refresh the page.</td>';
            }
        })
        .finally(() => {
            // Reset loading flag
            isLoading = false;
        });
}

/**
 * Create My Request Row
 * @param {object} request - Request data
 * @returns {HTMLElement} Table row element
 */
function createMyRequestRow(request) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    // Format dates
    const requestDate = new Date(request.request_date);
    const updatedDate = new Date(request.updated_at);
    
    const formattedRequestDate = requestDate.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const formattedUpdatedDate = updatedDate.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Get status badge
    const statusBadge = getStatusBadge(request.status);
    
    // Format quantity
    const quantityText = request.quantity && request.unit_of_measure 
        ? `${request.quantity} ${request.unit_of_measure}`
        : 'N/A';
    
    row.innerHTML = `
        <td class="px-4 py-3 text-sm font-semibold text-blue-600">${escapeHtml(request.tracking_id)}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(request.item_description)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(quantityText)}</td>
        <td class="px-4 py-3">${statusBadge}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(formattedRequestDate)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(formattedUpdatedDate)}</td>
        <td class="px-4 py-3">
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

/**
 * Update My Requests Pagination
 * @param {object} pagination - Pagination data
 */
function updateMyRequestsPagination(pagination) {
    const paginationDiv = document.getElementById('myRequestsPagination');
    const paginationInfo = document.getElementById('myRequestsPaginationInfo');
    const paginationButtons = document.getElementById('myRequestsPaginationButtons');
    
    if (!paginationDiv || !paginationInfo || !paginationButtons) return;
    
    if (pagination.total_records === 0) {
        paginationDiv.classList.add('hidden');
        return;
    }
    
    paginationDiv.classList.remove('hidden');
    
    // Update info
    paginationInfo.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total_records} requests`;
    
    // Clear buttons
    paginationButtons.innerHTML = '';
    
    // Previous button
    const prevButton = document.createElement('button');
    prevButton.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
    prevButton.textContent = 'Previous';
    prevButton.disabled = pagination.current_page === 1;
    prevButton.onclick = () => {
        if (pagination.current_page > 1) {
            loadMyRequests(pagination.current_page - 1);
        }
    };
    paginationButtons.appendChild(prevButton);
    
    // Page number buttons
    const maxButtons = 5;
    let startPage = Math.max(1, pagination.current_page - Math.floor(maxButtons / 2));
    let endPage = Math.min(pagination.total_pages, startPage + maxButtons - 1);
    
    if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    
    if (startPage > 1) {
        const firstButton = document.createElement('button');
        firstButton.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50';
        firstButton.textContent = '1';
        firstButton.onclick = () => loadMyRequests(1);
        paginationButtons.appendChild(firstButton);
        
        if (startPage > 2) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'px-3 py-2';
            ellipsis.textContent = '...';
            paginationButtons.appendChild(ellipsis);
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.className = i === pagination.current_page
            ? 'px-3 py-2 bg-[#103D1C] text-white rounded-lg'
            : 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50';
        pageButton.textContent = i;
        pageButton.onclick = () => loadMyRequests(i);
        paginationButtons.appendChild(pageButton);
    }
    
    if (endPage < pagination.total_pages) {
        if (endPage < pagination.total_pages - 1) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'px-3 py-2';
            ellipsis.textContent = '...';
            paginationButtons.appendChild(ellipsis);
        }
        
        const lastButton = document.createElement('button');
        lastButton.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50';
        lastButton.textContent = pagination.total_pages;
        lastButton.onclick = () => loadMyRequests(pagination.total_pages);
        paginationButtons.appendChild(lastButton);
    }
    
    // Next button
    const nextButton = document.createElement('button');
    nextButton.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
    nextButton.textContent = 'Next';
    nextButton.disabled = pagination.current_page === pagination.total_pages;
    nextButton.onclick = () => {
        if (pagination.current_page < pagination.total_pages) {
            loadMyRequests(pagination.current_page + 1);
        }
    };
    paginationButtons.appendChild(nextButton);
}

