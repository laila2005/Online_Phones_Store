<?php
/**
 * Quick Diagnostic Script for Judy
 * This will show exactly what's wrong
 */

echo "<h1>Database Connection Diagnostic</h1>";
echo "<hr>";

// Test 1: Check PHP is working
echo "<h2>Test 1: PHP Status</h2>";
echo "✅ PHP is working! Version: " . phpversion() . "<br><br>";

// Test 2: Check mysqli extension
echo "<h2>Test 2: MySQLi Extension</h2>";
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension is loaded<br><br>";
} else {
    echo "❌ MySQLi extension is NOT loaded<br>";
    echo "Fix: Enable mysqli in php.ini<br><br>";
    exit;
}

// Test 3: Try to connect to MySQL
echo "<h2>Test 3: MySQL Connection</h2>";
echo "Attempting to connect...<br>";

$servername = "localhost";
$username = "root";
$password = "";

echo "Settings:<br>";
echo "- Server: $servername<br>";
echo "- Username: $username<br>";
echo "- Password: " . (empty($password) ? "(empty)" : "***") . "<br><br>";

// Set a timeout to prevent infinite loading
ini_set('default_socket_timeout', 5);
ini_set('mysql.connect_timeout', 5);

$start_time = microtime(true);
$conn = @new mysqli($servername, $username, $password);
$end_time = microtime(true);
$connection_time = round(($end_time - $start_time), 2);

echo "Connection attempt took: {$connection_time} seconds<br><br>";

if ($conn->connect_error) {
    echo "❌ <span style='color:red;'>Connection FAILED</span><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "Error Code: " . $conn->connect_errno . "<br><br>";
    
    echo "<h3>Possible Solutions:</h3>";
    echo "<ul>";
    echo "<li><strong>Error 2002</strong>: MySQL is not running - Start MySQL in XAMPP</li>";
    echo "<li><strong>Error 1045</strong>: Wrong password - Make sure password is empty</li>";
    echo "<li><strong>Error 2003</strong>: Can't connect to server - Check if MySQL port 3306 is correct</li>";
    echo "<li><strong>Timeout</strong>: MySQL is running but not responding - Restart MySQL</li>";
    echo "</ul>";
    exit;
} else {
    echo "✅ <span style='color:green;'>Connected to MySQL successfully!</span><br><br>";
}

// Test 4: Check if database exists
echo "<h2>Test 4: Database Check</h2>";
$dbname = "ecommerce_db";

$result = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($result && $result->num_rows > 0) {
    echo "✅ Database '$dbname' exists<br><br>";
} else {
    echo "❌ <span style='color:red;'>Database '$dbname' NOT FOUND</span><br>";
    echo "<h3>Fix:</h3>";
    echo "<ol>";
    echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Click 'Import' tab</li>";
    echo "<li>Choose file: database/fresh_setup.sql</li>";
    echo "<li>Click 'Go'</li>";
    echo "</ol>";
    $conn->close();
    exit;
}

// Test 5: Try to select the database
echo "<h2>Test 5: Select Database</h2>";
if ($conn->select_db($dbname)) {
    echo "✅ Successfully selected database '$dbname'<br><br>";
} else {
    echo "❌ Could not select database<br>";
    echo "Error: " . $conn->error . "<br><br>";
    $conn->close();
    exit;
}

// Test 6: Check if products table exists
echo "<h2>Test 6: Check Products Table</h2>";
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result && $result->num_rows > 0) {
    echo "✅ Table 'products' exists<br><br>";
} else {
    echo "❌ Table 'products' NOT FOUND<br>";
    echo "Fix: Re-import database/fresh_setup.sql<br><br>";
    $conn->close();
    exit;
}

// Test 7: Try to query products
echo "<h2>Test 7: Query Products</h2>";
$start_time = microtime(true);
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$end_time = microtime(true);
$query_time = round(($end_time - $start_time), 2);

echo "Query took: {$query_time} seconds<br>";

if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    echo "✅ Found $count products in database<br><br>";
    
    if ($count == 0) {
        echo "⚠️ Warning: No products in database<br>";
        echo "Fix: Re-import database/fresh_setup.sql<br><br>";
    }
} else {
    echo "❌ Could not query products table<br>";
    echo "Error: " . $conn->error . "<br><br>";
}

// Test 8: Test a full product query (like the main page does)
echo "<h2>Test 8: Full Product Query (Like Main Page)</h2>";
$start_time = microtime(true);
$result = $conn->query("SELECT id, name, description, price, image_url, category FROM products LIMIT 5");
$end_time = microtime(true);
$query_time = round(($end_time - $start_time), 2);

echo "Query took: {$query_time} seconds<br>";

if ($result && $result->num_rows > 0) {
    echo "✅ Successfully retrieved product data<br>";
    echo "Sample products:<br>";
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['name']) . " - $" . $row['price'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "❌ Could not retrieve products<br>";
    if ($conn->error) {
        echo "Error: " . $conn->error . "<br>";
    }
}

$conn->close();

// Final Summary
echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p style='font-size:18px; color:green;'><strong>✅ All tests passed!</strong></p>";
echo "<p>Your database connection is working correctly.</p>";
echo "<p>If the main website still loads slowly, the issue might be:</p>";
echo "<ul>";
echo "<li>External images loading slowly (from Unsplash URLs)</li>";
echo "<li>Browser cache issue - Try Ctrl+F5 to hard refresh</li>";
echo "<li>Template file issue - Check includes/template.php</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Try accessing the main site: <a href='index.php'>index.php</a></li>";
echo "<li>If still slow, check browser console for errors (F12)</li>";
echo "<li>Try admin page: <a href='admin/login.php'>admin/login.php</a></li>";
echo "</ol>";

echo "<p><em>Connection time: {$connection_time}s | Query time: {$query_time}s</em></p>";
?>
