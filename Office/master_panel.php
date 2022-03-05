<!DOCTYPE html>
<html>
<head>
    <?php 
        session_start();
        $master_user = false;

        // check only master user can visit the same page
        if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user']==false) {
            header('Location: errors.php?error=InvalidAccessTryTOLogin');
            die();
        }
        $master_user = $_SESSION['master_user'];
        include('config.php');
    ?>
    <title>Home Page</title>
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
                        <a class="nav-item nav-link" href="requests.php">Activity</a>
                        <a class="nav-item nav-link" href="profile.php">Profile</a>
                        <?php if($master_user==true) {?>
                        <a class="nav-item nav-link active" href="master_panel.php"><h7 style="color: red;">Master Panel</h7><span class="sr-only">(current)</span></a>
                        <?php  } ?>
                        <a class="nav-item nav-link" href="logout.php">logout</a>
                        </div>
                    </div>
            </nav>
            <hr class="rounded">

            <h1 style="color: red; text-align:center; margin-top: 30px; font-style: italic">Master User Panel</h1>
            <div class="row" style="margin-left: 300px;">
                    <div class="card" style="width:400px; height: 200px; border-style: solid; border-width: 5px; margin: 20px; border-color: green; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic">Add Document</h4>
                            <a href="add_document.php" class="btn btn-primary" style="background-color: green">Click</a>
                        </div>
                    </div>

                    <div class="card" style="width:400px; height: 200px; border-style: solid; border-width: 5px; margin: 20px; border-color: green; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic">Add Proof</h4>
                            <a href="add_proof.php" class="btn btn-primary" style="background-color: green">Click</a>
                        </div>
                    </div>
                    <br>
                    <div class="card" style="width:400px; height: 200px; border-style: solid; border-width: 5px; margin: 20px; border-color: green; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic">Verify office Member</h4>
                            <a href="verify_user.php" class="btn btn-primary" style="background-color: green">Click</a>
                        </div>
                    </div>

                    <div class="card" style="width:400px; height: 200px; border-style: solid; border-width: 5px; margin: 20px; border-color: green; border-radius: 25px; align-content: center;">
                        <div class="card-img-overlay">
                            <h4 class="card-title" style="color:#000000; font-style: italic">Officer's Details</h4>
                            <a href="user_details.php" class="btn btn-primary" style="background-color: green">Click</a>
                        </div>
                    </div>
            </div>
        </body>
</html>