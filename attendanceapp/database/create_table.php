<?php
require_once __DIR__ . "/database.php";
$dbo = new database();

// 1. Create Tables ------------------------------------------------------------------------
$tables = [
    "student_details" => "CREATE TABLE IF NOT EXISTS student_details (
        id int auto_increment primary key,
        roll_no varchar(20) unique,
        name varchar(50),
        class_name varchar(50),
        password varchar(255) default null
    )",
    "course_details" => "CREATE TABLE IF NOT EXISTS course_details (
        id int auto_increment primary key,
        code varchar(20) unique,
        title varchar(50),
        credit int 
    )",
    "faculty_details" => "CREATE TABLE IF NOT EXISTS faculty_details (
        id int auto_increment primary key,
        user_name varchar(20) unique,
        name varchar(50),
        password varchar(255),
        role varchar(20) default 'FACULTY'
    )",
    "session_details" => "CREATE TABLE IF NOT EXISTS session_details (
        id int auto_increment primary key,
        year int,
        term varchar(50),
        unique(year,term)
    )",
    "course_registration" => "CREATE TABLE IF NOT EXISTS course_registration (
        student_id int,
        course_id int,
        session_id int,
        primary key(student_id,course_id,session_id)
    )",
    "course_allotment" => "CREATE TABLE IF NOT EXISTS course_allotment (
        faculty_id int,
        course_id int,
        session_id int,
        primary key(faculty_id,course_id,session_id)
    )",
    "attendance_details" => "CREATE TABLE IF NOT EXISTS attendance_details (
        faculty_id int,
        course_id int,
        session_id int,
        student_id int,
        on_date date,
        status varchar(10),
        primary key(faculty_id,course_id,session_id, student_id,on_date)
    )"
];

foreach ($tables as $name => $query) {
    try {
        $dbo->conn->exec($query);
        echo "<br>{$name} checked/created successfully.";
    } catch(PDOException $e) {
        echo "<br>Error creating {$name}: " . $e->getMessage();  
    }
}

// 2. Clear Existing Data (Remove all testing data) ---------------------------------------
$tables_to_clear = [
    'attendance_details', 
    'course_allotment', 
    'course_registration', 
    'student_details', 
    'course_details', 
    'faculty_details', 
    'session_details'
];

foreach ($tables_to_clear as $table) {
    try {
        $dbo->conn->exec("DELETE FROM {$table}");
        echo "<br>Table {$table} cleared of all previous data.";
    } catch(PDOException $e) {
        echo "<br>Error clearing {$table}: " . $e->getMessage();
    }
}

// 3. Insert Default Administrator Account -------------------------------------------------
$admin_pw = password_hash('admin123', PASSWORD_DEFAULT);
$c = "INSERT INTO faculty_details (id, user_name, password, name, role) VALUES
    (1, 'admin', :admin_pw, 'Administrator', 'ADMIN')
    ON DUPLICATE KEY UPDATE password=VALUES(password), name=VALUES(name), role=VALUES(role)";
try {
    $dbo->conn->prepare($c)->execute([':admin_pw' => $admin_pw]);
    echo "<br>Administrator account initialized successfully (User: admin, Pass: admin123).";
} catch (PDOException $e) {
    echo "<br>Error inserting Admin user: " . $e->getMessage();
}

// 4. Insert Default Sessions (Required for application initialization) -------------------
$c = "INSERT IGNORE INTO session_details (id,year,term) VALUES
(1, 2026, 'EVEN SEMESTER'), (2, 2026, 'ODD SEMESTER')";
try {
    $dbo->conn->exec($c);
    echo "<br>Default academic sessions (2026 EVEN/ODD) initialized.";
} catch (PDOException $e) {
    echo "<br>Error inserting default sessions: " . $e->getMessage();
}

echo "<br><br>Database successfully cleaned and initialized!";
?>