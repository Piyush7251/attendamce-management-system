// --- HTML Generators ---

function getSessionHTML(rv) {
    let x = `<option value=-1>SELECT ONE</option>`;
    for (let i = 0; i < rv.length; i++) {
        let cs = rv[i];
        x += `<option value=${cs['id']}>${cs['year']} ${cs['term']}</option>`;
    }
    return x;
}

function getCourseCardHTML(classlist) {
    let x = ``;
    for (let i = 0; i < classlist.length; i++) {
        let cc = classlist[i];
        // Safely store the object data directly in the DOM element
        x += `<div class="classcard" data-classobject='${JSON.stringify(cc)}'>${cc['code']} (${cc['class_name']})</div>`;
    }
    return x;
}

function getClassdetailsAreaHTML(classobject) {
    // More concise way to get YYYY-MM-DD format
    const ondate = new Date().toISOString().split('T')[0];
    
    let x = `<div class="classdetails">
        <div class="code-area">${classobject['code']}</div>
        <div class="title-area">${classobject['title']}</div>
        <div class="ondate-area">
            <input type="date" value='${ondate}' id='dtpondate'>
        </div>
    </div>`;
    return x;
}

function getStudentListHTML(studentList) {
    if (studentList.length === 0) {
        return '<div class="studentlist-header">No students registered for this course.</div>';
    }
    // Create a header row
    let x = `<div class="studentlist-header">
        <div class="slno-area">#</div>
        <div class="rollno-area">Roll No</div>
        <div class="name-area">Name</div>
        <div class="checkbox-area">Present</div>
    </div>`;
    
    for (let i = 0; i < studentList.length; i++) {
        let cs = studentList[i];
        let checkedState = cs['isPresent'] == 'YES' ? `checked` : ``;
        let rowcolor = cs['isPresent'] == 'YES' ? 'presentcolor' : 'absentcolor';

        x += `<div class="studentdetails ${rowcolor}" id="student${cs['id']}">
            <div class="slno-area">${(i + 1)}</div>
            <div class="rollno-area">${cs['roll_no']}</div>
            <div class="name-area">${cs['name']}</div>
            <div class="checkbox-area" data-studentid='${cs['id']}'>
                <input type="checkbox" class="cbpresent" data-studentid='${cs['id']}' ${checkedState}>
            </div>
        </div>`;
    }
    x += `<div class="reportsection">
        <button id="btnReport">GENERATE & DOWNLOAD REPORT</button>
    </div>
    <div id="divReport"></div>`;
    return x;
}

// --- AJAX Callers ---

function loadSessions() {
    $.ajax({
        url: "ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { action: "getSession" },
        success: function(rv) {
            let x = getSessionHTML(rv);
            $("#ddlclass").html(x);
        },
        error: function(e) {
            console.error("Failed to load sessions:", e);
        }
    });
}

function fetchFacultyCourses(sessionid) {
    $.ajax({
        url: "ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { sessionid: sessionid, action: "getFacultyCourses" },
        beforeSend: function() {
            $("#classlistarea").html("<p>Loading courses...</p>");
        },
        success: function(rv) {
            if (rv.length === 0) {
                $("#classlistarea").html("<p>No courses allotted for this session.</p>");
                return;
            }
            let x = getCourseCardHTML(rv);
            $("#classlistarea").html(x);
        },
        error: function(e) {
            $("#classlistarea").html("<p style='color:red;'>Failed to load courses.</p>");
            console.error("Failed to fetch courses:", e);
        }
    });
}

function fetchStudentList(sessionid, classid, ondate, class_name) {
    $.ajax({
        url: "ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { ondate: ondate, sessionid: sessionid, classid: classid, class_name: class_name, action: "getStudentList" },
        beforeSend: function() {
            $("#studentlistarea").html("<p>Loading student list...</p>");
        },
        success: function(rv) {
            let x = getStudentListHTML(rv);
            $("#studentlistarea").html(x);
        },
        error: function(e) {
            $("#studentlistarea").html("<p style='color:red;'>Failed to load student list.</p>");
            console.error("Failed to fetch student list:", e);
        }
    });
}

function saveAttendance(studentid, courseid, sessionid, ondate, ispresent) {
    $.ajax({
        url: "ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { studentid: studentid, courseid: courseid, sessionid: sessionid, ondate: ondate, ispresent: ispresent, action: "saveattendance" },
        success: function(rv) {
            // Update UI color based on selection
            if (ispresent == "YES") {
                $("#student" + studentid).removeClass('absentcolor').addClass('presentcolor');
            } else {
                $("#student" + studentid).removeClass('presentcolor').addClass('absentcolor');
            }
        },
        error: function(e) {
            console.error("Failed to save attendance:", e);
        }
    });
}

function downloadCSV(sessionid, classid, class_name) {
    let url = "ajaxhandler/attendanceAJAX.php?action=downloadReport&sessionid=" + sessionid + "&classid=" + classid + "&class_name=" + encodeURIComponent(class_name);
    window.location.href = url;
}

// --- Event Listeners ---

$(function() {
    loadSessions();

    $(document).on("click", "#btnLogout", function(e) {
        e.preventDefault(); // Forces the button to ONLY run our AJAX call
        
        $.ajax({
            url: "ajaxhandler/logoutAjax.php",
            type: "POST",
            dataType: "json",
            success: function(rv) {
                // window.location is slightly more reliable than document.location
                window.location.replace("login.php"); 
            },
            error: function(e) {
                console.error("Logout error details:", e);
                alert("Something went wrong during logout!");
            }
        });
    });

    $(document).on("change", "#ddlclass", function() {
        $("#classlistarea").empty();
        $("#classdetailsarea").empty();
        $("#studentlistarea").empty();
        
        let sessionid = $(this).val();
        if (sessionid != -1) {
            fetchFacultyCourses(sessionid);
        }     
    });

    $(document).on("click", ".classcard", function() {
        // Handle selected state for UI
        $('.classcard').removeClass('selected');
        $(this).addClass('selected');

        let classobject = $(this).data('classobject');
        $("#hiddenSelectedCourseID").val(classobject['id']);
        $("#hiddenSelectedClassName").val(classobject['class_name']);
        
        let x = getClassdetailsAreaHTML(classobject);
        $("#classdetailsarea").html(x);
        
        let sessionid = $("#ddlclass").val();
        let classid = classobject['id'];
        let class_name = classobject['class_name'];
        let ondate = $("#dtpondate").val();
        
        if (sessionid != -1) {
            fetchStudentList(sessionid, classid, ondate, class_name);
        }
    });

    $(document).on("click", ".cbpresent", function() {
        let ispresent = this.checked ? "YES" : "NO";     
        let studentid = $(this).data('studentid');
        let courseid = $("#hiddenSelectedCourseID").val();
        let sessionid = $("#ddlclass").val();
        let ondate = $("#dtpondate").val();
        
        saveAttendance(studentid, courseid, sessionid, ondate, ispresent);
    });

    $(document).on("change", "#dtpondate", function() {
        let sessionid = $("#ddlclass").val();
        let classid = $("#hiddenSelectedCourseID").val();
        let class_name = $("#hiddenSelectedClassName").val();
        let ondate = $("#dtpondate").val();
        
        if (sessionid != -1 && classid != -1) {
            fetchStudentList(sessionid, classid, ondate, class_name);
        }
    });

    $(document).on("click", "#btnReport", function() {
        $("#divReport").html("<span style='color:blue;'>Generating report...</span>");
        let sessionid = $("#ddlclass").val();
        let classid = $("#hiddenSelectedCourseID").val();
        let class_name = $("#hiddenSelectedClassName").val();
        downloadCSV(sessionid, classid, class_name);
    });
});