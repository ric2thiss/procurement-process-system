-- ============================================================================
-- FIX PASSWORD FOR TEST USERS
-- Run this to update all test users' passwords to: "password123"
-- ============================================================================

USE dts_db;

UPDATE users 
SET password_hash = '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6'
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

