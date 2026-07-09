<?php
session_start();

// SECURITY FIX: Ensure user is a logged-in Admin before proceeding
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "ERROR", "message" => "Access denied."]);
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../database/adminDetails.php";

$action = $_REQUEST['action'] ?? '';
if (empty($action)) {
    exit();
}
$dbo = new database();
$ado = new adminDetails();
switch ($action) {
    case "getFaculties":
        $rv = $ado->getAllFaculties($dbo);
        echo json_encode($rv);
        break;
    case "getCourses":
        $rv = $ado->getAllCourses($dbo);
        echo json_encode($rv);
        break;
    case "getAllotments":
        $sessionid = $_POST['sessionid'];
        $rv = $ado->getCourseAllotments($dbo, $sessionid);
        echo json_encode($rv);
        break;
    case "addAllotment":
        $facultyid = $_POST['facultyid'];
        $courseid = $_POST['courseid'];
        $sessionid = $_POST['sessionid'];
        $rv = $ado->addAllotment($dbo, $facultyid, $courseid, $sessionid);
        echo json_encode($rv);
        break;
    case "removeAllotment":
        $facultyid = $_POST['facultyid'];
        $courseid = $_POST['courseid'];
        $sessionid = $_POST['sessionid'];
        $rv = $ado->removeAllotment($dbo, $facultyid, $courseid, $sessionid);
        echo json_encode($rv);
        break;
    case "addFaculty":
        $name = $_POST['name'];
        $username = $_POST['user_name'];
        $password = $_POST['password'];
        $rv = $ado->addFaculty($dbo, $name, $username, $password);
        echo json_encode($rv);
        break;
    case "addCourse":
        $title = $_POST['title'];
        $code = $_POST['code'];
        $credit = $_POST['credit'];
        $rv = $ado->addCourse($dbo, $title, $code, $credit);
        echo json_encode($rv);
        break;
    case "addStudent":
        $name = $_POST['name'];
        $roll_no = $_POST['roll_no'];
        $class_name = $_POST['class_name'];
        $rv = $ado->addStudent($dbo, $name, $roll_no, $class_name);
        echo json_encode($rv);
        break;
}

exit();

?>