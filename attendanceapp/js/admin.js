// --- AJAX Callers ---

function loadSessions() {
    $.ajax({
        url: "ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { action: "getSession" },
        success: function(rv) {
            let x = `<option value=-1>SELECT ONE</option>`;
            for (let i = 0; i < rv.length; i++) {
                let cs = rv[i];
                x += `<option value=${cs['id']}>${cs['year']} ${cs['term']}</option>`;
            }
            $("#ddlclass").html(x);
        },
        error: function(e) {
            console.error("Failed to load sessions:", e);
        }
    });
}

function loadFaculties() {
    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getFaculties" },
        success: function(rv) {
            let x = `<option value=-1>Select Faculty</option>`;
            for (let i = 0; i < rv.length; i++) {
                let f = rv[i];
                x += `<option value=${f['id']}>${f['name']} (${f['user_name']})</option>`;
            }
            $("#ddlFaculty").html(x);
        }
    });
}

function loadCourses() {
    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getCourses" },
        success: function(rv) {
            let x = `<option value=-1>Select Course</option>`;
            for (let i = 0; i < rv.length; i++) {
                let c = rv[i];
                x += `<option value=${c['id']}>${c['code']} - ${c['title']}</option>`;
            }
            $("#ddlCourse").html(x);
        }
    });
}

function loadAllotments() {
    let sessionid = $("#ddlclass").val();
    if (sessionid == -1) {
        $("#allotmentListArea").html("<p>Please select a session.</p>");
        return;
    }

    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getAllotments", sessionid: sessionid },
        success: function(rv) {
            if (rv.length === 0) {
                $("#allotmentListArea").html("<p>No courses allocated for this session.</p>");
                return;
            }

            let x = '';
            for (let i = 0; i < rv.length; i++) {
                let item = rv[i];
                x += `
                <div class="allotment-item">
                    <div class="allotment-info">
                        <span class="faculty-name">${item.faculty_name}</span>
                        <span class="course-info">${item.code} - ${item.title} (${item.class_name})</span>
                    </div>
                    <button class="btn-remove" data-facultyid="${item.faculty_id}" data-courseid="${item.course_id}" data-classname="${item.class_name}">Remove</button>
                </div>
                `;
            }
            $("#allotmentListArea").html(x);
        }
    });
}

function loadCourseManagement() {
    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getCourses" },
        success: function(rv) {
            $("#courseStatsArea").text("Total Courses: " + rv.length);
            
            if (rv.length === 0) {
                $("#courseListArea").html("<p style='padding-left: 10px;'>No courses found.</p>");
                return;
            }

            let x = '';
            for (let i = 0; i < rv.length; i++) {
                let item = rv[i];
                // Safely store course data directly in DOM
                x += `
                <div class="allotment-item" id="courseRow${item.id}">
                    <div class="allotment-info">
                        <span class="faculty-name">${item.title}</span>
                        <span class="course-info">${item.code} (Credits: ${item.credit})</span>
                    </div>
                    <div class="course-actions">
                        <button class="btn-edit btnEditCourse" data-course='${JSON.stringify(item)}'>Edit</button>
                        <button class="btn-remove btnDeleteCourse" data-id="${item.id}" data-title="${item.title}">Delete</button>
                    </div>
                </div>
                `;
            }
            $("#courseListArea").html(x);
        }
    });
}

function loadFacultyManagement() {
    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getFaculties" },
        success: function(rv) {
            $("#facultyStatsArea").text("Total Faculty: " + rv.length);
            
            if (rv.length === 0) {
                $("#facultyListArea").html("<p style='padding-left: 10px;'>No faculty found.</p>");
                return;
            }

            let x = '';
            for (let i = 0; i < rv.length; i++) {
                let item = rv[i];
                x += `
                <div class="allotment-item" id="facultyRow${item.id}">
                    <div class="allotment-info">
                        <span class="faculty-name">${item.name}</span>
                        <span class="course-info">Username: ${item.user_name}</span>
                    </div>
                    <div class="course-actions">
                        <button class="btn-edit btnEditFaculty" data-faculty='${JSON.stringify(item)}'>Edit</button>
                        <button class="btn-remove btnDeleteFaculty" data-id="${item.id}" data-name="${item.name}">Delete</button>
                    </div>
                </div>
                `;
            }
            $("#facultyListArea").html(x);
        }
    });
}

function loadStudentManagement() {
    $.ajax({
        url: "ajaxhandler/adminAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getStudents" },
        success: function(rv) {
            $("#studentStatsArea").text("Total Students: " + rv.length);
            
            if (rv.length === 0) {
                $("#studentListArea").html("<p style='padding-left: 10px;'>No students found.</p>");
                return;
            }

            let x = '';
            for (let i = 0; i < rv.length; i++) {
                let item = rv[i];
                x += `
                <div class="allotment-item" id="studentRow${item.id}">
                    <div class="allotment-info">
                        <span class="faculty-name">${item.name}</span>
                        <span class="course-info">Roll No: ${item.roll_no} | Class: ${item.class_name}</span>
                    </div>
                    <div class="course-actions">
                        <button class="btn-edit btnEditStudent" data-student='${JSON.stringify(item)}'>Edit</button>
                        <button class="btn-remove btnDeleteStudent" data-id="${item.id}" data-name="${item.name}">Delete</button>
                    </div>
                </div>
                `;
            }
            $("#studentListArea").html(x);
        }
    });
}

// --- Event Listeners ---

$(function() {
    loadSessions();
    loadFaculties();
    loadCourses();

    $(document).on("click", "#btnLogout", function(e) {
        e.preventDefault();
        $.ajax({
            url: "ajaxhandler/logoutAjax.php",
            type: "POST",
            dataType: "json",
            success: function() {
                window.location.replace("login.php"); 
            }
        });
    });

    $(document).on("change", "#ddlclass", function() {
        loadAllotments();
    });

    $(document).on("click", "#btnAllocate", function() {
        let sessionid = $("#ddlclass").val();
        let facultyid = $("#ddlFaculty").val();
        let courseid = $("#ddlCourse").val();
        let class_name = $("#txtAllotClass").val().trim();

        if (sessionid == -1 || facultyid == -1 || courseid == -1 || class_name == "") {
            $("#allocateMsg").html("<span style='color:red'>Please select session, faculty, course, and enter class.</span>");
            return;
        }

        $("#allocateMsg").html("<span style='color:blue'>Allocating...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "addAllotment", 
                sessionid: sessionid,
                facultyid: facultyid,
                courseid: courseid,
                class_name: class_name
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#allocateMsg").html("<span style='color:green'>Allocated successfully!</span>");
                    $("#txtAllotClass").val("");
                    loadAllotments(); // refresh list
                } else {
                    $("#allocateMsg").html("<span style='color:red'>Allocation failed.</span>");
                }
            }
        });
    });

    $(document).on("click", ".btn-remove", function() {
        if(!confirm("Are you sure you want to remove this course allocation?")) return;

        let sessionid = $("#ddlclass").val();
        let facultyid = $(this).data("facultyid");
        let courseid = $(this).data("courseid");
        let class_name = $(this).data("classname");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "removeAllotment", 
                sessionid: sessionid,
                facultyid: facultyid,
                courseid: courseid,
                class_name: class_name
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    loadAllotments(); // refresh list
                } else {
                    alert("Failed to remove allocation.");
                }
            }
        });
    });

    $(document).on("click", "#btnAddFaculty", function() {
        let name = $("#txtNewFacName").val();
        let username = $("#txtNewFacUsername").val();
        let password = $("#txtNewFacPassword").val();

        if (name == "" || username == "" || password == "") {
            $("#addFacMsg").html("<span style='color:red'>Please fill all fields.</span>");
            return;
        }

        $("#addFacMsg").html("<span style='color:blue'>Adding...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "addFaculty", 
                name: name,
                user_name: username,
                password: password
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#addFacMsg").html("<span style='color:green'>Faculty added successfully!</span>");
                    $("#txtNewFacName").val("");
                    $("#txtNewFacUsername").val("");
                    $("#txtNewFacPassword").val("");
                    loadFaculties(); // refresh list
                } else {
                    $("#addFacMsg").html("<span style='color:red'>" + rv.status + "</span>");
                }
            }
        });
    });

    $(document).on("click", "#btnAddCourse", function() {
        let title = $("#txtNewCourseTitle").val();
        let code = $("#txtNewCourseCode").val();
        let credit = $("#txtNewCourseCredit").val();

        if (title == "" || code == "" || credit == "") {
            $("#addCourseMsg").html("<span style='color:red'>Please fill all fields.</span>");
            return;
        }

        $("#addCourseMsg").html("<span style='color:blue'>Adding...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "addCourse", 
                title: title,
                code: code,
                credit: credit
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#addCourseMsg").html("<span style='color:green'>Course added successfully!</span>");
                    $("#txtNewCourseTitle").val("");
                    $("#txtNewCourseCode").val("");
                    $("#txtNewCourseCredit").val("");
                    loadCourses(); // refresh list
                } else {
                    $("#addCourseMsg").html("<span style='color:red'>" + rv.status + "</span>");
                }
            }
        });
    });

    $(document).on("click", "#btnAddStudent", function() {
        let name = $("#txtNewStudentName").val();
        let roll_no = $("#txtNewStudentRoll").val();
        let class_name = $("#txtNewStudentClass").val();

        if (name == "" || roll_no == "" || class_name == "") {
            $("#addStudentMsg").html("<span style='color:red'>Please fill all fields.</span>");
            return;
        }

        $("#addStudentMsg").html("<span style='color:blue'>Adding...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "addStudent", 
                name: name,
                roll_no: roll_no,
                class_name: class_name
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#addStudentMsg").html("<span style='color:green'>Student added successfully!</span>");
                    $("#txtNewStudentName").val("");
                    $("#txtNewStudentRoll").val("");
                    $("#txtNewStudentClass").val("");
                } else {
                    $("#addStudentMsg").html("<span style='color:red'>" + rv.status + "</span>");
                }
            }
        });
    });

    // --- Course Management Event Listeners ---

    // Tab toggles
    $(document).on("click", "#tabAllocations", function() {
        $(".admin-tab").removeClass("active");
        $(this).addClass("active");
        $(".tab-content").hide();
        $("#allocationsTabContent").show();
        loadAllotments();
    });

    $(document).on("click", "#tabManageCourses", function() {
        $(".admin-tab").removeClass("active");
        $(this).addClass("active");
        $(".tab-content").hide();
        $("#manageCoursesTabContent").show();
        loadCourseManagement();
    });

    $(document).on("click", "#tabManageFaculty", function() {
        $(".admin-tab").removeClass("active");
        $(this).addClass("active");
        $(".tab-content").hide();
        $("#manageFacultyTabContent").show();
        loadFacultyManagement();
    });

    $(document).on("click", "#tabManageStudents", function() {
        $(".admin-tab").removeClass("active");
        $(this).addClass("active");
        $(".tab-content").hide();
        $("#manageStudentsTabContent").show();
        loadStudentManagement();
    });

    // Open edit modal
    $(document).on("click", ".btnEditCourse", function() {
        let course = $(this).data("course");
        $("#editCourseId").val(course.id);
        $("#txtEditCourseTitle").val(course.title);
        $("#txtEditCourseCode").val(course.code);
        $("#txtEditCourseCredit").val(course.credit);
        $("#editCourseMsg").empty();
        $("#editCourseModal").fadeIn();
    });

    // Cancel edit
    $(document).on("click", "#btnCancelEdit", function() {
        $("#editCourseModal").fadeOut();
    });

    // Save changes
    $(document).on("click", "#btnSaveCourse", function() {
        let id = $("#editCourseId").val();
        let title = $("#txtEditCourseTitle").val().trim();
        let code = $("#txtEditCourseCode").val().trim();
        let credit = $("#txtEditCourseCredit").val().trim();

        if (title === "" || code === "" || credit === "") {
            $("#editCourseMsg").html("<span style='color:red;'>All fields are required.</span>");
            return;
        }

        $("#editCourseMsg").html("<span style='color:blue;'>Saving changes...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "updateCourse",
                id: id,
                title: title,
                code: code,
                credit: credit
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#editCourseMsg").html("<span style='color:green;'>Course updated successfully!</span>");
                    setTimeout(function() {
                        $("#editCourseModal").fadeOut();
                        loadCourseManagement();
                        loadCourses(); // refresh dropdowns
                    }, 1000);
                } else {
                    $("#editCourseMsg").html("<span style='color:red;'>" + rv.status + "</span>");
                }
            },
            error: function() {
                $("#editCourseMsg").html("<span style='color:red;'>Failed to update course.</span>");
            }
        });
    });

    // Delete course
    $(document).on("click", ".btnDeleteCourse", function() {
        let id = $(this).data("id");
        let title = $(this).data("title");

        if (!confirm("Are you sure you want to delete '" + title + "'? This will also delete all allotments and attendance logs for this course!")) {
            return;
        }

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "deleteCourse",
                id: id
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    loadCourseManagement();
                    loadCourses(); // refresh dropdowns
                    loadAllotments(); // refresh allotments
                } else {
                    alert("Failed to delete course: " + rv.status);
                }
            },
            error: function() {
                alert("Failed to delete course.");
            }
        });
    });

    // --- Faculty Management Event Listeners ---

    // Open edit faculty modal
    $(document).on("click", ".btnEditFaculty", function() {
        let faculty = $(this).data("faculty");
        $("#editFacultyId").val(faculty.id);
        $("#txtEditFacultyName").val(faculty.name);
        $("#txtEditFacultyUsername").val(faculty.user_name);
        $("#txtEditFacultyPassword").val("");
        $("#editFacultyMsg").empty();
        $("#editFacultyModal").fadeIn();
    });

    // Cancel edit faculty
    $(document).on("click", "#btnCancelEditFaculty", function() {
        $("#editFacultyModal").fadeOut();
    });

    // Save faculty changes
    $(document).on("click", "#btnSaveFaculty", function() {
        let id = $("#editFacultyId").val();
        let name = $("#txtEditFacultyName").val().trim();
        let username = $("#txtEditFacultyUsername").val().trim();
        let password = $("#txtEditFacultyPassword").val().trim();

        if (name === "" || username === "") {
            $("#editFacultyMsg").html("<span style='color:red;'>Name and Username are required.</span>");
            return;
        }

        $("#editFacultyMsg").html("<span style='color:blue;'>Saving changes...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "updateFaculty",
                id: id,
                name: name,
                username: username,
                password: password
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#editFacultyMsg").html("<span style='color:green;'>Faculty updated successfully!</span>");
                    setTimeout(function() {
                        $("#editFacultyModal").fadeOut();
                        loadFacultyManagement();
                        loadFaculties(); // refresh dropdowns
                    }, 1000);
                } else {
                    $("#editFacultyMsg").html("<span style='color:red;'>" + rv.status + "</span>");
                }
            },
            error: function() {
                $("#editFacultyMsg").html("<span style='color:red;'>Failed to update faculty.</span>");
            }
        });
    });

    // Delete faculty
    $(document).on("click", ".btnDeleteFaculty", function() {
        let id = $(this).data("id");
        let name = $(this).data("name");

        if (!confirm("Are you sure you want to delete '" + name + "'? This will also delete all allotments and attendance logs for this faculty!")) {
            return;
        }

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "deleteFaculty",
                id: id
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    loadFacultyManagement();
                    loadFaculties(); // refresh dropdowns
                    loadAllotments(); // refresh allotments
                } else {
                    alert("Failed to delete faculty: " + rv.status);
                }
            },
            error: function() {
                alert("Failed to delete faculty.");
            }
        });
    });

    // --- Student Management Event Listeners ---

    // Open edit student modal
    $(document).on("click", ".btnEditStudent", function() {
        let student = $(this).data("student");
        $("#editStudentId").val(student.id);
        $("#txtEditStudentName").val(student.name);
        $("#txtEditStudentRoll").val(student.roll_no);
        $("#txtEditStudentClass").val(student.class_name);
        $("#editStudentMsg").empty();
        $("#editStudentModal").fadeIn();
    });

    // Cancel edit student
    $(document).on("click", "#btnCancelEditStudent", function() {
        $("#editStudentModal").fadeOut();
    });

    // Save student changes
    $(document).on("click", "#btnSaveStudent", function() {
        let id = $("#editStudentId").val();
        let name = $("#txtEditStudentName").val().trim();
        let roll_no = $("#txtEditStudentRoll").val().trim();
        let class_name = $("#txtEditStudentClass").val().trim();

        if (name === "" || roll_no === "" || class_name === "") {
            $("#editStudentMsg").html("<span style='color:red;'>All fields are required.</span>");
            return;
        }

        $("#editStudentMsg").html("<span style='color:blue;'>Saving changes...</span>");

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "updateStudent",
                id: id,
                name: name,
                roll_no: roll_no,
                class_name: class_name
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#editStudentMsg").html("<span style='color:green;'>Student updated successfully!</span>");
                    setTimeout(function() {
                        $("#editStudentModal").fadeOut();
                        loadStudentManagement();
                    }, 1000);
                } else {
                    $("#editStudentMsg").html("<span style='color:red;'>" + rv.status + "</span>");
                }
            },
            error: function() {
                $("#editStudentMsg").html("<span style='color:red;'>Failed to update student.</span>");
            }
        });
    });

    // Delete student
    $(document).on("click", ".btnDeleteStudent", function() {
        let id = $(this).data("id");
        let name = $(this).data("name");

        if (!confirm("Are you sure you want to delete student '" + name + "'? This will also delete all attendance logs for this student!")) {
            return;
        }

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "deleteStudent",
                id: id
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    loadStudentManagement();
                } else {
                    alert("Failed to delete student: " + rv.status);
                }
            },
            error: function() {
                alert("Failed to delete student.");
            }
        });
    });
});
