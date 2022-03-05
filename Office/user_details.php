<!DOCTYPE html>
<html>
    <head>

        <?php
            session_start();
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }

            $master_user = $_SESSION['master_user'];

            include('config.php');
            $master_id = $_SESSION['officer_id'];

            $sql = "SELECT * FROM `office_employee_information`;";
            if(!($result = $connection->query($sql))){
                header('Location: errors.php?error=UnknownErrorOccured');die();
            }
        ?>

            <style>
                hr.rounded {
                    border-top: 3px solid #b300b3;
                    margin: 0px;
                    border-radius: 3px;
                }
            </style>
        <title>Officer's Details</title>
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

        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Officer's Details</h1>

        <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Officer Id</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Status</th>
                        <th scope="col">View Profile</th>
                        <th scope="col" >History</th>
                        <th scope="col" >Unverify</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                while($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $first_name = $row['first_name'];
                    $last_name = $row['last_name'];
                    $email = $row['email'];
                    $verified = $row['verified'];

                    if($id == $master_id) {
                        continue;
                    }
            ?>
            <?php
                if($verified == 1) {
            ?>
                <tr>
                    <th scope="row"><?php echo $id;?></th>
                    <td><?php echo $first_name;?></td>
                    <td><?php echo $last_name;?></td>
                    <td><?php echo $email;?></td>
            
                    <td style="color: green;">Verified</td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="show_user.php?id=<?php echo $id;?>">Show Profile</a></td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="user_history.php?id=<?php echo $id;?>">View History</a></td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="server.php?unverify_user_id=<?php echo $id?>">Click Here to Unverify</a></td>
            
            <?php
                }
            ?>
            
                </tr>     
                
            <?php
                }
            ?>
                </tbody>
            </table>
        </div>
    </body>
</html>