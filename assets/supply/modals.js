/**
 * Modals Module
 * Handles all modal dialogs (process request, view details, etc.)
 */

import { escapeHtml, getStatusBadge, formatDateTime, showSuccessMessage, showErrorMessage, showToast } from './utils.js';
import { loadSupplyRequests } from './requests.js';
import { loadRISList } from './ris.js';
import { loadSidebarBadge } from './dashboard.js';

/**
 * Open Process Request Modal
 */
export function openProcessModal(trackingId) {
    const modal = document.getElementById('processModal');
    if (!modal) {
        console.error('Process modal element not found!');
        showErrorMessage('Error: Process modal not found. Please refresh the page.');
        return;
    }
    
    // Show modal immediately (will be populated with data)
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch request details
    fetch(`../../api/v1/shared/get_request_details.php?tracking_id=${encodeURIComponent(trackingId)}`, {
        method: 'GET',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                // Try to parse error message from response
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.request) {
                const request = data.request;
                
                // Populate modal with request details
                document.getElementById('modalTrackingId').textContent = request.tracking_id;
                document.getElementById('modalRequester').textContent = request.requester_name;
                document.getElementById('modalItem').textContent = request.item_description || 'N/A';
                document.getElementById('modalQuantity').textContent = `${request.quantity || 0} ${request.unit_of_measure || ''}`;
                
                // Store request ID for later use
                modal.dataset.requestId = request.supply_request_id;
                modal.dataset.trackingId = trackingId;
                
                // Reset decision sections
                document.getElementById('decisionSection').classList.add('hidden');
                document.getElementById('availableSection').classList.add('hidden');
                document.getElementById('notAvailableSection').classList.add('hidden');
            } else {
                showErrorMessage(data.message || 'Failed to load request details.');
                closeProcessModal();
            }
        })
        .catch(error => {
            console.error('Error loading request details:', error);
            showErrorMessage(error.message || 'Error loading request details. Please try again.');
            closeProcessModal();
        });
}

/**
 * Close Process Modal
 */
export function closeProcessModal() {
    const modal = document.getElementById('processModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset decision sections
        document.getElementById('decisionSection').classList.add('hidden');
        document.getElementById('availableSection').classList.add('hidden');
        document.getElementById('notAvailableSection').classList.add('hidden');
    }
}

/**
 * Check Inventory Availability
 */
export function checkInventory(event) {
    const modal = document.getElementById('processModal');
    if (!modal) return;
    
    const trackingId = modal.dataset.trackingId;
    const requestId = modal.dataset.requestId;
    
    if (!trackingId || !requestId) {
        showErrorMessage('Request information not found.');
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
    button.disabled = true;
    
    fetch('../../api/v1/supply/check_inventory.php', {
        method: 'POST',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            supply_request_id: requestId,
            tracking_id: trackingId
        })
    })
        .then(response => {
            if (!response.ok) {
                // Try to parse error message from response
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            button.innerHTML = originalText;
            button.disabled = false;
            
            if (data.success) {
                const decisionSection = document.getElementById('decisionSection');
                const availableSection = document.getElementById('availableSection');
                const notAvailableSection = document.getElementById('notAvailableSection');
                
                decisionSection.classList.remove('hidden');
                
                if (data.available) {
                    // Item is available
                    availableSection.classList.remove('hidden');
                    notAvailableSection.classList.add('hidden');
                    
                    // Update stock info
                    document.getElementById('currentStock').textContent = `${data.stock_on_hand} ${data.unit_of_measure || ''}`;
                    document.getElementById('stockLocation').textContent = data.location || 'N/A';
                } else {
                    // Item is not available
                    notAvailableSection.classList.remove('hidden');
                    availableSection.classList.add('hidden');
                    
                    // Update stock info
                    document.getElementById('currentStock').textContent = '0';
                }
            } else {
                showErrorMessage(data.message || 'Error checking inventory.');
            }
        })
        .catch(error => {
            console.error('Error checking inventory:', error);
            button.innerHTML = originalText;
            button.disabled = false;
            showErrorMessage('Error checking inventory availability.');
        });
}

/**
 * Show RIS Form
 */
export function showRISForm() {
    const modal = document.getElementById('processModal');
    if (!modal) return;
    
    const requestId = modal.dataset.requestId;
    const trackingId = modal.dataset.trackingId;
    
    if (!requestId || !trackingId) {
        showErrorMessage('Request information not found.');
        return;
    }
    
    // Generate RIS
    fetch('../../api/v1/supply/generate_ris.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            supply_request_id: requestId,
            tracking_id: trackingId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(`RIS generated successfully! RIS Number: ${data.ris_number}`);
                closeProcessModal();
                
                // Refresh requests, RIS list, and sidebar badge
                setTimeout(() => {
                    loadSupplyRequests();
                    loadRISList();
                    loadSidebarBadge();
                }, 1000);
            } else {
                showErrorMessage(data.message || 'Error generating RIS.');
            }
        })
        .catch(error => {
            console.error('Error generating RIS:', error);
            showErrorMessage('Error generating RIS.');
        });
}

/**
 * Forward to PPMP Management
 */
export function forwardToPPMP() {
    const modal = document.getElementById('processModal');
    if (!modal) return;
    
    const requestId = modal.dataset.requestId;
    const trackingId = modal.dataset.trackingId;
    
    if (!requestId || !trackingId) {
        showErrorMessage('Request information not found.');
        return;
    }
    
    if (!confirm(`Forward request ${trackingId} to PPMP Management?\n\nThis will mark the request as "Not Available" and forward it to PPMP Management module for PPMP creation.`)) {
        return;
    }
    
    fetch('../../api/v1/supply/forward_to_ppmp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            supply_request_id: requestId,
            tracking_id: trackingId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(`Request ${trackingId} forwarded to PPMP Management successfully!`);
                closeProcessModal();
                
                // Refresh requests list and sidebar badge
                setTimeout(() => {
                    loadSupplyRequests();
                    loadSidebarBadge();
                }, 1000);
            } else {
                showErrorMessage(data.message || 'Error forwarding request.');
            }
        })
        .catch(error => {
            console.error('Error forwarding to PPMP:', error);
            showErrorMessage('Error forwarding request.');
        });
}

/**
 * View Request Details
 */
export function viewRequestDetails(trackingId) {
    const modal = document.getElementById('viewDetailsModal');
    if (!modal) {
        console.error('View details modal element not found!');
        showErrorMessage('Error: View details modal not found. Please refresh the page.');
        return;
    }
    
    // Show loading state
    document.getElementById('viewDetailsTrackingId').textContent = 'Loading...';
    document.getElementById('viewDetailsRequester').textContent = 'Loading...';
    document.getElementById('viewDetailsItem').textContent = 'Loading...';
    document.getElementById('viewDetailsQuantity').textContent = 'Loading...';
    document.getElementById('viewDetailsUnit').textContent = 'Loading...';
    document.getElementById('viewDetailsRequestDate').textContent = 'Loading...';
    document.getElementById('viewDetailsJustification').textContent = 'Loading...';
    document.getElementById('viewDetailsStatus').innerHTML = '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Loading...</span>';
    
    // Show modal first
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch request details
    fetch(`../../api/v1/shared/get_request_details.php?tracking_id=${encodeURIComponent(trackingId)}`, {
        method: 'GET',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                // Try to parse error message from response
                return response.json().then(data => {
                    // Log debug info if available
                    if (data.debug_info) {
                        console.error('API Debug Info:', data.debug_info);
                        console.error('Required Roles:', data.required_roles);
                    }
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.request) {
                const request = data.request;
                
                // Populate modal with request details
                document.getElementById('viewDetailsTrackingId').textContent = request.tracking_id || '-';
                document.getElementById('viewDetailsRequester').textContent = request.requester_name || '-';
                document.getElementById('viewDetailsItem').textContent = request.item_description || 'N/A';
                document.getElementById('viewDetailsQuantity').textContent = request.quantity || '0';
                document.getElementById('viewDetailsUnit').textContent = request.unit_of_measure || '-';
                document.getElementById('viewDetailsRequestDate').textContent = formatDateTime(request.request_date);
                document.getElementById('viewDetailsJustification').textContent = request.justification || 'N/A';
                document.getElementById('viewDetailsStatus').innerHTML = getStatusBadge(request.status);
            } else {
                showErrorMessage(data.message || 'Failed to load request details.');
                closeViewDetailsModal();
            }
        })
        .catch(error => {
            console.error('Error loading request details:', error);
            showErrorMessage(error.message || 'Error loading request details. Please try again.');
            closeViewDetailsModal();
        });
}

/**
 * Close View Details Modal
 */
export function closeViewDetailsModal() {
    const modal = document.getElementById('viewDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/**
 * Show error message in add inventory item modal
 */
function showAddInventoryItemError(message) {
    const errorContainer = document.getElementById('addInventoryItemError');
    const errorText = document.getElementById('addInventoryItemErrorText');
    const successContainer = document.getElementById('addInventoryItemSuccess');
    
    if (errorContainer && errorText) {
        errorText.textContent = message;
        errorContainer.classList.remove('hidden');
        if (successContainer) {
            successContainer.classList.add('hidden');
        }
        
        // Scroll to error message
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Also show toast notification
        showToast(message, 'error');
    }
}

/**
 * Show success message in add inventory item modal
 */
function showAddInventoryItemSuccess(message) {
    const successContainer = document.getElementById('addInventoryItemSuccess');
    const successText = document.getElementById('addInventoryItemSuccessText');
    const errorContainer = document.getElementById('addInventoryItemError');
    
    if (successContainer && successText) {
        successText.textContent = message;
        successContainer.classList.remove('hidden');
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }
    }
}

/**
 * Close error message in add inventory item modal
 */
window.closeAddInventoryItemError = function() {
    const errorContainer = document.getElementById('addInventoryItemError');
    if (errorContainer) {
        errorContainer.classList.add('hidden');
    }
};

/**
 * Open Add Inventory Item Modal
 */
export function openAddInventoryItemModal() {
    const modal = document.getElementById('addInventoryItemModal');
    if (!modal) {
        console.error('Add inventory item modal element not found!');
        showErrorMessage('Error: Add inventory item modal not found. Please refresh the page.');
        return;
    }
    
    // Reset form
    const form = document.getElementById('addInventoryItemForm');
    if (form) {
        form.reset();
    }
    
    // Hide any previous error/success messages
    const errorContainer = document.getElementById('addInventoryItemError');
    const successContainer = document.getElementById('addInventoryItemSuccess');
    if (errorContainer) errorContainer.classList.add('hidden');
    if (successContainer) successContainer.classList.add('hidden');
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

/**
 * Close Add Inventory Item Modal
 */
export function closeAddInventoryItemModal() {
    const modal = document.getElementById('addInventoryItemModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset form
        const form = document.getElementById('addInventoryItemForm');
        if (form) {
            form.reset();
        }
    }
}

/**
 * Submit Add Inventory Item Form
 */
function submitAddInventoryItem() {
    const form = document.getElementById('addInventoryItemForm');
    if (!form) return;
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    submitButton.disabled = true;
    
    // Collect form data
    const itemCodeEl = document.getElementById('addItemCode');
    const itemDescriptionEl = document.getElementById('addItemDescription');
    const unitOfMeasureEl = document.getElementById('addItemUnitOfMeasure');
    const categoryEl = document.getElementById('addItemCategory');
    const standardPriceEl = document.getElementById('addItemStandardPrice');
    const reorderLevelEl = document.getElementById('addItemReorderLevel');
    const reorderQuantityEl = document.getElementById('addItemReorderQuantity');
    const stockOnHandEl = document.getElementById('addItemStockOnHand');
    const locationEl = document.getElementById('addItemLocation');
    const notesEl = document.getElementById('addItemNotes');
    
    if (!itemCodeEl || !itemDescriptionEl || !unitOfMeasureEl) {
        showErrorMessage('Form fields not found. Please refresh the page.', 'inventory-section');
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        return;
    }
    
    const formData = {
        item_code: itemCodeEl.value.trim(),
        item_description: itemDescriptionEl.value.trim(),
        unit_of_measure: unitOfMeasureEl.value.trim(),
        category: categoryEl && categoryEl.value.trim() ? categoryEl.value.trim() : null,
        standard_unit_price: standardPriceEl && standardPriceEl.value.trim() ? parseFloat(standardPriceEl.value) : null,
        reorder_level: reorderLevelEl && reorderLevelEl.value.trim() ? parseInt(reorderLevelEl.value) : 0,
        reorder_quantity: reorderQuantityEl && reorderQuantityEl.value.trim() ? parseInt(reorderQuantityEl.value) : 0,
        stock_on_hand: stockOnHandEl && stockOnHandEl.value.trim() ? parseInt(stockOnHandEl.value) : 0,
        location: locationEl && locationEl.value.trim() ? locationEl.value.trim() : null,
        notes: notesEl && notesEl.value.trim() ? notesEl.value.trim() : null
    };
    
    // Debug logging
    console.log('Form data being sent:', formData);
    
    fetch('../../api/v1/supply/add_inventory_item.php', {
        method: 'POST',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!response.ok) {
                        // Extract the user-friendly message from the JSON response
                        const errorMessage = data.message || `An error occurred (Status: ${response.status})`;
                        throw new Error(errorMessage);
                    }
                    return data;
                } catch (e) {
                    // If it's already our custom error with the message, re-throw it
                    if (e.message && !e.message.includes('HTTP error!') && !e.message.includes('Response:')) {
                        throw e;
                    }
                    // If JSON parsing fails, try to extract message from text
                    if (!response.ok) {
                        try {
                            const parsed = JSON.parse(text);
                            const errorMessage = parsed.message || `An error occurred (Status: ${response.status})`;
                            throw new Error(errorMessage);
                        } catch (parseError) {
                            // If we can't parse JSON, show a generic error
                            throw new Error(`An error occurred while processing your request. Please try again.`);
                        }
                    }
                    throw new Error('Invalid response from server. Please try again.');
                }
            });
        })
        .then(data => {
            if (data.success) {
                const successMessage = data.message || 'Inventory item added successfully!';
                showAddInventoryItemSuccess(successMessage);
                showToast(successMessage, 'success');
                
                // Close modal and reload inventory list after a short delay
                setTimeout(() => {
                    closeAddInventoryItemModal();
                    if (window.loadInventory) {
                        window.loadInventory();
                    }
                    // Also show success message in the inventory section
                    showSuccessMessage(successMessage, 'inventory-section');
                }, 1500);
            } else {
                const errorMessage = data.message || 'Failed to add inventory item.';
                showAddInventoryItemError(errorMessage);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error adding inventory item:', error);
            const errorMessage = error.message || 'An error occurred while adding the inventory item.';
            showAddInventoryItemError(errorMessage);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
}

/**
 * Open Edit Inventory Item Modal
 */
export function openEditInventoryItemModal(itemId) {
    const modal = document.getElementById('editInventoryItemModal');
    if (!modal) {
        console.error('Edit inventory item modal element not found!');
        showErrorMessage('Error: Edit inventory item modal not found. Please refresh the page.');
        return;
    }
    
    // Hide any previous error/success messages
    const errorContainer = document.getElementById('editInventoryItemError');
    const successContainer = document.getElementById('editInventoryItemSuccess');
    if (errorContainer) errorContainer.classList.add('hidden');
    if (successContainer) successContainer.classList.add('hidden');
    
    // Show loading state
    const form = document.getElementById('editInventoryItemForm');
    if (form) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
            submitButton.disabled = true;
        }
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Load item data
    fetch(`../../api/v1/supply/get_inventory_item.php?item_id=${encodeURIComponent(itemId)}`, {
        credentials: 'same-origin'
    })
        .then(response => {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!response.ok) {
                        const errorMessage = data.message || `An error occurred (Status: ${response.status})`;
                        throw new Error(errorMessage);
                    }
                    return data;
                } catch (e) {
                    if (e.message && !e.message.includes('HTTP error!') && !e.message.includes('Response:')) {
                        throw e;
                    }
                    throw new Error('Failed to load inventory item. Please try again.');
                }
            });
        })
        .then(data => {
            if (data.success && data.item) {
                populateEditForm(data.item);
            } else {
                throw new Error('Failed to load inventory item data.');
            }
        })
        .catch(error => {
            console.error('Error loading inventory item:', error);
            showEditInventoryItemError(error.message || 'Failed to load inventory item. Please try again.');
            closeEditInventoryItemModal();
        })
        .finally(() => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>Update Item';
                submitButton.disabled = false;
            }
        });
}

/**
 * Populate Edit Form with Item Data
 */
function populateEditForm(item) {
    document.getElementById('editItemId').value = item.item_id || '';
    document.getElementById('editItemCode').value = item.item_code || '';
    document.getElementById('editItemDescription').value = item.item_description || '';
    document.getElementById('editItemCategory').value = item.category || '';
    document.getElementById('editItemUnitOfMeasure').value = item.unit_of_measure || '';
    document.getElementById('editItemStandardPrice').value = item.standard_unit_price || '';
    document.getElementById('editItemStockOnHand').value = item.stock_on_hand || 0;
    document.getElementById('editItemReorderLevel').value = item.reorder_level || 0;
    document.getElementById('editItemReorderQuantity').value = item.reorder_quantity || 0;
    document.getElementById('editItemLocation').value = item.location || '';
    document.getElementById('editItemNotes').value = item.notes || '';
}

/**
 * Close Edit Inventory Item Modal
 */
export function closeEditInventoryItemModal() {
    const modal = document.getElementById('editInventoryItemModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset form
        const form = document.getElementById('editInventoryItemForm');
        if (form) {
            form.reset();
        }
        
        // Hide error/success messages
        const errorContainer = document.getElementById('editInventoryItemError');
        const successContainer = document.getElementById('editInventoryItemSuccess');
        if (errorContainer) errorContainer.classList.add('hidden');
        if (successContainer) successContainer.classList.add('hidden');
    }
}

/**
 * Show error message in edit inventory item modal
 */
function showEditInventoryItemError(message) {
    const errorContainer = document.getElementById('editInventoryItemError');
    const errorText = document.getElementById('editInventoryItemErrorText');
    const successContainer = document.getElementById('editInventoryItemSuccess');
    
    if (errorContainer && errorText) {
        errorText.textContent = message;
        errorContainer.classList.remove('hidden');
        if (successContainer) {
            successContainer.classList.add('hidden');
        }
        
        // Scroll to error message
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Also show toast notification
        showToast(message, 'error');
    }
}

/**
 * Show success message in edit inventory item modal
 */
function showEditInventoryItemSuccess(message) {
    const successContainer = document.getElementById('editInventoryItemSuccess');
    const successText = document.getElementById('editInventoryItemSuccessText');
    const errorContainer = document.getElementById('editInventoryItemError');
    
    if (successContainer && successText) {
        successText.textContent = message;
        successContainer.classList.remove('hidden');
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }
    }
}

/**
 * Close error message in edit inventory item modal
 */
window.closeEditInventoryItemError = function() {
    const errorContainer = document.getElementById('editInventoryItemError');
    if (errorContainer) {
        errorContainer.classList.add('hidden');
    }
};

/**
 * Submit Edit Inventory Item Form
 */
function submitEditInventoryItem() {
    const form = document.getElementById('editInventoryItemForm');
    if (!form) return;
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    submitButton.disabled = true;
    
    // Collect form data
    const itemIdEl = document.getElementById('editItemId');
    const itemCodeEl = document.getElementById('editItemCode');
    const itemDescriptionEl = document.getElementById('editItemDescription');
    const unitOfMeasureEl = document.getElementById('editItemUnitOfMeasure');
    const categoryEl = document.getElementById('editItemCategory');
    const standardPriceEl = document.getElementById('editItemStandardPrice');
    const stockOnHandEl = document.getElementById('editItemStockOnHand');
    const reorderLevelEl = document.getElementById('editItemReorderLevel');
    const reorderQuantityEl = document.getElementById('editItemReorderQuantity');
    const locationEl = document.getElementById('editItemLocation');
    const notesEl = document.getElementById('editItemNotes');
    
    if (!itemIdEl || !itemCodeEl || !itemDescriptionEl || !unitOfMeasureEl) {
        showEditInventoryItemError('Form fields not found. Please refresh the page.');
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        return;
    }
    
    const formData = {
        item_id: parseInt(itemIdEl.value),
        item_code: itemCodeEl.value.trim(),
        item_description: itemDescriptionEl.value.trim(),
        unit_of_measure: unitOfMeasureEl.value.trim(),
        category: categoryEl && categoryEl.value.trim() ? categoryEl.value.trim() : null,
        standard_unit_price: standardPriceEl && standardPriceEl.value.trim() ? parseFloat(standardPriceEl.value) : null,
        stock_on_hand: stockOnHandEl && stockOnHandEl.value !== '' ? parseInt(stockOnHandEl.value) : null,
        reorder_level: reorderLevelEl && reorderLevelEl.value.trim() ? parseInt(reorderLevelEl.value) : 0,
        reorder_quantity: reorderQuantityEl && reorderQuantityEl.value.trim() ? parseInt(reorderQuantityEl.value) : 0,
        location: locationEl && locationEl.value.trim() ? locationEl.value.trim() : null,
        notes: notesEl && notesEl.value.trim() ? notesEl.value.trim() : null
    };
    
    fetch('../../api/v1/supply/update_inventory_item.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!response.ok) {
                        const errorMessage = data.message || `An error occurred (Status: ${response.status})`;
                        throw new Error(errorMessage);
                    }
                    return data;
                } catch (e) {
                    if (e.message && !e.message.includes('HTTP error!') && !e.message.includes('Response:')) {
                        throw e;
                    }
                    try {
                        const parsed = JSON.parse(text);
                        const errorMessage = parsed.message || `An error occurred (Status: ${response.status})`;
                        throw new Error(errorMessage);
                    } catch (parseError) {
                        throw new Error(`An error occurred while processing your request. Please try again.`);
                    }
                }
            });
        })
        .then(data => {
            if (data.success) {
                const successMessage = data.message || 'Inventory item updated successfully!';
                showEditInventoryItemSuccess(successMessage);
                showToast(successMessage, 'success');
                
                // Close modal and reload inventory list after a short delay
                setTimeout(() => {
                    closeEditInventoryItemModal();
                    if (window.loadInventory) {
                        window.loadInventory();
                    }
                    // Also show success message in the inventory section
                    showSuccessMessage(successMessage, 'inventory-section');
                }, 1500);
            } else {
                const errorMessage = data.message || 'Failed to update inventory item.';
                showEditInventoryItemError(errorMessage);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error updating inventory item:', error);
            const errorMessage = error.message || 'An error occurred while updating the inventory item.';
            showEditInventoryItemError(errorMessage);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
}

/**
 * Open Inventory History Modal
 */
export function openInventoryHistoryModal(itemId) {
    const modal = document.getElementById('inventoryHistoryModal');
    if (!modal) {
        console.error('Inventory history modal element not found!');
        showErrorMessage('Error: Inventory history modal not found. Please refresh the page.');
        return;
    }
    
    // Show loading state
    const loadingEl = document.getElementById('historyLoading');
    const emptyEl = document.getElementById('historyEmpty');
    const contentEl = document.getElementById('historyContent');
    const tableBody = document.getElementById('historyTableBody');
    
    if (loadingEl) loadingEl.classList.remove('hidden');
    if (emptyEl) emptyEl.classList.add('hidden');
    if (contentEl) contentEl.classList.add('hidden');
    if (tableBody) tableBody.innerHTML = '';
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Load history data
    fetch(`../../api/v1/supply/get_inventory_history.php?item_id=${encodeURIComponent(itemId)}`, {
        credentials: 'same-origin'
    })
        .then(response => {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!response.ok) {
                        const errorMessage = data.message || `An error occurred (Status: ${response.status})`;
                        throw new Error(errorMessage);
                    }
                    return data;
                } catch (e) {
                    if (e.message && !e.message.includes('HTTP error!') && !e.message.includes('Response:')) {
                        throw e;
                    }
                    throw new Error('Failed to load inventory history. Please try again.');
                }
            });
        })
        .then(data => {
            if (loadingEl) loadingEl.classList.add('hidden');
            
            if (data.success && data.item && data.movements) {
                // Update item info
                const itemInfoEl = document.getElementById('historyItemInfo');
                if (itemInfoEl) {
                    itemInfoEl.textContent = `${escapeHtml(data.item.item_code)} - ${escapeHtml(data.item.item_description)}`;
                }
                
                if (data.movements && data.movements.length > 0) {
                    // Show content and populate table
                    if (contentEl) contentEl.classList.remove('hidden');
                    populateHistoryTable(data.movements, data.item.unit_of_measure || '');
                } else {
                    // Show empty state
                    if (emptyEl) emptyEl.classList.remove('hidden');
                }
            } else {
                throw new Error('Failed to load inventory history data.');
            }
        })
        .catch(error => {
            console.error('Error loading inventory history:', error);
            if (loadingEl) loadingEl.classList.add('hidden');
            if (emptyEl) {
                emptyEl.innerHTML = `
                    <i class="fas fa-exclamation-circle text-4xl text-red-300 mb-2"></i>
                    <p class="text-red-600">${escapeHtml(error.message || 'Failed to load inventory history.')}</p>
                `;
                emptyEl.classList.remove('hidden');
            }
            showToast(error.message || 'Failed to load inventory history.', 'error');
        });
}

/**
 * Populate History Table
 */
function populateHistoryTable(movements, unitOfMeasure) {
    const tableBody = document.getElementById('historyTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    movements.forEach(movement => {
        const row = createHistoryRow(movement, unitOfMeasure);
        tableBody.appendChild(row);
    });
}

/**
 * Create History Row
 */
function createHistoryRow(movement, unitOfMeasure) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    
    // Movement type badge
    const typeConfig = {
        'IN': { class: 'bg-green-100 text-green-800', label: 'IN', icon: 'fa-arrow-down' },
        'OUT': { class: 'bg-red-100 text-red-800', label: 'OUT', icon: 'fa-arrow-up' },
        'ADJUSTMENT': { class: 'bg-yellow-100 text-yellow-800', label: 'ADJUST', icon: 'fa-adjust' },
        'RETURN': { class: 'bg-blue-100 text-blue-800', label: 'RETURN', icon: 'fa-undo' }
    };
    
    const typeInfo = typeConfig[movement.movement_type] || { class: 'bg-gray-100 text-gray-800', label: movement.movement_type, icon: 'fa-circle' };
    const typeBadge = `<span class="px-2 py-1 ${typeInfo.class} text-xs rounded-full font-medium">
        <i class="fas ${typeInfo.icon} mr-1"></i>${typeInfo.label}
    </span>`;
    
    // Reference info
    let referenceInfo = '-';
    if (movement.reference_type && movement.reference_id) {
        referenceInfo = `${movement.reference_type} #${movement.reference_id}`;
    }
    
    // User info
    const userName = movement.first_name && movement.last_name 
        ? `${escapeHtml(movement.first_name)} ${escapeHtml(movement.last_name)}`
        : (movement.username ? escapeHtml(movement.username) : 'Unknown');
    
    // Format date
    const movementDate = movement.movement_date 
        ? formatDateTime(movement.movement_date)
        : '-';
    
    // Format quantity with correct sign
    let quantityDisplay = '';
    if (movement.movement_type === 'IN' || movement.movement_type === 'RETURN') {
        quantityDisplay = `+${escapeHtml(movement.quantity)}`;
    } else if (movement.movement_type === 'OUT') {
        quantityDisplay = `-${escapeHtml(movement.quantity)}`;
    } else if (movement.movement_type === 'ADJUSTMENT') {
        // For adjustments, show sign based on whether stock increased or decreased
        const stockChange = (movement.stock_after || 0) - (movement.stock_before || 0);
        quantityDisplay = stockChange >= 0 ? `+${escapeHtml(movement.quantity)}` : `-${escapeHtml(movement.quantity)}`;
    } else {
        quantityDisplay = escapeHtml(movement.quantity);
    }
    
    // Ensure stock_before and stock_after are displayed correctly
    const stockBefore = movement.stock_before !== null && movement.stock_before !== undefined 
        ? escapeHtml(movement.stock_before) 
        : '0';
    const stockAfter = movement.stock_after !== null && movement.stock_after !== undefined 
        ? escapeHtml(movement.stock_after) 
        : '0';
    
    row.innerHTML = `
        <td class="px-5 py-3 text-sm text-gray-900 whitespace-nowrap">${movementDate}</td>
        <td class="px-5 py-3 whitespace-nowrap">${typeBadge}</td>
        <td class="px-5 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
            ${quantityDisplay} ${escapeHtml(unitOfMeasure)}
        </td>
        <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">${stockBefore} ${escapeHtml(unitOfMeasure)}</td>
        <td class="px-5 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">${stockAfter} ${escapeHtml(unitOfMeasure)}</td>
        <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">${escapeHtml(referenceInfo)}</td>
        <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">${userName}</td>
        <td class="px-5 py-3 text-sm text-gray-600 break-words">${escapeHtml(movement.notes || '-')}</td>
    `;
    
    return row;
}

/**
 * Close Inventory History Modal
 */
export function closeInventoryHistoryModal() {
    const modal = document.getElementById('inventoryHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Clear table
        const tableBody = document.getElementById('historyTableBody');
        if (tableBody) {
            tableBody.innerHTML = '';
        }
    }
}

// Initialize add inventory item form submission
document.addEventListener('DOMContentLoaded', function() {
    const addInventoryItemForm = document.getElementById('addInventoryItemForm');
    if (addInventoryItemForm) {
        addInventoryItemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAddInventoryItem();
        });
    }
    
    // Initialize edit inventory item form submission
    const editInventoryItemForm = document.getElementById('editInventoryItemForm');
    if (editInventoryItemForm) {
        editInventoryItemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditInventoryItem();
        });
    }
});

// Make functions globally accessible
window.openProcessModal = openProcessModal;
window.closeProcessModal = closeProcessModal;
window.checkInventory = checkInventory;
window.showRISForm = showRISForm;
window.forwardToPPMP = forwardToPPMP;
window.openRequestDetailsModal = viewRequestDetails;
window.closeViewDetailsModal = closeViewDetailsModal;
window.openAddInventoryModal = openAddInventoryItemModal;
window.openAddInventoryItemModal = openAddInventoryItemModal;
window.closeAddInventoryItemModal = closeAddInventoryItemModal;
window.openEditInventoryModal = openEditInventoryItemModal;
window.openEditInventoryItemModal = openEditInventoryItemModal;
window.closeEditInventoryItemModal = closeEditInventoryItemModal;
window.openInventoryHistoryModal = openInventoryHistoryModal;
window.closeInventoryHistoryModal = closeInventoryHistoryModal;
window.openInventoryHistoryModal = openInventoryHistoryModal;
window.closeInventoryHistoryModal = closeInventoryHistoryModal;

