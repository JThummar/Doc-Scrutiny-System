<!DOCTYPE html>
<html>
    <head>

        <?php
            session_start();
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }
            include('config.php');
            $master_user = $_SESSION['master_user'];

            $master_id = $_SESSION['officer_id'];
            $id = $_GET['id'];
            $sql = "SELECT * FROM `office_employee_information` WHERE `office_employee_information`.`id` = $id;";
            if(!($result = $connection->query($sql))){
                header('Location: errors.php?error=UnknownErrorOccured');die();
            }
            $row = mysqli_fetch_assoc($result);
            $first_name = $row['first_name'];
            $middle_name = $row['middle_name'];
            $last_name = $row['last_name'];
            $birth_date = $row['birth_date'];
            $phone = $row['phone'];
            $email = $row['email'];

            $photo_name = $row['photo_name'];

            if($photo_name == null) {
                $photo_name = 'placeholder';
            }
            $path = "../Uploads/$photo_name" . ".jpeg";
        ?>

        <title><?php echo $first_name."'s Profile";?></title>
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
                    <a class="nav-item nav-link" href="home_page.php">Home Page <span class="sr-only">(current)</span></a>
                    <a class="nav-item nav-link" href="requests.php">Activity</a>
                    <a class="nav-item nav-link" href="profile.php">Profile</a>
                    <?php if($master_user==true) {?>
                    <a class="nav-item nav-link active" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>

        <hr class="rounded">

        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic"><?php echo $first_name." ".$last_name; ?></h1>    
    
        <form action="" method="POST" name="form" style="width: 500px; margin: auto; margin-top: 20px">
            <div class="wrapper">
                <img src="<?php echo $path; ?>" alt="Profile Picture" class="image--cover" >
            </div>
            <input name='id' value="<?php echo $id; ?>" readonly required hidden>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input name = 'first_name' value="<?php echo $first_name; ?>" readonly pattern="[a-zA-Z]{2,40}" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input name = 'middle_name' value="<?php echo $middle_name; ?>" readonly pattern="[a-zA-Z]{2,40}" type="text" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input name = 'last_name' value="<?php echo $last_name; ?>" readonly pattern="[a-zA-Z]{2,40}" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Birth Date:</label>
                        <input name = 'birth_date' value="<?php echo $birth_date; ?>" readonly  type="date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input name = 'phone_number' value="<?php echo $phone; ?>" readonly type="text" pattern="[0-9]{10}" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input name = 'email' value="<?php echo $email; ?>" readonly type="email" class="form-control" required>
            </div>
        </form>
    </body>
</html>