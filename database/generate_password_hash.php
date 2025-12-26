<?php
/**
 * Password Hash Generator
 * Utility script to generate password hashes for test users
 * 
 * Usage: php generate_password_hash.php [password]
 * If no password is provided, it will use "password123" as default
 */

$password = $argv[1] ?? 'password123';

echo "Generating password hash for: " . $password . "\n";
echo "Hash: " . password_hash($password, PASSWORD_DEFAULT) . "\n";
echo "\n";
echo "To verify this hash works, use:\n";
echo "password_verify('" . $password . "', 'HASH_HERE');\n";
echo "\n";
echo "SQL INSERT example:\n";
echo "INSERT INTO users (username, password_hash, ...) VALUES (\n";
echo "    'username',\n";
echo "    '" . password_hash($password, PASSWORD_DEFAULT) . "',\n";
echo "    ...\n";
echo ");\n";

