/**
 * Bookkeeper Dashboard JavaScript
 * Handles DV creation and management
 */

let chartInstances = {
    dvStatusChart: null,
    monthlyDVChart: null
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Bookkeeper statistics and monitoring' },
        'dv-creation': { title: 'DV Creation', subtitle: 'Create DVs from completed procurement documents' },
        'dv-management': { title: 'DV Management', subtitle: 'Manage and track all DVs' },
        'document-verification': { title: 'Document Verification', subtitle: 'Verify payment requirements and document completeness' },
        'document-linking': { title: 'Document Linking', subtitle: 'Link DV to related documents' },
        'dv-finalization': { title: 'DV Finalization', subtitle: 'Review DV for accuracy before forwarding' },
        'reports': { title: 'Payment Reports', subtitle: 'Generate payment reports and analytics' }
    };
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    document.getElementById('pageTitle').textContent = pageInfo.title;
    document.getElementById('pageSubtitle').textContent = pageInfo.subtitle;
}

function initializeCharts() {
    if (chartInstances.dvStatusChart) {
        chartInstances.dvStatusChart.destroy();
        chartInstances.dvStatusChart = null;
    }
    if (chartInstances.monthlyDVChart) {
        chartInstances.monthlyDVChart.destroy();
        chartInstances.monthlyDVChart = null;
    }

    const statusCtx = document.getElementById('dvStatusChart');
    if (statusCtx && !chartInstances.dvStatusChart) {
        chartInstances.dvStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'For Approval', 'Approved'],
                datasets: [{
                    data: [6, 8, 28],
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

    const monthlyCtx = document.getElementById('monthlyDVChart');
    if (monthlyCtx && !chartInstances.monthlyDVChart) {
        chartInstances.monthlyDVChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'DVs Created',
                    data: [35, 38, 40, 42, 45, 42],
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

function createDV(prNumber) {
    alert(`Creating DV for ${prNumber}\n\nThis would open the DV creation form.`);
}

function viewDV(dvNumber) {
    alert(`Viewing DV: ${dvNumber}\n\nThis would display the complete DV details.`);
}

function editDV(dvNumber) {
    alert(`Editing DV: ${dvNumber}\n\nThis would open the DV edit form.`);
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

function saveVerification() {
    alert('Document verification saved successfully.');
}

function linkDocument(docType) {
    alert(`Linking ${docType} document\n\nThis would open a search interface to find and link the document.`);
}

function verifyLinks() {
    alert('All document links verified successfully.');
}

function saveDocumentLinks() {
    alert('Document links saved successfully.');
}

function finalizeDV() {
    if (confirm('Are you sure you want to finalize this DV? It will be forwarded to School Head for signature.')) {
        alert('DV finalized and forwarded to School Head for signature.');
    }
}

