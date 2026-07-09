<?php
require_once __DIR__ . "/database.php";

class SessionDetails {
    public function getSessions($dbo) {
        $rv = [];
        $c = "SELECT * FROM session_details";   
        $s = $dbo->conn->prepare($c);
        try {
            $s->execute();
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Sessions Error: " . $e->getMessage());
        }
        return $rv;
    }
}
?>