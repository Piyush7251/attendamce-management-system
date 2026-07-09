<?php
class database {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    public $conn = null;

    public function __construct() {
        // Load production/secured variables from config.php if exists
        $db_host = null;
        $db_user = null;
        $db_pass = null;
        $db_name = null;
        if (file_exists(__DIR__ . "/config.php")) {
            include __DIR__ . "/config.php";
        }

        // Read configuration from environment variables with local fallbacks
        $this->servername = getenv('DB_HOST') ?: ($db_host ?? "localhost");
        $this->username = getenv('DB_USER') ?: ($db_user ?? "root");
        $this->password = getenv('DB_PASS') ?: ($db_pass ?? "");
        $this->dbname = getenv('DB_NAME') ?: ($db_name ?? "attendance_db");

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