<!DOCTYPE html>
<html>
    <head>

        <?php 
            session_start();

            // Check for the valid entry
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }
            include('config.php');
            $update = false;
            $name = "";$description = "";$id = "";

            $master_user = $_SESSION['master_user'];
            

            /// to edit the proof
            if(isset($_GET['edit_proof'])) {
                
                // check valid request
                if(!isset($_GET['edit_proof']) || !is_numeric($_GET['edit_proof'])) {
                    header('Location: add_proof.php');
                    die();
                }
                $id = $_GET['edit_proof'];

                
                $sql = "SELECT * FROM `proofs` WHERE id = '$id';";
                if(!($result = $connection->query($sql))){
                    header('Location: errors.php?error=InvalidService');
                    die();
                }

                if($result->num_rows == 0) {
                    header('Location: add_proof.php');
                    die();
                }
                $row = mysqli_fetch_assoc($result);
                $name = $row['proof_name'];
                $description = $row['proof_description'];

                $update = true;
            }
        ?>
            <style>
        hr.rounded {
            border-top: 3px solid #b300b3;
            margin: 0px;
            border-radius: 3px;
        }
            </style>
    
        <title>Add Proof</title>
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
        <?php
            $sql = "SELECT * FROM `proofs`;";

            if(!( $result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccurred');
                die();
            }
        ?>
        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Setup Proof</h1>

        <form method="POST" action="server.php" style="width: 500px; margin: auto; margin-top: 20px">

            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name = "name" pattern="[A-Za-Z0-9 ]" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" class="form-control" name = "description" pattern="[A-Za-Z0-9 ]" value="<?php echo $description; ?>" required>
            </div>
            <?php if ($update == true): ?>
                <button type="submit" name="update_proof" class="btn btn-primary" style="background-color: blue">update</button>
            <?php else: ?>
                <button type="submit" name="save_proof" class="btn btn-primary" style="background-color: blue">Save</button>
            <?php endif ?>
        </form>
    </body>
</html>