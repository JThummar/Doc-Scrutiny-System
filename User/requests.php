<html>
    <head>
        <?php
            session_start();

            //checking for the valid access
            if(!isset($_SESSION['id'])) {
                header('Location: errors.php?error=InvalidAccess');die();
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
                    <a class="nav-item nav-link" href="home_page.php"><h7 style="color: #a80585;">Home Page</h7> </a>
                    <a class="nav-item nav-link active" href="requests.php"><h7 style="color: red;">Activity</h7> <span class="sr-only">(current)</span></a>
                    <a class="nav-item nav-link" href="profile.php"><h7 style="color: #a80585;">Profile</h7></a>
                    <a class="nav-item nav-link" href="logout.php"><h7 style="color: #a80585;">logout</h7></a>
                    </div>
                </div>
        </nav>
        <hr class="rounded">
        
        <h1 style="color:#a80585; text-align:center; margin-top: 20px; font-style: italic">Your Activity's</h1>    
        <?php
                $total = 0;
                $accepted = 0;
                $rejected = 0;
                $pending = 0;
                $id = $_SESSION['id'];
                include('config.php');

                // Access the database for the all requests made by the user
                $sql = "SELECT * FROM `requests` WHERE `user_id` = $id ORDER BY `requests`.`time` DESC;";

                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownErrorOccured'); die();
                }
        ?>  
        <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Request Id</th>
                        <th scope="col">Document Name</th>
                        <th scope="col">Time</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
        <?php
                while($row = mysqli_fetch_assoc($result)) {
                    $request_id = $row['request_id'];
                    $document_id = $row['document_id'];
                    $time = $row['time'];
                    $status = $row['status'];
                    $documnet_name = "Not Available";

                    $sub_query = "SELECT * FROM `documents` WHERE `id` = $document_id";
                    if(!($sub_result = $connection->query($sub_query))) {
                        header('Location: errors.php?error=UnknownErrorOccured'); die();
                    }

                    // Count Stats for the user Activity
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
        ?>      
                <tr>
                    <th scope="row"><?php echo $request_id; ?></th>
                    <td><?php echo $documnet_name; ?></td>
                    <td><?php echo $time; ?></td>
                    <td><?php echo $status; ?></td>
                </tr>
        <?php
                }
        ?>  
            </tbody>
            </table>
        <div class="card-columns" style="margin-left: 250px;">
            <div class="card bg-primary">
                <div class="card-body text-center">
                <p class="card-text" style="font-size: 20px; color: white;">Total Requests: <?php echo $total; ?></p>
                </div>
            </div>
            <div class="card bg-warning">
                <div class="card-body text-center">
                <p class="card-text" style="font-size: 20px; color: white;">Pending: <?php echo $pending; ?></p>
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