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
                        <span class="course-info">${item.code} - ${item.title}</span>
                    </div>
                    <button class="btn-remove" data-facultyid="${item.faculty_id}" data-courseid="${item.course_id}">Remove</button>
                </div>
                `;
            }
            $("#allotmentListArea").html(x);
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

        if (sessionid == -1 || facultyid == -1 || courseid == -1) {
            $("#allocateMsg").html("<span style='color:red'>Please select session, faculty, and course.</span>");
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
                courseid: courseid
            },
            success: function(rv) {
                if (rv.status === "SUCCESS") {
                    $("#allocateMsg").html("<span style='color:green'>Allocated successfully!</span>");
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

        $.ajax({
            url: "ajaxhandler/adminAjax.php",
            type: "POST",
            dataType: "json",
            data: { 
                action: "removeAllotment", 
                sessionid: sessionid,
                facultyid: facultyid,
                courseid: courseid
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
});
