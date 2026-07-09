<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/database/database.php";
$dbo = new database();
$hashed_pw = password_hash("password", PASSWORD_DEFAULT);
$stmt = $dbo->conn->prepare("UPDATE student_details SET password = :pw WHERE roll_no = '202601'");
$stmt->execute([":pw" => $hashed_pw]);
echo "Password reset for student 202601 to 'password'.";
?>
