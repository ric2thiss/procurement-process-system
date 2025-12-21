/**
 * Document Tracking & Audit Dashboard JavaScript
 * Handles document tracking, audit logs, and transaction history
 */

let chartInstances = {
    transactionStatusChart: null
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Document tracking and audit statistics' },
        'document-tracking': { title: 'Document Tracking', subtitle: 'Track documents by tracking ID or document number' },
        'audit-logs': { title: 'Audit Logs', subtitle: 'View all system actions and changes' },
        'transaction-history': { title: 'Transaction History', subtitle: 'View complete transaction history and status changes' },
        'document-version': { title: 'Document Version History', subtitle: 'View complete version history of documents' },
        'advanced-queries': { title: 'Advanced Queries', subtitle: 'Query audit trail by time, user, or document' },
        'reports': { title: 'Audit Reports', subtitle: 'Generate audit reports and analytics' }
    };
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

function initializeCharts() {
    if (chartInstances.transactionStatusChart) {
        chartInstances.transactionStatusChart.destroy();
        chartInstances.transactionStatusChart = null;
    }

    const statusCtx = document.getElementById('transactionStatusChart');
    if (statusCtx && !chartInstances.transactionStatusChart) {
        chartInstances.transactionStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Completed', 'Pending'],
                datasets: [{
                    data: [156, 1091, 8],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b']
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
}

function searchDocument() {
    const trackingId = document.getElementById('trackingIdSearch').value.trim();
    const documentNumber = document.getElementById('documentNumberSearch').value.trim();
    
    if (!trackingId && !documentNumber) {
        alert('Please enter a tracking ID or document number.');
        return;
    }
    
    // Simulate search
    const results = document.getElementById('trackingResults');
    results.classList.remove('hidden');
    
    // Sample data
    document.getElementById('resultTrackingId').textContent = trackingId || documentNumber;
    document.getElementById('resultStatus').textContent = 'In Progress';
    document.getElementById('resultOffice').textContent = 'Procurement Office';
    document.getElementById('resultLastUpdated').textContent = new Date().toLocaleString();
}

function viewTransactionHistory(trackingId) {
    alert(`Viewing transaction history for ${trackingId}\n\nThis would show complete transaction history including all status changes, approvals, and actions.`);
}

function loadVersionHistory() {
    const docNumber = document.getElementById('versionDocNumber').value;
    if (!docNumber) {
        alert('Please enter a document number.');
        return;
    }
    alert(`Loading version history for ${docNumber}\n\nThis would load all versions of the document with change history.`);
}

function viewVersion(versionNumber) {
    alert(`Viewing version ${versionNumber}\n\nThis would display the document as it appeared at this version, including all fields and values.`);
}

function setQueryType(type) {
    // Hide all query forms
    document.getElementById('timeQueryForm').classList.add('hidden');
    document.getElementById('userQueryForm').classList.add('hidden');
    document.getElementById('documentQueryForm').classList.add('hidden');
    document.getElementById('queryResults').classList.add('hidden');
    
    // Remove active state from all buttons
    document.querySelectorAll('.query-type-btn').forEach(btn => {
        btn.classList.remove('border-green-600', 'bg-green-50');
    });
    
    // Show selected query form
    if (type === 'time') {
        document.getElementById('timeQueryForm').classList.remove('hidden');
        event.target.closest('.query-type-btn').classList.add('border-green-600', 'bg-green-50');
    } else if (type === 'user') {
        document.getElementById('userQueryForm').classList.remove('hidden');
        event.target.closest('.query-type-btn').classList.add('border-green-600', 'bg-green-50');
    } else if (type === 'document') {
        document.getElementById('documentQueryForm').classList.remove('hidden');
        event.target.closest('.query-type-btn').classList.add('border-green-600', 'bg-green-50');
    }
}

function executeTimeQuery() {
    document.getElementById('queryResults').classList.remove('hidden');
    alert('Executing time-based query...\n\nThis would query all actions within the specified date range and action type.');
}

function executeUserQuery() {
    document.getElementById('queryResults').classList.remove('hidden');
    alert('Executing user-based query...\n\nThis would query all actions performed by the specified user.');
}

function executeDocumentQuery() {
    document.getElementById('queryResults').classList.remove('hidden');
    alert('Executing document-based query...\n\nThis would query all actions related to the specified document.');
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

