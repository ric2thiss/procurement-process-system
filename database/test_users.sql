-- ============================================================================
-- TEST USERS FOR DOCUMENT TRACKING SYSTEM
-- Magallanes National High School
-- 
-- This file creates test users for each role module for testing purposes.
-- Default password for all test users: "password123"
-- 
-- IMPORTANT: Change these passwords in production!
-- ============================================================================

USE dts_db;

-- ============================================================================
-- 1. INSERT ROLES (if not already exists)
-- ============================================================================

INSERT INTO roles (role_code, role_name, description, is_active) VALUES
('TEACHER', 'Teacher / End-User', 'Request initiation', 1),
('SUPPLY', 'Supply Office Staff', 'Inventory management', 1),
('PPMP_MGR', 'PPMP Manager', 'PPMP & APP management', 1),
('PR_PPMP_MGR', 'Purchase Request & PPMP Manager', 'PR creation & validation', 1),
('PRINCIPAL', 'Principal / School Head', 'Executive approval', 1),
('BUDGET', 'Budget / Accounting Staff', 'Budget verification & ORS', 1),
('PROCUREMENT', 'Procurement Office Staff', 'Procurement execution', 1),
('BOOKKEEPER', 'Bookkeeper', 'DV preparation', 1),
('PAYMENT', 'Payment / Disbursement Staff', 'Payment processing', 1),
('ADMIN', 'System Administrator', 'Full system access', 1),
('AUDITOR', 'Auditor / Read-Only User', 'Audit & compliance', 1)
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name), description = VALUES(description);

-- ============================================================================
-- 2. INSERT OFFICES (if not already exists)
-- ============================================================================

INSERT INTO offices (office_code, office_name, office_type, description, is_active) VALUES
('SUPPLY_OFF', 'Supply Office', 'Supply Office', 'Manages inventory and supply requests', 1),
('BUDGET_OFF', 'Budget Office', 'Budget Office', 'Manages budget allocations and ORS', 1),
('ACCT_OFF', 'Accounting Office', 'Accounting Office', 'Handles accounting and financial matters', 1),
('PROC_OFF', 'Procurement Office', 'Procurement Office', 'Manages procurement activities', 1),
('PRIN_OFF', 'Principal Office', 'Principal Office', 'School administration', 1),
('BOOK_OFF', 'Bookkeeper Office', 'Bookkeeper Office', 'Handles bookkeeping and DV preparation', 1),
('PAY_OFF', 'Payment Office', 'Payment Office', 'Processes payments and disbursements', 1),
('ADMIN_OFF', 'Administration Office', 'Other', 'System administration', 1),
('AUDIT_OFF', 'Audit Office', 'Other', 'Audit and compliance', 1)
ON DUPLICATE KEY UPDATE office_name = VALUES(office_name), office_type = VALUES(office_type);

-- ============================================================================
-- 3. INSERT TEST USERS
-- Default password for all users: "password123"
-- Password hash: $2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6
-- ============================================================================

-- Teacher / End-User
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('teacher01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'teacher01@school.edu', 'Juan', 'Dela Cruz', 'M', 'EMP001', 
 (SELECT role_id FROM roles WHERE role_code = 'TEACHER' LIMIT 1), NULL, 'Mathematics', 'Teacher', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Supply Office Staff
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('supply01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'supply01@school.edu', 'Maria', 'Santos', 'R', 'EMP002',
 (SELECT role_id FROM roles WHERE role_code = 'SUPPLY' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'SUPPLY_OFF' LIMIT 1), 'Supply Office', 'Supply Officer', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- PPMP Manager
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('ppmp01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'ppmp01@school.edu', 'Carlos', 'Reyes', 'A', 'EMP003',
 (SELECT role_id FROM roles WHERE role_code = 'PPMP_MGR' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'PROC_OFF' LIMIT 1), 'Procurement Office', 'PPMP Manager', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Purchase Request & PPMP Manager
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('prppmp01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'prppmp01@school.edu', 'Ana', 'Garcia', 'L', 'EMP004',
 (SELECT role_id FROM roles WHERE role_code = 'PR_PPMP_MGR' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'PROC_OFF' LIMIT 1), 'Procurement Office', 'PR & PPMP Manager', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Principal / School Head
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('principal01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'principal01@school.edu', 'Roberto', 'Villanueva', 'S', 'EMP005',
 (SELECT role_id FROM roles WHERE role_code = 'PRINCIPAL' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'PRIN_OFF' LIMIT 1), 'Administration', 'School Principal', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Budget / Accounting Staff
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('budget01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'budget01@school.edu', 'Liza', 'Fernandez', 'M', 'EMP006',
 (SELECT role_id FROM roles WHERE role_code = 'BUDGET' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'BUDGET_OFF' LIMIT 1), 'Budget Office', 'Budget Officer', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Procurement Office Staff
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('procurement01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'procurement01@school.edu', 'Jose', 'Torres', 'C', 'EMP007',
 (SELECT role_id FROM roles WHERE role_code = 'PROCUREMENT' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'PROC_OFF' LIMIT 1), 'Procurement Office', 'Procurement Officer', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Bookkeeper
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('bookkeeper01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'bookkeeper01@school.edu', 'Patricia', 'Lopez', 'D', 'EMP008',
 (SELECT role_id FROM roles WHERE role_code = 'BOOKKEEPER' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'BOOK_OFF' LIMIT 1), 'Accounting Office', 'Bookkeeper', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Payment / Disbursement Staff
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('payment01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'payment01@school.edu', 'Michael', 'Cruz', 'B', 'EMP009',
 (SELECT role_id FROM roles WHERE role_code = 'PAYMENT' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'PAY_OFF' LIMIT 1), 'Payment Office', 'Payment Officer', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- System Administrator
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('admin', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'admin@school.edu', 'System', 'Administrator', NULL, 'EMP010',
 (SELECT role_id FROM roles WHERE role_code = 'ADMIN' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'ADMIN_OFF' LIMIT 1), 'IT Department', 'System Administrator', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Auditor / Read-Only User
INSERT INTO users (username, password_hash, email, first_name, last_name, middle_name, employee_id, role_id, office_id, department, position, is_active) VALUES
('auditor01', '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6', 'auditor01@school.edu', 'Susan', 'Mendoza', 'P', 'EMP011',
 (SELECT role_id FROM roles WHERE role_code = 'AUDITOR' LIMIT 1), 
 (SELECT office_id FROM offices WHERE office_code = 'AUDIT_OFF' LIMIT 1), 'Audit Office', 'Auditor', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- ============================================================================
-- TEST USERS SUMMARY
-- ============================================================================
-- 
-- All test users have the same password: "password123"
-- 
-- Username          | Role              | Dashboard
-- ------------------|-------------------|---------------------------------------
-- teacher01         | Teacher           | module/teacher/teacher_dashboard.html
-- supply01          | Supply Office     | module/supply-office/supply_dashboard.html
-- ppmp01            | PPMP Manager      | module/ppmp-management/ppmp_dashboard.html
-- prppmp01          | PR & PPMP Manager | module/purchase-request-ppmp/pr_ppmp_dashboard.html
-- principal01       | Principal         | module/principal/principal_dashboard.html
-- budget01          | Budget/Accounting | module/budgeting-accounting/budgeting_dashboard.html
-- procurement01     | Procurement       | module/procurement-office/procurement_dashboard.html
-- bookkeeper01      | Bookkeeper        | module/bookkeeper/bookkeeper_dashboard.html
-- payment01         | Payment           | module/payment-disbursement/payment_dashboard.html
-- admin             | Administrator    | module/admin/admin_dashboard.html
-- auditor01         | Auditor           | module/document-tracking-audit/audit_dashboard.html
-- 
-- ============================================================================

