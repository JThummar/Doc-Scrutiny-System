<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();


            // check for valid entry of the user
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

        <title>Add Document</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    </head>
    <body>

        <!-- Show alert for -->
        <?php 
            if(isset($_SESSION['office_document_updated'])) {
        ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Document <strong><?php echo $_SESSION['office_document_updated'] ;?></strong> updated successfully
        </div>

        <?php
                unset($_SESSION['office_document_updated']);
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
                    <a class="nav-item nav-link" href="master_panel.php"><h7 style="color: red;">Master Panel</h7></a>
                    <?php  } ?>
                    <a class="nav-item nav-link" href="logout.php">logout</a>
                    </div>
                </div>
        </nav>
        <hr class="rounded">
        <?php
            $sql = "SELECT * FROM `documents`;";

            if(!( $result = $connection->query($sql))) {
                header('Location: errors.php?error=UnknownErrorOccurred');
                die();
            }
        ?>
        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Available Documents</h1>

        <!-- Load the documents present in the database -->
        <div class="container" style="margin-top: 30px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Document Id</th>
                        <th scope="col">Document Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Change Visibility</th>
                    </tr>
                </thead>
                <tbody>
        <?php   
            while($row = mysqli_fetch_assoc($result)) {
                $ab = $row['active'];
                $cd = 'disable';
                $col= 'red';
                if($ab == 'disable') {
                    $cd = 'enable';
                    $col = 'green';
                }
                $document_id = $row['id'];

        ?>    
            <tr>
                    <th scope="row"><?php echo $row['id']; ?></th>
                    <td><?php echo $row['document_name']; ?></td>
                    <td><?php echo $row['document_description']; ?></td>
                    <td><button class="btn btn-primary" style="background-color: #0099cc"><a style="color: #ffffff" href="add_doc.php?edit_document=<?php echo $row['id'];?>">Edit</a></button></td>
                    <td style="text-align: center;"><button class="btn btn-primary" style="background-color: <?php echo $col; ?>;"><a style="color: #ffffff" href="server.php?change_document_visibility=<?php echo $cd;?>&document_id=<?php echo $document_id; ?>"><?php echo $cd;?></a></button></td>
            </tr>
        <?php        
            }
        ?>
            </tbody>
            </table>
            <form method="POST" action="add_doc.php" style="width: 500px; margin: auto; margin-top: 20px" >
                <button type="submit" class="btn btn-primary" style="background-color: blue; margin-left: 150px; margin-bottom: 50px;">Add New Document</button>
            </form>
            </div>
    </body>
</html>