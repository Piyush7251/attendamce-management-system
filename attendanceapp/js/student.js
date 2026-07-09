// --- HTML Generators ---

function getSummaryTableHTML(reportList) {
    if (reportList.length === 0) {
        return '<div class="no-records">You are not registered for any courses in this session.</div>';
    }
    
    let x = `<div class="summary-table">
        <div class="summary-header">
            <div class="col-code">Code</div>
            <div class="col-title">Course Title</div>
            <div class="col-faculty">Instructor</div>
            <div class="col-held">Conducted</div>
            <div class="col-attended">Attended</div>
            <div class="col-percent">Percentage</div>
        </div>`;
        
    for (let i = 0; i < reportList.length; i++) {
        let course = reportList[i];
        let badgeColor = course.percentage >= 75 ? 'present-badge' : 'absent-badge';
        let statusLine = course.percentage >= 75 ? 'sufficient-attendance' : 'critical-attendance';
        
        x += `<div class="summary-row ${statusLine}" data-courseid="${course.course_id}" data-coursetitle="${course.code} - ${course.title}" data-facultyname="${course.faculty_name}" data-held="${course.total_classes}" data-attended="${course.attended_classes}" data-percent="${course.percentage}">
            <div class="col-code">${course.code}</div>
            <div class="col-title">${course.title}</div>
            <div class="col-faculty">${course.faculty_name}</div>
            <div class="col-held">${course.total_classes}</div>
            <div class="col-attended">${course.attended_classes}</div>
            <div class="col-percent">
                <span class="pct-badge ${badgeColor}">${course.percentage}%</span>
            </div>
        </div>`;
    }
    x += `</div>`;
    return x;
}

function getDetailedLogsHTML(logs) {
    if (logs.length === 0) {
        return '<div class="no-logs">No attendance history logged for this course yet.</div>';
    }
    
    let x = '';
    for (let i = 0; i < logs.length; i++) {
        let log = logs[i];
        let statusClass = log.status === 'YES' ? 'log-present' : 'log-absent';
        let statusText = log.status === 'YES' ? 'Present' : 'Absent';
        
        // Format date string from YYYY-MM-DD to a more readable format e.g. "08 Jul 2026"
        let rawDate = new Date(log.on_date);
        let options = { day: '2-digit', month: 'short', year: 'numeric' };
        let formattedDate = rawDate.toLocaleDateString('en-GB', options);
        
        x += `
        <div class="log-row">
            <div class="log-date">${formattedDate}</div>
            <div class="log-status-badge ${statusClass}">${statusText}</div>
        </div>
        `;
    }
    return x;
}

// --- AJAX Callers ---

function loadProfile() {
    $.ajax({
        url: "ajaxhandler/studentAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getStudentProfile" },
        success: function(profile) {
            if (profile) {
                $("#lblStudentName").text(profile.name);
                $("#lblStudentRoll").text("Roll No: " + profile.roll_no);
                $("#lblStudentClass").text("Class: " + (profile.class_name || 'N/A'));
                
                // Set first letter avatar
                let initial = profile.name.trim().charAt(0).toUpperCase();
                $("#profileAvatar").text(initial);
            }
        },
        error: function(e) {
            console.error("Failed to load student profile:", e);
        }
    });
}

function loadSessions() {
    $.ajax({
        url: "ajaxhandler/studentAjax.php",
        type: "POST",
        dataType: "json",
        data: { action: "getSessions" },
        success: function(rv) {
            let x = ``;
            for (let i = 0; i < rv.length; i++) {
                let cs = rv[i];
                x += `<option value=${cs['id']}>${cs['year']} ${cs['term']}</option>`;
            }
            $("#ddlSession").html(x);
            
            // Trigger load for first selected session
            let sessionid = $("#ddlSession").val();
            if (sessionid) {
                fetchDashboardData(sessionid);
            }
        },
        error: function(e) {
            console.error("Failed to load sessions:", e);
        }
    });
}

function fetchDashboardData(sessionid) {
    $.ajax({
        url: "ajaxhandler/studentAjax.php",
        type: "POST",
        dataType: "json",
        data: { sessionid: sessionid, action: "getDashboardData" },
        beforeSend: function() {
            $("#summaryListArea").html("<p class='loading-text'>Loading summary...</p>");
        },
        success: function(rv) {
            let x = getSummaryTableHTML(rv);
            $("#summaryListArea").html(x);
            
            // Calculate and display overall status metrics
            calculateOverallStats(rv);
        },
        error: function(e) {
            $("#summaryListArea").html("<p class='error-text'>Failed to load data.</p>");
            console.error("Failed to fetch dashboard data:", e);
        }
    });
}

function fetchDetailedLogs(sessionid, courseid, courseTitle, facultyName, totalClasses, attendedClasses, percentage) {
    $.ajax({
        url: "ajaxhandler/studentAjax.php",
        type: "POST",
        dataType: "json",
        data: { sessionid: sessionid, courseid: courseid, action: "getDetailedLogs" },
        beforeSend: function() {
            $("#detailedLogsArea").html("<p class='loading-text'>Loading detailed logs...</p>");
        },
        success: function(logs) {
            // Set modal metadata
            $("#modalCourseTitle").text(courseTitle);
            $("#modalFacultyName").text("Instructor: " + facultyName);
            $("#modalClassesHeld").text(totalClasses);
            $("#modalClassesAttended").text(attendedClasses);
            $("#modalAttendancePercent").text(percentage + "%");
            
            // Render logs list
            let x = getDetailedLogsHTML(logs);
            $("#detailedLogsArea").html(x);
            
            // Open modal overlay
            $("#logsModal").addClass("active");
        },
        error: function(e) {
            $("#detailedLogsArea").html("<p class='error-text'>Failed to load logs.</p>");
            console.error("Failed to fetch detailed logs:", e);
        }
    });
}

function calculateOverallStats(reportList) {
    if (reportList.length === 0) {
        $("#lblOverallPercentage").text("--%");
        $("#lblOverallStatus").text("No registered courses").removeClass("status-good status-bad");
        return;
    }
    
    let totalClassesHeld = 0;
    let totalClassesAttended = 0;
    
    for (let i = 0; i < reportList.length; i++) {
        totalClassesHeld += reportList[i].total_classes;
        totalClassesAttended += reportList[i].attended_classes;
    }
    
    let overallPercentage = 0.00;
    if (totalClassesHeld > 0) {
        overallPercentage = Math.round((totalClassesAttended / totalClassesHeld) * 10000) / 100;
    }
    
    $("#lblOverallPercentage").text(overallPercentage.toFixed(2) + "%");
    
    if (overallPercentage >= 75.0) {
        $("#lblOverallPercentage").css("color", "#10b981");
        $("#lblOverallStatus").text("Sufficient attendance. Keep it up!").removeClass("status-bad").addClass("status-good");
    } else {
        $("#lblOverallPercentage").css("color", "#ef4444");
        $("#lblOverallStatus").text("Warning: Attendance is below 75%!").removeClass("status-good").addClass("status-bad");
    }
}

// --- Event Listeners ---

$(function() {
    loadProfile();
    loadSessions();

    // Session dropdown change
    $(document).on("change", "#ddlSession", function() {
        let sessionid = $(this).val();
        if (sessionid) {
            fetchDashboardData(sessionid);
        }     
    });

    // Logout button click
    $(document).on("click", "#btnLogout", function(e) {
        e.preventDefault();
        $.ajax({
            url: "ajaxhandler/logoutAjax.php",
            type: "POST",
            dataType: "json",
            success: function() {
                window.location.replace("login.php"); 
            },
            error: function(e) {
                console.error("Logout error details:", e);
                alert("Something went wrong during logout!");
            }
        });
    });

    // Row click handler to load detailed modal logs
    $(document).on("click", ".summary-row", function() {
        let courseid = $(this).data("courseid");
        let coursetitle = $(this).data("coursetitle");
        let facultyname = $(this).data("facultyname");
        let held = $(this).data("held");
        let attended = $(this).data("attended");
        let percent = $(this).data("percent");
        let sessionid = $("#ddlSession").val();
        
        if (sessionid && courseid) {
            fetchDetailedLogs(sessionid, courseid, coursetitle, facultyname, held, attended, percent);
        }
    });

    // Modal Close
    $(document).on("click", "#btnCloseModal", function() {
        $("#logsModal").removeClass("active");
    });
    
    // Close modal on clicking outside content area
    $(document).on("click", "#logsModal", function(e) {
        if ($(e.target).hasClass("modal-overlay")) {
            $("#logsModal").removeClass("active");
        }
    });
});
