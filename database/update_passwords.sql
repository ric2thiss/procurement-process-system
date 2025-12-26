-- ============================================================================
-- UPDATE PASSWORDS FOR EXISTING TEST USERS
-- Use this if you already imported test_users.sql with the wrong password hash
-- 
-- This will update all test users' passwords to: "password123"
-- Password hash: $2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6
-- ============================================================================

USE dts_db;

-- Update password for all test users
UPDATE users SET password_hash = '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6'
WHERE username IN (
    'teacher01',
    'supply01',
    'ppmp01',
    'prppmp01',
    'principal01',
    'budget01',
    'procurement01',
    'bookkeeper01',
    'payment01',
    'admin',
    'auditor01'
);

-- Verify the update
SELECT username, email, first_name, last_name, 
       (SELECT role_code FROM roles WHERE role_id = users.role_id) as role_code
FROM users 
WHERE username IN (
    'teacher01',
    'supply01',
    'ppmp01',
    'prppmp01',
    'principal01',
    'budget01',
    'procurement01',
    'bookkeeper01',
    'payment01',
    'admin',
    'auditor01'
)
ORDER BY username;

