/**
 * Teacher Dashboard - Main Entry Point
 * Initializes all modules and coordinates the dashboard functionality
 */

// Import modules
import { initializeDashboard, loadDashboardData } from './dashboard.js';
import { initializeNavigation, restoreSavedSection } from './navigation.js';
import { initializeForm } from './forms.js';
import { initializeDateTime, initializeMobileSidebar } from './ui.js';
import { initializeMyRequests } from './requests.js';
import { initializeRequestHistory } from './history.js';
import { initializeProfile, loadSidebarProfileImage } from './profile.js';
import { clearActiveSection } from './sections.js';

/**
 * Initialize Teacher Dashboard
 * Called when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize core functionality
        initializeNavigation();
        initializeForm();
        initializeDateTime();
        initializeMobileSidebar();
        
        // Initialize section-specific functionality
        initializeMyRequests();
        initializeRequestHistory();
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
        
        // Restore saved section or default to dashboard
        const savedSection = restoreSavedSection();
        if (!savedSection) {
            // Default to dashboard if no saved section
            initializeDashboard();
            loadDashboardData();
        } else {
            // Load dashboard data if restored section is dashboard
            if (savedSection === 'dashboard') {
                loadDashboardData();
            }
        }
        
        console.log('Teacher Dashboard initialized successfully');
    } catch (error) {
        console.error('Error initializing Teacher Dashboard:', error);
    }
});

