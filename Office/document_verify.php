<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();

            // check for valid entry
            if(!isset($_SESSION['officer_id'])) {
                header('Location: errors.php?error=InvalidAccess');
                die();
            }
            $officer_id = $_SESSION['officer_id'];
    
            include('config.php');

            // check for the valid entry
            if(!isset($_GET['document_id']) or (!is_numeric($_GET['document_id']))) {
                header('Location: errors.php?error=InvalidServiceRequest');die();
            }

            // check for the valid entry
            if(!isset($_GET['request_id']) or (!is_numeric($_GET['request_id']))) {
                header('Location: errors.php?error=InvalidServiceRequest');die();
            }

            $document_id = $_GET['document_id'];
            $request_id = $_GET['request_id'];

            $sql = "SELECT * FROM `requests` WHERE `request_id` = $request_id AND `document_id` = $document_id";
            if(!($result = $connection->query($sql)) or $result->num_rows == 0) {
                header('Location: errors.php?error=InvalidServiceRequest');die();
            }
            $row = mysqli_fetch_assoc($result);

            $status = $row['status'];
            $user_id = $row['user_id'];

            $sql = "SELECT * FROM `user_information` WHERE `user_information`.`id` = $user_id";
            if(!($result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccured');die();
            }
            $row = mysqli_fetch_assoc($result);

            $first_name = $row['first_name'];
            $middle_name = $row['middle_name'];
            $last_name = $row['last_name'];

            $birth_date = $row['birth_date'];

            $phone_number = $row['phone'];
            $email = $row['email'];

            $sql = "SELECT * FROM `documents` WHERE `id` = $document_id";
            if(!($result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccured');die();
            }
            $row = mysqli_fetch_assoc($result);
            $document_name = $row['document_name'];

            $master_user = $_SESSION['master_user'];


        ?>

        <title><?php echo "Request Id " . $request_id ?></title>
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
                    <a class="nav-item nav-link" href="master_panel.php">Master Panel</a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <h1 style="color:blue; text-align:center; margin-top: 20px; font-style: italic"><?php echo $first_name."'s Request "?></h1>    
        <form method="POST" name = "form" action = "server.php" style="width: 500px; margin: auto; margin-top: 20px" enctype="multipart/form-data">
            <input name='document_id' value="<?php echo $document_id; ?>" readonly required hidden>
            <input name='user_id' value="<?php echo $user_id; ?>" readonly required hidden>
            <input name='request_id' value="<?php echo $request_id; ?>" readonly required hidden>
            <input name='officer_id' value="<?php echo $officer_id; ?>" readonly required hidden>
            <input name='document_name' value="<?php echo $document_name; ?>" readonly required hidden>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input name = 'first_name' value="<?php echo $first_name; ?>" readonly type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input name = 'middle_name' value="<?php echo $middle_name; ?>" readonly type="text" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input name = 'last_name' value="<?php echo $last_name; ?>" readonly type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Birth Date:</label>
                        <input name = 'birth_date' value="<?php echo $birth_date; ?>" readonly type="date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input name = 'phone_number' value="<?php echo $phone_number; ?>" readonly type="text" pattern="[0-9]{10}" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input name = 'email' value="<?php echo $email; ?>" readonly type="email" class="form-control" required>
            </div>
            <?php
                $table_name = 'document_'. $document_id;
                $sql = "SELECT * FROM `$table_name`";

                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownErrorOccured');die();
                }
                while($row = mysqli_fetch_assoc($result)) {
                    $proof_id = $row['required_document'];

                    $sql = "SELECT * FROM `proofs` WHERE `id` = $proof_id";
                    if(!($sub_result = $connection->query($sql))) {
                        header('Location: errors.php?error=UnknownErrorOccured');die();
                    }
                    $sub_row = mysqli_fetch_assoc($sub_result);
                    $proof_name = $sub_row['proof_name'];
                    $file_name = $request_id . "_" . $proof_id;
            ?> 
            <div class="form-group" >
                <a style="color: #ffffff; background-color: #0099cc;" class="btn btn-primary" target="_blank" href="../Uploads/<?php echo $file_name;?>.jpeg">Click Here To See Proof</a>
                <label><?php echo $proof_name; ?></label>
            </div>
            <?php
                }
                if($status == 'pending') {
            ?>
            
            <div class="form-group" >
                <label for="feed">Put FeedBack</label><span style="color: red;">&nbsp; *</span>
                <textarea name="feedback_message" id="feed" class="form-control" required></textarea>
            </div>

            <!-- Button for accepting -->
            <button type="submit" name = 'accept' class="btn btn-primary" style="background-color: green; margin-bottom: 50px; margin-right: 20px;">Accept</button>
            <!-- Button for rejecting -->
            <button type="submit" name = 'reject' class="btn btn-primary" style="background-color: red; margin-bottom: 50px;">Reject</button>

        <?php 
            } else {
        ?>
            <hr  class="rounded">
            <p style="color: red; margin: 20px; font-size: 30px">Request Terminated</p>
            <p style="color: red; margin: 20px; font-size: 30px">Document was <?php echo $status?></p>
        <?php
            }
        ?>
        </form>
    </body>
</html>