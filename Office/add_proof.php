<!DOCTYPE html>
<html>
    <head>

        <?php 
            session_start();
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }
            include('config.php');

            $master_user = $_SESSION['master_user'];

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

        <!-- Apply for the proof -->
        <?php 
            if(isset($_SESSION['office_updated_proof'])) {
        ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Proof <strong><?php echo $_SESSION['office_updated_proof'] ;?></strong> updated successfully
        </div>

        <?php
                unset($_SESSION['office_updated_proof']);
            }
        ?>

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
            // load all the proof from the database
            $sql = "SELECT * FROM `proofs`;";

            if(!( $result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccurred');
                die();
            }
        ?>
        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Available Proofs</h1>
        
        <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Proof Id</th>
                        <th scope="col">Proof Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Edit</th>
                    </tr>
                </thead>
                <tbody>
        <?php   
            while($row = mysqli_fetch_assoc($result)) {
        ?>    
            <tr>
                    <th scope="row"><?php echo $row['id']; ?></th>
                    <td><?php echo $row['proof_name']; ?></td>
                    <td><?php echo $row['proof_description']; ?></td>
                    <td><button class="btn btn-primary" style="background-color: #0099cc"><a style="color: #ffffff" href="add_prf.php?edit_proof=<?php echo $row['id'];?>">Edit</a></button></td>
            </tr>
        <?php        
            }
        ?>
        </tbody>
        </table>
            <form method="POST" action="add_prf.php" style="width: 500px; margin: auto; margin-top: 20px" >
                <button type="submit" class="btn btn-primary" style="background-color: blue; margin-left: 150px; margin-bottom: 50px;">Add New Proof</button>
            </form>
        </div>
    </body>
</html>