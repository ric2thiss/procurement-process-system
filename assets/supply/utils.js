/**
 * Utility Functions
 * Shared helper functions used across the supply office dashboard
 */

/**
 * Escape HTML to prevent XSS attacks
 * @param {string} text - Text to escape
 * @returns {string} Escaped HTML string
 */
export function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Get status badge HTML for a given status
 * @param {string} status - Request status
 * @returns {string} HTML string for status badge
 */
export function getStatusBadge(status) {
    const statusConfig = {
        'Submitted': { class: 'bg-yellow-100 text-yellow-800', label: 'Submitted' },
        'Available': { class: 'bg-blue-100 text-blue-800', label: 'Available' },
        'Not Available': { class: 'bg-red-100 text-red-800', label: 'Not Available' },
        'Pending PPMP': { class: 'bg-orange-100 text-orange-800', label: 'Pending PPMP' },
        'For Approval': { class: 'bg-yellow-100 text-yellow-800', label: 'For Approval' },
        'Approved': { class: 'bg-green-100 text-green-800', label: 'Approved' },
        'Rejected': { class: 'bg-red-100 text-red-800', label: 'Rejected' },
        'Pending Budget': { class: 'bg-orange-100 text-orange-800', label: 'Pending Budget' },
        'Under Procurement': { class: 'bg-blue-100 text-blue-800', label: 'Under Procurement' },
        'DV Processing': { class: 'bg-indigo-100 text-indigo-800', label: 'DV Processing' },
        'Paid': { class: 'bg-purple-100 text-purple-800', label: 'Paid' },
        'Completed': { class: 'bg-purple-100 text-purple-800', label: 'Completed' },
        'Cancelled': { class: 'bg-red-100 text-red-800', label: 'Cancelled' },
        'Issued': { class: 'bg-blue-100 text-blue-800', label: 'Issued' },
        'Generated': { class: 'bg-yellow-100 text-yellow-800', label: 'Generated' },
        'Received': { class: 'bg-green-100 text-green-800', label: 'Received' },
        'Draft': { class: 'bg-gray-100 text-gray-800', label: 'Draft' },
        'Forwarded to Accounting': { class: 'bg-indigo-100 text-indigo-800', label: 'Forwarded to Accounting' }
    };
    
    const config = statusConfig[status] || { class: 'bg-gray-100 text-gray-800', label: status };
    return `<span class="px-2 py-1 ${config.class} text-xs rounded-full font-medium">${escapeHtml(config.label)}</span>`;
}

/**
 * Format date to readable string
 * @param {string|Date} date - Date to format
 * @param {object} options - Formatting options
 * @returns {string} Formatted date string
 */
export function formatDate(date, options = {}) {
    if (!date) return 'N/A';
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) return 'Invalid Date';
    
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        ...options
    };
    
    return dateObj.toLocaleDateString('en-US', defaultOptions);
}

/**
 * Format datetime to readable string
 * @param {string|Date} date - Date to format
 * @returns {string} Formatted datetime string
 */
export function formatDateTime(date) {
    if (!date) return 'N/A';
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) return 'Invalid Date';
    
    return dateObj.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Show a success message
 * @param {string} message - Message to display
 * @param {string} containerId - Container ID to show message in
 */
export function showSuccessMessage(message, containerId = 'supply-requests-section') {
    const section = document.getElementById(containerId);
    if (!section) return;
    
    // Remove existing messages
    const existingMsg = section.querySelector('.success-message, .error-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'success-message bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded';
    messageDiv.innerHTML = `<div class="flex items-start"><i class="fas fa-check-circle mr-2 mt-1"></i><span class="whitespace-pre-line">${escapeHtml(message)}</span></div>`;
    
    const container = section.querySelector('.bg-white.rounded-lg.shadow-md');
    if (container) {
        try {
            const firstElement = container.firstElementChild;
            if (firstElement) {
                firstElement.insertAdjacentElement('beforebegin', messageDiv);
            } else {
                container.appendChild(messageDiv);
            }
        } catch (e) {
            console.error("Error inserting message:", e);
            container.appendChild(messageDiv);
        }
    } else {
        section.prepend(messageDiv);
    }
    
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

/**
 * Show an error message
 * @param {string} message - Message to display
 * @param {string} containerId - Container ID to show message in
 */
export function showErrorMessage(message, containerId = 'supply-requests-section') {
    const section = document.getElementById(containerId);
    if (!section) return;
    
    // Remove existing messages
    const existingMsg = section.querySelector('.success-message, .error-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'error-message bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded';
    messageDiv.innerHTML = `<div class="flex items-start"><i class="fas fa-exclamation-circle mr-2 mt-1"></i><span class="whitespace-pre-line">${escapeHtml(message)}</span></div>`;
    
    const container = section.querySelector('.bg-white.rounded-lg.shadow-md');
    if (container) {
        try {
            const firstElement = container.firstElementChild;
            if (firstElement) {
                firstElement.insertAdjacentElement('beforebegin', messageDiv);
            } else {
                container.appendChild(messageDiv);
            }
        } catch (e) {
            console.error("Error inserting message:", e);
            container.appendChild(messageDiv);
        }
    } else {
        section.prepend(messageDiv);
    }
    
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 8000);
}

/**
 * Show Toast Notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast: 'success', 'error', 'info', 'warning'
 */
export function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'fixed top-4 right-4 z-[9999] space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    // Determine colors based on type
    const typeConfig = {
        'success': { bg: 'bg-green-500', border: 'border-green-600', icon: 'fa-check-circle' },
        'error': { bg: 'bg-red-500', border: 'border-red-600', icon: 'fa-exclamation-circle' },
        'warning': { bg: 'bg-yellow-500', border: 'border-yellow-600', icon: 'fa-exclamation-triangle' },
        'info': { bg: 'bg-blue-500', border: 'border-blue-600', icon: 'fa-info-circle' }
    };
    
    const config = typeConfig[type] || typeConfig['info'];
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification ${config.bg} ${config.border} border-l-4 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] max-w-[500px] animate-slide-in`;
    toast.innerHTML = `
        <i class="fas ${config.icon} text-xl"></i>
        <div class="flex-1">
            <p class="font-semibold">${escapeHtml(message)}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 ml-2">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add animation styles if not already added
    if (!document.getElementById('toastStyles')) {
        const style = document.createElement('style');
        style.id = 'toastStyles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            .animate-slide-in {
                animation: slideIn 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    }
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.animation = 'slideIn 0.3s ease-out reverse';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

