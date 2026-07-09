<?php
require_once __DIR__ . "/database.php";

class studentDetails {
    public function verifyStudent($dbo, $roll_no, $password) {
        $rv = ["id" => -1, "status" => "ERROR", "name" => "", "roll_no" => ""];
        $c = "SELECT id, name, password, roll_no FROM student_details WHERE roll_no = :roll";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":roll" => $roll_no]);
            if ($s->rowCount() > 0) {
                $result = $s->fetch(PDO::FETCH_ASSOC);
                // Verify using password hash
                if ($result['password'] !== null && password_verify($password, $result['password'])) {
                    $rv = [
                        "id" => $result['id'], 
                        "status" => "ALL OK", 
                        "name" => $result['name'], 
                        "roll_no" => $result['roll_no']
                    ];
                } else {
                    $rv = ["id" => $result['id'], "status" => "Wrong password", "name" => "", "roll_no" => ""];
                }
            } else {
                $rv = ["id" => -1, "status" => "ROLL NUMBER DOES NOT EXIST", "name" => "", "roll_no" => ""];
            }
        } catch (PDOException $e) {
            error_log("Student Verification Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getStudentProfile($dbo, $student_id) {
        $rv = null;
        $c = "SELECT id, name, roll_no, class_name FROM student_details WHERE id = :id";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":id" => $student_id]);
            $rv = $s->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Student Profile Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getStudentAttendanceReport($dbo, $student_id, $session_id) {
        $rv = [];
        
        // 1. Get all courses allotted to the student's class in this session
        $c = "SELECT DISTINCT cd.id as course_id, cd.code, cd.title, cd.credit
              FROM course_allotment AS ca
              JOIN course_details AS cd ON ca.course_id = cd.id
              JOIN student_details AS sd ON sd.class_name = ca.class_name
              WHERE sd.id = :sid AND ca.session_id = :sessid";
        $s = $dbo->conn->prepare($c);
        
        try {
            $s->execute([":sid" => $student_id, ":sessid" => $session_id]);
            $courses = $s->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($courses as $course) {
                $cid = $course['course_id'];
                
                // 2. Get total classes held for this course + session
                $c_total = "SELECT COUNT(DISTINCT on_date) as total_held 
                            FROM attendance_details 
                            WHERE course_id = :cid AND session_id = :sessid";
                $s_total = $dbo->conn->prepare($c_total);
                $s_total->execute([":cid" => $cid, ":sessid" => $session_id]);
                $total_held = (int)($s_total->fetch(PDO::FETCH_ASSOC)['total_held'] ?? 0);
                
                // 3. Get classes attended by this student for this course + session
                $c_attended = "SELECT COUNT(DISTINCT on_date) as total_attended 
                               FROM attendance_details 
                               WHERE student_id = :sid AND course_id = :cid 
                                 AND session_id = :sessid AND status = 'YES'";
                $s_attended = $dbo->conn->prepare($c_attended);
                $s_attended->execute([":sid" => $student_id, ":cid" => $cid, ":sessid" => $session_id]);
                $total_attended = (int)($s_attended->fetch(PDO::FETCH_ASSOC)['total_attended'] ?? 0);
                
                // 4. Fetch the faculty name who teaches this course
                $c_fac = "SELECT fd.name as faculty_name 
                          FROM course_allotment AS ca
                          JOIN faculty_details AS fd ON ca.faculty_id = fd.id
                          WHERE ca.course_id = :cid AND ca.session_id = :sessid 
                          LIMIT 1";
                $s_fac = $dbo->conn->prepare($c_fac);
                $s_fac->execute([":cid" => $cid, ":sessid" => $session_id]);
                $fac_row = $s_fac->fetch(PDO::FETCH_ASSOC);
                $faculty_name = $fac_row ? $fac_row['faculty_name'] : 'Not Assigned';
                
                // 5. Calculate percentage
                $percentage = 0.00;
                if ($total_held > 0) {
                    $percentage = round(($total_attended / $total_held) * 100.0, 2);
                }
                
                $rv[] = [
                    "course_id" => $cid,
                    "code" => $course['code'],
                    "title" => $course['title'],
                    "credit" => $course['credit'],
                    "faculty_name" => $faculty_name,
                    "total_classes" => $total_held,
                    "attended_classes" => $total_attended,
                    "percentage" => $percentage
                ];
            }
        } catch (PDOException $e) {
            error_log("Get Student Attendance Report Error: " . $e->getMessage());
        }
        
        return $rv;
    }

    public function getDetailedAttendanceLogs($dbo, $student_id, $course_id, $session_id) {
        $rv = [];
        $c = "SELECT on_date, status 
              FROM attendance_details 
              WHERE student_id = :sid AND course_id = :cid AND session_id = :sessid 
              ORDER BY on_date DESC";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":sid" => $student_id, ":cid" => $course_id, ":sessid" => $session_id]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Detailed Attendance Logs Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function resetStudentPassword($dbo, $roll_no, $new_password) {
        $rv = ["status" => "ERROR"];
        // Verify student exists
        $check_c = "SELECT id FROM student_details WHERE roll_no = :roll";
        $check_s = $dbo->conn->prepare($check_c);
        try {
            $check_s->execute([":roll" => $roll_no]);
            if ($check_s->rowCount() == 0) {
                return ["status" => "Roll number does not exist"];
            }
            
            $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
            $c = "UPDATE student_details SET password = :pw WHERE roll_no = :roll";
            $s = $dbo->conn->prepare($c);
            $s->execute([":pw" => $hashed_pw, ":roll" => $roll_no]);
            $rv = ["status" => "SUCCESS"];
        } catch (PDOException $e) {
            error_log("Reset Student Password Error: " . $e->getMessage());
            $rv = ["status" => "Database error"];
        }
        return $rv;
    }
}
?>
