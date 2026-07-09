<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/database/config.php";

echo "Host: " . $db_host . "<br>";
echo "User: " . $db_user . "<br>";
echo "DB: " . $db_name . "<br>";

try {
    // Try connecting to MySQL without selecting a database first
    $conn = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL successfully without database selection!<br>";
    
    // Now try selecting the database
    $conn->exec("USE `$db_name`");
    echo "Selected database `$db_name` successfully!<br>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
