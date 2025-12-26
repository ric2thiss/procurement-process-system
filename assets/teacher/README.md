# Teacher Dashboard - Modular JavaScript Structure

## Overview
This directory contains the modular JavaScript code for the Teacher Dashboard, organized by concern for better maintainability and readability.

## File Structure

```
assets/teacher/
├── main.js              # Main entry point - initializes all modules
├── utils.js             # Utility functions (escapeHtml, getStatusBadge, date formatting, messages)
├── navigation.js        # Navigation and section management
├── sections.js          # Section show/hide logic
├── dashboard.js         # Dashboard statistics and recent requests
├── forms.js             # Form handling (new request form)
├── requests.js          # My Requests functionality (search, filters, pagination)
├── modals.js            # Modal dialogs (request details)
├── tracking.js          # Document tracking functionality
├── history.js           # Request history with filters
├── issued-items.js      # Issued items (RIS) display
├── profile.js           # Profile and settings management
└── ui.js                # UI utilities (mobile sidebar, date/time)
```

## Module Responsibilities

### `main.js`
- Entry point for the application
- Initializes all modules on DOMContentLoaded
- Coordinates module initialization

### `utils.js`
- **escapeHtml()** - XSS prevention
- **getStatusBadge()** - Status badge HTML generation
- **formatDate()** - Date formatting
- **formatDateTime()** - DateTime formatting
- **showSuccessMessage()** - Success message display
- **showErrorMessage()** - Error message display

### `navigation.js`
- Navigation item click handlers
- Page title updates
- Anchor link handling
- Active state management

### `sections.js`
- Section visibility management
- Auto-loading data when sections are shown

### `dashboard.js`
- Dashboard statistics loading
- Recent requests display
- Dashboard initialization

### `forms.js`
- New supply request form handling
- Form validation
- Request type toggling
- Form submission

### `requests.js`
- My Requests table display
- Search functionality (debounced)
- Status and date filtering
- Pagination

### `modals.js`
- Request details modal
- Modal open/close
- Request details display

### `tracking.js`
- Document tracking search
- Tracking results display
- Timeline generation
- Status history display

### `history.js`
- Request history display
- History search and filters
- Completed/cancelled/rejected requests

### `issued-items.js`
- Issued items (RIS) display
- Item details view

### `profile.js`
- Profile information loading
- Profile update
- Password change
- Profile form handling

### `ui.js`
- Mobile sidebar toggle
- Date/time display
- UI utility functions

## Usage

The main.js file is loaded as an ES6 module:
```html
<script type="module" src="../../assets/teacher/main.js"></script>
```

All modules use ES6 import/export syntax for clean dependency management.

## Benefits

1. **Separation of Concerns** - Each module has a single responsibility
2. **Maintainability** - Easy to find and modify specific functionality
3. **Readability** - Smaller, focused files are easier to understand
4. **Testability** - Modules can be tested independently
5. **Reusability** - Utility functions can be reused across modules
6. **Scalability** - Easy to add new features without cluttering existing code

