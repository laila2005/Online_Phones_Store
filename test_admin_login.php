<?php
// Test script to diagnose admin login issues
define("SECURE_ACCESS", true);
include "includes/db_connect.php";

echo "<h1>Admin Login Diagnostic</h1>";
echo "<hr>";

// Test 1: Check database connection
echo "<h2>Test 1: Database Connection</h2>";
if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error . "<br>";
    exit;
} else {
    echo "✅ Connected to database successfully<br><br>";
}

// Test 2: Check if admin_users table exists
echo "<h2>Test 2: Check admin_users Table</h2>";
$result = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($result && $result->num_rows > 0) {
    echo "✅ Table 'admin_users' exists<br><br>";
} else {
    echo "❌ Table 'admin_users' NOT FOUND<br>";
    echo "<strong>Fix:</strong> Import database/fresh_setup.sql in phpMyAdmin<br><br>";
    exit;
}

// Test 3: Count admin users
echo "<h2>Test 3: Count Admin Users</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM admin_users");
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    echo "Found <strong>$count</strong> admin users in database<br><br>";
    
    if ($count == 0) {
        echo "❌ No admin users found!<br>";
        echo "<strong>Fix:</strong> Import database/fresh_setup.sql in phpMyAdmin<br><br>";
        exit;
    }
} else {
    echo "❌ Error querying admin_users: " . $conn->error . "<br><br>";
    exit;
}

// Test 4: List all admin users
echo "<h2>Test 4: List All Admin Users</h2>";
$result = $conn->query("SELECT id, username, email, role FROM admin_users");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['username'] . "</strong></td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ Could not retrieve admin users<br><br>";
}

// Test 5: Test password verification for 'admin' user
echo "<h2>Test 5: Test Password Verification</h2>";
$username = 'admin';
$password = 'admin123';

$stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo "Found user: <strong>" . $user['username'] . "</strong><br>";
    echo "Password hash in DB: <code>" . substr($user['password'], 0, 30) . "...</code><br>";
    
    // Test password verification
    if (password_verify($password, $user['password'])) {
        echo "✅ Password 'admin123' <strong>VERIFIED</strong> successfully!<br>";
        echo "<span style='color:green;'>Login should work with: admin / admin123</span><br><br>";
    } else {
        echo "❌ Password 'admin123' <strong>FAILED</strong> verification<br>";
        echo "<span style='color:red;'>Password hash might be incorrect</span><br><br>";
    }
} else {
    echo "❌ User 'admin' not found in database<br><br>";
}

// Test 6: Test if '1234' password exists
echo "<h2>Test 6: Check for '1234' Password</h2>";
$password_1234 = '1234';
$result = $conn->query("SELECT username FROM admin_users");
$found_1234 = false;

if ($result) {
    while($user = $result->fetch_assoc()) {
        $check_stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
        $check_stmt->bind_param("s", $user['username']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $user_data = $check_result->fetch_assoc();
        
        if (password_verify($password_1234, $user_data['password'])) {
            echo "✅ User '<strong>" . $user['username'] . "</strong>' has password '1234'<br>";
            $found_1234 = true;
        }
    }
    
    if (!$found_1234) {
        echo "❌ No users with password '1234' found<br>";
        echo "Note: You need to add user 'ahad' with the SQL script<br><br>";
    }
}

echo "<hr>";
echo "<h2>Summary & Recommendations</h2>";

// Check if fresh_setup.sql was imported
$result = $conn->query("SELECT COUNT(*) as count FROM admin_users");
$row = $result->fetch_assoc();
$user_count = $row['count'];

if ($user_count >= 3) {
    echo "<p style='color:green;'><strong>✅ Database appears to be properly imported</strong></p>";
    echo "<p>Try logging in with:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin | <strong>Password:</strong> admin123</li>";
    echo "<li><strong>Username:</strong> judy | <strong>Password:</strong> admin123</li>";
    echo "<li><strong>Username:</strong> habiba | <strong>Password:</strong> admin123</li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php'>Go to Login Page</a></p>";
} else {
    echo "<p style='color:red;'><strong>❌ Database needs to be imported</strong></p>";
    echo "<ol>";
    echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Click on 'ecommerce_db' database</li>";
    echo "<li>Click 'Import' tab</li>";
    echo "<li>Choose file: database/fresh_setup.sql</li>";
    echo "<li>Click 'Go'</li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}

$conn->close();
?>
