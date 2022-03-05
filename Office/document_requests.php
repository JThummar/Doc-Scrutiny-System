<!DOCTYPE html>
<html>
    <head>

    <?php

        session_start();


        // Check for the valid entry
        if(!isset($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');
            die();
        }

        include('config.php');
        if(!isset($_GET['document_id']) or (!is_numeric($_GET['document_id']))) {
            header('Location: errors.php?error=InvalidServiceRequest');die();
        }
        
        $document_id = $_GET['document_id'];
        $sql = "SELECT * FROM `documents` WHERE `id` = $document_id";
        $result = $connection->query($sql);
        if($result->num_rows == 0 ) {
            header('Location: errors.php?error=InvalidServiceRequest');die();
        }

        $row = mysqli_fetch_assoc($result);

        $master_user = $_SESSION['master_user'];


    ?> 

        <title><?php echo $row['document_name'] ?> </title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    </head>
    <body>

        <?php
            if(isset($_SESSION['office_document_response'])) {
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['office_document_response']; ?>
            </div>
        <?php
                unset($_SESSION['office_document_response']);
            }
        ?>

        <?php
            if(isset($_SESSION['office_document_response_reject'])) {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['office_document_response_reject']; ?>
            </div>
        <?php
                unset($_SESSION['office_document_response_reject']);
            }
        ?>
    
        <!-- check for all the requests -->
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
                    <a class="nav-item nav-link" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <h1 style="color:blue; text-align:center; margin-top: 20px; font-style: italic"><?php echo $row['document_name']." Requests "?></h1>    
        <?php 
            $sql = "SELECT * FROM `requests` WHERE `document_id` = $document_id AND `status` = 'pending';";
            $result = $connection->query($sql);
            if($result->num_rows == 0) {
            ?>
                <p style="color: green; margin: 20px; font-size: 40px">No Request Pending!</p>
            <?php
            } else {
            ?>
                <div class="container" style="margin-top: 30px; width: 800px;">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Request Id</th>
                            <th scope="col">Time</th>
                            <th scope="col">Check</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            $request_id = $row['request_id'];
                            $time=$row['time'];
                    ?>
                        <tr>
                                <th scope="row"><?php echo $request_id; ?></th>
                                <td><?php echo $time; ?></td>
                                <td><a style="color: #ffffff; background-color: #0099cc;" class="btn btn-primary" href="document_verify.php?document_id=<?php echo $document_id; ?>&request_id=<?php echo $request_id; ?>">Show It</a></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
                </table>
                </div>
            <?php
                }
            ?>
    </body>
</html>