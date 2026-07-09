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

    public function updateCourse($dbo, $id, $title, $code, $credit) {
        $rv = ["status" => "ERROR"];
        $c = "UPDATE course_details SET title = :title, code = :code, credit = :credit WHERE id = :id";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":title" => $title, ":code" => $code, ":credit" => $credit, ":id" => $id]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Course code already exists"];
            } else {
                error_log("Update Course Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function deleteCourse($dbo, $id) {
        $rv = ["status" => "ERROR"];
        try {
            $dbo->conn->beginTransaction();

            // 1. Delete from attendance_details
            $s1 = $dbo->conn->prepare("DELETE FROM attendance_details WHERE course_id = :id");
            $s1->execute([":id" => $id]);

            // 2. Delete from course_allotment
            $s2 = $dbo->conn->prepare("DELETE FROM course_allotment WHERE course_id = :id");
            $s2->execute([":id" => $id]);

            // 3. Delete from course_registration (just in case)
            $s3 = $dbo->conn->prepare("DELETE FROM course_registration WHERE course_id = :id");
            $s3->execute([":id" => $id]);

            // 4. Delete from course_details
            $s4 = $dbo->conn->prepare("DELETE FROM course_details WHERE id = :id");
            $s4->execute([":id" => $id]);

            $dbo->conn->commit();
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            $dbo->conn->rollBack();
            error_log("Delete Course Error: " . $e->getMessage());
            $rv = ["status" => "Database error"];
        }
        return $rv;
    }

    public function updateFaculty($dbo, $id, $name, $username, $password) {
        $rv = ["status" => "ERROR"];
        if (trim($password) !== "") {
            $c = "UPDATE faculty_details SET name = :name, user_name = :username, password = :password WHERE id = :id";
            $s = $dbo->conn->prepare($c);
            $params = [":name" => $name, ":username" => $username, ":password" => $password, ":id" => $id];
        } else {
            $c = "UPDATE faculty_details SET name = :name, user_name = :username WHERE id = :id";
            $s = $dbo->conn->prepare($c);
            $params = [":name" => $name, ":username" => $username, ":id" => $id];
        }
        try {
            $s->execute($params);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Username already exists"];
            } else {
                error_log("Update Faculty Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function deleteFaculty($dbo, $id) {
        $rv = ["status" => "ERROR"];
        try {
            $dbo->conn->beginTransaction();

            $s1 = $dbo->conn->prepare("DELETE FROM attendance_details WHERE faculty_id = :id");
            $s1->execute([":id" => $id]);

            $s2 = $dbo->conn->prepare("DELETE FROM course_allotment WHERE faculty_id = :id");
            $s2->execute([":id" => $id]);

            $s3 = $dbo->conn->prepare("DELETE FROM faculty_details WHERE id = :id");
            $s3->execute([":id" => $id]);

            $dbo->conn->commit();
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            $dbo->conn->rollBack();
            error_log("Delete Faculty Error: " . $e->getMessage());
            $rv = ["status" => "Database error"];
        }
        return $rv;
    }

    public function getStudents($dbo) {
        $rv = [];
        $c = "SELECT id, name, roll_no, class_name FROM student_details ORDER BY roll_no";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute();
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Students Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function updateStudent($dbo, $id, $name, $roll_no, $class_name) {
        $rv = ["status" => "ERROR"];
        $c = "UPDATE student_details SET name = :name, roll_no = :roll, class_name = :class_name WHERE id = :id";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":name" => $name, ":roll" => $roll_no, ":class_name" => $class_name, ":id" => $id]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $rv = ["status" => "Roll number already exists"];
            } else {
                error_log("Update Student Error: " . $e->getMessage());
                $rv = ["status" => "Database error"];
            }
        }
        return $rv;
    }

    public function deleteStudent($dbo, $id) {
        $rv = ["status" => "ERROR"];
        try {
            $dbo->conn->beginTransaction();

            $s1 = $dbo->conn->prepare("DELETE FROM attendance_details WHERE student_id = :id");
            $s1->execute([":id" => $id]);

            $s2 = $dbo->conn->prepare("DELETE FROM course_registration WHERE student_id = :id");
            $s2->execute([":id" => $id]);

            $s3 = $dbo->conn->prepare("DELETE FROM student_details WHERE id = :id");
            $s3->execute([":id" => $id]);

            $dbo->conn->commit();
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            $dbo->conn->rollBack();
            error_log("Delete Student Error: " . $e->getMessage());
            $rv = ["status" => "Database error"];
        }
        return $rv;
    }
}
?>