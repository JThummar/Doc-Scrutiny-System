<html>
    <head>
        <title>Recover Password</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <style>
            label {
                font-style: italic;
                font-size: 20px;
            }
        </style>
        <script>
            function validate() {

                var password = document.forms['form']['password'].value;
                var re_password = document.forms['form']['re_password'].value;
                var error = '';
                
                if(password !== null && password !== '' && re_password !== null && re_password !== '') {
                    if(password !== re_password) {
                        error = error.concat("Password Confirmation Failed!! \n");
                    }
                } else {
                    error = error.concat("Either Password or Re-Password is/are Empty!! \n");
                }

                if(error !== '') {
                    alert(error);
                    return false;
                }

                return true;
            }
        </script>
    </head>
    <body>
        <?php 
            include('config.php');
            $id = "";
            $hash = "";
            if(isset($_GET['id']) && isset($_GET['type']) && isset($_GET['hash']) ) {
                $id = $_GET['id'];
                $hash = $_GET['hash'];

                if(!is_numeric($id)) {
                    header('Location: login.php');die();
                }

                if($_GET['type'] != 'office') {
                    header('Location: errors.php?error=UnknownErrorOccured');die();
                }

                $name =$id . "_office";
                $sql = "SELECT * FROM `password_recover_hash` WHERE `name` = '$name' AND `hash` = '$hash';";

                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownErrorOccured');die();
                }

            } else {
                header('Location: login.php');die();
            }
        ?>
        <form action="server.php" method="POST" name = 'form' style="width: 500px; margin: auto; margin-top: 20px" onsubmit="return validate()">
            <input type="hidden" name = 'id' value="<?php echo $id;?>">
            <input type="hidden" name = 'hash' value="<?php echo $hash?>">
            <div class="form-group">
                <label for="1">Password</label>
                <input type="password" id="1" name="password" placeholder="Password: e.g. ABcd!@12" class="form-control" required/>
            </div>
            <div class="form-group">
                <label for="2">Re-Type Password</label>
                <input type="password" id="2" name="re_password" placeholder="Retype you password" class="form-control" required/>
            </div>
            <button type="submit" name='recover_password' class="btn btn-primary" style="background-color: blue">Submit</button>
        </form>
    </body>
</html>