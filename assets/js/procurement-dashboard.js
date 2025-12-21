/**
 * Procurement Office Dashboard JavaScript
 * Handles procurement queue, PO management, and supplier management
 */

let chartInstances = {
    procurementStatusChart: null,
    monthlyProcurementChart: null
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Procurement statistics and monitoring' },
        'procurement-queue': { title: 'Procurement Queue', subtitle: 'Review and process approved PRs with ORS' },
        'procurement-checklist': { title: 'Procurement Checklist', subtitle: 'Complete procurement checklists for PRs' },
        'po-checklist': { title: 'PO Checklist', subtitle: 'Complete PO checklist items before generating PO' },
        'purchase-orders': { title: 'Purchase Orders', subtitle: 'Manage Purchase Orders (PO)' },
        'delivery-receipt': { title: 'Delivery & Receipt', subtitle: 'Verify delivery and receipt of procured items' },
        'suppliers': { title: 'Suppliers', subtitle: 'Manage supplier relationships' },
        'reports': { title: 'Procurement Reports', subtitle: 'Generate procurement reports and analytics' }
    };
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

function initializeCharts() {
    if (chartInstances.procurementStatusChart) {
        chartInstances.procurementStatusChart.destroy();
        chartInstances.procurementStatusChart = null;
    }
    if (chartInstances.monthlyProcurementChart) {
        chartInstances.monthlyProcurementChart.destroy();
        chartInstances.monthlyProcurementChart = null;
    }

    const statusCtx = document.getElementById('procurementStatusChart');
    if (statusCtx && !chartInstances.procurementStatusChart) {
        chartInstances.procurementStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [8, 12, 38],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981']
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

    const monthlyCtx = document.getElementById('monthlyProcurementChart');
    if (monthlyCtx && !chartInstances.monthlyProcurementChart) {
        chartInstances.monthlyProcurementChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'POs Generated',
                    data: [30, 35, 40, 42, 45, 45],
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

function processProcurement(prNumber) {
    alert(`Processing procurement for ${prNumber}\n\nThis would open the procurement processing workflow.`);
}

function viewProcurementDetails(prNumber) {
    alert(`Viewing procurement details for ${prNumber}\n\nThis would show complete procurement information.`);
}

function viewPO(poNumber) {
    alert(`Viewing PO: ${poNumber}\n\nThis would display the complete Purchase Order details.`);
}

function openChecklist(prNumber) {
    alert(`Opening checklist for ${prNumber}\n\nThis would open the procurement checklist form.`);
}

function createNewPO() {
    alert('Create New PO\n\nThis would open a form to create a new Purchase Order.');
}

function recordDelivery(poNumber) {
    alert(`Recording delivery for ${poNumber}\n\nThis would open the delivery recording form.`);
}

function addSupplier() {
    alert('Add Supplier\n\nThis would open a form to add a new supplier.');
}

function editSupplier(supplierCode) {
    alert(`Editing supplier: ${supplierCode}\n\nThis would open the supplier edit form.`);
}

function viewSupplierHistory(supplierCode) {
    alert(`Viewing history for supplier: ${supplierCode}\n\nThis would show supplier transaction history.`);
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

function openChecklist(prNumber) {
    const detailedChecklist = document.getElementById('detailedChecklist');
    if (detailedChecklist) {
        detailedChecklist.classList.remove('hidden');
    }
}

function closeChecklist() {
    const detailedChecklist = document.getElementById('detailedChecklist');
    if (detailedChecklist) {
        detailedChecklist.classList.add('hidden');
    }
}

function saveChecklist() {
    alert('Procurement checklist saved successfully.');
    closeChecklist();
}

function savePOChecklist() {
    alert('PO checklist saved successfully.');
}

function generatePO() {
    if (confirm('Generate Purchase Order based on completed checklist?')) {
        alert('Purchase Order generated successfully.');
    }
}

function verifyDelivery(poNumber) {
    alert(`Verifying delivery for ${poNumber}\n\nThis would open a form to record delivery details, date, and condition.`);
}

function verifyReceipt(poNumber) {
    alert(`Verifying receipt for ${poNumber}\n\nThis would open a form to verify receipt, check items, and complete acceptance documentation.`);
}

