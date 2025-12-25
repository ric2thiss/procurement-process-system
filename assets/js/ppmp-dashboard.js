/**
 * PPMP Management Dashboard JavaScript
 * Handles navigation, form submissions, and interactions for PPMP Management module
 */

// Store chart instances to prevent multiple initializations
let chartInstances = {
    budgetChart: null,
    categoryChart: null
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'PPMP management and monitoring' },
        'ppmp-list': { title: 'PPMP List', subtitle: 'View and manage all PPMP documents' },
        'create-ppmp': { title: 'Create New PPMP', subtitle: 'Create a new annual PPMP' },
        'items-management': { title: 'PPMP Items Management', subtitle: 'Manage items in PPMP' },
        'amendments': { title: 'PPMP Amendments', subtitle: 'Manage amendments and revisions' },
        'app-consolidation': { title: 'APP Consolidation', subtitle: 'BAC Secretariat - Consolidate PPMPs into APP (STEP 5)' },
        'ppmp-approvals': { title: 'PPMP Pending Approvals', subtitle: 'Track PPMPs forwarded to Principal for approval' },
        'reports': { title: 'Reports & Analytics', subtitle: 'PPMP reports and analytics' }
    };
    
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

/**
 * Initialize Charts
 */
function initializeCharts() {
    // Destroy existing charts if they exist
    if (chartInstances.budgetChart) {
        chartInstances.budgetChart.destroy();
        chartInstances.budgetChart = null;
    }
    if (chartInstances.categoryChart) {
        chartInstances.categoryChart.destroy();
        chartInstances.categoryChart = null;
    }

    // Budget Utilization Chart
    const budgetCtx = document.getElementById('budgetChart');
    if (budgetCtx && !chartInstances.budgetChart) {
        chartInstances.budgetChart = new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Allocated', 'Utilized', 'Remaining'],
                datasets: [{
                    data: [2500000, 1250000, 1250000],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#e5e7eb'
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

    // Items by Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx && !chartInstances.categoryChart) {
        chartInstances.categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: ['Office Supplies', 'Teaching Materials', 'Equipment', 'Consumables'],
                datasets: [{
                    label: 'Number of Items',
                    data: [45, 52, 20, 10],
                    backgroundColor: '#10b981'
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
                        display: false
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
    const ppmpForm = document.getElementById('ppmpForm');
    if (ppmpForm) {
        ppmpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePPMPCreation();
        });
    }
}

/**
 * Handle PPMP Creation
 */
function handlePPMPCreation() {
    const form = document.getElementById('ppmpForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    const year = document.getElementById('ppmpYear').value;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    submitButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        alert(`PPMP ${year} created successfully!\n\nYou can now start adding items to the PPMP.`);
        form.reset();
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Navigate to items management
        setTimeout(() => {
            showSection('items-management');
            document.querySelector('[data-section="items-management"]').click();
        }, 1500);
    }, 1500);
}

/**
 * Reset PPMP Form
 */
function resetPPMPForm() {
    const form = document.getElementById('ppmpForm');
    if (form) {
        form.reset();
    }
}

/**
 * View PPMP
 */
function viewPPMP(year) {
    alert(`Viewing PPMP ${year}\n\nThis would display the complete PPMP document with all items and schedules.`);
}

/**
 * Edit PPMP
 */
function editPPMP(year) {
    alert(`Editing PPMP ${year}\n\nThis would allow you to edit PPMP details and items.`);
    // In real implementation, would navigate to edit mode
}

/**
 * Print PPMP
 */
function printPPMP(year) {
    alert(`Printing PPMP ${year}\n\nThis would generate a printable PDF of the PPMP document.`);
}

/**
 * Show Add Item Modal
 */
function showAddItemModal() {
    alert('Add Item to PPMP\n\nThis would open a form to add a new item to the PPMP with:\n- Item description\n- Quantity and unit\n- Unit price\n- Budget allocation\n- Procurement mode\n- Monthly schedule');
}

/**
 * Edit PPMP Item
 */
function editPPMPItem(itemCode) {
    alert(`Editing PPMP Item: ${itemCode}\n\nThis would open an edit form for the PPMP item.`);
}

/**
 * Delete PPMP Item
 */
function deletePPMPItem(itemCode) {
    if (confirm(`Are you sure you want to delete item ${itemCode} from PPMP?`)) {
        alert(`Item ${itemCode} deleted successfully.`);
        // In real implementation, would remove from list
    }
}

/**
 * Create Amendment
 */
function createAmendment() {
    alert('Create PPMP Amendment\n\nThis would open a form to create a new amendment to the PPMP.');
}

/**
 * View Amendment
 */
function viewAmendment(amendId) {
    alert(`Viewing Amendment: ${amendId}\n\nThis would display the amendment details and changes.`);
}

/**
 * Approve Amendment
 */
function approveAmendment(amendId) {
    if (confirm(`Approve Amendment ${amendId}?`)) {
        alert(`Amendment ${amendId} approved successfully.`);
        // In real implementation, would update status
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

/**
 * Create APP
 */
function createAPP() {
    alert('Create APP\n\nThis would open a form to create a new Annual Procurement Plan (APP) by consolidating approved PPMPs.');
}

/**
 * View APP
 */
function viewAPP(appYear) {
    alert(`Viewing APP ${appYear}\n\nThis would display the complete APP document with all consolidated PPMPs and items.`);
}

/**
 * Submit APP for Approval (to HoPE)
 */
function submitAPPForApproval(appYear) {
    if (confirm(`Submit APP ${appYear} to HoPE for approval?`)) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
        button.disabled = true;

        // Simulate API call
        setTimeout(() => {
            showSuccessMessage(`APP ${appYear} submitted to HoPE for approval. Status: Pending HoPE Approval.`);
            button.innerHTML = originalText;
            button.disabled = false;

            // Refresh APP consolidation section
            setTimeout(() => {
                showSection('app-consolidation');
                updatePageTitle('app-consolidation');
            }, 1500);
        }, 1500);
    }
}

/**
 * View PPMP Details
 */
function viewPPMPDetails(ppmpNumber) {
    alert(`Viewing PPMP Details: ${ppmpNumber}\n\nThis would display complete PPMP details including items, budget, and status.`);
}

/**
 * View PPMP Status (Pending Approval)
 */
function viewPPMPStatus(ppmpNumber) {
    alert(`Viewing PPMP Status: ${ppmpNumber}\n\nThis would display the PPMP status and approval tracking information:\n- Current status (Pending Principal Approval)\n- Date forwarded\n- Approval progress\n- Expected completion date`);
}

/**
 * Add PPMP to APP
 */
function addToAPP(ppmpNumber) {
    if (confirm(`Add ${ppmpNumber} to APP 2025?`)) {
        showSuccessMessage(`${ppmpNumber} added to APP 2025 successfully.`);
        
        // Refresh APP consolidation section
        setTimeout(() => {
            showSection('app-consolidation');
            updatePageTitle('app-consolidation');
        }, 1500);
    }
}

