<?php
// Force errors to show if anything goes wrong
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session

// SECURITY FIX: Ensure user is logged in before proceeding
if (!isset($_SESSION['current_user'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["status" => "ERROR", "message" => "User not logged in."]);
    exit();
}

// Use __DIR__ to safely link your database files
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../database/sessionDetails.php";
require_once __DIR__ . "/../database/facultyDetails.php";
require_once __DIR__ . "/../database/courseRegistrationDetails.php";
require_once __DIR__ . "/../database/attendanceDetails.php";

function createCSVReport($list, $filename) {
    $error = 0;
    $path = $_SERVER['DOCUMENT_ROOT']; // Keep this for file saving
    $finalFileName = $path . $filename;
    try {
        $fp = fopen($finalFileName, "w");
        foreach ($list as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
    } catch (Exception $e) {
        $error = 1;
        error_log("CSV Creation Error: " . $e->getMessage());
    }
}

$action = $_REQUEST['action'] ?? '';
if (empty($action)) {
    exit();
}

$dbo = new Database();
$facultyid = $_SESSION['current_user']; // SECURITY FIX: Use the logged-in user's ID from the session

switch ($action) {
    case "getSession":
        $sobj = new SessionDetails();
        $rv = $sobj->getSessions($dbo);
        echo json_encode($rv);
        break;
    case "getFacultyCourses":
        $sessionid = $_POST['sessionid'];
        $fo = new faculty_details();
        $rv = $fo->getCoursesInASession($dbo, $sessionid, $facultyid);
        echo json_encode($rv);
        break;
    case "getStudentList":
        $classid = $_POST['classid'];
        $sessionid = $_POST['sessionid'];
        $ondate = $_POST['ondate'];
        
        $crgo = new CourseRegistrationDetails();
        $allstudents = $crgo->getRegisteredStudentsWithAttendance($dbo, $sessionid, $classid, $facultyid, $ondate);

        echo json_encode($allstudents);
        break;
    case "saveattendance":
        $courseid = $_POST['courseid'];
        $sessionid = $_POST['sessionid'];
        $studentid = $_POST['studentid'];
        $ondate = $_POST['ondate'];
        $status = $_POST['ispresent'];
        
        $ado = new attendanceDetails();
        $rv = $ado->saveAttendance($dbo, $sessionid, $courseid, $facultyid, $studentid, $ondate, $status);
        echo json_encode($rv);
        break;
    case "downloadReport":
        $courseid = $_POST['classid'];
        $sessionid = $_POST['sessionid'];
        
        $ado = new attendanceDetails();
        $list = $ado->getAttenDanceReport($dbo, $sessionid, $courseid, $facultyid);
        
        $unique_id = uniqid();
        $filename = "/attendanceapp/report_{$unique_id}.csv";
        
        createCSVReport($list, $filename);
        $rv = ["filename" => $filename];
        echo json_encode($rv);
        break;
}

exit();

?>