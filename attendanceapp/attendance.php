<?php
session_start();
    if(isset($_SESSION["current_user"]))
        {
          $facid=$_SESSION["current_user"];
        }
    else{
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
    <link rel="stylesheet" href="css/attendance.css">
    <title>Attendance App</title>
</head>
<body>
     <div class="page">
        <div class="header-area">
            <div class="logo-area"> <h2 class="logo">ATTENDANCE APP</h2></div>
            <div class="logout-area"><button class="btnlogout" id="btnLogout">LOGOUT</button></div>
        </div>

        <main class="main-content">
            <aside class="left-panel">
                <div class="control-panel">
                    <h3 class="panel-title">Session</h3>
                    <select class="ddlclass" id="ddlclass">
                        <!-- Options will be loaded by JS -->
                    </select>
                </div>
                <div class="control-panel">
                    <h3 class="panel-title">Your Courses</h3>
                    <div class="classlist-area" id="classlistarea">
                        <!-- Course cards will be loaded by JS -->
                    </div>
                </div>
            </aside>

            <section class="right-panel">
                <div class="classdetails-area" id="classdetailsarea"></div>
                <div class="studentlist-area" id="studentlistarea"></div>
            </section>
        </main>
     </div>
     <input type="hidden" id="hiddenFacId" value=<?php echo($facid) ?>>
     <input type="hidden" id="hiddenSelectedCourseID" value=-1>
     <input type="hidden" id="hiddenSelectedClassName" value="">
    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/attendance.js?v=<?php echo time(); ?>"></script>
    <!--renamed the files just to keep the filenames
    similar, nothing more than that-->
</body>
</html>