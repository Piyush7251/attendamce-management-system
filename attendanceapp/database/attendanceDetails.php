<?php
require_once __DIR__ . "/database.php";

class attendanceDetails {
    public function saveAttendance($dbo, $session, $course, $fac, $student, $ondate, $status) {
        $rv = [-1];
        $c = "INSERT INTO attendance_details (session_id, course_id, faculty_id, student_id, on_date, status)
              VALUES (:session_id, :course_id, :faculty_id, :student_id, :on_date, :status)
              ON DUPLICATE KEY UPDATE status = :status_update";
        $s = $dbo->conn->prepare($c);
        
        try {
            $s->execute([
                ":session_id" => $session,
                ":course_id" => $course,
                ":faculty_id" => $fac,
                ":student_id" => $student,
                ":on_date" => $ondate,
                ":status" => $status,
                ":status_update" => $status
            ]);
            $rv = [1];
        } catch (PDOException $e) {
            error_log("Save/Update Attendance Error: " . $e->getMessage());
            $rv = [$e->getMessage()];
        }
        return $rv;
    }

    public function getPresentListOfAClassByAFacOnADate($dbo, $session, $course, $fac, $ondate) {
        $rv = [];
        $c = "SELECT student_id FROM attendance_details 
              WHERE session_id = :session_id AND course_id = :course_id
              AND faculty_id = :faculty_id AND on_date = :on_date AND status = 'YES'";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":session_id" => $session, ":course_id" => $course, ":faculty_id" => $fac, ":on_date" => $ondate]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Present List Error: " . $e->getMessage());
        }
        return $rv;
    }

    public function getAttenDanceReport($dbo, $session, $course, $fac, $class_name) {
        $report = [];
        $sessionName = ''; $facname = ''; $courseName = '';

        // Safely fetch names using fetch() instead of fetchAll()[0]
        try {
            $s = $dbo->conn->prepare("SELECT * FROM session_details WHERE id = :id");
            $s->execute([":id" => $session]);
            $sd = $s->fetch(PDO::FETCH_ASSOC);
            if($sd) $sessionName = $sd['year'] . " " . $sd['term'];

            $s = $dbo->conn->prepare("SELECT * FROM faculty_details WHERE id = :id");
            $s->execute([":id" => $fac]);
            $sd = $s->fetch(PDO::FETCH_ASSOC);
            if($sd) $facname = $sd['name'];

            $s = $dbo->conn->prepare("SELECT * FROM course_details WHERE id = :id");
            $s->execute([":id" => $course]);
            $sd = $s->fetch(PDO::FETCH_ASSOC);
            if($sd) $courseName = $sd['code'] . "-" . $sd['title'];
        } catch (PDOException $e) {
            error_log("Fetch Details Error: " . $e->getMessage());
        }

        array_push($report, ["Session:", $sessionName]);
        array_push($report, ["Course:", $courseName]);
        array_push($report, ["Faculty:", $facname]);
        array_push($report, ["Class/Group:", $class_name]);

        $total = 0; $start = ''; $end = '';
        $c = "SELECT DISTINCT on_date FROM attendance_details 
              WHERE session_id = :session_id AND course_id = :course_id AND faculty_id = :faculty_id
              ORDER BY on_date";
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":session_id" => $session, ":course_id" => $course, ":faculty_id" => $fac]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
            $total = count($rv);
            if ($total > 0) {
                $start = $rv[0]['on_date'];
                $end = $rv[$total - 1]['on_date'];
            }
        } catch (PDOException $ee) {
            error_log("Date Range Error: " . $ee->getMessage());
        }
        
        array_push($report, ["Total Classes:", $total]);
        array_push($report, ["Start Date:", $start]);
        array_push($report, ["End Date:", $end]);
        array_push($report, []); // Empty row for spacing

        $rv = [];
        $c = "SELECT rsd.id, rsd.roll_no, rsd.name, COUNT(ad.on_date) as attended FROM 
              (SELECT sd.id, sd.roll_no, sd.name 
               FROM student_details as sd
               WHERE sd.class_name = :class_name) as rsd 
              LEFT JOIN attendance_details as ad 
              ON rsd.id = ad.student_id AND ad.session_id = :session_id 
              AND ad.course_id = :course_id AND status = 'YES' AND ad.faculty_id = :faculty_id
              GROUP BY rsd.id";
              
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute([":session_id" => $session, ":course_id" => $course, ":faculty_id" => $fac, ":class_name" => $class_name]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ee) {
            error_log("Report Generation Error: " . $ee->getMessage());
        }

        for ($i = 0; $i < count($rv); $i++) {
            $rv[$i]['percent'] = 0.00;
            if ($total > 0) {
                $rv[$i]['percent'] = round($rv[$i]['attended'] / $total * 100.0, 2);
            }
        }

        array_push($report, ["SL No", "Roll No", "Name", "Attended", "Percent (%)"]);
        $report = array_merge($report, $rv);
        
        return $report;
    }
}
?>