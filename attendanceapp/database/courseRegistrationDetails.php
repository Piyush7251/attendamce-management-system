<?php
require_once __DIR__ . "/database.php";

class CourseRegistrationDetails {
    public function getRegisteredStudents($dbo, $sessionid, $courseid, $class_name) {
        $rv = [];
        $c = "SELECT sd.id, sd.roll_no, sd.name 
              FROM student_details AS sd
              WHERE sd.class_name = :class_name";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":class_name" => $class_name]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Registered Students Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getRegisteredStudentsWithAttendance($dbo, $sessionid, $courseid, $facid, $class_name, $ondate) {
        $rv = [];
        $c = "SELECT 
                sd.id, 
                sd.roll_no, 
                sd.name, 
                CASE WHEN ad.status = 'YES' THEN 'YES' ELSE 'NO' END AS isPresent
              FROM 
                student_details AS sd
              LEFT JOIN 
                attendance_details AS ad ON sd.id = ad.student_id 
                                         AND ad.session_id = :sessionid
                                         AND ad.course_id = :courseid
                                         AND ad.faculty_id = :facid
                                         AND ad.on_date = :ondate
              WHERE 
                sd.class_name = :class_name
              ORDER BY
                sd.roll_no";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":sessionid" => $sessionid, ":courseid" => $courseid, ":facid" => $facid, ":class_name" => $class_name, ":ondate" => $ondate]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Registered Students w/ Attendance Error: " . $e->getMessage());
        }
        return $rv;
    }
}
?>