<?php
// 1. Force PHP to show errors instead of crashing silently
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session at the top

// 2. Use __DIR__ to safely navigate up one folder and into /database
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../database/facultyDetails.php";
require_once __DIR__ . "/../database/studentDetails.php";

$action = $_REQUEST["action"] ?? '';
$dbo = new Database();
$fdo = new faculty_details();
$rv = ['status' => 'INVALID_ACTION']; // Default response

switch ($action) {
    case "verifyUser":
        $un = $_POST["user_name"];
        $pw = $_POST["password"];
        $login_type = $_POST["login_type"] ?? "staff";

        if ($login_type === "student") {
            $sdo = new studentDetails();
            $rv = $sdo->verifyStudent($dbo, $un, $pw);

            if ($rv['status'] == "ALL OK") {
                $_SESSION['current_user'] = $rv['id'];
                $_SESSION['role'] = 'STUDENT'; 
                $rv['role'] = 'STUDENT';
            }
        } else {
            $rv = $fdo->verifyUser($dbo, $un, $pw);

            if ($rv['status'] == "ALL OK") {
                $_SESSION['current_user'] = $rv['id'];
                $_SESSION['role'] = $rv['role']; 
            }
        }
        break;

    case "registerUser":
        // BUG FIX: Add missing handler for user registration
        $name = $_POST['name'];
        $un = $_POST['user_name'];
        $pw = $_POST['password'];
        $rv = $fdo->registerUser($dbo, $name, $un, $pw);
        break;

    case "resetPassword":
        $un = $_POST['user_name'];
        $pw = $_POST['password'];
        
        // Try resetting faculty password first
        $rv = $fdo->resetPassword($dbo, $un, $pw);
        if ($rv['status'] == "Username does not exist") {
            // Try student password reset using their roll number
            $sdo = new studentDetails();
            $rv = $sdo->resetStudentPassword($dbo, $un, $pw);
        }
        break;
}

echo json_encode($rv);
exit(); // Best practice: stop script after sending response

?>