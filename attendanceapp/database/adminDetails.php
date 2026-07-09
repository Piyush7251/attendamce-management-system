<?php
require_once __DIR__ . "/database.php";

class adminDetails {
    public function getAllFaculties($dbo) {
        $rv = [];
        $c = "SELECT id, name, user_name FROM faculty_details WHERE role != 'ADMIN'";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute();
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Faculties Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getAllCourses($dbo) {
        $rv = [];
        $c = "SELECT id, title, code, credit FROM course_details";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute();
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Courses Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getCourseAllotments($dbo, $sessionid) {
        $rv = [];
        $c = "SELECT ca.faculty_id, ca.course_id, ca.class_name, cd.title, cd.code, fd.name as faculty_name 
              FROM course_allotment AS ca
              JOIN course_details AS cd ON ca.course_id = cd.id 
              JOIN faculty_details AS fd ON ca.faculty_id = fd.id
              WHERE ca.session_id = :sessionid";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":sessionid" => $sessionid]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Course Allotments Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function addAllotment($dbo, $facultyid, $courseid, $sessionid, $class_name) {
        $rv = ["status" => "ERROR"];
        $c = "INSERT IGNORE INTO course_allotment (faculty_id, course_id, session_id, class_name) VALUES (:fid, :cid, :sid, :class_name)";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":fid" => $facultyid, ":cid" => $courseid, ":sid" => $sessionid, ":class_name" => $class_name]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            error_log("Add Allotment Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function removeAllotment($dbo, $facultyid, $courseid, $sessionid, $class_name) {
        $rv = ["status" => "ERROR"];
        $c = "DELETE FROM course_allotment WHERE faculty_id = :fid AND course_id = :cid AND session_id = :sid AND class_name = :class_name";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":fid" => $facultyid, ":cid" => $courseid, ":sid" => $sessionid, ":class_name" => $class_name]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            error_log("Remove Allotment Error: " . $e->getMessage());
        }
        return $rv;
    }
    public function addFaculty($dbo, $name, $username, $password) {
        $rv = ["status" => "ERROR"];
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $c = "INSERT INTO faculty_details (name, user_name, password, role) VALUES (:name, :un, :pw, 'FACULTY')";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":name" => $name, ":un" => $username, ":pw" => $hashed_pw]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Username already exists"];
            } else {
                error_log("Add Faculty Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function addCourse($dbo, $title, $code, $credit) {
        $rv = ["status" => "ERROR"];
        $c = "INSERT INTO course_details (title, code, credit) VALUES (:title, :code, :credit)";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":title" => $title, ":code" => $code, ":credit" => $credit]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Course code already exists"];
            } else {
                error_log("Add Course Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function addStudent($dbo, $name, $roll_no, $class_name) {
        $rv = ["status" => "ERROR"];
        $c = "INSERT INTO student_details (name, roll_no, class_name) VALUES (:name, :roll, :class_name)";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":name" => $name, ":roll" => $roll_no, ":class_name" => $class_name]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Roll No already exists"];
            } else {
                error_log("Add Student Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }
}
?>