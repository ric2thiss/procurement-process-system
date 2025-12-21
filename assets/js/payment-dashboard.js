/**
 * Payment & Disbursement Dashboard JavaScript
 * Handles payment processing and cheque management
 */

let chartInstances = {
    paymentStatusChart: null,
    monthlyPaymentChart: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeNavigation();
    initializeDateTime();
    initializeMobileSidebar();
    initializeCharts();
});

function initializeDashboard() {
    showSection('dashboard');
}

function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            showSection(section);
            updatePageTitle(section);
        });
    });
}

function showSection(sectionId) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.add('hidden'));
    const targetSection = document.getElementById(sectionId + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
        targetSection.classList.add('fade-in');
        if (sectionId === 'dashboard') {
            setTimeout(() => initializeCharts(), 100);
        }
    }
}

function updatePageTitle(sectionId) {
    const titles = {
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Payment and disbursement statistics' },
        'payment-processing': { title: 'Payment Processing', subtitle: 'Process payments for signed DVs' },
        'budget-release': { title: 'Budget Release', subtitle: 'Process budget release for signed DVs' },
        'cheque-management': { title: 'Cheque Management', subtitle: 'Manage cheques and signatures' },
        'cheque-signature': { title: 'Cheque Signature', subtitle: 'Manage cheque signature routing and approval' },
        'payment-reconciliation': { title: 'Payment Reconciliation', subtitle: 'Reconcile payments and track payment status' },
        'reports': { title: 'Payment Reports', subtitle: 'Generate payment reports and analytics' }
    };
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

function initializeCharts() {
    if (chartInstances.paymentStatusChart) {
        chartInstances.paymentStatusChart.destroy();
        chartInstances.paymentStatusChart = null;
    }
    if (chartInstances.monthlyPaymentChart) {
        chartInstances.monthlyPaymentChart.destroy();
        chartInstances.monthlyPaymentChart = null;
    }

    const statusCtx = document.getElementById('paymentStatusChart');
    if (statusCtx && !chartInstances.paymentStatusChart) {
        chartInstances.paymentStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Paid'],
                datasets: [{
                    data: [5, 3, 30],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const monthlyCtx = document.getElementById('monthlyPaymentChart');
    if (monthlyCtx && !chartInstances.monthlyPaymentChart) {
        chartInstances.monthlyPaymentChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Payments Processed',
                    data: [30, 35, 38, 40, 42, 38],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'top' } }
            }
        });
    }
}

function processPayment(dvNumber) {
    alert(`Processing payment for ${dvNumber}\n\nThis would open the payment processing workflow.`);
}

function viewCheque(chequeNumber) {
    alert(`Viewing cheque: ${chequeNumber}\n\nThis would display the complete cheque details.`);
}

function issueCheque(chequeNumber) {
    alert(`Issuing cheque: ${chequeNumber}\n\nThis would mark the cheque as issued.`);
}

function initializeDateTime() {
    function updateDateTime() {
        const now = new Date();
        const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const dateElement = document.getElementById('currentDate');
        const timeElement = document.getElementById('currentTime');
        if (dateElement) dateElement.textContent = date;
        if (timeElement) timeElement.textContent = time;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

function initializeMobileSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('hidden');
        });
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.add('hidden');
        });
    }
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('open');
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
            }
        });
    });
}

function processBudgetRelease(dvNumber) {
    const details = document.getElementById('budgetReleaseDetails');
    if (details) {
        details.classList.remove('hidden');
    }
}

function cancelBudgetRelease() {
    const details = document.getElementById('budgetReleaseDetails');
    if (details) {
        details.classList.add('hidden');
    }
}

function confirmBudgetRelease() {
    if (confirm('Confirm budget release? Reserved budget will be converted to obligation.')) {
        alert('Budget released successfully. Cheque can now be generated.');
        cancelBudgetRelease();
    }
}

function viewSignatureWorkflow(chequeNumber) {
    const details = document.getElementById('signatureWorkflowDetails');
    if (details) {
        details.classList.toggle('hidden');
    }
}

function routeForSignature(chequeNumber) {
    alert(`Routing cheque ${chequeNumber} for signature\n\nThis would route the cheque to the next required signatory.`);
}

function runReconciliation() {
    alert('Running payment reconciliation...\n\nThis would compare payment records with bank statements and identify discrepancies.');
}

function reconcilePayment(chequeNumber) {
    if (confirm(`Reconcile payment for ${chequeNumber}?`)) {
        alert(`Payment ${chequeNumber} reconciled successfully.`);
    }
}

function viewReconciliation(chequeNumber) {
    alert(`Viewing reconciliation details for ${chequeNumber}\n\nThis would show reconciliation status, bank statement match, and any discrepancies.`);
}

