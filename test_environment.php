<?php
echo "<h2>System Environment Check</h2>";

echo "<h3>1. PHP Version:</h3>";
echo PHP_VERSION . "<br>";

echo "<h3>2. File Paths:</h3>";
echo "Current File: " . __FILE__ . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

echo "<h3>3. Directory Contents:</h3>";
$dir = __DIR__;
echo "Checking: " . $dir . "<br>";
$files = scandir($dir);
echo "<ul>";
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        echo "<li>" . $file . "</li>";
    }
}
echo "</ul>";

echo "<h3>4. MySQL Connection Test:</h3>";
$conn = @mysqli_connect('localhost', 'root', '');
if ($conn) {
    echo "✓ MySQL connection successful!<br>";
    mysqli_close($conn);
} else {
    echo "✗ MySQL connection failed!<br>";
}

echo "<h3>5. Database Test:</h3>";
$conn = @mysqli_connect('localhost', 'root', '', 'inventory_system');
if ($conn) {
    echo "✓ Database 'inventory_system' exists!<br>";
    mysqli_close($conn);
} else {
    echo "✗ Database 'inventory_system' not found!<br>";
}

echo "<h3>6. Solution:</h3>";
if (!file_exists(__DIR__ . '/login.php')) {
    echo "✗ login.php is missing!<br>";
    echo "Create the file: " . __DIR__ . "/login.php<br>";
}
?>