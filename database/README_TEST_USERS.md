# Test Users for Document Tracking System

This document provides information about the test users created for testing the login system.

## Quick Setup

1. Make sure your database is created and the schema is loaded (`dts_schema.sql`)
2. Run the test users SQL file:
   ```sql
   source database/test_users.sql;
   ```
   Or import it through phpMyAdmin or your MySQL client.

## Test Users

All test users have the same password: **`password123`**

| Username | Role | Email | Dashboard |
|----------|------|-------|-----------|
| `teacher01` | Teacher / End-User | teacher01@school.edu | Teacher Dashboard |
| `supply01` | Supply Office Staff | supply01@school.edu | Supply Dashboard |
| `ppmp01` | PPMP Manager | ppmp01@school.edu | PPMP Dashboard |
| `prppmp01` | PR & PPMP Manager | prppmp01@school.edu | PR-PPMP Dashboard |
| `principal01` | Principal / School Head | principal01@school.edu | Principal Dashboard |
| `budget01` | Budget / Accounting Staff | budget01@school.edu | Budgeting Dashboard |
| `procurement01` | Procurement Office Staff | procurement01@school.edu | Procurement Dashboard |
| `bookkeeper01` | Bookkeeper | bookkeeper01@school.edu | Bookkeeper Dashboard |
| `payment01` | Payment / Disbursement Staff | payment01@school.edu | Payment Dashboard |
| `admin` | System Administrator | admin@school.edu | Admin Dashboard |
| `auditor01` | Auditor / Read-Only User | auditor01@school.edu | Audit Dashboard |

## Testing Login

1. Navigate to `http://localhost/dts/auth/login.php`
2. Enter any of the usernames above
3. Enter password: `password123`
4. You should be redirected to the appropriate dashboard for that role

## Security Note

⚠️ **IMPORTANT**: These are test users with weak passwords. **DO NOT** use these in production!

Before deploying to production:
- Change all passwords
- Use strong, unique passwords for each user
- Consider implementing password policies
- Review and update user permissions

## Creating New Test Users

To create additional test users, you can use this template:

```sql
INSERT INTO users (username, password_hash, email, first_name, last_name, role_id, is_active)
VALUES (
    'newusername',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password123
    'email@school.edu',
    'First',
    'Last',
    (SELECT role_id FROM roles WHERE role_code = 'ROLE_CODE' LIMIT 1),
    1
);
```

To generate a new password hash for a different password, use PHP:

```php
<?php
echo password_hash('your_password_here', PASSWORD_DEFAULT);
?>
```

## Troubleshooting

### User cannot login
- Verify the user exists: `SELECT * FROM users WHERE username = 'username';`
- Check if user is active: `SELECT is_active FROM users WHERE username = 'username';`
- Verify role exists: `SELECT * FROM roles WHERE role_code = 'ROLE_CODE';`

### Wrong dashboard redirect
- Check the role_code in the `roles` table matches the mapping in `includes/auth.php`
- Verify the dashboard file exists at the specified path

### Password not working
- The default password hash is for "password123"
- If you changed it, verify the hash matches the password using `password_verify()`

