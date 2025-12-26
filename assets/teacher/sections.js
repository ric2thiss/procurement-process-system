/**
 * Section Management Module
 * Handles showing/hiding different sections of the dashboard
 */

import { loadMyRequests } from './requests.js';
import { loadIssuedItems } from './issued-items.js';
import { loadRequestHistory } from './history.js';
import { loadUserProfile } from './profile.js';

const STORAGE_KEY = 'teacher_dashboard_active_section';

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
        if (sectionId === 'my-requests') {
            loadMyRequests();
        } else if (sectionId === 'issued-items') {
            loadIssuedItems();
        } else if (sectionId === 'history') {
            loadRequestHistory();
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
        'dashboard': { title: 'Dashboard Overview', subtitle: 'Your supply request statistics' },
        'new-request': { title: 'New Supply Request', subtitle: 'Create and submit a new request' },
        'my-requests': { title: 'My Requests', subtitle: 'View and manage all your requests' },
        'tracking': { title: 'Track Document', subtitle: 'Monitor request progress' },
        'issued-items': { title: 'Issued Items', subtitle: 'Items issued from inventory' },
        'history': { title: 'Request History', subtitle: 'View past transactions' },
        'profile': { title: 'Profile & Settings', subtitle: 'Manage your account information' }
    };
    
    const pageInfo = titles[sectionId] || { title: 'Dashboard', subtitle: '' };
    const titleEl = document.getElementById('pageTitle');
    const subtitleEl = document.getElementById('pageSubtitle');
    
    if (titleEl) titleEl.textContent = pageInfo.title;
    if (subtitleEl) subtitleEl.textContent = pageInfo.subtitle;
}

