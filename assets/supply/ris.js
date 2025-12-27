/**
 * RIS Management Module
 * Handles Requisition and Issue Slip (RIS) listing and management
 */

import { escapeHtml, getStatusBadge, formatDate, showSuccessMessage, showErrorMessage } from './utils.js';

/**
 * Load RIS List
 */
export function loadRISList() {
    const tbody = document.getElementById('risTableBody');
    const loadingRow = document.getElementById('risLoading');
    const emptyRow = document.getElementById('risEmpty');
    
    if (!tbody) return;
    
    // Show loading state
    if (loadingRow) {
        loadingRow.classList.remove('hidden');
    }
    if (emptyRow) {
        emptyRow.classList.add('hidden');
    }
    
    fetch('../../api/v1/supply/get_ris_list.php')
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
            
            if (data.success && data.ris_list && data.ris_list.length > 0) {
                // Hide empty state
                if (emptyRow) {
                    emptyRow.classList.add('hidden');
                }
                
                // Clear existing rows
                const existingRows = tbody.querySelectorAll('tr:not(#risLoading):not(#risEmpty)');
                existingRows.forEach(row => row.remove());
                
                // Add RIS rows
                data.ris_list.forEach(ris => {
                    const row = createRISRow(ris);
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
            console.error('Error loading RIS list:', error);
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="8" class="px-4 py-8 text-center text-red-600">Error loading RIS list. Please refresh the page.</td>';
            }
        });
}

/**
 * Create RIS Row
 */
function createRISRow(ris) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    const issueDate = formatDate(ris.issue_date);
    const statusBadge = getStatusBadge(ris.status);
    
    row.innerHTML = `
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-blue-600">${escapeHtml(ris.ris_number)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">${escapeHtml(ris.supply_request_tracking_id || 'N/A')}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900">${escapeHtml(ris.requester_name)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">${escapeHtml(ris.item_description || 'N/A')}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">${escapeHtml(ris.quantity || 0)} ${escapeHtml(ris.unit_of_measure || '')}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">${escapeHtml(issueDate)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3">${statusBadge}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3">
            <button onclick="viewRIS('${escapeHtml(ris.ris_number)}')" class="text-blue-600 hover:text-blue-800 mr-2 p-1" title="View RIS">
                <i class="fas fa-eye"></i>
            </button>
            <button onclick="printRIS('${escapeHtml(ris.ris_number)}')" class="text-green-600 hover:text-green-800 p-1" title="Print RIS">
                <i class="fas fa-print"></i>
            </button>
        </td>
    `;
    
    return row;
}

// Make functions globally accessible
window.viewRIS = function(risNumber) {
    // This will be handled by modals.js
    if (window.openRISModal) {
        window.openRISModal(risNumber);
    }
};

window.printRIS = function(risNumber) {
    // Open RIS in new window for printing
    window.open(`../../pdf/ris/ris_form.html?ris_number=${encodeURIComponent(risNumber)}`, '_blank');
};

