/**
 * Principal / School Head Dashboard JavaScript
 * Handles navigation, PR approval/rejection, and DV signing
 */

// Store chart instances to prevent multiple initializations
let chartInstances = {
    approvalStatusChart: null,
    monthlyTrendChart: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeNavigation();
    initializeDateTime();
    initializeMobileSidebar();
    initializeCharts();
    initializeApprovalHandlers();
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Principal approval statistics and monitoring' },
        'pending-approvals': { title: 'Pending Approvals', subtitle: 'Review and approve or reject purchase requests' },
        'ppmp-signatures': { title: 'PPMP Signatures', subtitle: 'Review and sign Project Procurement Management Plans' },
        'ris-signatures': { title: 'RIS Signatures', subtitle: 'Review and sign Requisition and Issue Slips' },
        'pr-history': { title: 'PR History', subtitle: 'View all approved and rejected purchase requests' },
        'dv-signing': { title: 'DV Signing', subtitle: 'Review and sign disbursement vouchers' },
        'approval-delegation': { title: 'Approval Delegation', subtitle: 'Delegate approval authority temporarily' },
        'approval-history': { title: 'Approval History', subtitle: 'Complete history of approval decisions' },
        'reports': { title: 'Approval Reports', subtitle: 'Generate approval reports and analytics' }
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
    if (chartInstances.approvalStatusChart) {
        chartInstances.approvalStatusChart.destroy();
        chartInstances.approvalStatusChart = null;
    }
    if (chartInstances.monthlyTrendChart) {
        chartInstances.monthlyTrendChart.destroy();
        chartInstances.monthlyTrendChart = null;
    }

    // Approval Status Chart
    const approvalStatusCtx = document.getElementById('approvalStatusChart');
    if (approvalStatusCtx && !chartInstances.approvalStatusChart) {
        chartInstances.approvalStatusChart = new Chart(approvalStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Rejected', 'Pending'],
                datasets: [{
                    data: [42, 5, 8],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444',
                        '#f59e0b'
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

    // Monthly Trend Chart
    const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
    if (monthlyTrendCtx && !chartInstances.monthlyTrendChart) {
        chartInstances.monthlyTrendChart = new Chart(monthlyTrendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Approved',
                    data: [35, 38, 40, 42, 45, 42],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Rejected',
                    data: [3, 4, 5, 4, 5, 5],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
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
 * Initialize Approval Handlers
 */
function initializeApprovalHandlers() {
    const remarksTextarea = document.getElementById('approvalRemarks');
    if (remarksTextarea) {
        remarksTextarea.addEventListener('input', function() {
            const remarksRequired = document.getElementById('remarksRequired');
            if (this.value.trim().length > 0) {
                remarksRequired.classList.add('hidden');
            }
        });
    }
}

/**
 * Review PR
 */
function reviewPR(prNumber) {
    // Show modal with PR details
    const modal = document.getElementById('prReviewModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // In a real application, fetch PR details from API
        // For now, we'll use sample data
        loadPRDetails(prNumber);
    }
}

/**
 * Load PR Details
 */
function loadPRDetails(prNumber) {
    // Sample data - in real app, fetch from API
    const prData = {
        'PR-2025-090': {
            prNumber: 'PR-2025-090',
            dateReceived: '2025-01-15',
            requester: 'Jane Smith',
            priority: 'Urgent',
            itemDescription: 'Projector Lamp',
            quantity: '1 piece',
            unitPrice: '₱15,000.00',
            totalAmount: '₱15,000.00',
            specifications: 'High brightness, compatible with Epson projectors',
            ppmpRef: 'PPMP-2025-045',
            ppmpStatus: '✓ Included in PPMP',
            justification: 'Needed for classroom projector that is currently not functional. Required for teaching activities.',
            budgetAllocated: '₱50,000.00',
            budgetStatus: '✓ Budget Available'
        },
        'PR-2025-089': {
            prNumber: 'PR-2025-089',
            dateReceived: '2025-01-14',
            requester: 'John Doe',
            priority: 'Normal',
            itemDescription: 'Whiteboard Markers (Set of 10)',
            quantity: '20 sets',
            unitPrice: '₱125.00',
            totalAmount: '₱2,500.00',
            specifications: 'Non-permanent, assorted colors',
            ppmpRef: 'PPMP-2025-042',
            ppmpStatus: '✓ Included in PPMP',
            justification: 'Regular classroom supplies needed for teaching activities.',
            budgetAllocated: '₱10,000.00',
            budgetStatus: '✓ Budget Available'
        },
        'PR-2025-088': {
            prNumber: 'PR-2025-088',
            dateReceived: '2025-01-13',
            requester: 'Robert Johnson',
            priority: 'Urgent',
            itemDescription: 'Laboratory Equipment',
            quantity: '1 set',
            unitPrice: '₱45,000.00',
            totalAmount: '₱45,000.00',
            specifications: 'Complete laboratory equipment set for chemistry lab',
            ppmpRef: 'PPMP-2025-038',
            ppmpStatus: '✓ Included in PPMP',
            justification: 'Required for laboratory activities and experiments. Current equipment is outdated and non-functional.',
            budgetAllocated: '₱100,000.00',
            budgetStatus: '✓ Budget Available'
        }
    };
    
    const data = prData[prNumber] || prData['PR-2025-090'];
    
    // Populate modal fields
    document.getElementById('modalPRNumber').textContent = data.prNumber;
    document.getElementById('modalDateReceived').textContent = data.dateReceived;
    document.getElementById('modalRequester').textContent = data.requester;
    document.getElementById('modalPriority').textContent = data.priority;
    document.getElementById('modalItemDescription').textContent = data.itemDescription;
    document.getElementById('modalQuantity').textContent = data.quantity;
    document.getElementById('modalUnitPrice').textContent = data.unitPrice;
    document.getElementById('modalTotalAmount').textContent = data.totalAmount;
    document.getElementById('modalSpecifications').textContent = data.specifications;
    document.getElementById('modalPPMPRef').textContent = data.ppmpRef;
    document.getElementById('modalPPMPStatus').textContent = data.ppmpStatus;
    document.getElementById('modalJustification').textContent = data.justification;
    document.getElementById('modalBudgetAllocated').textContent = data.budgetAllocated;
    document.getElementById('modalBudgetStatus').textContent = data.budgetStatus;
    
    // Store current PR number for approval/rejection
    document.getElementById('prReviewModal').setAttribute('data-pr-number', prNumber);
    
    // Clear remarks
    document.getElementById('approvalRemarks').value = '';
    document.getElementById('remarksRequired').classList.remove('hidden');
}

/**
 * Close PR Review Modal
 */
function closePRReviewModal() {
    const modal = document.getElementById('prReviewModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Clear remarks
    const remarks = document.getElementById('approvalRemarks');
    if (remarks) {
        remarks.value = '';
    }
}

/**
 * Approve PR
 */
function approvePR() {
    const modal = document.getElementById('prReviewModal');
    const prNumber = modal.getAttribute('data-pr-number');
    const remarks = document.getElementById('approvalRemarks').value.trim();
    
    // Validate remarks (optional for approval, but good practice)
    if (remarks.length === 0) {
        if (!confirm('Proceed with approval without remarks?')) {
            return;
        }
    }
    
    // Show loading state
    const approveButton = event.target;
    const originalText = approveButton.innerHTML;
    approveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Approving...';
    approveButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success message
        showSuccessMessage(`Purchase Request ${prNumber} has been approved successfully!`);
        
        // Close modal
        closePRReviewModal();
        
        // Update pending count
        updatePendingCount();
        
        // Refresh pending approvals list
        setTimeout(() => {
            showSection('pending-approvals');
            document.querySelector('[data-section="pending-approvals"]').click();
        }, 1500);
        
        approveButton.innerHTML = originalText;
        approveButton.disabled = false;
    }, 1500);
}

/**
 * Reject PR
 */
function rejectPR() {
    const modal = document.getElementById('prReviewModal');
    const prNumber = modal.getAttribute('data-pr-number');
    const remarks = document.getElementById('approvalRemarks').value.trim();
    
    // Validate remarks (required for rejection)
    if (remarks.length === 0) {
        alert('Remarks are required when rejecting a purchase request. Please provide a reason for rejection.');
        document.getElementById('approvalRemarks').focus();
        return;
    }
    
    // Confirm rejection
    if (!confirm(`Are you sure you want to reject ${prNumber}? This action cannot be undone.`)) {
        return;
    }
    
    // Show loading state
    const rejectButton = event.target;
    const originalText = rejectButton.innerHTML;
    rejectButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Rejecting...';
    rejectButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success message
        showSuccessMessage(`Purchase Request ${prNumber} has been rejected.`);
        
        // Close modal
        closePRReviewModal();
        
        // Update pending count
        updatePendingCount();
        
        // Refresh pending approvals list
        setTimeout(() => {
            showSection('pending-approvals');
            document.querySelector('[data-section="pending-approvals"]').click();
        }, 1500);
        
        rejectButton.innerHTML = originalText;
        rejectButton.disabled = false;
    }, 1500);
}

/**
 * View PR Details
 */
function viewPRDetails(prNumber) {
    reviewPR(prNumber);
}

/**
 * View PR History
 */
function viewPRHistory(prNumber) {
    alert(`Viewing history for ${prNumber}\n\nThis would show complete approval/rejection history including:\n- Approval/rejection date\n- Remarks\n- Status changes\n- Related documents`);
}

/**
 * Sign DV
 */
function signDV(dvNumber) {
    if (!confirm(`Are you sure you want to sign Disbursement Voucher ${dvNumber}?`)) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showSuccessMessage(`Disbursement Voucher ${dvNumber} has been signed successfully!`);
        
        // Update DV count
        updateDVCount();
        
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Refresh DV list
        setTimeout(() => {
            showSection('dv-signing');
            document.querySelector('[data-section="dv-signing"]').click();
        }, 1500);
    }, 1500);
}

/**
 * View DV
 */
function viewDV(dvNumber) {
    alert(`Viewing DV: ${dvNumber}\n\nThis would display the complete DV details including:\n- PR reference\n- ORS reference\n- PO reference\n- Payment details\n- Supplier information`);
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
 * Update DV Count
 */
function updateDVCount() {
    const dvCount = document.getElementById('dvCount');
    if (dvCount) {
        const currentCount = parseInt(dvCount.textContent) || 0;
        const newCount = Math.max(0, currentCount - 1);
        dvCount.textContent = newCount;
        
        if (newCount === 0) {
            dvCount.classList.add('hidden');
        }
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
 * Open Delegation Modal
 */
function openDelegationModal() {
    alert('Delegation Modal: This would open a form to delegate approval authority to another user with start/end dates.');
}

/**
 * Revoke Delegation
 */
function revokeDelegation(delegationId) {
    if (confirm('Are you sure you want to revoke this delegation?')) {
        alert(`Delegation ${delegationId} revoked successfully.`);
    }
}

/**
 * View Approval Details
 */
function viewApprovalDetails(prNumber) {
    alert(`Viewing approval details for ${prNumber}\n\nThis would show complete approval history including:\n- Decision made\n- Processing time\n- Remarks\n- Digital signature details`);
}

/**
 * Review PPMP for Signature
 */
function reviewPPMP(ppmpNumber) {
    if (confirm(`Review PPMP ${ppmpNumber} for signature?\n\nThis will open the PPMP document for review. You can approve and sign it once verified.`)) {
        alert(`Opening PPMP ${ppmpNumber} for review.\n\nThis would display the complete PPMP document with all items, budget allocations, and procurement schedules.\n\nAfter review, you can approve and sign the PPMP.`);
        // In a real application, this would open a modal or navigate to PPMP review page
    }
}

/**
 * Review RIS for Signature
 */
function reviewRIS(risNumber) {
    if (confirm(`Review RIS ${risNumber} for signature?\n\nThis will open the RIS document for review. You can approve and sign it once verified.`)) {
        alert(`Opening RIS ${risNumber} for review.\n\nThis would display the complete RIS document with item details, quantities, and issue information.\n\nAfter review, you can approve and sign the RIS.`);
        // In a real application, this would open a modal or navigate to RIS review page
    }
}

