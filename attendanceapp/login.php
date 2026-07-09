<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/loader.css">
    <title>Login Page</title>
</head>
<body>
    <form class="loginform" id="loginForm" onsubmit="return false;">
        <div class="login-tabs">
            <button class="login-tab active" id="tabStudent" type="button">Student Login</button>
            <button class="login-tab" id="tabStaff" type="button">Staff Login</button>
        </div>
        <input type="hidden" id="loginType" value="student">
        
        <h2 class="form-title" id="loginTitle">Student Login</h2>
        
        <div class="inputgroup">
            <input type="text" id="txtUsername" required autocomplete="username" placeholder="e.g. 2410302001">
            <label for="txtUsername" id="lblUsername">Roll Number</label>
        </div>

        <div class="inputgroup">
            <input type="password" id="txtPassword" required autocomplete="current-password">
            <label for="txtPassword" id="lblPassword">Password</label>
        </div>

        <div class="divcallforaction">
            <button type="submit" class="btnlogin inactivecolor" id="btnLogin">Login</button>
        </div>  

        <div class="form-links">
            <a href="#" id="linkReset">Forgot Password?</a>
            <a href="#" id="linkRegister" style="display: none;">Create Account</a>
        </div>
        
        <div class="diverror" id="diverror" role="alert" aria-live="assertive">
            <span class="errormessage" id="errormessage">Error goes here</span>
        </div>
    </form>

    <form class="loginform" id="registerForm" onsubmit="return false;" style="display: none;">
        <h2 class="form-title">Register Faculty</h2>
        
        <div class="inputgroup">
            <input type="text" id="txtRegName" required>
            <label for="txtRegName">Full Name</label>
        </div>

        <div class="inputgroup">
            <input type="text" id="txtRegUsername" required autocomplete="username">
            <label for="txtRegUsername">Username</label>
        </div>

        <div class="inputgroup">
            <input type="password" id="txtRegPassword" required autocomplete="new-password">
            <label for="txtRegPassword">Password</label>
        </div>

        <div class="divcallforaction">
            <button type="submit" class="btnlogin inactivecolor" id="btnRegister">Register</button>
        </div>  

        <div class="form-links">
            <a href="#" class="linkBackToLogin">Back to Login</a>
        </div>
        
        <div class="diverror" id="divRegError" role="alert" aria-live="assertive">
            <span class="errormessage" id="regErrorMessage">Error goes here</span>
        </div>
    </form>

    <form class="loginform" id="resetPasswordForm" onsubmit="return false;" style="display: none;">
        <h2 class="form-title">Reset Password</h2>
        
        <div class="inputgroup">
            <input type="text" id="txtResetUsername" required autocomplete="username">
            <label for="txtResetUsername">Username</label>
        </div>

        <div class="inputgroup">
            <input type="password" id="txtResetPassword" required autocomplete="new-password">
            <label for="txtResetPassword">New Password</label>
        </div>

        <div class="divcallforaction">
            <button type="submit" class="btnlogin inactivecolor" id="btnReset">Reset Password</button>
        </div>  

        <div class="form-links">
            <a href="#" class="linkBackToLogin">Back to Login</a>
        </div>
        
        <div class="diverror" id="divResetError" role="alert" aria-live="assertive">
            <span class="errormessage" id="resetErrorMessage">Error goes here</span>
        </div>
    </form>

    <div class="lockscreen" id="lockscreen" aria-hidden="true">
        <div class="spinner" id="spinner"></div>
        <p class="lblwait topmargin" id="lblwait"></p>
    </div>

    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/login.js?v=<?php echo time(); ?>"></script>
</body>
</html>