<?php
require_once __DIR__ . "/database.php";

class CourseRegistrationDetails {
    public function getRegisteredStudents($dbo, $sessionid, $courseid) {
        $rv = [];
        $c = "SELECT sd.id, sd.roll_no, sd.name 
              FROM student_details AS sd
              JOIN course_registration AS crg ON crg.student_id = sd.id 
              WHERE crg.session_id = :sessionid AND crg.course_id = :courseid";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":sessionid" => $sessionid, ":courseid" => $courseid]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Registered Students Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getRegisteredStudentsWithAttendance($dbo, $sessionid, $courseid, $facid, $ondate) {
        $rv = [];
        $c = "SELECT 
                sd.id, 
                sd.roll_no, 
                sd.name, 
                CASE WHEN ad.status = 'YES' THEN 'YES' ELSE 'NO' END AS isPresent
              FROM 
                student_details AS sd
              JOIN 
                course_registration AS crg ON crg.student_id = sd.id
              LEFT JOIN 
                attendance_details AS ad ON sd.id = ad.student_id 
                                        AND ad.session_id = crg.session_id
                                        AND ad.course_id = crg.course_id
                                        AND ad.faculty_id = :facid
                                        AND ad.on_date = :ondate
              WHERE 
                crg.session_id = :sessionid AND crg.course_id = :courseid
              ORDER BY
                sd.roll_no";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":sessionid" => $sessionid, ":courseid" => $courseid, ":facid" => $facid, ":ondate" => $ondate]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Registered Students w/ Attendance Error: " . $e->getMessage());
        }
        return $rv;
    }
}
?>