<?php
session_start();

echo "<h1>Session Debug</h1>";
echo "<hr>";

echo "<h2>Current Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session Status:</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . " (2 = active)<br>";
echo "Session Name: " . session_name() . "<br>";

echo "<hr>";
echo "<h2>Test Login Session</h2>";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "✅ <span style='color:green;'>You are LOGGED IN</span><br>";
    echo "Admin ID: " . (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'Not set') . "<br>";
    echo "Username: " . (isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Not set') . "<br>";
    echo "Role: " . (isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Not set') . "<br>";
    echo "<br>";
    echo "<a href='admin/products.php'>Go to Products Page</a><br>";
    echo "<a href='admin/logout.php'>Logout</a>";
} else {
    echo "❌ <span style='color:red;'>You are NOT logged in</span><br>";
    echo "<br>";
    echo "<a href='admin/login.php'>Go to Login Page</a>";
}

echo "<hr>";
echo "<h2>Manual Session Test</h2>";
echo "<p>Click button to manually set session:</p>";
echo "<form method='POST'>";
echo "<button type='submit' name='set_session'>Set Login Session</button>";
echo "</form>";

if (isset($_POST['set_session'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['admin_role'] = 'admin';
    echo "<p style='color:green;'>Session set! Refresh page to see changes.</p>";
    echo "<a href='test_session.php'>Refresh</a>";
}
?>
