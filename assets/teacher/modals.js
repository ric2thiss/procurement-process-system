/**
 * Modals Module
 * Handles request details modal and other modal interactions
 */

import { escapeHtml, getStatusBadge, formatDate, formatDateTime } from './utils.js';
import { trackRequest } from './tracking.js';

/**
 * View Request Details
 * Opens modal with request details
 * @param {string} trackingId - Tracking ID of the request
 */
export function viewRequestDetails(trackingId) {
    if (!trackingId) {
        console.error('Tracking ID is required');
        return;
    }
    
    // Show modal
    const modal = document.getElementById('requestDetailsModal');
    const loading = document.getElementById('requestDetailsLoading');
    const error = document.getElementById('requestDetailsError');
    const content = document.getElementById('requestDetailsContent');
    const errorMessage = document.getElementById('requestDetailsErrorMessage');
    
    if (!modal) return;
    
    // Reset states
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    error.classList.add('hidden');
    content.classList.add('hidden');
    
    // Store tracking ID for track button
    modal.dataset.trackingId = trackingId;
    
    // Fetch request details
    fetch(`../../api/v1/shared/get_request_details.php?tracking_id=${encodeURIComponent(trackingId)}`)
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
            loading.classList.add('hidden');
            
            if (data.success && data.request) {
                displayRequestDetails(data);
                content.classList.remove('hidden');
            } else {
                error.classList.remove('hidden');
                errorMessage.textContent = data.message || 'Failed to load request details.';
            }
        })
        .catch(err => {
            console.error('Error fetching request details:', err);
            loading.classList.add('hidden');
            error.classList.remove('hidden');
            errorMessage.textContent = err.message || 'An error occurred while loading request details. Please try again.';
        });
}

/**
 * Display Request Details in Modal
 * @param {object} data - Request data from API
 */
function displayRequestDetails(data) {
    const request = data.request;
    const items = data.items || [];
    
    // Format dates
    const formattedRequestDate = formatDateTime(request.request_date);
    const formattedUpdatedDate = formatDateTime(request.updated_at);
    const expectedDate = request.expected_delivery_date 
        ? formatDate(request.expected_delivery_date)
        : 'Not specified';
    
    // Update basic info
    const detailTrackingId = document.getElementById('detailTrackingId');
    const detailRequestDate = document.getElementById('detailRequestDate');
    const detailUpdatedAt = document.getElementById('detailUpdatedAt');
    const detailPriority = document.getElementById('detailPriority');
    
    if (detailTrackingId) detailTrackingId.textContent = request.tracking_id;
    if (detailRequestDate) detailRequestDate.textContent = formattedRequestDate;
    if (detailUpdatedAt) detailUpdatedAt.textContent = formattedUpdatedDate;
    if (detailPriority) detailPriority.textContent = request.priority || 'Normal';
    
    // Update status badge
    const statusBadge = getStatusBadge(request.status);
    const statusContainer = document.getElementById('detailStatus');
    if (statusContainer) {
        statusContainer.innerHTML = statusBadge;
    }
    
    // Update status text
    const detailStatusText = document.getElementById('detailStatusText');
    if (detailStatusText) {
        detailStatusText.textContent = request.status;
    }
    
    // Update item description (first item or combined)
    const itemDescription = document.getElementById('detailItemDescription');
    const quantityElement = document.getElementById('detailQuantity');
    
    if (itemDescription && quantityElement) {
        if (items.length > 0) {
            const firstItem = items[0];
            itemDescription.textContent = firstItem.item_description || 'N/A';
            
            const quantityText = firstItem.quantity && firstItem.unit_of_measure 
                ? `Quantity: ${firstItem.quantity} ${firstItem.unit_of_measure}`
                : items.length > 1 
                    ? `${items.length} item(s)`
                    : 'Quantity: N/A';
            quantityElement.textContent = quantityText;
        } else {
            itemDescription.textContent = 'No items found';
            quantityElement.textContent = '';
        }
    }
    
    // Update justification (brief - truncate if too long)
    const justification = request.justification || 'No justification provided.';
    const justificationElement = document.getElementById('detailJustification');
    if (justificationElement) {
        if (justification.length > 200) {
            justificationElement.textContent = justification.substring(0, 200) + '...';
        } else {
            justificationElement.textContent = justification;
        }
    }
}

/**
 * Close Request Details Modal
 */
export function closeRequestDetailsModal() {
    const modal = document.getElementById('requestDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Track Request from Details Modal
 */
export function trackRequestFromDetails() {
    const modal = document.getElementById('requestDetailsModal');
    if (modal && modal.dataset.trackingId) {
        closeRequestDetailsModal();
        setTimeout(() => {
            trackRequest(modal.dataset.trackingId);
        }, 300);
    }
}

// Make functions globally accessible
window.viewRequestDetails = viewRequestDetails;
window.closeRequestDetailsModal = closeRequestDetailsModal;
window.trackRequestFromDetails = trackRequestFromDetails;

