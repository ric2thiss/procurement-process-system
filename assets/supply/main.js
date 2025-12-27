/**
 * Supply Office Dashboard - Main Entry Point
 * Initializes all modules and coordinates the dashboard functionality
 */

// Import modules
import { initializeDashboard, loadDashboardData, loadSidebarBadge } from './dashboard.js';
import { initializeNavigation, restoreSavedSection } from './navigation.js';
import { initializeDateTime, initializeMobileSidebar } from './ui.js';
import { initializeSupplyRequests } from './requests.js';
import { initializeInventory } from './inventory.js';
import { initializeProfile, loadSidebarProfileImage } from './profile.js';
import { clearActiveSection } from './sections.js';
// Import modals to ensure global functions are available
import './modals.js';

/**
 * Initialize Supply Office Dashboard
 * Called when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize core functionality
        initializeNavigation();
        initializeDateTime();
        initializeMobileSidebar();
        
        // Initialize section-specific functionality
        initializeSupplyRequests();
        initializeInventory();
        initializeProfile();
        
        // Load sidebar profile image on initialization
        loadSidebarProfileImage();
        
        // Handle logout - clear localStorage before redirecting
        const logoutLinks = document.querySelectorAll('a[href*="logout.php"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Clear the saved section from localStorage
                clearActiveSection();
                // Allow the default navigation to proceed
            });
        });
        
        // Load sidebar badge on initialization (regardless of section)
        loadSidebarBadge();
        
        // Restore saved section or default to dashboard
        const savedSection = restoreSavedSection();
        if (!savedSection) {
            // Default to dashboard if no saved section
            initializeDashboard();
        } else {
            // Load dashboard data if restored section is dashboard
            if (savedSection === 'dashboard') {
                loadDashboardData();
            }
        }
        
        console.log('Supply Office Dashboard initialized successfully');
    } catch (error) {
        console.error('Error initializing Supply Office Dashboard:', error);
    }
});

