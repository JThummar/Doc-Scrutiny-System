<!-- CSS complete -->
<!DOCTYPE html>
<html> 
    <head>
        <script>
            function validate() {

                var password = document.forms['form']['password'].value;
                var re_password = document.forms['form']['re_password'].value;
                var date_of_birth = document.forms['form']['date_of_birth'].value;

                var error = '';
                
                if(password !== null && password !== '' && re_password !== null && re_password !== '') {
                    if(password !== re_password) {
                        error = error.concat("Password Confirmation Failed!! \n");
                    }
                } else {
                    error = error.concat("Either Password or Re-Password is/are Empty!! \n");
                }

                date_of_birth = new Date(date_of_birth);

                var q = new Date();
                var m = q.getMonth();
                var d = q.getDate();
                var y = q.getFullYear();

                var diff = y - date_of_birth.getFullYear();
                if(diff >= 100) {
                    error = error.concat("Greater than 100 years!! Enter Valid date \n");
                }

                var today = new Date(y,m,d);

                m = date_of_birth.getMonth();
                d = date_of_birth.getDate();
                y = date_of_birth.getFullYear();

                date_of_birth = new Date(date_of_birth);
                
                if(today < date_of_birth){
                    error = error.concat("Invallid Date!! Date should be earlier than today's date \n");
                }

                if(error !== '') {
                    alert(error);
                    return false;
                }

                return true;
            }
        </script>
        
        <title>Sign up</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <style>
            /* Rounded border */
            hr.rounded {
                border-top: 10px solid #a80585;  
                margin-left: 50px;  margin-right: 50px;
                border-radius: 3px;
            }
            label {
                font-style: italic;
                font-size: 20px;
            }
        </style>
    </head>
    <body>

            <?php
                session_start();
                if(isset($_SESSION['message_email_signup'])) {
            ?>
            <script>
                alert("This email is registered!!!");
            </script>
            <?php
                    unset($_SESSION['message_email_signup']);
                }
                if(isset($_SESSION['forgot_password_error'])) {
            ?>
            <script>
                alert('<?php  echo $_SESSION['forgot_password_error'];?> ');
            </script>
            <?php
                    unset($_SESSION['forgot_password_error']);
                }
            ?>

        <h1 style="color: #a80585; text-align:center; margin-top: 30px; font-style: italic">Registration</h1>
        <form method="POST" action="server.php" name="form" style="width: 500px; margin: auto; margin-top: 30px" onsubmit="return validate()">
        <div class="row">
            <div class="col">
                <div class="form-group">    
                    <label for="fname1">First Name</label> 
                    <input type="text" id="fname1" name = "first_name" pattern="[a-zA-Z]{2,40}" placeholder="First Name" class="form-control" required/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="mname1">Middle Name</label> 
                    <input type="text" id="mname1" name = "middle_name" pattern="[a-zA-Z]{2,40}" placeholder="Middle Name" class="form-control" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="lname1">Last Name</label> 
                    <input type="text" id="lname1" name = "last_name" pattern="[a-zA-Z]{2,40}" placeholder="Last Name" class="form-control" required/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="dob1">Date of Birth</label>
                    <input type="date" id="dob1" name="date_of_birth" placeholder="Birth Date" class="form-control" required/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="phone1">Phone Number</label>
            <input type="text" id="phone1" name="phone_number" pattern="[0-9]{10}" placeholder="Phone Number" class="form-control" required/>
        </div>
        <div class="form-group">  
            <label for="email1">Email </label>
            <input type="email" id="email1" name = "email" placeholder="Email" class="form-control" required/>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="password1">Password</label>
                    <input type="password" id="password1" name="password" placeholder="Password: e.g. ABcd!@12" class="form-control" required/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="repass1">Re-Type Password</label>
                    <input type="password" id="repass1" name="re_password" placeholder="Retype you password" class="form-control" required/>
                </div>
            </div>
        </div>
            <button type="submit" name="sign_up" class="btn btn-primary" style="background-color: #a80585">Create Account</button>
        </form>
        <hr class="rounded">
        <div class="form-group" style="width: 500px; margin: auto;">
            <a href="login.php"><h7 style="color: #a80585;">Already Have Account? Sign In Click Here</h7></a>
        </div>
    </body>
</html>