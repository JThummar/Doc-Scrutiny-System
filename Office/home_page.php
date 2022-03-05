<!DOCTYPE html>
<html>
<head>
    <?php 
        session_start();
        $master_user = false;

        // Check for the valid entry
        if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user'])) {
            header('Location: errors.php?error=InvalidAccessTryTOLogin');
            die();
        }
        $master_user = $_SESSION['master_user'];
        include('config.php');
    ?>

    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <style>
            /* Rounded border */
            hr.rounded {
                border-top: 3px solid #a80585;
                margin: 0px;
                border-radius: 3px;
            }

            #aaa {
                cursor: not-allowed;
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
                    <a class="nav-item nav-link active" href="home_page.php">Home Page <span class="sr-only">(current)</span></a>
                    <a class="nav-item nav-link" href="requests.php">Activity</a>
                    <a class="nav-item nav-link" href="profile.php">Profile</a>
                    <?php if($master_user==true) {?>
                    <a class="nav-item nav-link" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <hr class="rounded">
        <h1 style="color: blue; text-align:center; margin-top: 30px; font-style: italic">Welcome to the Home page</h1>
        <div class="row" style="margin-left: 100px;">
            <?php
                $sql = "SELECT * FROM `documents`";
                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownError');die();
                }
                
                $id=0;
                while($row = mysqli_fetch_assoc($result)) {

                    if($row['active'] == 'enable') {
                        $id=$id+1;

                        $document_id = $row['id'];
                        $sub_query = "SELECT COUNT(*) AS `count` FROM `requests` WHERE `document_id` = $document_id AND `status` = 'pending';";
                        if(!($sub_result = $connection->query($sub_query))) {
                            header('Location: errors.php?error=UnknownError');die();
                        }
                        $sub_row = mysqli_fetch_assoc($sub_result);
                        $count = $sub_row['count'];
            ?>      
                    <div class="card" style="width:400px; height: 280px; border-style: solid; border-width: 7px; margin: 20px; border-color: blue; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic"><?php echo $row['document_name'];?></h4>
                            <p class="card-text" style="color:#000000; font-style: italic"><?php echo $row['document_description'];?></p>

                                <button type="button" class="btn btn-primary" id="aaa">
                                    Pending Requests <span class="badge badge-light"><?php echo $count?></span>
                                </button><br><br>
                            <a href="document_requests.php?document_id=<?php echo $row['id']; ?>" class="btn btn-primary" style="background-color:#3333ff">Check Requests</a>
                        </div>
                    </div>
            <?php       
                    if($id==3) {
                    ?>
                    <br>
                    <?php
                        $id=0;
                    }
                    }
                }
            ?>
            </div>
        </body>
</html>