<?php
// Test admin login credentials
define("SECURE_ACCESS", true);
include 'includes/db_connect.php';

$username = 'lolo';

// Get user from database
$stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    echo "<h2>Testing Admin Login for: " . htmlspecialchars($username) . "</h2>";
    echo "<p><strong>Password hash in database:</strong><br>" . htmlspecialchars($user['password']) . "</p>";
    
    // Test different passwords
    $testPasswords = ['password', '12345678', 'admin', 'lolo'];
    
    echo "<h3>Testing passwords:</h3>";
    foreach ($testPasswords as $testPass) {
        $verify = password_verify($testPass, $user['password']);
        $plain = ($testPass === $user['password']);
        
        echo "<p><strong>Password: '$testPass'</strong><br>";
        echo "password_verify: " . ($verify ? "✅ MATCH" : "❌ NO MATCH") . "<br>";
        echo "Plain text match: " . ($plain ? "✅ MATCH" : "❌ NO MATCH") . "</p>";
    }
    
    // Generate new hash for 12345678
    $newHash = password_hash('12345678', PASSWORD_DEFAULT);
    echo "<hr>";
    echo "<h3>To set password to '12345678', run this SQL:</h3>";
    echo "<pre>UPDATE admin_users SET password = '$newHash' WHERE username = 'lolo';</pre>";
    
} else {
    echo "<p style='color:red;'>User '$username' not found in database!</p>";
}

$stmt->close();
$conn->close();
?>
