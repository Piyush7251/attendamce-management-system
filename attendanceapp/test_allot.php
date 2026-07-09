<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/database/adminDetails.php";

$dbo = new database();
$ado = new adminDetails();

// 1. Add test faculty
$fac_res = $ado->addFaculty($dbo, "Test Faculty", "testfac", "password");
echo "Add Faculty: " . json_encode($fac_res) . "<br>";

// 2. Add test course
$course_res = $ado->addCourse($dbo, "Test Course", "TEST-101", 3);
echo "Add Course: " . json_encode($course_res) . "<br>";

// 3. Add allotment (session 1, faculty 2, course 1, class BCA)
try {
    $stmt = $dbo->conn->query("SELECT id FROM faculty_details WHERE user_name = 'testfac'");
    $fac_id = $stmt->fetchColumn();
    
    $stmt = $dbo->conn->query("SELECT id FROM course_details WHERE code = 'TEST-101'");
    $course_id = $stmt->fetchColumn();
    
    echo "Fac ID: $fac_id, Course ID: $course_id<br>";
    
    $allot_res = $ado->addAllotment($dbo, $fac_id, $course_id, 1, "BCA");
    echo "Allotment Result: " . json_encode($allot_res) . "<br>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
