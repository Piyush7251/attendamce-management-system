<?php
session_start();
    if(!isset($_SESSION["current_user"]))
    {
        header("location:"."/attendanceapp/login.php");
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">
    <title>Admin Dashboard</title>
</head>
<body>
     <div class="page">
        <div class="header-area">
            <div class="logo-area"> <h2 class="logo">ADMIN DASHBOARD</h2></div>
            <div class="logout-area"><button class="btnlogout" id="btnLogout">LOGOUT</button></div>
        </div>

        <main class="main-content">
            <aside class="left-panel">
                <div class="control-panel">
                    <h3 class="panel-title">Select Session</h3>
                    <select class="ddlclass" id="ddlclass">
                        <!-- Options will be loaded by JS -->
                    </select>
                </div>
                
                <div class="control-panel action-panel">
                    <h3 class="panel-title">Allocate Course</h3>
                    <div class="form-group">
                        <label>Faculty</label>
                        <select id="ddlFaculty"></select>
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <select id="ddlCourse"></select>
                    </div>
                    <div class="form-group">
                        <label>Class/Group (e.g., BCA)</label>
                        <input type="text" id="txtAllotClass" class="form-input" placeholder="e.g. BCA, MCA">
                    </div>
                    <button id="btnAllocate" class="btn-primary">Assign Course</button>
                    <div id="allocateMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
                </div>

                <div class="control-panel action-panel">
                    <h3 class="panel-title">Add Faculty</h3>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="txtNewFacName" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="txtNewFacUsername" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="txtNewFacPassword" class="form-input">
                    </div>
                    <button id="btnAddFaculty" class="btn-primary">Add Faculty</button>
                    <div id="addFacMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
                </div>

                <div class="control-panel action-panel">
                    <h3 class="panel-title">Add Course</h3>
                    <div class="form-group">
                        <label>Course Title</label>
                        <input type="text" id="txtNewCourseTitle" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Course Code</label>
                        <input type="text" id="txtNewCourseCode" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Credits</label>
                        <input type="number" id="txtNewCourseCredit" class="form-input">
                    </div>
                    <button id="btnAddCourse" class="btn-primary">Add Course</button>
                    <div id="addCourseMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
                </div>

                <div class="control-panel action-panel">
                    <h3 class="panel-title">Add Student</h3>
                    <div class="form-group">
                        <label>Student Name</label>
                        <input type="text" id="txtNewStudentName" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" id="txtNewStudentRoll" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Class/Group (e.g., BTech 2nd Sem)</label>
                        <input type="text" id="txtNewStudentClass" class="form-input" placeholder="e.g. BCA, BTech 2nd Sem">
                    </div>
                    <button id="btnAddStudent" class="btn-primary">Add Student</button>
                    <div id="addStudentMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
                </div>
            </aside>

            <section class="right-panel">
                <div class="classdetails-area">
                    <div class="tab-container">
                        <button class="admin-tab active" id="tabAllocations">Course Allocations</button>
                        <button class="admin-tab" id="tabManageCourses">Manage Courses</button>
                        <button class="admin-tab" id="tabManageFaculty">Manage Faculty</button>
                        <button class="admin-tab" id="tabManageStudents">Manage Students</button>
                    </div>
                </div>
                
                <!-- Tab 1 content: Allocations -->
                <div class="tab-content" id="allocationsTabContent">
                    <div class="studentlist-area" id="allotmentListArea">
                        <!-- Allocations will be loaded here -->
                    </div>
                </div>
                
                <!-- Tab 2 content: Course Management -->
                <div class="tab-content" id="manageCoursesTabContent" style="display: none;">
                    <div class="course-stats" id="courseStatsArea" style="margin-bottom: 20px; font-weight: 600; font-size: 1.1em; color: #a0aec0; padding-left: 10px;">
                        <!-- Course stats will load here -->
                    </div>
                    <div class="studentlist-area" id="courseListArea">
                        <!-- Courses will be loaded here -->
                    </div>
                </div>

                <!-- Tab 3 content: Faculty Management -->
                <div class="tab-content" id="manageFacultyTabContent" style="display: none;">
                    <div class="course-stats" id="facultyStatsArea" style="margin-bottom: 20px; font-weight: 600; font-size: 1.1em; color: #a0aec0; padding-left: 10px;">
                        <!-- Faculty stats will load here -->
                    </div>
                    <div class="studentlist-area" id="facultyListArea">
                        <!-- Faculty will be loaded here -->
                    </div>
                </div>

                <!-- Tab 4 content: Student Management -->
                <div class="tab-content" id="manageStudentsTabContent" style="display: none;">
                    <div class="course-stats" id="studentStatsArea" style="margin-bottom: 20px; font-weight: 600; font-size: 1.1em; color: #a0aec0; padding-left: 10px;">
                        <!-- Student stats will load here -->
                    </div>
                    <div class="studentlist-area" id="studentListArea">
                        <!-- Students will be loaded here -->
                    </div>
                </div>
            </section>
        </main>
     </div>
     
     <!-- Edit Course Modal Overlay -->
     <div id="editCourseModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">Edit Course</h3>
            <input type="hidden" id="editCourseId">
            <div class="form-group">
                <label>Course Title</label>
                <input type="text" id="txtEditCourseTitle" class="form-input">
            </div>
            <div class="form-group">
                <label>Course Code</label>
                <input type="text" id="txtEditCourseCode" class="form-input">
            </div>
            <div class="form-group">
                <label>Credits</label>
                <input type="number" id="txtEditCourseCredit" class="form-input">
            </div>
            <div class="modal-actions">
                <button id="btnSaveCourse" class="btn-primary">Save Changes</button>
                <button id="btnCancelEdit" class="btn-secondary">Cancel</button>
            </div>
            <div id="editCourseMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
        </div>
     </div>

     <!-- Edit Faculty Modal Overlay -->
     <div id="editFacultyModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">Edit Faculty</h3>
            <input type="hidden" id="editFacultyId">
            <div class="form-group">
                <label>Faculty Name</label>
                <input type="text" id="txtEditFacultyName" class="form-input">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="txtEditFacultyUsername" class="form-input">
            </div>
            <div class="form-group">
                <label>Password (leave blank to keep unchanged)</label>
                <input type="password" id="txtEditFacultyPassword" class="form-input" placeholder="New Password">
            </div>
            <div class="modal-actions">
                <button id="btnSaveFaculty" class="btn-primary">Save Changes</button>
                <button id="btnCancelEditFaculty" class="btn-secondary">Cancel</button>
            </div>
            <div id="editFacultyMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
        </div>
     </div>

     <!-- Edit Student Modal Overlay -->
     <div id="editStudentModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">Edit Student</h3>
            <input type="hidden" id="editStudentId">
            <div class="form-group">
                <label>Student Name</label>
                <input type="text" id="txtEditStudentName" class="form-input">
            </div>
            <div class="form-group">
                <label>Roll Number</label>
                <input type="text" id="txtEditStudentRoll" class="form-input">
            </div>
            <div class="form-group">
                <label>Class/Group</label>
                <input type="text" id="txtEditStudentClass" class="form-input">
            </div>
            <div class="modal-actions">
                <button id="btnSaveStudent" class="btn-primary">Save Changes</button>
                <button id="btnCancelEditStudent" class="btn-secondary">Cancel</button>
            </div>
            <div id="editStudentMsg" style="margin-top: 10px; font-size: 0.9em;"></div>
        </div>
     </div>
     
    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/admin.js?v=<?php echo time(); ?>"></script>
</body>
</html>