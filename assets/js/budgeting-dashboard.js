/**
 * Budgeting / Accounting Dashboard JavaScript
 * Handles budget verification, ORS management, and budget allocation
 */

// Store chart instances to prevent multiple initializations
let chartInstances = {
    budgetUtilizationChart: null,
    monthlyBudgetChart: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeNavigation();
    initializeDateTime();
    initializeMobileSidebar();
    initializeCharts();
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Budget and accounting statistics' },
        'ppmp-review': { title: 'PPMP Review', subtitle: 'Review PPMP submissions and verify budget availability (STEP 4)' },
        'budget-verification': { title: 'Budget Verification', subtitle: 'Verify budget availability for PRs and RIS' },
        'ors-management': { title: 'ORS Management', subtitle: 'Create and manage Obligation Request Status' },
        'ors-checklist': { title: 'ORS Checklist', subtitle: 'Complete ORS checklist items' },
        'budget-allocation': { title: 'Budget Allocation', subtitle: 'Manage budget allocations by category' },
        'pending-budget': { title: 'Pending Budget', subtitle: 'Requests waiting for budget allocation' },
        'obligation-tracking': { title: 'Obligation Tracking', subtitle: 'Track obligations and commitments' },
        'reports': { title: 'Budget Reports', subtitle: 'Generate budget reports and analytics' }
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
    if (chartInstances.budgetUtilizationChart) {
        chartInstances.budgetUtilizationChart.destroy();
        chartInstances.budgetUtilizationChart = null;
    }
    if (chartInstances.monthlyBudgetChart) {
        chartInstances.monthlyBudgetChart.destroy();
        chartInstances.monthlyBudgetChart = null;
    }

    // Budget Utilization Chart
    const budgetUtilizationCtx = document.getElementById('budgetUtilizationChart');
    if (budgetUtilizationCtx && !chartInstances.budgetUtilizationChart) {
        chartInstances.budgetUtilizationChart = new Chart(budgetUtilizationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Allocated', 'Obligated', 'Available'],
                datasets: [{
                    data: [2500000, 1300000, 1200000],
                    backgroundColor: [
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981'
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '₱' + context.parsed.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Monthly Budget Trend Chart
    const monthlyBudgetCtx = document.getElementById('monthlyBudgetChart');
    if (monthlyBudgetCtx && !chartInstances.monthlyBudgetChart) {
        chartInstances.monthlyBudgetChart = new Chart(monthlyBudgetCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Budget Allocated',
                    data: [200000, 250000, 300000, 350000, 400000, 450000],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Budget Obligated',
                    data: [150000, 180000, 220000, 280000, 320000, 380000],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-US');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        });
    }
}

/**
 * Verify Budget
 */
function verifyBudget(docNumber, docType) {
    // Show modal with document details
    const modal = document.getElementById('budgetVerificationModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Load document details
        loadDocumentDetails(docNumber, docType);
    }
}

/**
 * Load Document Details
 */
function loadDocumentDetails(docNumber, docType) {
    // Sample data - in real app, fetch from API
    const docData = {
        'PR-2025-090': {
            docNumber: 'PR-2025-090',
            docType: 'Purchase Request',
            requester: 'Jane Smith',
            amount: '₱15,000.00',
            totalBudget: '₱2,500,000.00',
            availableBudget: '₱1,200,000.00',
            requestedAmount: '₱15,000.00',
            ppmpRef: 'PPMP-2025-045',
            ppmpBudget: '₱50,000.00',
            hasPPMP: true
        },
        'RIS-2025-042': {
            docNumber: 'RIS-2025-042',
            docType: 'Requisition Issue Slip',
            requester: 'John Doe',
            amount: '₱3,500.00',
            totalBudget: '₱2,500,000.00',
            availableBudget: '₱1,200,000.00',
            requestedAmount: '₱3,500.00',
            hasPPMP: false
        },
        'PR-2025-088': {
            docNumber: 'PR-2025-088',
            docType: 'Purchase Request',
            requester: 'Robert Johnson',
            amount: '₱45,000.00',
            totalBudget: '₱2,500,000.00',
            availableBudget: '₱35,000.00',
            requestedAmount: '₱45,000.00',
            ppmpRef: 'PPMP-2025-038',
            ppmpBudget: '₱100,000.00',
            hasPPMP: true
        }
    };
    
    const data = docData[docNumber] || docData['PR-2025-090'];
    
    // Populate modal fields
    document.getElementById('modalDocNumber').textContent = data.docNumber;
    document.getElementById('modalDocType').textContent = data.docType;
    document.getElementById('modalRequester').textContent = data.requester;
    document.getElementById('modalAmount').textContent = data.amount;
    document.getElementById('modalTotalBudget').textContent = data.totalBudget;
    document.getElementById('modalAvailableBudget').textContent = data.availableBudget;
    document.getElementById('modalRequestedAmount').textContent = data.requestedAmount;
    
    // Show/hide PPMP section
    const ppmpSection = document.getElementById('ppmpInfoSection');
    if (data.hasPPMP) {
        ppmpSection.classList.remove('hidden');
        document.getElementById('modalPPMPRef').textContent = data.ppmpRef;
        document.getElementById('modalPPMPBudget').textContent = data.ppmpBudget;
    } else {
        ppmpSection.classList.add('hidden');
    }
    
    // Store current document info
    const modal = document.getElementById('budgetVerificationModal');
    modal.setAttribute('data-doc-number', docNumber);
    modal.setAttribute('data-doc-type', docType);
    
    // Reset budget check result
    document.getElementById('budgetCheckResult').classList.add('hidden');
    document.getElementById('actionButtons').classList.add('hidden');
}

/**
 * Close Budget Verification Modal
 */
function closeBudgetVerificationModal() {
    const modal = document.getElementById('budgetVerificationModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/**
 * Check Budget Availability
 */
function checkBudgetAvailability() {
    const modal = document.getElementById('budgetVerificationModal');
    const docNumber = modal.getAttribute('data-doc-number');
    const availableBudgetText = document.getElementById('modalAvailableBudget').textContent;
    const requestedAmountText = document.getElementById('modalRequestedAmount').textContent;
    
    // Extract numeric values (remove ₱ and commas)
    const availableBudget = parseFloat(availableBudgetText.replace(/[₱,]/g, ''));
    const requestedAmount = parseFloat(requestedAmountText.replace(/[₱,]/g, ''));
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        const budgetCheckResult = document.getElementById('budgetCheckResult');
        const budgetAvailableSection = document.getElementById('budgetAvailableSection');
        const budgetNotAvailableSection = document.getElementById('budgetNotAvailableSection');
        const actionButtons = document.getElementById('actionButtons');
        const reserveBudgetBtn = document.getElementById('reserveBudgetBtn');
        const tagPendingBtn = document.getElementById('tagPendingBtn');
        
        budgetCheckResult.classList.remove('hidden');
        
        if (availableBudget >= requestedAmount) {
            // Budget is available
            budgetAvailableSection.classList.remove('hidden');
            budgetNotAvailableSection.classList.add('hidden');
            reserveBudgetBtn.classList.remove('hidden');
            tagPendingBtn.classList.add('hidden');
        } else {
            // Budget not available
            budgetNotAvailableSection.classList.remove('hidden');
            budgetAvailableSection.classList.add('hidden');
            reserveBudgetBtn.classList.add('hidden');
            tagPendingBtn.classList.remove('hidden');
            
            const shortfall = requestedAmount - availableBudget;
            document.getElementById('budgetShortfallMessage').textContent = 
                `Insufficient budget. Shortfall: ₱${shortfall.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
        
        actionButtons.classList.remove('hidden');
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
}

/**
 * Reserve Budget
 */
function reserveBudget() {
    const modal = document.getElementById('budgetVerificationModal');
    const docNumber = modal.getAttribute('data-doc-number');
    const docType = modal.getAttribute('data-doc-type');
    
    if (!confirm(`Are you sure you want to reserve budget for ${docNumber}?`)) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showSuccessMessage(`Budget reserved successfully for ${docNumber}. ${docType === 'pr' ? 'PR will be forwarded to Principal for approval.' : 'RIS processing will continue.'}`);
        
        // Close modal
        closeBudgetVerificationModal();
        
        // Update pending count
        updatePendingCount();
        
        // Refresh verification list
        setTimeout(() => {
            showSection('budget-verification');
            document.querySelector('[data-section="budget-verification"]').click();
        }, 1500);
        
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1500);
}

/**
 * Tag as Pending Budget
 */
function tagAsPendingBudget() {
    const modal = document.getElementById('budgetVerificationModal');
    const docNumber = modal.getAttribute('data-doc-number');
    
    if (!confirm(`Tag ${docNumber} as Pending Budget? This will move it to the waiting list.`)) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showSuccessMessage(`${docNumber} has been tagged as Pending Budget and added to the waiting list.`);
        
        // Close modal
        closeBudgetVerificationModal();
        
        // Update pending count
        updatePendingCount();
        updatePendingBudgetCount();
        
        // Refresh verification list
        setTimeout(() => {
            showSection('budget-verification');
            document.querySelector('[data-section="budget-verification"]').click();
        }, 1500);
        
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1500);
}

/**
 * Create New ORS
 */
function createNewORS() {
    alert('Create New ORS\n\nThis would open a form to create a new Obligation Request Status (ORS) document.');
}

/**
 * View ORS
 */
function viewORS(orsNumber) {
    alert(`Viewing ORS: ${orsNumber}\n\nThis would display the complete ORS details including:\n- PR reference\n- Item details\n- Budget allocation\n- Fund source\n- Checklist items`);
}

/**
 * Edit ORS
 */
function editORS(orsNumber) {
    alert(`Editing ORS: ${orsNumber}\n\nThis would open the ORS edit form.`);
}

/**
 * Review Pending Budget
 */
function reviewPendingBudget(docNumber) {
    alert(`Reviewing pending budget request: ${docNumber}\n\nThis would show details and allow budget allocation review.`);
}

/**
 * Edit Allocation
 */
function editAllocation(category) {
    alert(`Editing budget allocation for: ${category}\n\nThis would open the budget allocation edit form.`);
}

/**
 * Add Budget Allocation
 */
function addBudgetAllocation() {
    alert('Add Budget Allocation\n\nThis would open a form to add a new budget allocation.');
}

/**
 * Update Pending Count
 */
function updatePendingCount() {
    const pendingCount = document.getElementById('pendingCount');
    if (pendingCount) {
        const currentCount = parseInt(pendingCount.textContent) || 0;
        const newCount = Math.max(0, currentCount - 1);
        pendingCount.textContent = newCount;
        
        if (newCount === 0) {
            pendingCount.classList.add('hidden');
        }
    }
}

/**
 * Update Pending Budget Count
 */
function updatePendingBudgetCount() {
    const pendingBudgetCount = document.getElementById('pendingBudgetCount');
    if (pendingBudgetCount) {
        const currentCount = parseInt(pendingBudgetCount.textContent) || 0;
        const newCount = currentCount + 1;
        pendingBudgetCount.textContent = newCount;
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
    messageDiv.className = 'success-message fixed top-20 right-6 z-50';
    messageDiv.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(messageDiv, main.firstChild);
        
        setTimeout(() => {
            messageDiv.style.opacity = '0';
            messageDiv.style.transition = 'opacity 0.3s';
            setTimeout(() => {
                messageDiv.remove();
            }, 300);
        }, 5000);
    }
}

/**
 * Complete ORS Checklist
 */
function completeORSChecklist(orsNumber) {
    showSection('ors-checklist');
    document.getElementById('checklistORSNumber').textContent = orsNumber;
    // Update page title
    updatePageTitle('ors-checklist');
}

/**
 * Save ORS Checklist
 */
function saveORSChecklist() {
    alert('ORS Checklist saved successfully.');
}

/**
 * Finalize ORS
 */
function finalizeORS() {
    if (confirm('Are you sure you want to finalize this ORS? It will be forwarded to Procurement Office.')) {
        alert('ORS finalized and forwarded to Procurement Office.');
        showSection('ors-management');
        updatePageTitle('ors-management');
    }
}

/**
 * Review PPMP
 */
function reviewPPMP(ppmpNumber) {
    // In a real application, fetch PPMP details from API
    // For now, use sample data
    document.getElementById('reviewPPMPNumber').textContent = ppmpNumber;
    
    // Show modal
    document.getElementById('ppmpReviewModal').classList.remove('hidden');
}

/**
 * Close PPMP Review Modal
 */
function closePPMPReviewModal() {
    document.getElementById('ppmpReviewModal').classList.add('hidden');
    document.getElementById('ppmpReviewRemarks').value = '';
}

/**
 * Submit PPMP Review
 */
function submitPPMPReview() {
    const decision = document.querySelector('input[name="ppmpReviewDecision"]:checked').value;
    const remarks = document.getElementById('ppmpReviewRemarks').value.trim();
    const ppmpNumber = document.getElementById('reviewPPMPNumber').textContent;

    if (!confirm(`Submit review for ${ppmpNumber}?`)) {
        return;
    }

    // Show loading state
    const submitButton = event.target;
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    submitButton.disabled = true;

    // Simulate API call
    setTimeout(() => {
        if (decision === 'approved') {
            showSuccessMessage(`${ppmpNumber} reviewed. Budget available. Forwarded to BAC Secretariat for APP consolidation.`);
        } else {
            showSuccessMessage(`${ppmpNumber} marked as Pending Budget. Will be deferred to next budget cycle.`);
        }

        closePPMPReviewModal();
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;

        // Refresh PPMP review list
        setTimeout(() => {
            showSection('ppmp-review');
            updatePageTitle('ppmp-review');
        }, 1500);
    }, 1500);
}

