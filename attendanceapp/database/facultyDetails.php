<?php
// Safely link the database file without relying on folder names
require_once __DIR__ . "/database.php";

class faculty_details {
    public function verifyUser($dbo, $un, $pw) {
        $rv = ["id" => -1, "status" => "ERROR", "role" => ""];
        $c = "SELECT id, password, role FROM faculty_details WHERE user_name = :un";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":un" => $un]);
            if ($s->rowCount() > 0) {
                $result = $s->fetch(PDO::FETCH_ASSOC); 
                
                if (password_verify($pw, $result['password'])) {
                    $rv = ["id" => $result['id'], "status" => "ALL OK", "role" => $result['role']];
                } else {
                    $rv = ["id" => $result['id'], "status" => "Wrong password", "role" => ""];
                }
            } else {
                $rv = ["id" => -1, "status" => "USER NAME DOES NOT EXIST", "role" => ""];
            }
        } catch (PDOException $e) {
            error_log("Verification Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getCoursesInASession($dbo, $sessionid, $facid) {
        $rv = [];
        $c = "SELECT cd.id, cd.code, cd.title, ca.class_name FROM course_allotment AS ca
              JOIN course_details AS cd ON ca.course_id = cd.id 
              WHERE ca.faculty_id = :facid AND ca.session_id = :sessionid";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":facid" => $facid, ":sessionid" => $sessionid]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Courses Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function registerUser($dbo, $name, $un, $pw) {
        $rv = ["status" => "ERROR"];
        $hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
        $c = "INSERT INTO faculty_details (user_name, name, password) VALUES (:un, :name, :pw)";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":un" => $un, ":name" => $name, ":pw" => $hashed_pw]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate username)
                $rv = ["status" => "Username already exists"];
            } else {
                error_log("Registration Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function resetPassword($dbo, $un, $pw) {
        $rv = ["status" => "ERROR"];
        // Check if user exists first
        $check_c = "SELECT id FROM faculty_details WHERE user_name = :un";
        $check_s = $dbo->conn->prepare($check_c);
        $check_s->execute([":un" => $un]);
        if ($check_s->rowCount() == 0) {
            return ["status" => "Username does not exist"];
        }

        $hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
        $c = "UPDATE faculty_details SET password = :pw WHERE user_name = :un";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":pw" => $hashed_pw, ":un" => $un]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            error_log("Reset Password Error: " . $e->getMessage());
            $rv = ["status" => "Database error"];
        }
        return $rv;
    }
}
?>