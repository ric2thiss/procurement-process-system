/**
 * Tracking Module
 * Handles document tracking functionality
 */

import { escapeHtml, getStatusBadge, formatDate, formatDateTime } from './utils.js';
import { showSection } from './sections.js';

/**
 * Track Request
 * Navigates to tracking section and searches for a tracking ID
 * @param {string} trackingId - Tracking ID to search for
 */
export function trackRequest(trackingId) {
    // Navigate to tracking section and search
    showSection('tracking');
    const trackingLink = document.querySelector('[data-section="tracking"]');
    if (trackingLink) {
        trackingLink.click();
    }
    
    // Set tracking ID and trigger search
    const trackingInput = document.getElementById('trackingIdInput');
    if (trackingInput) {
        trackingInput.value = trackingId;
        // Trigger tracking directly
        setTimeout(() => {
            trackDocument(null);
        }, 300);
    }
}

/**
 * Track Document (from tracking form)
 * Make it globally accessible for inline onsubmit handler
 */
window.trackDocument = function(event) {
    if (event) {
        event.preventDefault();
    }
    
    const trackingIdInput = document.getElementById('trackingIdInput');
    if (!trackingIdInput) {
        console.error('Tracking ID input not found');
        return;
    }
    
    const trackingId = trackingIdInput.value.trim();
    
    if (!trackingId) {
        showTrackingError('Please enter a tracking ID');
        return;
    }
    
    // Show loading state
    const form = trackingIdInput.closest('form');
    const button = form ? form.querySelector('button[type="submit"]') : null;
    const resultsDiv = document.getElementById('trackingResults');
    const emptyDiv = document.getElementById('trackingEmpty');
    
    // Hide results and empty state, show loading
    if (resultsDiv) resultsDiv.classList.add('hidden');
    if (emptyDiv) emptyDiv.classList.add('hidden');
    
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
        button.disabled = true;
        
        // Fetch tracking data
        fetch(`../../api/v1/shared/track_document.php?tracking_id=${encodeURIComponent(trackingId)}`)
            .then(response => response.json())
            .then(data => {
                button.innerHTML = originalText;
                button.disabled = false;
                
                if (data.success) {
                    displayTrackingResults(data);
                } else {
                    showTrackingError(data.message || 'Request not found. Please check the tracking ID.');
                }
            })
            .catch(error => {
                console.error('Error tracking document:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                showTrackingError('An error occurred while fetching tracking information. Please try again.');
            });
    } else {
        // If button not found, just fetch and display
        fetch(`../../api/v1/shared/track_document.php?tracking_id=${encodeURIComponent(trackingId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayTrackingResults(data);
                } else {
                    showTrackingError(data.message || 'Request not found.');
                }
            })
            .catch(error => {
                console.error('Error tracking document:', error);
                showTrackingError('An error occurred while fetching tracking information.');
            });
    }
};

/**
 * Display Tracking Results
 * @param {object} data - Tracking data from API
 */
function displayTrackingResults(data) {
    const resultsDiv = document.getElementById('trackingResults');
    const emptyDiv = document.getElementById('trackingEmpty');
    
    if (!resultsDiv || !data || !data.request) {
        showTrackingError('Invalid tracking data received.');
        return;
    }
    
    // Hide empty state, show results
    if (emptyDiv) emptyDiv.classList.add('hidden');
    resultsDiv.classList.remove('hidden');
    resultsDiv.classList.add('fade-in');
    
    const request = data.request;
    const trackingHistory = data.tracking_history || [];
    
    // Format dates
    const formattedRequestDate = formatDateTime(request.request_date);
    const formattedUpdatedDate = formatDateTime(request.updated_at);
    
    // Get status badge
    const statusBadge = getStatusBadge(request.status);
    
    // Update Info Card
    const infoCard = document.getElementById('trackingInfoCard');
    if (infoCard) {
        const quantityText = request.quantity && request.unit_of_measure 
            ? `${request.quantity} ${request.unit_of_measure}`
            : 'N/A';
        
        infoCard.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Tracking ID</p>
                    <p class="text-lg font-bold text-gray-900">${escapeHtml(request.tracking_id)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Status</p>
                    <div class="mt-1">${statusBadge}</div>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Item Description</p>
                    <p class="text-base text-gray-900">${escapeHtml(request.item_description)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Quantity</p>
                    <p class="text-base text-gray-900">${escapeHtml(quantityText)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date Submitted</p>
                    <p class="text-base text-gray-900">${escapeHtml(formattedRequestDate)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-base text-gray-900">${escapeHtml(formattedUpdatedDate)}</p>
                </div>
                ${request.priority ? `
                <div>
                    <p class="text-sm text-gray-600">Priority</p>
                    <p class="text-base text-gray-900">${escapeHtml(request.priority)}</p>
                </div>
                ` : ''}
                ${request.expected_delivery_date ? `
                <div>
                    <p class="text-sm text-gray-600">Expected Delivery Date</p>
                    <p class="text-base text-gray-900">${escapeHtml(formatDate(request.expected_delivery_date))}</p>
                </div>
                ` : ''}
            </div>
        `;
    }
    
    // Update Timeline
    const timeline = document.getElementById('trackingTimeline');
    if (timeline) {
        timeline.innerHTML = generateTrackingTimeline(request.status, trackingHistory);
    }
    
    // Update Status History
    const statusHistory = document.getElementById('trackingStatusHistory');
    if (statusHistory) {
        if (trackingHistory.length > 0) {
            statusHistory.innerHTML = trackingHistory.map(history => {
                const formattedHistoryDate = formatDateTime(history.tracked_at);
                
                return `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">${escapeHtml(history.status)}</p>
                            ${history.remarks ? `<p class="text-sm text-gray-600">${escapeHtml(history.remarks)}</p>` : ''}
                            <p class="text-xs text-gray-500 mt-1">By: ${escapeHtml(history.tracked_by)}</p>
                            ${history.office_name && history.office_name !== 'N/A' ? `<p class="text-xs text-gray-500">Office: ${escapeHtml(history.office_name)}</p>` : ''}
                        </div>
                        <p class="text-sm text-gray-500">${escapeHtml(formattedHistoryDate)}</p>
                    </div>
                `;
            }).join('');
        } else {
            statusHistory.innerHTML = `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">${escapeHtml(request.status)}</p>
                        <p class="text-sm text-gray-600">Request created and submitted</p>
                    </div>
                    <p class="text-sm text-gray-500">${escapeHtml(formattedRequestDate)}</p>
                </div>
            `;
        }
    }
    
    // Scroll to results
    resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Generate Tracking Timeline
 * @param {string} currentStatus - Current request status
 * @param {array} trackingHistory - Array of tracking history entries
 * @returns {string} HTML string for timeline
 */
function generateTrackingTimeline(currentStatus, trackingHistory) {
    // Define all possible statuses in order
    const statusFlow = [
        { status: 'Submitted', label: 'Request Submitted', description: 'Your request has been submitted successfully', icon: 'fa-check' },
        { status: 'Available', label: 'Item Available', description: 'Item is available in inventory', icon: 'fa-check-circle' },
        { status: 'Not Available', label: 'Item Not Available', description: 'Item not in inventory, PR created', icon: 'fa-exclamation-triangle' },
        { status: 'Pending PPMP', label: 'Pending PPMP', description: 'Awaiting PPMP validation', icon: 'fa-clock' },
        { status: 'For Approval', label: 'For Approval', description: 'Awaiting principal approval', icon: 'fa-user-check' },
        { status: 'Approved', label: 'Approved', description: 'Request has been approved', icon: 'fa-check-double' },
        { status: 'Pending Budget', label: 'Pending Budget', description: 'Awaiting budget allocation', icon: 'fa-dollar-sign' },
        { status: 'Under Procurement', label: 'Under Procurement', description: 'Procurement in progress', icon: 'fa-shopping-cart' },
        { status: 'DV Processing', label: 'DV Processing', description: 'Disbursement voucher being processed', icon: 'fa-file-invoice-dollar' },
        { status: 'Paid', label: 'Paid', description: 'Payment has been processed', icon: 'fa-money-check' },
        { status: 'Completed', label: 'Completed', description: 'Request has been completed', icon: 'fa-check-circle' }
    ];
    
    // Get unique statuses from history
    const statusesInHistory = [...new Set(trackingHistory.map(h => h.status))];
    if (!statusesInHistory.includes(currentStatus)) {
        statusesInHistory.push(currentStatus);
    }
    
    // Find current status index
    const currentIndex = statusFlow.findIndex(s => s.status === currentStatus);
    
    let timelineHTML = '';
    
    statusFlow.forEach((statusInfo, index) => {
        const isCompleted = statusesInHistory.includes(statusInfo.status) && index <= currentIndex;
        const isCurrent = statusInfo.status === currentStatus;
        
        // Find history entry for this status
        const historyEntry = trackingHistory.find(h => h.status === statusInfo.status);
        const historyDate = historyEntry ? new Date(historyEntry.tracked_at) : null;
        const formattedDate = historyDate ? formatDateTime(historyEntry.tracked_at) : '';
        
        let bgColor = 'bg-gray-300';
        let icon = 'fa-circle';
        let opacity = 'opacity-50';
        
        if (isCompleted) {
            bgColor = 'bg-green-500';
            icon = 'fa-check';
            opacity = '';
        } else if (isCurrent) {
            bgColor = 'bg-yellow-500';
            icon = 'fa-clock';
            opacity = '';
        }
        
        timelineHTML += `
            <div class="flex items-start space-x-4 ${opacity}">
                <div class="flex-shrink-0 w-8 h-8 ${bgColor} rounded-full flex items-center justify-center z-10">
                    <i class="fas ${icon} text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">${escapeHtml(statusInfo.label)}</p>
                    <p class="text-sm text-gray-600">${escapeHtml(statusInfo.description)}</p>
                    ${formattedDate ? `<p class="text-xs text-gray-500 mt-1">${escapeHtml(formattedDate)}</p>` : ''}
                </div>
            </div>
        `;
    });
    
    return timelineHTML;
}

/**
 * Show Tracking Error
 * @param {string} message - Error message to display
 */
function showTrackingError(message) {
    const resultsDiv = document.getElementById('trackingResults');
    const emptyDiv = document.getElementById('trackingEmpty');
    
    if (resultsDiv) resultsDiv.classList.add('hidden');
    
    if (emptyDiv) {
        emptyDiv.classList.remove('hidden');
        emptyDiv.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-6xl mb-4"></i>
                <p class="text-red-600 font-semibold mb-2">${escapeHtml(message)}</p>
                <p class="text-gray-600 text-sm">Please check the tracking ID and try again.</p>
            </div>
        `;
    }
}

// Make trackRequest globally accessible
window.trackRequest = trackRequest;

