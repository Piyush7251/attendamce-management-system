<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/database/database.php";
$dbo = new database();

echo "--- Student Details ---<br>";
$stmt = $dbo->conn->query("SELECT * FROM student_details");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row) . "<br>";
}

echo "<br>--- Course Allotments ---<br>";
$stmt = $dbo->conn->query("SELECT * FROM course_allotment");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row) . "<br>";
}

echo "<br>--- Course Details ---<br>";
$stmt = $dbo->conn->query("SELECT * FROM course_details");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row) . "<br>";
}
?>
