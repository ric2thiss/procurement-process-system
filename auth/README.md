# Authentication System Setup

This directory contains the authentication system for the Document Tracking System.

## Files

- **login.php** - Main login page with form processing
- **logout.php** - Logout handler that destroys session and redirects

## Backend Files

The authentication logic is separated into the following files:

- **config/database.php** - Database connection configuration
- **includes/auth.php** - Authentication logic (login, validation, role mapping)
- **includes/session.php** - Session management helpers

## Database Configuration

Before using the login system, you need to configure your database connection in `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dts_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## User Setup

To create a user account, you need to insert a user into the database. Here's a sample SQL:

```sql
-- First, ensure you have roles in the roles table
INSERT INTO roles (role_code, role_name, description) VALUES
('ADMIN', 'System Administrator', 'Full system access'),
('TEACHER', 'Teacher / End-User', 'Request initiation'),
('SUPPLY', 'Supply Office Staff', 'Inventory management'),
('PPMP_MGR', 'PPMP Manager', 'PPMP & APP management'),
('PR_PPMP_MGR', 'PR & PPMP Manager', 'PR creation & validation'),
('PRINCIPAL', 'Principal / School Head', 'Executive approval'),
('BUDGET', 'Budget / Accounting Staff', 'Budget verification & ORS'),
('PROCUREMENT', 'Procurement Office Staff', 'Procurement execution'),
('BOOKKEEPER', 'Bookkeeper', 'DV preparation'),
('PAYMENT', 'Payment / Disbursement Staff', 'Payment processing'),
('AUDITOR', 'Auditor / Read-Only User', 'Audit & compliance')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- Create a test admin user (password: admin123)
-- IMPORTANT: Change this password after first login!
INSERT INTO users (username, password_hash, email, first_name, last_name, role_id, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin@school.edu',
    'System',
    'Administrator',
    (SELECT role_id FROM roles WHERE role_code = 'ADMIN' LIMIT 1),
    1
);
```

## Password Hashing

Passwords are hashed using PHP's `password_hash()` function with bcrypt. To create a password hash:

```php
$password = 'your_password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
```

## Role-Based Dashboard Redirects

After successful login, users are redirected to their role-specific dashboard:

- **TEACHER** → `module/teacher/teacher_dashboard.html`
- **SUPPLY** → `module/supply-office/supply_dashboard.html`
- **PPMP_MGR** → `module/ppmp-management/ppmp_dashboard.html`
- **PR_PPMP_MGR** → `module/purchase-request-ppmp/pr_ppmp_dashboard.html`
- **PRINCIPAL** → `module/principal/principal_dashboard.html`
- **BUDGET** → `module/budgeting-accounting/budgeting_dashboard.html`
- **PROCUREMENT** → `module/procurement-office/procurement_dashboard.html`
- **BOOKKEEPER** → `module/bookkeeper/bookkeeper_dashboard.html`
- **PAYMENT** → `module/payment-disbursement/payment_dashboard.html`
- **ADMIN** → `module/admin/admin_dashboard.html`
- **AUDITOR** → `module/document-tracking-audit/audit_dashboard.html`

## Security Features

- ✅ Password hashing with bcrypt
- ✅ Prepared statements (SQL injection prevention)
- ✅ Session security (httponly cookies)
- ✅ Input validation
- ✅ Error handling
- ✅ XSS protection (htmlspecialchars)

## Usage

1. Navigate to `/auth/login.php`
2. Enter username/email and password
3. Upon successful login, you'll be redirected to your role-specific dashboard
4. To logout, navigate to `/auth/logout.php` or implement a logout button in your dashboard

## Troubleshooting

### Database Connection Error
- Check your database credentials in `config/database.php`
- Ensure MySQL/MariaDB is running
- Verify the database `dts_db` exists

### Login Fails
- Verify the user exists in the database
- Check that `is_active = 1` for the user
- Ensure the password hash is correct
- Check PHP error logs for detailed error messages

### Session Issues
- Ensure PHP sessions are enabled
- Check file permissions on session directory
- Verify session configuration in `includes/session.php`

