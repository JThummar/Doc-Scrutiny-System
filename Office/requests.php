<!DOCTYPE html>
<html>
<head>
        <?php
            session_start();
            $master_user = false;
            if(isset($_SESSION['master_user'])) {
                $master_user = $_SESSION['master_user'];
            }
        ?>
        <title>Your Activity's</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <style>
            /* Rounded border */
            hr.rounded {
                border-top: 3px solid #b300b3;
                margin: 0px;
                border-radius: 3px;
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
                    <a class="nav-item nav-link active" href="requests.php">Activity <span class="sr-only">(current)</span></a>
                    <a class="nav-item nav-link" href="profile.php">Profile</a>
                    <?php if($master_user==true) {?>
                    <a class="nav-item nav-link" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <hr class="rounded">
    <h1 style="color:blue; text-align:center; margin-top: 20px; font-style: italic">Your Activity's</h1>    
    
    <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Request Id</th>
                        <th scope="col">User Id</th>
                        <th scope="col">Document</th>
                        <th scope="col">time</th>
                        <th scope="col">Status</th>
                        <th scope="col">Show</th>
                    </tr>
                </thead>
                <tbody>
    <?php
            $total = 0;
            $accepted = 0;
            $rejected = 0;
            $pending = 0;
            $id = $_SESSION['officer_id'];
            include('config.php');
            $sql = "SELECT * FROM `requests` WHERE `officer_id` = $id ORDER BY `time` DESC;";
            if(!($result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccured'); die();
            }

            while($row = mysqli_fetch_assoc($result)) {
                $request_id = $row['request_id'];
                $document_id = $row['document_id'];
                $time = $row['time'];
                $status = $row['status'];
                $user_id = $row['user_id'];
                $documnet_name = "Not Available";

                $sub_query = "SELECT * FROM `documents` WHERE `id` = $document_id";
                if(!($sub_result = $connection->query($sub_query))) {
                    header('Location: errors.php?error=UnknownErrorOccured'); die();
                }

                $total++;
                if($status == 'accepted') {
                    $accepted++;
                } elseif ($status == 'rejected') {
                    $rejected++;
                } elseif($status == 'pending') {
                    $pending++;
                }
                
                if($sub_result->num_rows  > 0) {
                    $row = mysqli_fetch_assoc($sub_result);
                    $documnet_name = $row['document_name'];
                }

                $link_to_document = "document_verify.php?document_id=$document_id&request_id=$request_id";
    ?>
            <tr>
                    <th scope="row"><?php echo $request_id; ?></th>
                    <td><?php echo $user_id;?></td>
                    <td><?php echo $documnet_name?></td>
                    <td><?php echo $time?></td>
                    <td><?php echo $status?></td>
                    <td><a class="btn btn-primary" style="background-color: #0099cc; color: #ffffff" href="<?php echo $link_to_document;?>">Show</a></td>
            </tr>
    <?php
            }
    ?>
    </tbody>
    </table>
    <div class="card-columns" style="margin-left: 200px; margin-right: 200px;">
            <div class="card bg-primary">
                <div class="card-body text-center">
                <p class="card-text" style="font-size: 20px; color: white;">Total Requests: <?php echo $total; ?></p>
                </div>
            </div>
            <div class="card bg-success">
                <div class="card-body text-center">
                <p class="card-text" style="font-size: 20px; color: white;">Accepted: <?php echo $accepted; ?></p>
                </div>
            </div>
            <div class="card bg-danger">
                <div class="card-body text-center">
                <p class="card-text" style="font-size: 20px; color: white;">Rejected: <?php echo $rejected; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>