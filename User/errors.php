<!-- Showing an Error -->

<html>
    <head>
        <title>Error</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    </head>
<?php
    if(isset($_GET['error'])) {
        $message = $_GET['error'] . "";
    ?>
        <p style="color: red; margin: 20px; font-size: 40px"><?php echo $message; ?></p>
    <?php
        header('login.php');
    } else {
        die("Unknown Error");
    }
?>
</html>