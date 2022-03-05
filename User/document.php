<!DOCTYPE html>
<html>
    <head>
        <?php
            include('config.php');

            session_start();

            //To check the user is logged in or not 
            if(!isset($_SESSION['id'])) {
                header('Location: errors.php?error=InvalidSuccess');
            }

            $user_id = $_SESSION['id'];

            // To check the document id in url is valid or not 
            if(!isset($_GET['document_id'])) {
                header('Location: errors.php?error=NoSuchUrlFound');
            }
            $document_id = $_GET['document_id'];
            $sql = "SELECT * from `documents` WHERE `documents`.`id` = $document_id";

            //utility function
            function error() {
                header('Location: errors.php?error=UnknownErrorOccured');
            }

            if(!($result = $connection->query($sql))) {
                error();
            }

            if($result->num_rows == 0) {
                header('Location: errors.php?error=NoSuchDocumentFound');
            }
            $table_name = 'document_' . $document_id;
            $row = mysqli_fetch_assoc($result);
            $document_name = $row['document_name']; 
            $document_description = $row['document_description'];
        ?>

        <title><?php echo $document_name; ?></title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <style>
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
                <a class="nav-item nav-link" href="home_page.php"><h7 style="color: #a80585;">Home Page </h7><span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="requests.php"><h7 style="color: red;">Activity</h7></a>
                <a class="nav-item nav-link" href="profile.php"><h7 style="color: #a80585;">Profile</h7></a>
                <a class="nav-item nav-link" href="logout.php"><h7 style="color: #a80585;">logout</h7></a>
                </div>
            </div>
    </nav>

            <h1 style="color: #a80585; text-align:center; margin-top: 20px; font-style: italic"><?php echo $document_name." Application Form"; ?></h1>
            <?php
                $sql = "SELECT * FROM `user_information` WHERE `user_information`.`id` = $user_id";
                if(!($result = $connection->query($sql))) {
                    error();
                }
                $row = mysqli_fetch_assoc($result);

                $first_name = $row['first_name'];
                $middle_name = $row['middle_name'];
                $last_name = $row['last_name'];

                $birth_date = $row['birth_date'];

                $phone_number = $row['phone'];
                $email = $row['email'];
                $mobile_verification = $row['mobile_verification'];

                //User information Fetched from the database
            ?>    

            <!-- Form for the applying for the Document -->
            <form method="POST" name = "form" action = "server.php" style="width: 500px; margin: auto; margin-top: 20px" enctype="multipart/form-data">

                <input name='document_id' value="<?php echo $document_id; ?>" readonly required hidden>
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
                $sql = "SELECT `required_document` FROM `$table_name`";
                if(!($result = $connection->query($sql))) {
                    error();
                }

                $count = 0;
            ?>    
                <input name = 'required_proof' value="<?php echo $result->num_rows; ?>" readonly required hidden>
                <span style="font-size: small; color: #a80585;">Upload Jpeg Format Images and size should be less than 5MB</span>
            <?php

                //Fetching required Data from the database
                while($row=mysqli_fetch_assoc($result)){
                    $count++;

                    $proof_id = $row['required_document'];

                    $sql = "SELECT * FROM `proofs` WHERE `proofs`.`id` = $proof_id";
                    if(!($sub_result = $connection->query($sql))) {
                        error();
                    }

                    $sub_row = mysqli_fetch_assoc($sub_result);

                    $proof_name = $sub_row['proof_name'];
                    $proof_description = $sub_row['proof_description'];
            ?>
                <input name = 'proof_<?php echo $count?>' value="<?php echo $proof_id; ?>" readonly required hidden>
                <div class="form-group">
                        <label><?php echo $proof_name; ?></label>
                        <input name = "<?php echo 'proof_id_' . $proof_id; ?>" type="file" class="form-control-file" required>
                        <span style="font-size: small; color: green;"><?php echo $proof_description; ?></span>
                </div>
            <?php 
                    //Showing an Error if any while uploading a photos
                    if(isset($_SESSION['error_' . $proof_id])) {
                        ?>
                            <p style="color: red;"><?php echo $_SESSION['error_' . $proof_id]; ?></p>
                        <?php
                        unset($_SESSION['error_' . $proof_id]);
                    }
                } 
            ?>
                <button type="submit" name="apply_document" class="btn btn-primary" style="background-color: #a80585; margin-bottom: 70px;">Apply for Verification</button>
            </form>
    </body>
</html>