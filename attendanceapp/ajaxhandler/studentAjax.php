<?php
// Force errors to show if anything goes wrong
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session

// SECURITY CHECK: Ensure user is logged in as a STUDENT
if (!isset($_SESSION['current_user']) || $_SESSION['role'] !== 'STUDENT') {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "ERROR", "message" => "Access denied. Student authorization required."]);
    exit();
}

require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../database/studentDetails.php";
require_once __DIR__ . "/../database/sessionDetails.php";

$action = $_REQUEST['action'] ?? '';
if (empty($action)) {
    exit();
}

$dbo = new database();
$student_id = $_SESSION['current_user'];
$sdo = new studentDetails();

switch ($action) {
    case "getSessions":
        $sobj = new SessionDetails();
        $rv = $sobj->getSessions($dbo);
        echo json_encode($rv);
        break;
        
    case "getStudentProfile":
        $profile = $sdo->getStudentProfile($dbo, $student_id);
        echo json_encode($profile);
        break;

    case "getDashboardData":
        $session_id = $_POST['sessionid'];
        $report = $sdo->getStudentAttendanceReport($dbo, $student_id, $session_id);
        echo json_encode($report);
        break;

    case "getDetailedLogs":
        $session_id = $_POST['sessionid'];
        $course_id = $_POST['courseid'];
        $logs = $sdo->getDetailedAttendanceLogs($dbo, $student_id, $course_id, $session_id);
        echo json_encode($logs);
        break;
}

exit();
?>
