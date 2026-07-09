<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/database/studentDetails.php";

$dbo = new database();
$sdo = new studentDetails();

// Let's find a student from student_details
try {
    $stmt = $dbo->conn->query("SELECT id, name, roll_no, class_name FROM student_details LIMIT 1");
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) {
        echo "No students found in database!<br>";
        exit();
    }
    
    echo "Testing with Student: " . json_encode($student) . "<br>";
    
    // Test profile retrieval
    $profile = $sdo->getStudentProfile($dbo, $student['id']);
    echo "Profile: " . json_encode($profile) . "<br>";
    
    // Test dashboard report (using session 1)
    $report = $sdo->getStudentAttendanceReport($dbo, $student['id'], 1);
    echo "Report (Session 1): " . json_encode($report) . "<br>";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
}
?>
