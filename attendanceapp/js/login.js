function tryLogin() {
    let un=$("#txtUsername").val();
    let pw=$("#txtPassword").val();
    let login_type = $("#loginType").val();
    if(un.trim()!=="" && pw.trim()!=="") {
       $.ajax({
        url:"ajaxhandler/loginAjax.php",
        type:"POST",
        dataType:"json",
        data:{user_name:un, password:pw, login_type:login_type, action:"verifyUser"},
        beforeSend:function(){
            $("#diverror").removeClass("applyerrordiv");
            $("#lockscreen").addClass("applylockscreen");
        },
        success:function(rv){
            $("#lockscreen").removeClass("applylockscreen");
            if(rv['status']=="ALL OK") {
                if(rv['role'] == "ADMIN") {
                    document.location.replace("admin.php");
                } else if(rv['role'] == "STUDENT") {
                    document.location.replace("student.php");
                } else {
                    document.location.replace("attendance.php");
                }
            } else {
                $("#diverror").addClass("applyerrordiv");
                $("#errormessage").text(rv['status']);
            }
        },
        error:function(){
            alert("oops something went wrong");
        },
       });
    }
}

function tryRegister() {
    let name=$("#txtRegName").val();
    let un=$("#txtRegUsername").val();
    let pw=$("#txtRegPassword").val();
    if(name.trim()!=="" && un.trim()!=="" && pw.trim()!=="") {
       $.ajax({
        url:"ajaxhandler/loginAjax.php",
        type:"POST",
        dataType:"json",
        data:{name:name, user_name:un, password:pw, action:"registerUser"},
        beforeSend:function(){
            $("#divRegError").removeClass("applyerrordiv");
            $("#lockscreen").addClass("applylockscreen");
        },
        success:function(rv){
            $("#lockscreen").removeClass("applylockscreen");
            if(rv['status']=="SUCCESS") {
                alert("Registration successful. Please login.");
                $(".linkBackToLogin").trigger("click");
            } else {
                $("#divRegError").addClass("applyerrordiv");
                $("#regErrorMessage").text(rv['status']);
            }
        },
        error:function(){
            alert("oops something went wrong");
        },
       });
    }
}

function tryResetPassword() {
    let un=$("#txtResetUsername").val();
    let pw=$("#txtResetPassword").val();
    if(un.trim()!=="" && pw.trim()!=="") {
       $.ajax({
        url:"ajaxhandler/loginAjax.php",
        type:"POST",
        dataType:"json",
        data:{user_name:un, password:pw, action:"resetPassword"},
        beforeSend:function(){
            $("#divResetError").removeClass("applyerrordiv");
            $("#lockscreen").addClass("applylockscreen");
        },
        success:function(rv){
            $("#lockscreen").removeClass("applylockscreen");
            if(rv['status']=="SUCCESS") {
                alert("Password reset successfully. Please login.");
                $(".linkBackToLogin").trigger("click");
            } else {
                $("#divResetError").addClass("applyerrordiv");
                $("#resetErrorMessage").text(rv['status']);
            }
        },
        error:function(){
            alert("oops something went wrong");
        },
       });
    }
}

$(function(e){
    // Tab switching logic (Staff vs Student)
    $("#tabStaff").click(function(e) {
        e.preventDefault();
        $(".login-tab").removeClass("active");
        $(this).addClass("active");
        $("#loginType").val("staff");
        $("#loginTitle").text("Faculty Login");
        $("#lblUsername").text("Username");
        $("#txtUsername").attr("placeholder", "");
        $("#linkRegister").show(); // Allow registering new faculty
        $("form").trigger("reset");
        $("#btnLogin").removeClass("activecolor").addClass("inactivecolor");
        $("#diverror").removeClass("applyerrordiv");
    });

    $("#tabStudent").click(function(e) {
        e.preventDefault();
        $(".login-tab").removeClass("active");
        $(this).addClass("active");
        $("#loginType").val("student");
        $("#loginTitle").text("Student Login");
        $("#lblUsername").text("Roll Number");
        $("#txtUsername").attr("placeholder", "e.g. 2410302001");
        $("#linkRegister").hide(); // Registering is only for staff
        $("form").trigger("reset");
        $("#btnLogin").removeClass("activecolor").addClass("inactivecolor");
        $("#diverror").removeClass("applyerrordiv");
    });

    // Form Switching Logic
    $("#linkRegister").click(function(e) {
        e.preventDefault();
        $("#loginForm, #resetPasswordForm").hide();
        $("#registerForm").fadeIn();
    });

    $("#linkReset").click(function(e) {
        e.preventDefault();
        $("#loginForm, #registerForm").hide();
        $("#resetPasswordForm").fadeIn();
    });

    $(".linkBackToLogin").click(function(e) {
        e.preventDefault();
        $("#registerForm, #resetPasswordForm").hide();
        $("#loginForm").fadeIn();
        $("form").trigger("reset"); // clear inputs
        $(".diverror").removeClass("applyerrordiv");
        $("button").removeClass("activecolor").addClass("inactivecolor");
    });

    // Input validations
    $(document).on("keyup","#loginForm input",function(e){
        $("#diverror").removeClass("applyerrordiv");
        let un=$("#txtUsername").val();
        let pw=$("#txtPassword").val();
        if(un.trim()!=="" && pw.trim()!=="") {
          $("#btnLogin").removeClass("inactivecolor").addClass("activecolor");
        } else {
          $("#btnLogin").removeClass("activecolor").addClass("inactivecolor");
        }
    }); 

    $(document).on("keyup","#registerForm input",function(e){
        $("#divRegError").removeClass("applyerrordiv");
        let name=$("#txtRegName").val();
        let un=$("#txtRegUsername").val();
        let pw=$("#txtRegPassword").val();
        if(name.trim()!=="" && un.trim()!=="" && pw.trim()!=="") {
          $("#btnRegister").removeClass("inactivecolor").addClass("activecolor");
        } else {
          $("#btnRegister").removeClass("activecolor").addClass("inactivecolor");
        }
    }); 

    $(document).on("keyup","#resetPasswordForm input",function(e){
        $("#divResetError").removeClass("applyerrordiv");
        let un=$("#txtResetUsername").val();
        let pw=$("#txtResetPassword").val();
        if(un.trim()!=="" && pw.trim()!=="") {
          $("#btnReset").removeClass("inactivecolor").addClass("activecolor");
        } else {
          $("#btnReset").removeClass("activecolor").addClass("inactivecolor");
        }
    }); 

    // Button clicks
    $(document).on("click","#btnLogin",function(e){
        e.preventDefault();
        tryLogin();
    });
    
    $(document).on("click","#btnRegister",function(e){
        e.preventDefault();
        tryRegister();
    });

    $(document).on("click","#btnReset",function(e){
        e.preventDefault();
        tryResetPassword();
    });
});
