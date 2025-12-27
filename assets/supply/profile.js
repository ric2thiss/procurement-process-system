/**
 * Profile Module
 * Handles user profile and settings functionality
 */

import { escapeHtml, formatDate } from './utils.js';

/**
 * Initialize Profile Section
 */
export function initializeProfile() {
    // Profile form submission
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateUserProfile();
        });
    }
    
    // Password form submission
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword();
        });
    }

    // Profile image upload
    const profileImageInput = document.getElementById('profileImageInput');
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                uploadProfileImage(e.target.files[0]);
            }
        });
    }
}

/**
 * Load User Profile
 */
export function loadUserProfile() {
    fetch('../../api/v1/shared/get_user_profile.php', {
        credentials: 'same-origin' // Include cookies/session
    })
        .then(response => {
            if (!response.ok) {
                // Try to parse error message from response
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.profile) {
                const profile = data.profile;
                
                // Populate form fields
                document.getElementById('profileFirstName').value = profile.first_name || '';
                document.getElementById('profileLastName').value = profile.last_name || '';
                document.getElementById('profileEmail').value = profile.email || '';
                document.getElementById('profilePhone').value = profile.phone || '';
                
                // Populate account information
                document.getElementById('profileUserId').textContent = profile.user_id || '-';
                document.getElementById('profileRole').textContent = profile.role_name || profile.role || '-';
                
                if (profile.created_at) {
                    document.getElementById('profileCreatedAt').textContent = formatDate(profile.created_at);
                } else {
                    document.getElementById('profileCreatedAt').textContent = '-';
                }

                // Display profile image
                updateProfileImageDisplay(profile.profile_image);
                // Also update sidebar profile image
                updateSidebarProfileImage(profile.profile_image);
            } else {
                showProfileError('Failed to load profile information.');
            }
        })
        .catch(error => {
            console.error('Error loading profile:', error);
            showProfileError('An error occurred while loading profile information.');
        });
}

/**
 * Update User Profile
 */
function updateUserProfile() {
    const form = document.getElementById('profileForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitButton.disabled = true;
    
    const formData = {
        first_name: document.getElementById('profileFirstName').value.trim(),
        last_name: document.getElementById('profileLastName').value.trim(),
        email: document.getElementById('profileEmail').value.trim(),
        phone: document.getElementById('profilePhone').value.trim()
    };
    
    fetch('../../api/v1/shared/update_user_profile.php', {
        method: 'POST',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(data => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            if (data.success) {
                showProfileSuccess('Profile updated successfully!');
                // Reload profile to get updated data
                setTimeout(() => {
                    loadUserProfile();
                }, 1000);
            } else {
                showProfileError(data.message || 'Failed to update profile.');
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            showProfileError('An error occurred while updating profile.');
        });
}

/**
 * Change Password
 */
function changePassword() {
    const form = document.getElementById('passwordForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Client-side validation
    if (newPassword !== confirmPassword) {
        showProfileError('New password and confirm password do not match.');
        return;
    }
    
    if (newPassword.length < 8) {
        showProfileError('New password must be at least 8 characters long.');
        return;
    }
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Changing...';
    submitButton.disabled = true;
    
    const formData = {
        current_password: currentPassword,
        new_password: newPassword,
        confirm_password: confirmPassword
    };
    
    fetch('../../api/v1/shared/change_password.php', {
        method: 'POST',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(data => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            if (data.success) {
                showProfileSuccess('Password changed successfully!');
                // Clear password fields
                form.reset();
            } else {
                showProfileError(data.message || 'Failed to change password.');
            }
        })
        .catch(error => {
            console.error('Error changing password:', error);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            showProfileError('An error occurred while changing password.');
        });
}

/**
 * Show Profile Success Message
 * @param {string} message - Success message
 */
function showProfileSuccess(message) {
    const profileSection = document.getElementById('profile-section');
    if (!profileSection) return;
    
    // Remove existing messages
    const existingMsg = profileSection.querySelector('.profile-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'profile-message bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded';
    messageDiv.innerHTML = `<div class="flex items-start"><i class="fas fa-check-circle mr-2 mt-1"></i><span>${escapeHtml(message)}</span></div>`;
    
    const container = profileSection.querySelector('.bg-white.rounded-lg.shadow-md');
    if (container) {
        const firstElement = container.firstElementChild;
        if (firstElement) {
            firstElement.insertAdjacentElement('afterend', messageDiv);
        } else {
            container.prepend(messageDiv);
        }
    }
    
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

/**
 * Upload Profile Image
 * @param {File} file - Image file to upload
 */
function uploadProfileImage(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showProfileError('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        return;
    }

    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        showProfileError('File size exceeds maximum allowed size of 5MB.');
        return;
    }

    const formData = new FormData();
    formData.append('profile_image', file);

    const loadingIndicator = document.getElementById('profileImageLoading');
    const removeBtn = document.getElementById('removeProfileImageBtn');
    
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
    
    fetch('../../api/v1/shared/upload_profile_image.php', {
        method: 'POST',
        credentials: 'same-origin', // Include cookies/session
        body: formData
    })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, get text response for debugging
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                });
            }
        })
        .then(data => {
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            
            if (data.success) {
                showProfileSuccess('Profile image uploaded successfully!');
                // Update image display
                if (data.image_path) {
                    updateProfileImageDisplay(data.image_path);
                    // Also update sidebar profile image
                    updateSidebarProfileImage(data.image_path);
                }
                // Show remove button
                if (removeBtn) removeBtn.classList.remove('hidden');
            } else {
                showProfileError(data.message || 'Failed to upload profile image.');
            }
        })
        .catch(error => {
            console.error('Error uploading profile image:', error);
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            showProfileError('An error occurred while uploading profile image: ' + (error.message || 'Unknown error'));
        });
}

/**
 * Load Sidebar Profile Image
 * Loads the profile image for the sidebar on page initialization
 */
export function loadSidebarProfileImage() {
    fetch('../../api/v1/shared/get_user_profile.php', {
        credentials: 'same-origin' // Include cookies/session
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.profile) {
                const profileImage = data.profile.profile_image || null;
                updateSidebarProfileImage(profileImage);
            }
        })
        .catch(error => {
            console.error('Error loading sidebar profile image:', error);
        });
}

/**
 * Update Sidebar Profile Image
 * @param {string} imagePath - Path to the profile image
 */
export function updateSidebarProfileImage(imagePath) {
    const sidebarImage = document.getElementById('sidebarProfileImage');
    const sidebarPlaceholder = document.getElementById('sidebarProfileImagePlaceholder');

    if (!sidebarImage || !sidebarPlaceholder) return;

    if (imagePath && imagePath.trim() !== '') {
        // Add timestamp to prevent caching issues
        const imageUrl = '../../' + imagePath + '?t=' + Date.now();
        
        // Set up error handler before setting src
        sidebarImage.onerror = function() {
            sidebarImage.classList.add('hidden');
            sidebarPlaceholder.classList.remove('hidden');
        };

        // Set up load handler
        sidebarImage.onload = function() {
            sidebarImage.classList.remove('hidden');
            sidebarPlaceholder.classList.add('hidden');
        };

        // Set the image source (this will trigger onload or onerror)
        sidebarImage.src = imageUrl;
    } else {
        // No image, show placeholder
        sidebarImage.classList.add('hidden');
        sidebarPlaceholder.classList.remove('hidden');
    }
}

/**
 * Update Profile Image Display
 * @param {string} imagePath - Path to the profile image
 */
function updateProfileImageDisplay(imagePath) {
    const imageDisplay = document.getElementById('profileImageDisplay');
    const imagePlaceholder = document.getElementById('profileImagePlaceholder');
    const removeBtn = document.getElementById('removeProfileImageBtn');

    if (!imageDisplay || !imagePlaceholder) return;

    if (imagePath && imagePath.trim() !== '') {
        // Add timestamp to prevent caching issues
        const imageUrl = '../../' + imagePath + '?t=' + Date.now();
        
        // Set up error handler before setting src
        imageDisplay.onerror = function() {
            imageDisplay.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            if (removeBtn) removeBtn.classList.add('hidden');
        };

        // Set up load handler
        imageDisplay.onload = function() {
            imageDisplay.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
        };

        // Set the image source (this will trigger onload or onerror)
        imageDisplay.src = imageUrl;
        
        // Show remove button if image exists
        if (removeBtn) removeBtn.classList.remove('hidden');
    } else {
        // No image, show placeholder
        imageDisplay.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
        if (removeBtn) removeBtn.classList.add('hidden');
    }
}

/**
 * Remove Profile Image
 */
window.removeProfileImage = function() {
    if (!confirm('Are you sure you want to remove your profile image?')) {
        return;
    }

    const loadingIndicator = document.getElementById('profileImageLoading');
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');

    fetch('../../api/v1/shared/upload_profile_image.php', {
        method: 'DELETE',
        credentials: 'same-origin', // Include cookies/session
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            
            if (data.success) {
                showProfileSuccess('Profile image removed successfully!');
                updateProfileImageDisplay(null);
                // Also update sidebar profile image
                updateSidebarProfileImage(null);
                // Clear file input
                const imageInput = document.getElementById('profileImageInput');
                if (imageInput) imageInput.value = '';
            } else {
                showProfileError(data.message || 'Failed to remove profile image.');
            }
        })
        .catch(error => {
            console.error('Error removing profile image:', error);
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            showProfileError('An error occurred while removing profile image.');
        });
};

/**
 * Show Profile Error Message
 * @param {string} message - Error message
 */
function showProfileError(message) {
    const profileSection = document.getElementById('profile-section');
    if (!profileSection) return;
    
    // Remove existing messages
    const existingMsg = profileSection.querySelector('.profile-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'profile-message bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded';
    messageDiv.innerHTML = `<div class="flex items-start"><i class="fas fa-exclamation-circle mr-2 mt-1"></i><span>${escapeHtml(message)}</span></div>`;
    
    const container = profileSection.querySelector('.bg-white.rounded-lg.shadow-md');
    if (container) {
        const firstElement = container.firstElementChild;
        if (firstElement) {
            firstElement.insertAdjacentElement('afterend', messageDiv);
        } else {
            container.prepend(messageDiv);
        }
    }
    
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 8000);
}

