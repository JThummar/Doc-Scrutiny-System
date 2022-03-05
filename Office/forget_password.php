<!DOCTYPE html>
<html>
    <head>
        <title>Forget Password</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <style>
            label {
                font-style: italic;
                font-size: 20px;
            }
        </style>
    </head>

    <body>
        <form action="server.php" method="POST" style="width: 500px; margin: auto; margin-top: 20px">
        <div class="form-group">
            <label for="email1">Enter Email</label>
            <input type="email" id="email1" name="email" class="form-control" required>
        </div>
            <button type="submit" name = "forget_password" class="btn btn-primary" style="background-color: blue">Submit</button>
        </form>
        
    </body>
</html>