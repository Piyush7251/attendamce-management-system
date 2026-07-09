<?php
session_start();
if (isset($_SESSION["current_user"]) && $_SESSION["role"] === "STUDENT") {
    $student_id = $_SESSION["current_user"];
} else {
    header("location:" . "/attendanceapp/login.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/student.css?v=<?php echo time(); ?>">
    <title>Student Portal - Attendance Management</title>
</head>
<body>
     <div class="page">
        <div class="header-area">
            <div class="logo-area"><h2 class="logo">STUDENT PORTAL</h2></div>
            <div class="logout-area"><button class="btnlogout" id="btnLogout">LOGOUT</button></div>
        </div>

        <main class="main-content">
            <aside class="left-panel">
                <!-- Student Profile Card -->
                <div class="control-panel profile-card">
                    <div class="profile-avatar" id="profileAvatar">S</div>
                    <h3 class="profile-name" id="lblStudentName">Loading Name...</h3>
                    <p class="profile-meta" id="lblStudentRoll">Roll No: --</p>
                    <p class="profile-meta" id="lblStudentClass">Class: --</p>
                </div>

                <!-- Session Selection -->
                <div class="control-panel">
                    <h3 class="panel-title">Select Session</h3>
                    <select class="ddlclass" id="ddlSession">
                        <!-- Loaded dynamically -->
                    </select>
                </div>

                <!-- Statistics summary cards -->
                <div class="control-panel stats-card">
                    <h3 class="panel-title">Overall Status</h3>
                    <div class="overall-percentage" id="lblOverallPercentage">--%</div>
                    <p class="overall-meta" id="lblOverallStatus">Minimum required: 75%</p>
                </div>
            </aside>

            <section class="right-panel">
                <div class="classdetails-area">
                    <h3 class="panel-title-large">Your Attendance Summary</h3>
                    <p style="font-size: 13px; color: #64748b; margin-top: 4px; margin-bottom: 12px; font-weight: 500;">(Click on a course row below to view detailed date-wise attendance logs)</p>
                </div>
                
                <div class="studentlist-area" id="summaryListArea">
                    <!-- Summary table loaded dynamically -->
                </div>
            </section>
        </main>
     </div>

     <!-- Detailed Log Modal -->
     <div class="modal-overlay" id="logsModal">
         <div class="modal-content">
             <div class="modal-header">
                 <div>
                     <h3 class="modal-title" id="modalCourseTitle">Course Name</h3>
                     <p class="modal-subtitle" id="modalFacultyName">Faculty: Name</p>
                 </div>
                 <button class="modal-close" id="btnCloseModal">&times;</button>
             </div>
             <div class="modal-body">
                 <div class="log-stats">
                     <div class="log-stat-item">
                         <span class="log-stat-value" id="modalClassesHeld">0</span>
                         <span class="log-stat-label">Total Classes</span>
                     </div>
                     <div class="log-stat-item">
                         <span class="log-stat-value" id="modalClassesAttended">0</span>
                         <span class="log-stat-label">Attended</span>
                     </div>
                     <div class="log-stat-item">
                         <span class="log-stat-value" id="modalAttendancePercent">0%</span>
                         <span class="log-stat-label">Percentage</span>
                     </div>
                 </div>
                 
                 <div class="detail-logs-list" id="detailedLogsArea">
                     <!-- Loaded dynamically -->
                 </div>
             </div>
         </div>
     </div>

     <input type="hidden" id="hiddenStudentId" value="<?php echo $student_id; ?>">
     <script src="js/jquery-4.0.0.min.js"></script>
     <script src="js/student.js?v=<?php echo time(); ?>"></script>
</body>
</html>
