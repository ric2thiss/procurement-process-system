/**
 * Purchase Request & PPMP Dashboard JavaScript
 * Handles navigation, form submissions, and interactions for PR & PPMP module
 */

// Store chart instances to prevent multiple initializations
let chartInstances = {
    prStatusChart: null,
    ppmpValidationChart: null
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
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
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
            
            // Close sidebar on mobile after navigation
            if (window.innerWidth < 1024) {
                if (sidebar) {
                    sidebar.classList.remove('open');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('hidden');
                }
            }
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Purchase Request and PPMP validation' },
        'purchase-requests': { title: 'Purchase Requests', subtitle: 'Manage and track all purchase requests' },
        'create-pr': { title: 'Create Purchase Request', subtitle: 'Create a new Purchase Request from Supply Request' },
        'ppmp-validation': { title: 'PPMP Validation', subtitle: 'Validate Purchase Requests against PPMP' },
        'pending-ppmp': { title: 'Pending PPMP', subtitle: 'Purchase Requests not included in current PPMP' },
        'reports': { title: 'PR & PPMP Reports', subtitle: 'Generate reports and analytics' }
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
    if (chartInstances.prStatusChart) {
        chartInstances.prStatusChart.destroy();
        chartInstances.prStatusChart = null;
    }
    if (chartInstances.ppmpValidationChart) {
        chartInstances.ppmpValidationChart.destroy();
        chartInstances.ppmpValidationChart = null;
    }

    // PR Status Chart
    const prStatusCtx = document.getElementById('prStatusChart');
    if (prStatusCtx && !chartInstances.prStatusChart) {
        chartInstances.prStatusChart = new Chart(prStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['PPMP Validated', 'Pending PPMP', 'Forwarded', 'Approved'],
                datasets: [{
                    data: [42, 3, 38, 35],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#3b82f6',
                        '#8b5cf6'
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

    // PPMP Validation Chart
    const ppmpValidationCtx = document.getElementById('ppmpValidationChart');
    if (ppmpValidationCtx && !chartInstances.ppmpValidationChart) {
        chartInstances.ppmpValidationChart = new Chart(ppmpValidationCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Validated',
                    data: [12, 15, 10, 5],
                    backgroundColor: '#10b981'
                }, {
                    label: 'Pending',
                    data: [3, 2, 1, 0],
                    backgroundColor: '#f59e0b'
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
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

/**
 * Initialize Date and Time
 */
function initializeDateTime() {
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
        const dateElement = document.getElementById('currentDate');
        const timeElement = document.getElementById('currentTime');
        
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
        }
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
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
    
    // Close sidebar on window resize if it becomes desktop view
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
}

/**
 * Initialize Forms
 */
function initializeForms() {
    // PR Form
    const prForm = document.getElementById('prForm');
    if (prForm) {
        prForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePRFormSubmit();
        });
    }

    // PPMP Validation Form
    const ppmpValidationForm = document.getElementById('ppmpValidationForm');
    if (ppmpValidationForm) {
        ppmpValidationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePPMPValidationSubmit();
        });
    }

    // Supply Request Select Change
    const supplyRequestSelect = document.getElementById('supplyRequestSelect');
    if (supplyRequestSelect) {
        supplyRequestSelect.addEventListener('change', function() {
            loadSupplyRequestData(this.value);
        });
    }

    // Calculate total amount when quantity or unit price changes
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unitPrice');
    const totalAmountInput = document.getElementById('totalAmount');

    if (quantityInput && unitPriceInput && totalAmountInput) {
        [quantityInput, unitPriceInput].forEach(input => {
            input.addEventListener('input', function() {
                calculateTotalAmount();
            });
        });
    }
}

/**
 * Calculate Total Amount
 */
function calculateTotalAmount() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unitPrice').value) || 0;
    const totalAmount = quantity * unitPrice;
    
    const totalAmountInput = document.getElementById('totalAmount');
    if (totalAmountInput) {
        totalAmountInput.value = '₱ ' + totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}

/**
 * Load Supply Request Data
 */
function loadSupplyRequestData(supplyRequestId) {
    // Mock data - in real implementation, this would fetch from API
    const supplyRequestData = {
        '2025-SR-025': {
            requester: 'John Doe',
            item: 'A4 Bond Paper (Ream)',
            quantity: 50,
            unit: 'ream'
        },
        '2025-SR-024': {
            requester: 'Jane Smith',
            item: 'Whiteboard Markers (Set)',
            quantity: 20,
            unit: 'set'
        },
        '2025-SR-023': {
            requester: 'Robert Johnson',
            item: 'Projector Lamp',
            quantity: 1,
            unit: 'piece'
        }
    };

    const data = supplyRequestData[supplyRequestId];
    if (data) {
        document.getElementById('requesterName').value = data.requester;
        document.getElementById('itemDescription').value = data.item;
        document.getElementById('quantity').value = data.quantity;
        document.getElementById('unit').value = data.unit;
    }
}

/**
 * Handle PR Form Submit
 */
function handlePRFormSubmit() {
    // Validate form
    const form = document.getElementById('prForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    submitButton.disabled = true;

    // Simulate API call
    setTimeout(() => {
        alert('Purchase Request created successfully!');
        resetPRForm();
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 1500);
}

/**
 * Reset PR Form
 */
function resetPRForm() {
    const form = document.getElementById('prForm');
    if (form) {
        form.reset();
        document.getElementById('totalAmount').value = '';
    }
}

/**
 * Handle PPMP Validation Submit
 */
function handlePPMPValidationSubmit() {
    const prSelect = document.getElementById('prSelect');
    const prNumber = prSelect.value;

    if (!prNumber) {
        alert('Please select a Purchase Request to validate.');
        return;
    }

    // Show loading state
    const submitButton = document.getElementById('ppmpValidationForm').querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validating...';
    submitButton.disabled = true;

    // Simulate validation
    setTimeout(() => {
        showPPMPValidationResult(prNumber, true);
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 2000);
}

/**
 * Show PPMP Validation Result
 */
function showPPMPValidationResult(prNumber, isValid) {
    const resultDiv = document.getElementById('ppmpMatchResult');
    if (!resultDiv) return;

    if (isValid) {
        resultDiv.innerHTML = `
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    <h5 class="font-semibold text-green-900">PPMP Match Found</h5>
                </div>
                <div class="text-sm text-green-700 space-y-1">
                    <p><strong>PPMP Item:</strong> A4 Bond Paper (Ream)</p>
                    <p><strong>PPMP Code:</strong> PPMP-2025-001</p>
                    <p><strong>Budget Available:</strong> ₱ 50,000.00</p>
                    <p><strong>Schedule:</strong> Q1 2025</p>
                </div>
                <button onclick="linkToPPMP('${prNumber}')" class="mt-3 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-link mr-2"></i>Link to PPMP Item
                </button>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    <h5 class="font-semibold text-red-900">No PPMP Match Found</h5>
                </div>
                <p class="text-sm text-red-700">This item is not included in the current PPMP. The PR will be tagged as "Pending PPMP".</p>
                <button onclick="markAsPendingPPMP('${prNumber}')" class="mt-3 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-clock mr-2"></i>Mark as Pending PPMP
                </button>
            </div>
        `;
    }

    resultDiv.classList.remove('hidden');
}

/**
 * Validate PPMP
 */
function validatePPMP(prNumber) {
    // Navigate to PPMP validation section
    showSection('ppmp-validation');
    
    // Update active nav
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
    document.querySelector('[data-section="ppmp-validation"]').classList.add('active');
    updatePageTitle('ppmp-validation');

    // Set PR in select
    const prSelect = document.getElementById('prSelect');
    if (prSelect) {
        prSelect.value = prNumber;
    }

    // Trigger validation
    setTimeout(() => {
        handlePPMPValidationSubmit();
    }, 500);
}

/**
 * View PR
 */
function viewPR(prNumber) {
    alert(`Viewing PR: ${prNumber}\n\nThis would open a detailed view of the Purchase Request.`);
    // In real implementation, this would open a modal or navigate to detail page
}

/**
 * View PPMP Link
 */
function viewPPMPLink(prNumber) {
    alert(`Viewing PPMP Link for: ${prNumber}\n\nThis would show the linked PPMP item details.`);
    // In real implementation, this would show PPMP linkage details
}

/**
 * Link to PPMP
 */
function linkToPPMP(prNumber) {
    // Show loading
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Linking...';
    button.disabled = true;

    // Simulate API call
    setTimeout(() => {
        alert(`PR ${prNumber} successfully linked to PPMP item!\n\nStatus updated to "PPMP Validated - For Budget Check"`);
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Reset form
        resetValidationForm();
    }, 1500);
}

/**
 * Mark as Pending PPMP
 */
function markAsPendingPPMP(prNumber) {
    if (confirm(`Mark PR ${prNumber} as Pending PPMP?`)) {
        alert(`PR ${prNumber} has been marked as Pending PPMP.\n\nThe request will be queued for PPMP inclusion review.`);
        resetValidationForm();
    }
}

/**
 * Request PPMP Amendment
 */
function requestPPMPAmendment(prNumber) {
    if (confirm(`Request PPMP amendment for PR ${prNumber}?`)) {
        alert(`PPMP amendment request has been submitted for PR ${prNumber}.\n\nThe PPMP Manager will be notified.`);
    }
}

/**
 * Reset Validation Form
 */
function resetValidationForm() {
    const form = document.getElementById('ppmpValidationForm');
    if (form) {
        form.reset();
    }
    const resultDiv = document.getElementById('ppmpMatchResult');
    if (resultDiv) {
        resultDiv.classList.add('hidden');
        resultDiv.innerHTML = '';
    }
}

