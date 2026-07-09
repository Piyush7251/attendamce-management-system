<?php
require_once __DIR__ . '/database/database.php';
$dbo = new database();
try {
    // 1. Add class_name if not exists
    try {
        $dbo->conn->exec("ALTER TABLE student_details ADD COLUMN class_name VARCHAR(50)");
        echo "<br>Added class_name column.";
    } catch (PDOException $e) {
        // Ignored if column already exists
    }

    // 2. Add password if not exists
    try {
        $dbo->conn->exec("ALTER TABLE student_details ADD COLUMN password VARCHAR(255) DEFAULT NULL");
        echo "<br>Added password column to student_details.";
    } catch (PDOException $e) {
        // Ignored if column already exists
    }

    // 3. Initialize passwords to hashed roll numbers for any students lacking passwords
    $stmt = $dbo->conn->prepare("SELECT id, roll_no FROM student_details WHERE password IS NULL OR password = ''");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($students) > 0) {
        $update_stmt = $dbo->conn->prepare("UPDATE student_details SET password = :pw WHERE id = :id");
        $count = 0;
        foreach ($students as $student) {
            $hashed = password_hash($student['roll_no'], PASSWORD_DEFAULT);
            $update_stmt->execute([':pw' => $hashed, ':id' => $student['id']]);
            $count++;
        }
        echo "<br>Initialized passwords for $count student(s).";
    }

    echo "<br>DB Schema updated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>