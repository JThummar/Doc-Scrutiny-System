<html> 
    <head>
        <?php
            session_start();
            if(isset($_SESSION['password_changed'])) {
        ?>
                <div class="alert alert-success alert-dismissible">
                    Password Has Been <strong>Changed!</strong>
                </div>
        <?php
                unset($_SESSION['password_changed']);
            }
            if(isset($_SESSION['id'])) {
                header('Location: home_page.php');
            }
        ?>

        <title>Log in</title>
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
            input.form-check-input {
                width: 20px; 
                height: 20px;
            }
        </style>

    </head>
    <body>
        <h1 style="color: #a80585; text-align:center; margin-top: 50px; font-style: italic">Public Log In</h1>
        <form method="POST" action="server.php" style="width: 500px; margin: auto; margin-top: 35px">
        <div class="form-group">
            <label for="Email1">Email </label>
            <input type="email" id="Email1" class="form-control" name = "email" placeholder="Email" value="<?php if(isset($_COOKIE['user_login'])){ echo $_COOKIE['user_login']; } ?>" required/>
        </div>
        <div class="form-group">
            <label for="password1">Password</label>
            <input type="password" id="password1" class="form-control" name="password" placeholder="Password: e.g. ABcd!@12" value="<?php if(isset($_COOKIE['user_password'])){ echo $_COOKIE['user_password']; } ?>" required/>
        </div>    
            <?php 
                if(!isset($_COOKIE['user_login'])) {
            ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="Remember1" name="remember_me">
                <label class="form-check-label" for="Remember1" name="remember">&nbsp; Remember me for a 7 Day</label>
            </div>
            <br>
            <?php
                }
            ?>
            <button type="submit" name="sign_in" class="btn btn-primary" style="background-color: #a80585">Submit</button>
        </form>
        <hr class="rounded">
        <div class="form-group" style="width: 500px; margin: auto;">
            <a href="index.php"><h7 style="color: #a80585;">Don't have account? Create You Account Click Here</h7></a>
        </div>
        <div class="form-group" style="width: 500px; margin: auto;">
            <a href='forget_password.php'><h7 style="color: #a80585;">Forget Password? Recover Your Account Here</h7></a>
        </div> 
    </body>
</html>