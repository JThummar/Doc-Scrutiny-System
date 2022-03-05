<?php 
    $connection = mysqli_connect("localhost","root",'',"project");

    if(!$connection){
        header('Location: errors.php?error=ConnectionToDataBaseFailed');
    }

    $sender_email = "jackdeveloper222@gmail.com";
    $sender_password = "Project0909";
?>