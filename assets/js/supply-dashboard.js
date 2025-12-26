/**
 * Supply Office Dashboard JavaScript
 * Handles navigation, form submissions, and interactions for Supply Office module
 */

// Store chart instances to prevent multiple initializations
let chartInstances = {
    inventoryChart: null,
    requestChart: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeNavigation();
    initializeDateTime();
    initializeMobileSidebar();
    initializeCharts();
    initializeForms();
});

/**
 * Initialize Dashboard
 */
function initializeDashboard() {
    // Set default section
    showSection('dashboard');
    // Update page title on initial load
    updatePageTitle('dashboard');
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
        
        // Reinitialize charts if dashboard section is shown
        if (sectionId === 'dashboard') {
            setTimeout(() => {
                initializeCharts();
            }, 100);
        }
    }
}

/**
 * Update Page Title
 */
function updatePageTitle(sectionId) {
    const titles = {
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Supply office statistics and monitoring' },
        'supply-requests': { title: 'Supply Requests', subtitle: 'Review and process supply requests' },
        'inventory': { title: 'Inventory Management', subtitle: 'Manage inventory items and stock levels' },
        'ris-management': { title: 'RIS Management', subtitle: 'Manage Requisition and Issue Slips' },
        'pr-creation': { title: 'Create Purchase Request', subtitle: 'Create PR for unavailable items' },
        'reports': { title: 'Inventory Reports', subtitle: 'Generate inventory reports and analytics' }
    };
    
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    
    // Safely update title and subtitle
    const titleElement = document.getElementById('pageTitle');
    const subtitleElement = document.getElementById('pageSubtitle');
    
    if (titleElement) {
        titleElement.textContent = pageInfo.title;
    }
    
    if (subtitleElement) {
        subtitleElement.textContent = pageInfo.subtitle;
    }
}

/**
 * Initialize Charts
 */
function initializeCharts() {
    // Destroy existing charts if they exist
    if (chartInstances.inventoryChart) {
        chartInstances.inventoryChart.destroy();
        chartInstances.inventoryChart = null;
    }
    if (chartInstances.requestChart) {
        chartInstances.requestChart.destroy();
        chartInstances.requestChart = null;
    }

    // Inventory Status Chart
    const inventoryCtx = document.getElementById('inventoryChart');
    if (inventoryCtx && !chartInstances.inventoryChart) {
        chartInstances.inventoryChart = new Chart(inventoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    data: [1247, 8, 2],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Request Processing Chart
    const requestCtx = document.getElementById('requestChart');
    if (requestCtx && !chartInstances.requestChart) {
        chartInstances.requestChart = new Chart(requestCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'RIS Generated',
                    data: [12, 15, 10, 18],
                    backgroundColor: '#10b981'
                }, {
                    label: 'PR Created',
                    data: [5, 8, 6, 9],
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }
}

/**
 * Initialize Forms
 */
function initializeForms() {
    const prForm = document.getElementById('prForm');
    if (prForm) {
        prForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePRSubmission();
        });
    }
}

/**
 * Handle PR Submission
 */
function handlePRSubmission() {
    const form = document.getElementById('prForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    submitButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        alert('Purchase Request created successfully!\nPR Number: PR-2025-090');
        form.reset();
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 1500);
}

/**
 * Process Request
 */
function processRequest(trackingId) {
    // Show modal with request details
    const modal = document.getElementById('processModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Set request details in modal (would be fetched from API)
        document.getElementById('modalTrackingId').textContent = trackingId;
    }
}

/**
 * Close Process Modal
 */
function closeProcessModal() {
    const modal = document.getElementById('processModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Reset decision sections
    document.getElementById('decisionSection').classList.add('hidden');
    document.getElementById('availableSection').classList.add('hidden');
    document.getElementById('notAvailableSection').classList.add('hidden');
}

/**
 * Check Inventory
 */
function checkInventory() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
    button.disabled = true;
    
    // Simulate inventory check
    setTimeout(() => {
        // Randomly decide if available or not (in real app, this would be from API)
        const isAvailable = Math.random() > 0.3; // 70% chance available
        
        const decisionSection = document.getElementById('decisionSection');
        const availableSection = document.getElementById('availableSection');
        const notAvailableSection = document.getElementById('notAvailableSection');
        
        decisionSection.classList.remove('hidden');
        
        if (isAvailable) {
            availableSection.classList.remove('hidden');
            notAvailableSection.classList.add('hidden');
            document.getElementById('currentStock').textContent = '25 sets';
        } else {
            notAvailableSection.classList.remove('hidden');
            availableSection.classList.add('hidden');
            document.getElementById('currentStock').textContent = '0 sets';
        }
        
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
}

/**
 * Show RIS Form
 */
function showRISForm() {
    alert('RIS Form would open here.\n\nIn the actual implementation, this would:\n1. Generate RIS with details\n2. Allow editing of stock numbers, prices\n3. Generate PDF/printable format\n4. Forward to Accounting Office');
    
    // Close modal
    closeProcessModal();
    
    // Show success message
    showSuccessMessage('RIS generated successfully! RIS-2025-043');
    
    // Refresh requests list
    setTimeout(() => {
        showSection('supply-requests');
        document.querySelector('[data-section="supply-requests"]').click();
    }, 2000);
}

/**
 * Forward to PPMP Management (STEP 3: Create/Update PPMP)
 */
function forwardToPPMP() {
    // Close modal
    closeProcessModal();
    
    // Get the current request details
    const trackingId = document.getElementById('modalTrackingId')?.textContent || 'N/A';
    const itemDescription = document.getElementById('modalItem')?.textContent || 'N/A';
    const quantity = document.getElementById('modalQuantity')?.textContent || 'N/A';
    
    // Show confirmation message
    if (confirm(`Forward request ${trackingId} to PPMP Management?\n\nItem: ${itemDescription}\nQuantity: ${quantity}\n\nThis will mark the request as "Item Not Available - Procurement Needed" and forward it to PPMP Management module for PPMP creation (STEP 3).`)) {
        // Simulate forwarding
        const forwardButton = event.target;
        const originalText = forwardButton.innerHTML;
        forwardButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Forwarding...';
        forwardButton.disabled = true;
        
        setTimeout(() => {
            alert(`Request ${trackingId} forwarded to PPMP Management successfully!\n\nStatus: Item Not Available - Procurement Needed\n\nNext Steps:\n1. Create/Update PPMP (STEP 3)\n2. Submit PPMP to Budget Office (STEP 4)\n3. BAC consolidates into APP (STEP 5)\n4. Create PR after APP approval (STEP 6)`);
            
            // Update status in UI (in real implementation, this would update the database)
            forwardButton.innerHTML = originalText;
            forwardButton.disabled = false;
            closeProcessModal();
            
            // Refresh the supply requests list
            setTimeout(() => {
                showSection('supply-requests');
                updatePageTitle('supply-requests');
            }, 1500);
        }, 1500);
    }
}

/**
 * View Request Details
 */
function viewRequestDetails(trackingId) {
    alert(`Viewing details for ${trackingId}\n\nThis would show complete request details including:\n- Requester information\n- Item specifications\n- Justification\n- Status history`);
}

/**
 * View RIS
 */
function viewRIS(risNumber) {
    alert(`Viewing RIS: ${risNumber}\n\nThis would display the complete RIS details and allow printing.`);
}

/**
 * Print RIS
 */
function printRIS(risNumber) {
    alert(`Printing RIS: ${risNumber}\n\nThis would generate a printable PDF of the RIS.`);
}

/**
 * Check PPMP
 */
function checkPPMP() {
    const button = event.target;
    const originalText = button.innerHTML;
    const resultDiv = document.getElementById('ppmpResult');
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
    button.disabled = true;
    
    // Simulate PPMP check
    setTimeout(() => {
        const isInPPMP = Math.random() > 0.5;
        
        if (isInPPMP) {
            resultDiv.innerHTML = `
                <div class="alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Item is included in PPMP</strong>
                    <p class="text-sm mt-1">PPMP Reference: PPMP-2025-045</p>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Item is not included in PPMP</strong>
                    <p class="text-sm mt-1">This request may need PPMP amendment or will be queued for next budget year.</p>
                </div>
            `;
        }
        
        resultDiv.classList.remove('hidden');
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
}

/**
 * Edit Inventory Item
 */
function editInventoryItem(itemCode) {
    alert(`Editing inventory item: ${itemCode}\n\nThis would open an edit form for the inventory item.`);
}

/**
 * View Inventory History
 */
function viewInventoryHistory(itemCode) {
    alert(`Viewing inventory history for: ${itemCode}\n\nThis would show stock movement history.`);
}

/**
 * Show Add Item Modal
 */
function showAddItemModal() {
    alert('Add New Item Modal\n\nThis would open a form to add a new item to inventory.');
}

/**
 * Reset PR Form
 */
function resetPRForm() {
    const form = document.getElementById('prForm');
    if (form) {
        form.reset();
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
    
    // Toggle sidebar function
    function toggleSidebar() {
        if (sidebar) {
            const isOpen = sidebar.classList.contains('open');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    }
    
    // Open sidebar function
    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add('open');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('hidden');
        }
    }
    
    // Close sidebar function
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('open');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('hidden');
        }
    }
    
    // Make closeSidebar globally available for onclick handler
    window.closeSidebar = closeSidebar;
    
    // Toggle button click handler
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Overlay click handler - close sidebar when clicking outside
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }
    
    // Prevent sidebar clicks from closing the sidebar
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Close sidebar when clicking nav items on mobile
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    });
    
    // Close sidebar on window resize if it becomes desktop view
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
}

/**
 * Show Success Message
 */
function showSuccessMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'success-message';
    messageDiv.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(messageDiv, main.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

