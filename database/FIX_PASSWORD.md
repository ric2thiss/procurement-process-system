# Fix Password Issue

If you're getting "wrong password" error, follow these steps:

## Option 1: Update Existing Users (Recommended)

If you already imported the test users, just update their passwords:

```sql
-- Run this SQL file:
source database/update_passwords.sql;
```

Or copy and paste the SQL from `database/update_passwords.sql` into phpMyAdmin.

## Option 2: Delete and Re-import

1. Delete existing test users:
```sql
DELETE FROM users WHERE username IN (
    'teacher01', 'supply01', 'ppmp01', 'prppmp01', 'principal01',
    'budget01', 'procurement01', 'bookkeeper01', 'payment01',
    'admin', 'auditor01'
);
```

2. Re-import the updated `test_users.sql` file

## Option 3: Update Single User

To update just one user's password:

```sql
UPDATE users 
SET password_hash = '$2y$10$gUBsoW5XzSsSydZwQZTW7uTPCp9XkI6Q8eIjnuupdDa4xDKQ6c1Y6'
WHERE username = 'admin';
```

## Verify Password Works

After updating, test login with:
- Username: `admin` (or any test username)
- Password: `password123`

## Generate New Password Hash

If you want to use a different password, generate a new hash:

```bash
php database/generate_password_hash.php your_new_password
```

Then update the user:
```sql
UPDATE users 
SET password_hash = 'GENERATED_HASH_HERE'
WHERE username = 'username';
```

