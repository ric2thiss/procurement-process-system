/**
 * Navigation Module
 * Handles section navigation and page title updates
 */

import { showSection, getActiveSection, clearActiveSection } from './sections.js';

/**
 * Initialize navigation system
 */
export function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Show section (this will save to localStorage, update nav, and update page title)
            showSection(section);
        });
    });
    
    // Handle anchor links (like #new-request)
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                const sectionId = href.substring(1);
                const navItem = document.querySelector(`[data-section="${sectionId}"]`);
                if (navItem) {
                    e.preventDefault();
                    navItem.click();
                }
            }
        });
    });
}

/**
 * Restore saved section from localStorage
 * @returns {string|null} The restored section ID or null if not found/invalid
 */
export function restoreSavedSection() {
    const savedSection = getActiveSection();
    
    if (savedSection) {
        // Validate that the section exists
        const targetSection = document.getElementById(savedSection + '-section');
        if (targetSection) {
            // Show the saved section (this will update nav and page title)
            showSection(savedSection);
            
            return savedSection;
        } else {
            // Invalid section, clear it
            console.warn('Saved section not found, clearing localStorage');
            clearActiveSection();
        }
    }
    
    return null;
}

/**
 * Navigate to New Request Section
 */
export function navigateToNewRequest() {
    const navItem = document.querySelector('[data-section="new-request"]');
    if (navItem) {
        navItem.click();
    } else {
        showSection('new-request');
    }
}


// Make globally accessible
window.navigateToNewRequest = navigateToNewRequest;

