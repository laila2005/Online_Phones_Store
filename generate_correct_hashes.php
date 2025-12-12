<?php
// Generate correct password hashes

echo "<h1>Generate Correct Password Hashes</h1>";
echo "<hr>";

// Password: admin123
$password_admin123 = 'admin123';
$hash_admin123 = password_hash($password_admin123, PASSWORD_DEFAULT);

echo "<h2>Password: admin123</h2>";
echo "Hash: <code>$hash_admin123</code><br><br>";

// Password: 1234
$password_1234 = '1234';
$hash_1234 = password_hash($password_1234, PASSWORD_DEFAULT);

echo "<h2>Password: 1234</h2>";
echo "Hash: <code>$hash_1234</code><br><br>";

echo "<hr>";
echo "<h2>SQL to Fix Admin Users</h2>";
echo "<p>Copy and paste this into phpMyAdmin SQL tab:</p>";
echo "<textarea rows='15' cols='100' style='font-family: monospace;'>";
echo "-- Fix admin user passwords\n\n";
echo "-- Set password 'admin123' for admin, judy, habiba\n";
echo "UPDATE admin_users SET password = '$hash_admin123' WHERE username = 'admin';\n";
echo "UPDATE admin_users SET password = '$hash_admin123' WHERE username = 'judy';\n";
echo "UPDATE admin_users SET password = '$hash_admin123' WHERE username = 'habiba';\n\n";
echo "-- Add new user 'ahad' with password '1234'\n";
echo "INSERT INTO admin_users (username, password, email, full_name, role) \n";
echo "VALUES ('ahad', '$hash_1234', 'ahad@phonesstore.com', 'Ahad Admin', 'admin')\n";
echo "ON DUPLICATE KEY UPDATE password = '$hash_1234';\n";
echo "</textarea>";

echo "<hr>";
echo "<h2>Test Password Verification</h2>";

// Test the hash we just generated
if (password_verify('admin123', $hash_admin123)) {
    echo "✅ Hash for 'admin123' verified successfully<br>";
} else {
    echo "❌ Hash verification failed<br>";
}

if (password_verify('1234', $hash_1234)) {
    echo "✅ Hash for '1234' verified successfully<br>";
} else {
    echo "❌ Hash verification failed<br>";
}
?>
