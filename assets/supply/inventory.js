/**
 * Inventory Management Module
 * Handles inventory items listing, filtering, and management
 */

import { escapeHtml, getStatusBadge, showSuccessMessage, showErrorMessage } from './utils.js';

let inventoryCurrentPage = 1;
let inventoryFilters = {
    search: '',
    category: '',
    stock_status: ''
};

/**
 * Initialize Inventory
 */
export function initializeInventory() {
    // Set up filter event listeners
    const searchInput = document.getElementById('inventorySearch');
    const categoryFilter = document.getElementById('inventoryCategoryFilter');
    const stockStatusFilter = document.getElementById('inventoryStockStatusFilter');
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                inventoryFilters.search = this.value.trim();
                inventoryCurrentPage = 1;
                loadInventory();
            }, 500);
        });
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            inventoryFilters.category = this.value;
            inventoryCurrentPage = 1;
            loadInventory();
        });
    }
    
    if (stockStatusFilter) {
        stockStatusFilter.addEventListener('change', function() {
            inventoryFilters.stock_status = this.value;
            inventoryCurrentPage = 1;
            loadInventory();
        });
    }
}

/**
 * Load Inventory Items
 */
export function loadInventory() {
    const tbody = document.getElementById('inventoryTableBody');
    const loadingRow = document.getElementById('inventoryLoading');
    const emptyRow = document.getElementById('inventoryEmpty');
    
    if (!tbody) return;
    
    // Show loading state
    if (loadingRow) {
        loadingRow.classList.remove('hidden');
    }
    if (emptyRow) {
        emptyRow.classList.add('hidden');
    }
    
    // Build query string
    const params = new URLSearchParams({
        page: inventoryCurrentPage,
        limit: 10,
        ...inventoryFilters
    });
    
    fetch(`../../api/v1/supply/get_inventory_items.php?${params}`)
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
            
            if (data.success && data.items && data.items.length > 0) {
                // Hide empty state
                if (emptyRow) {
                    emptyRow.classList.add('hidden');
                }
                
                // Clear existing rows
                const existingRows = tbody.querySelectorAll('tr:not(#inventoryLoading):not(#inventoryEmpty)');
                existingRows.forEach(row => row.remove());
                
                // Add inventory rows
                data.items.forEach(item => {
                    const row = createInventoryRow(item);
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
            console.error('Error loading inventory:', error);
            if (loadingRow) {
                loadingRow.innerHTML = '<td colspan="8" class="px-4 py-8 text-center text-red-600">Error loading inventory. Please refresh the page.</td>';
            }
        });
}

/**
 * Create Inventory Row
 */
function createInventoryRow(item) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    // Determine stock status
    let stockStatus = 'In Stock';
    let stockStatusClass = 'bg-green-100 text-green-800';
    let stockColor = 'text-gray-900';
    
    if (item.stock_on_hand === 0) {
        stockStatus = 'Out of Stock';
        stockStatusClass = 'bg-red-100 text-red-800';
        stockColor = 'text-red-600';
    } else if (item.stock_on_hand <= item.reorder_level) {
        stockStatus = 'Low Stock';
        stockStatusClass = 'bg-yellow-100 text-yellow-800';
        stockColor = 'text-yellow-600';
    }
    
    const statusBadge = `<span class="px-2 py-1 ${stockStatusClass} text-xs rounded-full font-medium">${stockStatus}</span>`;
    
    row.innerHTML = `
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-gray-900">${escapeHtml(item.item_code)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-900 break-words">${escapeHtml(item.item_description)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden md:table-cell">${escapeHtml(item.category || 'N/A')}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell">${escapeHtml(item.unit_of_measure)}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3">
            <span class="text-xs lg:text-sm font-semibold ${stockColor}">${escapeHtml(item.stock_on_hand)}</span>
            <span class="text-xs text-gray-500"> (Threshold: ${escapeHtml(item.reorder_level)})</span>
        </td>
        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-600 hidden lg:table-cell break-words">${escapeHtml(item.location || 'N/A')}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3">${statusBadge}</td>
        <td class="px-3 lg:px-4 py-2 lg:py-3">
            <button onclick="editInventoryItem('${escapeHtml(item.item_id)}')" class="text-blue-600 hover:text-blue-800 mr-2 p-1" title="Edit Item">
                <i class="fas fa-edit"></i>
            </button>
            <button onclick="viewInventoryHistory('${escapeHtml(item.item_id)}')" class="text-purple-600 hover:text-purple-800 p-1" title="View History">
                <i class="fas fa-history"></i>
            </button>
        </td>
    `;
    
    return row;
}

// Make functions globally accessible
window.editInventoryItem = function(itemId) {
    // This will be handled by modals.js
    if (window.openEditInventoryModal) {
        window.openEditInventoryModal(itemId);
    }
};

window.viewInventoryHistory = function(itemId) {
    // This will be handled by modals.js
    if (window.openInventoryHistoryModal) {
        window.openInventoryHistoryModal(itemId);
    }
};

window.showAddItemModal = function() {
    // This will be handled by modals.js
    if (window.openAddInventoryItemModal) {
        window.openAddInventoryItemModal();
    } else if (window.openAddInventoryModal) {
        window.openAddInventoryModal();
    }
};

// Export loadInventory for use in modals
window.loadInventory = loadInventory;

