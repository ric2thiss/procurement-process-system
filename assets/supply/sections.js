/**
 * Section Management Module
 * Handles showing/hiding different sections of the dashboard
 */

import { loadSupplyRequests } from './requests.js';
import { loadInventory } from './inventory.js';
import { loadRISList } from './ris.js';
import { loadDashboardData } from './dashboard.js';
import { loadUserProfile } from './profile.js';

const STORAGE_KEY = 'supply_dashboard_active_section';

/**
 * Save active section to localStorage
 * @param {string} sectionId - Section identifier
 */
function saveActiveSection(sectionId) {
    try {
        localStorage.setItem(STORAGE_KEY, sectionId);
    } catch (error) {
        console.warn('Failed to save active section to localStorage:', error);
    }
}

/**
 * Get active section from localStorage
 * @returns {string|null} Section identifier or null if not found
 */
export function getActiveSection() {
    try {
        return localStorage.getItem(STORAGE_KEY);
    } catch (error) {
        console.warn('Failed to get active section from localStorage:', error);
        return null;
    }
}

/**
 * Clear saved active section from localStorage
 */
export function clearActiveSection() {
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        console.warn('Failed to clear active section from localStorage:', error);
    }
}

/**
 * Show a specific section
 * @param {string} sectionId - Section identifier
 */
export function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.add('hidden'));
    
    // Show target section
    const targetSection = document.getElementById(sectionId + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
        targetSection.classList.add('fade-in');
        
        // Save to localStorage
        saveActiveSection(sectionId);
        
        // Update active nav item
        updateActiveNavItem(sectionId);
        
        // Update page title
        updatePageTitle(sectionId);
        
        // Load data for specific sections
        if (sectionId === 'dashboard') {
            loadDashboardData();
        } else if (sectionId === 'supply-requests') {
            loadSupplyRequests();
        } else if (sectionId === 'inventory') {
            loadInventory();
        } else if (sectionId === 'ris-management') {
            loadRISList();
        } else if (sectionId === 'profile') {
            loadUserProfile();
        }
    }
}

/**
 * Update active navigation item
 * @param {string} sectionId - Section identifier
 */
function updateActiveNavItem(sectionId) {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        const itemSection = item.getAttribute('data-section');
        if (itemSection === sectionId) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

/**
 * Update Page Title
 * @param {string} sectionId - Section identifier
 */
export function updatePageTitle(sectionId) {
    const titles = {
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Supply office statistics and monitoring' },
        'supply-requests': { title: 'Supply Requests', subtitle: 'Review and process supply requests' },
        'inventory': { title: 'Inventory Management', subtitle: 'Manage inventory items and stock levels' },
        'ris-management': { title: 'RIS Management', subtitle: 'Manage Requisition and Issue Slips' },
        'reports': { title: 'Inventory Reports', subtitle: 'Generate inventory reports and analytics' },
        'profile': { title: 'Profile & Settings', subtitle: 'Manage your account information' }
    };
    
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    const titleEl = document.getElementById('pageTitle');
    const subtitleEl = document.getElementById('pageSubtitle');
    
    if (titleEl) {
        titleEl.textContent = pageInfo.title;
    }
    if (subtitleEl) {
        subtitleEl.textContent = pageInfo.subtitle;
    }
}

