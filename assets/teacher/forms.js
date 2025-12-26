/**
 * Forms Module
 * Handles form submissions, validation, and form state management
 */

import { showSuccessMessage, showErrorMessage } from './utils.js';
import { showSection } from './sections.js';
import { loadDashboardData } from './dashboard.js';

/**
 * Initialize Form Handlers
 */
export function initializeForm() {
    const form = document.getElementById('supplyRequestForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            try {
                handleFormSubmission();
            } catch (error) {
                console.error('Form submission error:', error);
                showErrorMessage('An error occurred while submitting the form. Please try again.');
            }
        });
        
        // Initialize request type toggle
        try {
            toggleRequestType();
        } catch (error) {
            console.error('Error initializing request type:', error);
        }
    }
}

/**
 * Handle Form Submission
 */
function handleFormSubmission() {
    const form = document.getElementById('supplyRequestForm');
    if (!form) {
        console.error('Form not found');
        showErrorMessage('Form not found. Please refresh the page.');
        return;
    }
    
    // Validate form
    try {
        if (!validateForm()) {
            return;
        }
    } catch (error) {
        console.error('Validation error:', error);
        showErrorMessage('Validation error: ' + error.message);
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) {
        console.error('Submit button not found');
        showErrorMessage('Submit button not found. Please refresh the page.');
        return;
    }
    
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    submitButton.disabled = true;
    
    // Create FormData
    const formData = new FormData(form);
    
    // Submit to API
    fetch('../../api/v1/teacher/submit_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reset form
            form.reset();
            try {
                toggleRequestType(); // Reset form visibility
            } catch (error) {
                console.error('Error resetting form:', error);
            }
            
            // Show success message
            showSuccessMessage(`Request submitted successfully! Tracking ID: ${data.tracking_id}`);
            
            // Reload dashboard data
            loadDashboardData();
            
            // Redirect to my requests after 2 seconds
            setTimeout(() => {
                try {
                    showSection('my-requests');
                    const myRequestsLink = document.querySelector('[data-section="my-requests"]');
                    if (myRequestsLink) {
                        myRequestsLink.click();
                    }
                } catch (error) {
                    console.error('Error navigating to my requests:', error);
                }
            }, 2000);
        } else {
            // Show error message
            let errorMsg = data.message || 'An error occurred while submitting your request.';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '\n\n' + data.errors.join('\n');
            }
            showErrorMessage(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Network error. Please check your connection and try again. Error: ' + error.message);
    })
    .finally(() => {
        // Reset button
        if (submitButton) {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });
}

/**
 * Validate Form
 */
function validateForm() {
    const requestTypeElement = document.querySelector('input[name="requestType"]:checked');
    if (!requestTypeElement) {
        showErrorMessage('Please select a request type.');
        return false;
    }
    
    const requestType = requestTypeElement.value;
    let isValid = true;
    const errors = [];
    
    // Remove previous error classes
    document.querySelectorAll('.form-error').forEach(field => {
        field.classList.remove('form-error');
    });
    
    // Validate item description / event name
    const itemDescription = document.getElementById('itemDescription');
    if (!itemDescription.value.trim()) {
        itemDescription.classList.add('form-error');
        errors.push('Item description/Event name is required.');
        isValid = false;
    } else if (itemDescription.value.trim().length < 5) {
        itemDescription.classList.add('form-error');
        errors.push('Item description/Event name must be at least 5 characters.');
        isValid = false;
    }
    
    // Validate based on request type
    if (requestType === 'food') {
        // Validate event name
        const eventName = document.getElementById('eventName');
        if (!eventName.value.trim()) {
            eventName.classList.add('form-error');
            errors.push('Event name is required.');
            isValid = false;
        }
        
        // Validate number of participants
        const numberOfParticipants = document.getElementById('numberOfParticipants');
        const participantsValue = parseInt(numberOfParticipants.value);
        if (!numberOfParticipants.value || participantsValue <= 0) {
            numberOfParticipants.classList.add('form-error');
            errors.push('Number of participants must be greater than 0.');
            isValid = false;
        } else if (participantsValue > 10000) {
            numberOfParticipants.classList.add('form-error');
            errors.push('Number of participants must not exceed 10,000.');
            isValid = false;
        }
        
        // Validate estimated budget
        const estimatedBudget = document.getElementById('estimatedBudget');
        const budgetValue = parseFloat(estimatedBudget.value);
        if (!estimatedBudget.value || budgetValue <= 0) {
            estimatedBudget.classList.add('form-error');
            errors.push('Estimated budget must be greater than 0.');
            isValid = false;
        } else if (budgetValue > 1000000) {
            estimatedBudget.classList.add('form-error');
            errors.push('Estimated budget must not exceed ₱1,000,000.');
            isValid = false;
        }
    } else if (['equipment', 'supplies', 'services', 'other'].includes(requestType)) {
        // Validate quantity
        const quantity = document.getElementById('quantity');
        const qtyValue = parseInt(quantity.value);
        if (!quantity.value || qtyValue <= 0) {
            quantity.classList.add('form-error');
            errors.push('Quantity must be greater than 0.');
            isValid = false;
        } else if (qtyValue > 10000) {
            quantity.classList.add('form-error');
            errors.push('Quantity must not exceed 10,000.');
            isValid = false;
        }
        
        // Validate unit of measure
        const unitOfMeasure = document.getElementById('unitOfMeasure');
        if (!unitOfMeasure.value) {
            unitOfMeasure.classList.add('form-error');
            errors.push('Unit of measure is required.');
            isValid = false;
        }
    }
    
    // Validate justification
    const justification = document.getElementById('justification');
    if (!justification.value.trim()) {
        justification.classList.add('form-error');
        errors.push('Justification/purpose is required.');
        isValid = false;
    } else if (justification.value.trim().length < 10) {
        justification.classList.add('form-error');
        errors.push('Justification must be at least 10 characters.');
        isValid = false;
    }
    
    // Validate expected date if provided
    const expectedDate = document.getElementById('expectedDate');
    if (expectedDate.value) {
        const dateValue = new Date(expectedDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (dateValue < today) {
            expectedDate.classList.add('form-error');
            errors.push('Expected delivery date cannot be in the past.');
            isValid = false;
        }
    }
    
    if (!isValid) {
        showErrorMessage('Please correct the following errors:\n\n' + errors.join('\n'));
    }
    
    return isValid;
}

/**
 * Toggle Request Type
 * Show/hide fields based on selected request type
 */
export function toggleRequestType() {
    const requestType = document.querySelector('input[name="requestType"]:checked');
    if (!requestType) return;
    
    const requestTypeValue = requestType.value;
    
    // Update selected state for radio buttons
    document.querySelectorAll('.request-type-option').forEach(option => {
        option.classList.remove('selected');
    });
    if (requestType.closest('.request-type-option')) {
        requestType.closest('.request-type-option').classList.add('selected');
    }
    
    // Get containers
    const quantityContainer = document.getElementById('quantityContainer');
    const unitOfMeasureContainer = document.getElementById('unitOfMeasureContainer');
    const eventNameContainer = document.getElementById('eventNameContainer');
    const eventDateContainer = document.getElementById('eventDateContainer');
    const numberOfParticipantsContainer = document.getElementById('numberOfParticipantsContainer');
    const estimatedBudgetContainer = document.getElementById('estimatedBudgetContainer');
    const itemDescriptionLabel = document.getElementById('itemDescriptionLabel');
    const itemDescription = document.getElementById('itemDescription');
    const itemDescriptionHelp = document.getElementById('itemDescriptionHelp');
    
    // Define configuration for each request type
    const typeConfig = {
        'equipment': {
            showQuantity: true,
            showEventFields: false,
            label: 'Item Description',
            placeholder: 'Describe the equipment needed',
            help: 'Provide a clear and detailed description'
        },
        'food': {
            showQuantity: false,
            showEventFields: true,
            label: 'Event Name',
            placeholder: 'Enter the name of the event',
            help: 'Name of the event or occasion'
        },
        'supplies': {
            showQuantity: true,
            showEventFields: false,
            label: 'Item Description',
            placeholder: 'Describe the supplies needed',
            help: 'Provide a clear and detailed description'
        },
        'services': {
            showQuantity: true,
            showEventFields: false,
            label: 'Service Description',
            placeholder: 'Describe the service needed',
            help: 'Provide a clear and detailed description'
        },
        'other': {
            showQuantity: true,
            showEventFields: false,
            label: 'Item Description',
            placeholder: 'Describe what you need',
            help: 'Provide a clear and detailed description'
        }
    };
    
    const config = typeConfig[requestTypeValue] || typeConfig['other'];
    
    // Show/hide quantity fields
    if (config.showQuantity) {
        quantityContainer?.classList.remove('hidden');
        unitOfMeasureContainer?.classList.remove('hidden');
        document.getElementById('quantity').required = true;
        document.getElementById('unitOfMeasure').required = true;
    } else {
        quantityContainer?.classList.add('hidden');
        unitOfMeasureContainer?.classList.add('hidden');
        document.getElementById('quantity').required = false;
        document.getElementById('unitOfMeasure').required = false;
    }
    
    // Show/hide event fields (for food requests)
    if (config.showEventFields) {
        eventNameContainer?.classList.remove('hidden');
        eventDateContainer?.classList.remove('hidden');
        numberOfParticipantsContainer?.classList.remove('hidden');
        estimatedBudgetContainer?.classList.remove('hidden');
        document.getElementById('eventName').required = true;
        document.getElementById('numberOfParticipants').required = true;
        document.getElementById('estimatedBudget').required = true;
    } else {
        eventNameContainer?.classList.add('hidden');
        eventDateContainer?.classList.add('hidden');
        numberOfParticipantsContainer?.classList.add('hidden');
        estimatedBudgetContainer?.classList.add('hidden');
        document.getElementById('eventName').required = false;
        document.getElementById('numberOfParticipants').required = false;
        document.getElementById('estimatedBudget').required = false;
    }
    
    // Update label and placeholder
    if (itemDescriptionLabel) {
        itemDescriptionLabel.textContent = config.label;
    }
    if (itemDescription) {
        itemDescription.placeholder = config.placeholder;
    }
    if (itemDescriptionHelp) {
        itemDescriptionHelp.textContent = config.help;
    }
}

// Make toggleRequestType globally accessible for inline handlers
window.toggleRequestType = toggleRequestType;

