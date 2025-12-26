/**
 * Issued Items Module
 * Handles issued items (RIS) functionality
 */

import { escapeHtml, getStatusBadge, formatDate } from './utils.js';
import { trackRequest } from './tracking.js';

/**
 * Load Issued Items
 */
export function loadIssuedItems() {
    const tbody = document.getElementById('issuedItemsTableBody');
    const loadingRow = document.getElementById('issuedItemsLoading');
    const emptyRow = document.getElementById('issuedItemsEmpty');
    
    if (!tbody) return;
    
    // Show loading
    if (loadingRow) loadingRow.classList.remove('hidden');
    if (emptyRow) emptyRow.classList.add('hidden');
    
    // Clear existing rows
    const existingRows = tbody.querySelectorAll('tr:not(#issuedItemsLoading):not(#issuedItemsEmpty)');
    existingRows.forEach(row => row.remove());
    
    fetch('../../api/v1/teacher/get_issued_items.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Hide loading
            if (loadingRow) loadingRow.classList.add('hidden');
            
            if (data.success && data.items && data.items.length > 0) {
                // Hide empty state
                if (emptyRow) emptyRow.classList.add('hidden');
                
                // Add item rows
                data.items.forEach(item => {
                    const row = createIssuedItemRow(item);
                    tbody.appendChild(row);
                });
            } else {
                // Show empty state
                if (emptyRow) emptyRow.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading issued items:', error);
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="6" class="px-4 py-8 text-center text-red-600">Error loading issued items. Please refresh the page.</td>';
            }
        });
}

/**
 * Create Issued Item Row
 * @param {object} item - Issued item data
 * @returns {HTMLElement} Table row element
 */
function createIssuedItemRow(item) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    // Format date
    const formattedDate = formatDate(item.issue_date);
    
    // Get status badge
    let statusBadge = '';
    const status = item.status || 'Issued';
    switch (status) {
        case 'Received':
        case 'Completed':
            statusBadge = '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Received</span>';
            break;
        case 'Issued':
            statusBadge = '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Issued</span>';
            break;
        case 'Generated':
            statusBadge = '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Generated</span>';
            break;
        default:
            statusBadge = `<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-medium">${escapeHtml(status)}</span>`;
    }
    
    // Format quantity
    const quantityText = `${item.quantity} ${item.unit_of_measure || ''}`;
    
    row.innerHTML = `
        <td class="px-4 py-3 text-sm font-semibold text-blue-600">${escapeHtml(item.ris_number)}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(item.item_description)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(quantityText)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(formattedDate)}</td>
        <td class="px-4 py-3">${statusBadge}</td>
        <td class="px-4 py-3">
            <button class="text-blue-600 hover:text-blue-800" onclick="viewIssuedItemDetails('${escapeHtml(item.ris_number)}', '${escapeHtml(item.tracking_id || '')}')" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
        </td>
    `;
    
    return row;
}

/**
 * View Issued Item Details
 * @param {string} risNumber - RIS number
 * @param {string} trackingId - Tracking ID (optional)
 */
function viewIssuedItemDetails(risNumber, trackingId) {
    // If tracking ID is available, navigate to tracking section
    if (trackingId) {
        trackRequest(trackingId);
    } else {
        // Otherwise, show RIS details (can be expanded later)
        alert(`RIS Number: ${risNumber}\n\nDetails view will be implemented here.`);
    }
}

// Make globally accessible
window.viewIssuedItemDetails = viewIssuedItemDetails;

