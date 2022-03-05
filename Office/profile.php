<!DOCTYPE html>
<html>
<head>

    <script>
        function validate() {

            var password = document.forms['form']['password'].value;
            var re_password = document.forms['form']['re_password'].value;
            var date_of_birth = document.forms['form']['birth_date'].value;

            var error = '';
            
            // Check for the password
            if(password !== null && password !== '' && re_password !== null && re_password !== '') {
                if(password !== re_password) {
                    error = error.concat("Password Confirmation Failed!! \n");
                }
            } else {
                error = error.concat("Either Password or Re-Password is/are Empty!! \n");
            }

            // check for date of birth
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
    <?php
        session_start();
        if(!isset($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }
        $id = $_SESSION['officer_id'];

        $update = false;
        $update_picture = false;

        include('config.php');

        // get request for the profile
        if(isset($_GET['id'])) {
            if(!is_numeric($_GET['id']) || $_GET['id'] != $id) {
                header('Location: profile.php');die();
            }
            $update = true;
        }

        // Get request for the profile picture
        if(isset($_GET['profile_id'])) {
            if(!is_numeric($_GET['profile_id']) || $_GET['profile_id'] != $id) {
                header('Location: profile.php');die();
            }
            $update_picture = true;
        }
        $master_user = false;
        if(isset($_SESSION['master_user'])) {
            $master_user = $_SESSION['master_user'];
        }
    ?>

    <title><?php echo $_SESSION['officer_first_name']; ?></title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <style>
        /* Rounded border */
        hr.rounded {
            border-top: 3px solid #b300b3;
            margin: 0px;
            border-radius: 3px;
        }
        label {
            font-style: italic;
            font-size: 20px;
        }
        .wrapper {
            padding: 20px;
        }
        .image--cover {
            width: 200px;
            height: 200px;
            margin-left: 130px;
            border-radius: 50%;
            -webkit-box-shadow: 2px 2px 10px 0px rgba(0, 0, 0, 1);
            -moz-box-shadow: 2px 2px 10px 0px rgba(0, 0, 0, 1);
            box-shadow: 2px 2px 10px 0px rgba(0, 0, 0, 1);
            
            object-fit: cover;
            object-position: center right;
        }
    </style>
</head>
<body>
        <nav class="navbar navbar-expand-lg navbar-bg-primary bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                    <a class="nav-item nav-link" href="home_page.php">Home Page </a>
                    <a class="nav-item nav-link" href="requests.php">Activity </a>
                    <a class="nav-item nav-link active" href="profile.php">Profile <span class="sr-only">(current)</span></a>
                    <?php if($master_user==true) {?>
                    <a class="nav-item nav-link" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <hr class="rounded">
    <?php 
        if(isset($_SESSION['error_message_email_change'])) {
            $not_available = $_SESSION['error_message_email_change'];
    ?>  

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><?php echo $not_available ?></strong> is registerd by other user
        </div>
    <?php
            unset($_SESSION['error_message_email_change']);
        }

        if(isset($_SESSION['office_update_profile'])) {
    ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['office_update_profile']; ?>
        </div>

    <?php
            unset($_SESSION['office_update_profile']);
        } 

        $sql = "SELECT * FROM `office_employee_information` where `id` = $id;";
        $result = $connection->query($sql);
        $row = mysqli_fetch_assoc($result);

        $first_name = $row['first_name'];
        $middle_name = $row['middle_name'];
        $last_name = $row['last_name'];
        $birth_date = $row['birth_date'];
        $phone = $row['phone'];
        $email = $row['email'];
        $photo_name = $row['photo_name'];
        
        if($photo_name == NULL) {
            $photo_name = 'placeholder';
        }

        $path = "../Uploads/$photo_name" . ".jpeg";
    ?>
    <h1 style="color:blue; text-align:center; margin-top: 20px; font-style: italic"><?php echo $first_name." ".$last_name; ?></h1>    

    <form action="server.php" method="POST" name="form" style="width: 500px; margin: auto; margin-top: 20px" onsubmit="return validate()" enctype="multipart/form-data">
        <div class="wrapper">
            <img src="<?php echo $path; ?>" alt="Profile Picture" class="image--cover" >
        </div>
        <input name='id' value="<?php echo $id; ?>" readonly required hidden>

        <div class="row">
            <div class="col">
                <div class="form-group">    
                <label >First Name:</label>
                <input name = 'first_name' class="form-control" value="<?php echo $first_name; ?>" <?php if($update == false) {echo "readonly";} ?> pattern="[a-zA-Z]{2,40}" type="text" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Middle Name:</label>
                    <input name = 'middle_name' class="form-control" value="<?php echo $middle_name; ?>" <?php if($update == false) {echo "readonly";} ?> pattern="[a-zA-Z]{2,40}" type="text" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Last Name:</label>
                    <input name = 'last_name' class="form-control" value="<?php echo $last_name; ?>" <?php if($update == false) {echo "readonly";} ?> pattern="[a-zA-Z]{2,40}" type="text" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Birth Date:</label>
                    <input name = 'birth_date' class="form-control" value="<?php echo $birth_date; ?>" <?php if($update == false) {echo "readonly";} ?>  type="date" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Phone Number:</label>
            <input name = 'phone_number' class="form-control" value="<?php echo $phone; ?>" <?php if($update == false) {echo "readonly";} ?> type="text" pattern="[0-9]{10}" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input name = 'email' class="form-control" value="<?php echo $email; ?>" <?php if($update == false) {echo "readonly";} ?> type="email" required>
        </div>

        <?php
            // update profile
            if($update) {
        ?>
            <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter old or new password you want to set" class="form-control" required/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Re-Type Password</label>
                    <input type="password" name="re_password" placeholder="Retype you password" class="form-control" required/>
                </div>
            </div>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary" style="background-color: blue; margin-bottom: 50px;">Update Profile</button>
        <?php
            // update profile picture
            } else if ($update_picture) {
        ?>    
                    <div class="form-group">
                        <label>Upload Profile Photo</label>
                        <input type="file" name="profile_pic" class="form-control-file" required>
                        <span style="font-size: small; color: green">upload Jpeg and size should be less than 5MB</span>
                    </div>

                    <?php
                        if(isset($_SESSION['message_update_picture'])) {
                            $message = $_SESSION['message_update_picture'];
                    ?>
                        <p style="color: red;"><?php echo $message; ?></p>
                    <?php

                            unset($_SESSION['message_update_picture']);
                        }
                    ?>
            <button type="submit" name="update_picture" class="btn btn-primary" style="background-color: blue; margin-bottom: 50px;">Update Picture</button>

        <?php
            }
            // if not option for the updating the website
            else {
        ?>
            <a class="btn btn-primary" style="background-color: blue; margin-bottom: 50px; margin-right: 20px; color: #ffffff" href="<?php echo "profile.php?id=$id"; ?>">Edit Your Profile</a>   
            <a class="btn btn-primary" style="background-color: blue; margin-bottom: 50px; color: #ffffff" href="<?php echo "profile.php?profile_id=$id"; ?>">Edit Profile Picture</a>    
        <?php
            }
        ?>

    </form>
</body>
</html>