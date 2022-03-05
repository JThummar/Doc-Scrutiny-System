<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();


            // check that only master user can access
            if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
                header('Location: errors.php?error=InvalidAccessTryTOLogin');
                die();
            }
            include('config.php');

            
            $update = false;
            $name = "";$description = "";$id = "";
            $master_user = $_SESSION['master_user'];

            $required_proofs = array();

            // check if get request then then allow to add the existenst user
            if(isset($_GET['edit_document'])) {
                
                //check for the ID
                if(!isset($_GET['edit_document']) || !is_numeric($_GET['edit_document'])) {
                    header('Location: add_document.php');
                    die();
                }
                $id = $_GET['edit_document'];

                $sql = "SELECT * FROM `documents` WHERE id = '$id';";
                if(!($result = $connection->query($sql))){
                    header('Location: errors.php?error=InvalidService');
                    die();
                }

                // check document is present name
                if($result->num_rows == 0) {
                    header('Location: add_document.php');
                    die();
                }

                $row = mysqli_fetch_assoc($result);
                $name = $row['document_name'];
                $description = $row['document_description'];

                $sql = "SELECT * FROM `document_$id`;";
                if(!($result = $connection->query($sql))){
                    header('Location: errors.php?error=InvalidService');
                    die();
                }

                // collect all required proofs
                $ii = 0;
                while($row = mysqli_fetch_assoc($result)) {
                    $required_proofs[$ii] = $row['required_document'];
                    $ii++;
                }
                $update = true;
            }
        ?>
        <title>Add Document</title>
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
            input.form-check-input {
                width: 20px; 
                height: 20px;
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
        <h1 style="color:red; text-align:center; margin-top: 20px; font-style: italic">Setup Document</h1>
        <form method="POST" action="server.php" style="width: 500px; margin: auto; margin-top: 20px">
                    <input type="hidden" name="id" value = "<?php echo $id; ?>" required>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" class="form-control"  value= "<?php echo $name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="desc">Description:</label>
                        <input type="text" id="desc" name="description" class="form-control"  value= "<?php echo $description; ?>" required>
                    </div>
                    <?php
                        $sql = "SELECT * FROM `proofs`;";
                        if(!( $result = $connection->query($sql))) {
                            header('Location: errors.php?error=UnknownErrorOccurred');
                            die();
                        }
                        $total_fields = $result->num_rows;

                        $i = 0;
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <!-- keep check box to add the proof to the document -->  
                        <div class="form-check">
                            <input type="checkbox" name='proof_<?php echo $i;?>' id=<?php echo $i;?> class="form-check-input" value='<?php echo $row['id'];?>' <?php if(in_array($row['id'],$required_proofs)){echo "checked";} ?>>
                            <label class="form-check-label" for="<?php echo $i; ?>">&nbsp; <?php echo $row['proof_name']; ?></label>
                        </div> <br>
                    <?php
                            $i++;
                        }
                    ?>
                    <input type="hidden" name='total_proofs' value="<?php echo $total_fields; ?>">
                    <?php if ($update == true): ?>
                        <!-- update document-->
                        <button type="submit" name="update_document" class="btn btn-primary" style="background-color: blue">update</button>
                    <?php else: ?>
                        <!-- Add new document-->
                        <button type="submit" name="save_document" class="btn btn-primary" style="background-color: blue">Save</button>
                    <?php endif ?>

                </form>
            </div>
    </body>
</html>