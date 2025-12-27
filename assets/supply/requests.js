/**
 * Supply Requests Module
 * Handles supply requests listing, filtering, and processing
 */

import { escapeHtml, getStatusBadge, formatDateTime, showSuccessMessage, showErrorMessage } from './utils.js';
import { loadSidebarBadge } from './dashboard.js';
import { openProcessModal, viewRequestDetails as viewDetails } from './modals.js';

let currentPage = 1;
let currentFilters = {
    search: '',
    status: '',
    from_date: '',
    to_date: ''
};

/**
 * Initialize Supply Requests
 */
export function initializeSupplyRequests() {
    // Set up filter event listeners
    const searchInput = document.getElementById('supplyRequestsSearch');
    const statusFilter = document.getElementById('supplyRequestsStatusFilter');
    const fromDateFilter = document.getElementById('supplyRequestsFromDate');
    const toDateFilter = document.getElementById('supplyRequestsToDate');
    
    if (searchInput) {
        const clearBtn = document.getElementById('clearSearchBtn');
        let searchTimeout;
        
        // Show/hide clear button based on input
        function toggleClearButton() {
            if (clearBtn) {
                if (searchInput.value.trim() !== '') {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }
        }
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value.trim();
            toggleClearButton();
            
            searchTimeout = setTimeout(() => {
                currentFilters.search = searchValue;
                currentPage = 1;
                loadSupplyRequests();
            }, 500);
        });
        
        // Also handle Enter key for immediate search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                currentFilters.search = this.value.trim();
                currentPage = 1;
                loadSupplyRequests();
            }
        });
        
        // Initial state
        toggleClearButton();
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentFilters.status = this.value;
            currentPage = 1;
            loadSupplyRequests();
        });
    }
    
    if (fromDateFilter) {
        fromDateFilter.addEventListener('change', function() {
            currentFilters.from_date = this.value;
            currentPage = 1;
            loadSupplyRequests();
        });
    }
    
    if (toDateFilter) {
        toDateFilter.addEventListener('change', function() {
            currentFilters.to_date = this.value;
            currentPage = 1;
            loadSupplyRequests();
        });
    }
}

/**
 * Load Supply Requests
 */
export function loadSupplyRequests() {
    const tbody = document.getElementById('supplyRequestsTableBody');
    const loadingRow = document.getElementById('supplyRequestsLoading');
    const emptyRow = document.getElementById('supplyRequestsEmpty');
    
    if (!tbody) return;
    
    // Show loading state and hide empty state
    if (loadingRow) {
        loadingRow.classList.remove('hidden');
    }
    if (emptyRow) {
        emptyRow.classList.add('hidden');
    }
    
    // Clear all existing data rows immediately when loading starts
    const existingRows = tbody.querySelectorAll('tr:not(#supplyRequestsLoading):not(#supplyRequestsEmpty)');
    existingRows.forEach(row => row.remove());
    
    // Build query string - only include non-empty filters
    const params = new URLSearchParams({
        page: currentPage,
        limit: 10
    });
    
    // Add filters only if they have values
    if (currentFilters.search && currentFilters.search.trim() !== '') {
        params.append('search', currentFilters.search.trim());
    }
    if (currentFilters.status && currentFilters.status !== '') {
        params.append('status', currentFilters.status);
    }
    if (currentFilters.from_date && currentFilters.from_date !== '') {
        params.append('from_date', currentFilters.from_date);
    }
    if (currentFilters.to_date && currentFilters.to_date !== '') {
        params.append('to_date', currentFilters.to_date);
    }
    
    fetch(`../../api/v1/supply/get_supply_requests.php?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Hide loading state
            if (loadingRow) {
                loadingRow.classList.add('hidden');
            }
            
            // Ensure all existing rows are cleared (in case they weren't cleared earlier)
            const remainingRows = tbody.querySelectorAll('tr:not(#supplyRequestsLoading):not(#supplyRequestsEmpty)');
            remainingRows.forEach(row => row.remove());
            
            if (data.success && data.requests && data.requests.length > 0) {
                // Hide empty state
                if (emptyRow) {
                    emptyRow.classList.add('hidden');
                }
                
                // Add request rows
                data.requests.forEach(request => {
                    const row = createRequestRow(request);
                    tbody.appendChild(row);
                });
                
                // Update pagination
                updatePagination(data.pagination);
            } else {
                // Show empty state
                if (emptyRow) {
                    emptyRow.classList.remove('hidden');
                }
                
                // Hide pagination when no results
                const paginationDiv = document.getElementById('supplyRequestsPagination');
                if (paginationDiv) {
                    paginationDiv.classList.add('hidden');
                }
            }
            
            // Always update sidebar badge from API to get accurate total count
            loadSidebarBadge();
        })
        .catch(error => {
            console.error('Error loading supply requests:', error);
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="7" class="px-4 py-8 text-center text-red-600">Error loading requests. Please refresh the page.</td>';
            }
        });
}

/**
 * Clear Search
 * Resets search and reloads requests
 */
export function clearSearch() {
    const searchInput = document.getElementById('supplyRequestsSearch');
    if (searchInput) {
        searchInput.value = '';
    }
    currentFilters.search = '';
    currentPage = 1;
    loadSupplyRequests();
}


/**
 * Create Request Row
 */
function createRequestRow(request) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    const requestDate = formatDateTime(request.request_date);
    const statusBadge = getStatusBadge(request.status);
    
    // Create cells
    const trackingIdCell = document.createElement('td');
    trackingIdCell.className = 'px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600';
    trackingIdCell.textContent = request.tracking_id;
    
    const requesterCell = document.createElement('td');
    requesterCell.className = 'px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900';
    requesterCell.textContent = request.requester_name;
    
    const itemCell = document.createElement('td');
    itemCell.className = 'px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words';
    itemCell.textContent = request.item_description || 'N/A';
    
    const quantityCell = document.createElement('td');
    quantityCell.className = 'px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell';
    quantityCell.textContent = `${request.quantity || 0} ${request.unit_of_measure || ''}`;
    
    const dateCell = document.createElement('td');
    dateCell.className = 'px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell';
    dateCell.textContent = requestDate;
    
    const statusCell = document.createElement('td');
    statusCell.className = 'px-3 lg:px-4 py-2 lg:py-3';
    statusCell.innerHTML = statusBadge;
    
    const actionsCell = document.createElement('td');
    actionsCell.className = 'px-3 lg:px-4 py-2 lg:py-3';
    
    // Store tracking_id in a variable to ensure it's captured correctly
    const trackingId = request.tracking_id;
    
    // Create Process Request button
    const processBtn = document.createElement('button');
    processBtn.className = 'text-green-600 hover:text-green-800 mr-2 p-1 cursor-pointer';
    processBtn.title = 'Process Request';
    processBtn.innerHTML = '<i class="fas fa-cog"></i>';
    processBtn.type = 'button'; // Prevent form submission if inside a form
    processBtn.setAttribute('data-tracking-id', trackingId);
    
    // Use a closure to capture the trackingId
    (function(tId) {
        processBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Try multiple ways to call the function
            if (window.openProcessModal && typeof window.openProcessModal === 'function') {
                window.openProcessModal(tId);
            } else if (typeof openProcessModal === 'function') {
                openProcessModal(tId);
            } else {
                console.error('openProcessModal is not available');
                alert('Error: Process function not available. Please refresh the page.');
            }
        });
    })(trackingId);
    
    // Create View Details button
    const viewBtn = document.createElement('button');
    viewBtn.className = 'text-blue-600 hover:text-blue-800 p-1 cursor-pointer';
    viewBtn.title = 'View Details';
    viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
    viewBtn.type = 'button'; // Prevent form submission if inside a form
    viewBtn.setAttribute('data-tracking-id', trackingId);
    
    // Use a closure to capture the trackingId
    (function(tId) {
        viewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Try multiple ways to call the function
            if (window.openRequestDetailsModal && typeof window.openRequestDetailsModal === 'function') {
                window.openRequestDetailsModal(tId);
            } else if (typeof viewDetails === 'function') {
                viewDetails(tId);
            } else {
                console.error('View details function is not available');
                alert('Error: View details function not available. Please refresh the page.');
            }
        });
    })(trackingId);
    
    actionsCell.appendChild(processBtn);
    actionsCell.appendChild(viewBtn);
    
    // Append all cells to row
    row.appendChild(trackingIdCell);
    row.appendChild(requesterCell);
    row.appendChild(itemCell);
    row.appendChild(quantityCell);
    row.appendChild(dateCell);
    row.appendChild(statusCell);
    row.appendChild(actionsCell);
    
    return row;
}

/**
 * Update Pagination
 */
function updatePagination(pagination) {
    const paginationDiv = document.getElementById('supplyRequestsPagination');
    const paginationInfo = document.getElementById('supplyRequestsPaginationInfo');
    const paginationButtons = document.getElementById('supplyRequestsPaginationButtons');
    
    if (!paginationDiv || !paginationInfo || !paginationButtons) return;
    
    if (pagination.total_pages > 1) {
        paginationDiv.classList.remove('hidden');
        paginationInfo.textContent = `Showing ${pagination.from}-${pagination.to} of ${pagination.total_records} requests`;
        
        // Clear existing buttons
        paginationButtons.innerHTML = '';
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50';
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = pagination.current_page === 1;
        prevBtn.addEventListener('click', () => {
            if (pagination.current_page > 1) {
                currentPage = pagination.current_page - 1;
                loadSupplyRequests();
            }
        });
        paginationButtons.appendChild(prevBtn);
        
        // Page number buttons
        for (let i = 1; i <= pagination.total_pages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 ${i === pagination.current_page ? 'bg-[#103D1C] text-white' : ''}`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                loadSupplyRequests();
            });
            paginationButtons.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50';
        nextBtn.textContent = 'Next';
        nextBtn.disabled = pagination.current_page === pagination.total_pages;
        nextBtn.addEventListener('click', () => {
            if (pagination.current_page < pagination.total_pages) {
                currentPage = pagination.current_page + 1;
                loadSupplyRequests();
            }
        });
        paginationButtons.appendChild(nextBtn);
    } else {
        paginationDiv.classList.add('hidden');
    }
}

// Make functions globally accessible for any inline onclick handlers
window.processRequest = function(trackingId) {
    console.log('window.processRequest called with:', trackingId);
    if (window.openProcessModal && typeof window.openProcessModal === 'function') {
        window.openProcessModal(trackingId);
    } else if (openProcessModal && typeof openProcessModal === 'function') {
        openProcessModal(trackingId);
    } else {
        console.error('openProcessModal not available');
        alert('Error: Process function not available. Please refresh the page.');
    }
};

window.viewRequestDetails = function(trackingId) {
    console.log('window.viewRequestDetails called with:', trackingId);
    if (window.openRequestDetailsModal && typeof window.openRequestDetailsModal === 'function') {
        window.openRequestDetailsModal(trackingId);
    } else if (viewDetails && typeof viewDetails === 'function') {
        viewDetails(trackingId);
    } else {
        console.error('View details function not available');
        alert('Error: View details function not available. Please refresh the page.');
    }
};

window.clearSearch = function() {
    clearSearch();
};

