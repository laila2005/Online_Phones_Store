<?php
/**
 * Database Connection Test Script
 * 
 * This script tests your MySQL connection and helps diagnose issues.
 * Access it at: http://localhost/Online_Phones_Store/test_connection.php
 */

echo "<h1>MySQL Connection Test</h1>";
echo "<hr>";

// Test 1: Check if mysqli extension is loaded
echo "<h2>Test 1: PHP mysqli Extension</h2>";
if (extension_loaded('mysqli')) {
    echo "✅ <span style='color:green;'>mysqli extension is loaded</span><br>";
} else {
    echo "❌ <span style='color:red;'>mysqli extension is NOT loaded</span><br>";
    echo "Fix: Enable mysqli in php.ini<br>";
    exit;
}

// Test 2: Try to connect with default XAMPP settings
echo "<hr><h2>Test 2: Connection with Default Settings</h2>";
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP has no password

echo "Attempting to connect with:<br>";
echo "- Host: <strong>$servername</strong><br>";
echo "- Username: <strong>$username</strong><br>";
echo "- Password: <strong>" . (empty($password) ? "(empty)" : "***") . "</strong><br><br>";

$conn = @new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    echo "❌ <span style='color:red;'>Connection FAILED</span><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "Error Code: " . $conn->connect_errno . "<br><br>";
    
    echo "<h3>Possible Solutions:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running in XAMPP Control Panel</li>";
    echo "<li>If you set a password, update the \$password variable in this file</li>";
    echo "<li>Check if port 3306 is available</li>";
    echo "<li>See TROUBLESHOOTING_MYSQL.md for detailed fixes</li>";
    echo "</ul>";
    exit;
} else {
    echo "✅ <span style='color:green;'>Connection SUCCESSFUL!</span><br>";
}

// Test 3: Check if database exists
echo "<hr><h2>Test 3: Check Database</h2>";
$dbname = "ecommerce_db";
$result = $conn->query("SHOW DATABASES LIKE '$dbname'");

if ($result->num_rows > 0) {
    echo "✅ <span style='color:green;'>Database '$dbname' exists</span><br>";
} else {
    echo "❌ <span style='color:red;'>Database '$dbname' NOT found</span><br>";
    echo "Fix: Import database/schema.sql via phpMyAdmin<br>";
    $conn->close();
    exit;
}

// Test 4: Connect to the database
echo "<hr><h2>Test 4: Select Database</h2>";
if ($conn->select_db($dbname)) {
    echo "✅ <span style='color:green;'>Successfully selected database '$dbname'</span><br>";
} else {
    echo "❌ <span style='color:red;'>Could not select database</span><br>";
    echo "Error: " . $conn->error . "<br>";
    $conn->close();
    exit;
}

// Test 5: Check if tables exist
echo "<hr><h2>Test 5: Check Tables</h2>";
$tables = ['products', 'admin_users'];
$allTablesExist = true;

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✅ <span style='color:green;'>Table '$table' exists</span><br>";
    } else {
        echo "❌ <span style='color:red;'>Table '$table' NOT found</span><br>";
        $allTablesExist = false;
    }
}

if (!$allTablesExist) {
    echo "<br>Fix: Re-import database/schema.sql<br>";
}

// Test 6: Check products data
echo "<hr><h2>Test 6: Check Products Data</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    if ($count > 0) {
        echo "✅ <span style='color:green;'>Found $count products in database</span><br>";
    } else {
        echo "⚠️ <span style='color:orange;'>No products found (table is empty)</span><br>";
        echo "Fix: Import database/schema.sql to add sample products<br>";
    }
} else {
    echo "❌ <span style='color:red;'>Could not query products table</span><br>";
    echo "Error: " . $conn->error . "<br>";
}

// Test 7: Check your db_connect.php file
echo "<hr><h2>Test 7: Your Configuration</h2>";
echo "Your <strong>includes/db_connect.php</strong> should have:<br>";
echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ddd;'>";
echo "<?php\n";
echo "\$servername = \"localhost\";\n";
echo "\$username = \"root\";\n";
echo "\$password = \"\";  // Empty for default XAMPP\n";
echo "\$dbname = \"ecommerce_db\";\n";
echo "</pre>";

// Final summary
echo "<hr><h2>Summary</h2>";
if ($conn->ping()) {
    echo "✅ <span style='color:green; font-size:18px;'><strong>All tests passed! Your database is working correctly.</strong></span><br><br>";
    echo "You can now access your website at: <a href='index.php'>http://localhost/Online_Phones_Store/</a><br>";
} else {
    echo "⚠️ <span style='color:orange;'>Some issues detected. Please review the errors above.</span><br>";
}

$conn->close();

echo "<hr>";
echo "<p><em>After fixing any issues, refresh this page to test again.</em></p>";
echo "<p><strong>Note:</strong> Delete this file after testing for security reasons.</p>";
?>
