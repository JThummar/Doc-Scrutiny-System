<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();

            //Checking the valid entry
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }
            include('config.php');
            $sql = "SELECT * FROM `office_employee_information` WHERE `verified` = 0 AND `email_verification` = 1;";

            if(!($result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccured');die();
            }
            $master_user = $_SESSION['master_user'];

        ?>

        <style>
                hr.rounded {
                    border-top: 3px solid #b300b3;
                    margin: 0px;
                    border-radius: 3px;
                }
            </style>
        <title>Verify User</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
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
    <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Unverified Goverment Officer's</h1>
    <?php 
        if($result->num_rows == 0) {
    ?>
        <p style="color: green; margin: 20px; font-size: 30px">No Unverified Officer Remain</p>
    <?php
        } else {
    ?>
        <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Officer id</th>
                        <th scope="col">Officer Name</th>
                        <th scope="col">Officer Email</th>
                        <th scope="col">View Profile</th>
                        <th scope="col">Verify Him/Her</th>

                    </tr>
                </thead>
                <tbody>
            <?php
                while($row = mysqli_fetch_assoc($result)) {
                    $officer_id = $row['id'];
                    $officer_name  = $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'];
                    $officer_email = $row['email'];
            ?>
                <tr>
                    <th scope="row"><?php echo $officer_id;?></th>
                    <td><?php echo $officer_name;?></td>
                    <td><?php echo $officer_email;?></td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="show_user.php?id=<?php echo $officer_id;?>">Show Profile</a></td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="server.php?verify_user_id=<?php echo $officer_id?>">Click Here to Verify</a></td>
                </tr>
            <?php
                }
            ?>
            </tbody>
            </table>
        </div>
        <?php  } 
        ?>
    </body>
</html>