<?php
class database {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    public $conn = null;

    public function __construct() {
        // Read configuration from environment variables with local fallbacks
        $this->servername = getenv('DB_HOST') ?: "localhost";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASS') ?: "";
        $this->dbname = getenv('DB_NAME') ?: "attendance_db";

        try {
            $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Halt execution completely if connection fails
            die("Database Connection failed: " . $e->getMessage());
        }
    }
}
?>