<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();

            // Check for the valid access of the user
            if(!isset($_SESSION['email'])) {
                header('Location: errors.php?error=InvalidAccess');
            }
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
        </style>
    </head>
    <body>

    
    <?php
        // Showing an alert message for When user successfully requested for the document
        if(isset($_SESSION['user_document_applied'])) {
            ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Request for <strong><?php echo $_SESSION['user_document_applied'] ;?></strong> has made successfully check for mail
        </div>

            <?php
            unset($_SESSION['user_document_applied']);
        }
    ?>

    <nav class="navbar navbar-expand-lg navbar-bg-primary bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                <a class="nav-item nav-link active" href="home_page.php"><h7 style="color: #a80585;">Home Page </h7><span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="requests.php"><h7 style="color: red;">Activity</h7></a>
                <a class="nav-item nav-link" href="profile.php"><h7 style="color: #a80585;">Profile</h7></a>
                <a class="nav-item nav-link" href="logout.php"><h7 style="color: #a80585;">logout</h7></a></div>
            </div>
    </nav>

    <!-- Show the User all document -->

    <hr class="rounded">
        <h1 style="color: #a80585; text-align:center; margin-top: 20px; font-style: italic">Welcome to the Home page</h1>
        <div class="row" style="margin-left: 100px;">
            <?php
                $sql = "SELECT * FROM `documents`";
                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownError');die();
                }
                
                $id=0;
                while($row = mysqli_fetch_assoc($result)) {

                    // Check if the document is enabled by the admin or not
                    if($row['active'] == 'enable') {
                        $id=$id+1;
            ?>      
                    <div class="card" style="width:400px; height: 250px; border-style: solid; border-width: 7px; margin: 20px; border-color: #a80585; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic"><?php echo $row['document_name'];?></h4>
                            <p class="card-text" style="color:#000000; font-style: italic"><?php echo $row['document_description'];?></p>
                            <a href="document.php?document_id=<?php echo $row['id']; ?>" class="btn btn-primary" style="background-color:#a80585">Apply</a>
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