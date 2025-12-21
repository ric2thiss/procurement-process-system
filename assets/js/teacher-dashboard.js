/**
 * Teacher Dashboard JavaScript
 * Handles navigation, form submissions, and interactions for Teacher module
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeNavigation();
    initializeForm();
    initializeDateTime();
    initializeMobileSidebar();
});

/**
 * Initialize Dashboard
 */
function initializeDashboard() {
    // Set default section
    showSection('dashboard');
}

/**
 * Initialize Navigation
 */
function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Update active state
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Show section
            showSection(section);
            
            // Update page title
            updatePageTitle(section);
        });
    });
}

/**
 * Show Section
 */
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.add('hidden'));
    
    // Show target section
    const targetSection = document.getElementById(sectionId + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
        targetSection.classList.add('fade-in');
    }
}

/**
 * Update Page Title
 */
function updatePageTitle(sectionId) {
    const titles = {
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Your supply request statistics' },
        'new-request': { title: 'New Supply Request', subtitle: 'Create and submit a new request' },
        'my-requests': { title: 'My Requests', subtitle: 'View and manage all your requests' },
        'tracking': { title: 'Track Document', subtitle: 'Monitor request progress' },
        'issued-items': { title: 'Issued Items', subtitle: 'Items issued from inventory' },
        'history': { title: 'Request History', subtitle: 'View past transactions' }
    };
    
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

/**
 * Initialize Form
 */
function initializeForm() {
    const form = document.getElementById('supplyRequestForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission();
        });
    }
}

/**
 * Handle Form Submission
 */
function handleFormSubmission() {
    const form = document.getElementById('supplyRequestForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    submitButton.disabled = true;
    
    // Simulate form submission (replace with actual API call)
    setTimeout(() => {
        // Reset form
        form.reset();
        
        // Show success message
        showSuccessMessage('Supply request submitted successfully! Tracking ID: 2025-SR-025');
        
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Redirect to my requests after 2 seconds
        setTimeout(() => {
            showSection('my-requests');
            document.querySelector('[data-section="my-requests"]').click();
        }, 2000);
    }, 1500);
}

/**
 * Validate Form
 */
function validateForm() {
    const requiredFields = ['itemDescription', 'quantity', 'unitOfMeasure', 'justification'];
    let isValid = true;
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field.value.trim()) {
            field.classList.add('form-error');
            isValid = false;
        } else {
            field.classList.remove('form-error');
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields');
    }
    
    return isValid;
}

/**
 * Reset Form
 */
function resetForm() {
    const form = document.getElementById('supplyRequestForm');
    if (form) {
        form.reset();
        // Remove error classes
        form.querySelectorAll('.form-error').forEach(field => {
            field.classList.remove('form-error');
        });
    }
}

/**
 * Show Success Message
 */
function showSuccessMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'success-message';
    messageDiv.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    
    const section = document.getElementById('new-request-section');
    if (section) {
        section.insertBefore(messageDiv, section.firstChild);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

/**
 * View Request Details
 */
function viewRequestDetails(trackingId) {
    // Show request details modal or navigate to details page
    alert(`Viewing details for ${trackingId}\n\nThis would open a detailed view of the request.`);
}

/**
 * Track Request
 */
function trackRequest(trackingId) {
    // Navigate to tracking section and search
    showSection('tracking');
    document.querySelector('[data-section="tracking"]').click();
    
    // Set tracking ID and trigger search
    const trackingInput = document.getElementById('trackingIdInput');
    if (trackingInput) {
        trackingInput.value = trackingId;
        // Simulate tracking
        setTimeout(() => {
            displayTrackingResults(trackingId);
        }, 500);
    }
}

/**
 * Track Document (from tracking form)
 */
function trackDocument(event) {
    event.preventDefault();
    const trackingId = document.getElementById('trackingIdInput').value.trim();
    
    if (!trackingId) {
        alert('Please enter a tracking ID');
        return;
    }
    
    // Show loading state
    const button = event.target.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        displayTrackingResults(trackingId);
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
}

/**
 * Display Tracking Results
 */
function displayTrackingResults(trackingId) {
    const resultsDiv = document.getElementById('trackingResults');
    const emptyDiv = document.getElementById('trackingEmpty');
    
    if (resultsDiv && emptyDiv) {
        emptyDiv.classList.add('hidden');
        resultsDiv.classList.remove('hidden');
        resultsDiv.classList.add('fade-in');
    }
}

/**
 * Initialize Date and Time
 */
function initializeDateTime() {
    function updateDateTime() {
        const now = new Date();
        const date = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const time = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const dateElement = document.getElementById('currentDate');
        const timeElement = document.getElementById('currentTime');
        
        if (dateElement) dateElement.textContent = date;
        if (timeElement) timeElement.textContent = time;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

/**
 * Initialize Mobile Sidebar
 */
function initializeMobileSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('hidden');
            }
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.add('hidden');
        });
    }
    
    // Close sidebar when clicking nav items on mobile
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('open');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('hidden');
                }
            }
        });
    });
}

/**
 * View Request (from dashboard)
 */
function viewRequest(trackingId) {
    showSection('my-requests');
    document.querySelector('[data-section="my-requests"]').click();
    // Scroll to or highlight the specific request
    setTimeout(() => {
        viewRequestDetails(trackingId);
    }, 300);
}

