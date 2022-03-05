<?php
    require_once('../PHPMailer/PHPMailerAutoload.php');
    $mail = new PHPMailer(true);

    include('config.php');
    session_start();


    // Signup the user
    if(isset($_POST['sign_up'])) {
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $user_password = $_POST['password'];

        // first name
        if(empty($first_name)) {
            header('Location: errors.php?error=FirstNameIsEmpty');
        } else {
            $len = strlen($first_name);
            if($len > 50 || $len <3) {
                header('Location: errors.php?error=FirstNameLengthShouldBeBetween2to50');
            }
            if(!ctype_alpha($first_name)){
                // header('Location: errors.php?error=FirstNameShouldOnlyContainAphabetsEitherLowerOrHiger');
            }
        }

        // middle name
        if(empty($middle_name)) {
            header('Location: errors.php?error=MiddleNameIsEmpty');
        } else {
            $len = strlen($first_name);
            if($len > 50 || $len <3) {
                header('Location: errors.php?error=MiddleNameLengthShouldBeBetween2to50');
            }
            if(!ctype_alpha($first_name)){
                header('Location: errors.php?error=MiddleNameShouldOnlyContainAphabetsEitherLowerOrHiger');
            }
        }

        // last name
        if(empty($last_name)) {
            header('Location: errors.php?error=LastNameIsEmpty');
        } else {
            $len = strlen($first_name);
            if($len > 50 || $len <3) {
                header('Location: errors.php?error=LastNameLengthShouldBeBetween2to50');
            }
            if(!ctype_alpha($first_name)){
                header('Location: errors.php?error=LastNameShouldOnlyContainAphabetsEitherLowerOrHiger');
            }
        }

        // Validating Date of Birth Should not be less than 150 years and greater than current date

        if(empty($date_of_birth)) {
            header('Location: errors.php?error=BirthDateIsEmpty');
        } else {
            date_default_timezone_set('Asia/Kolkata');
            $today = date('y-d-m'); 
            $diff = (strtotime($date_of_birth) - strtotime($today));
            $years = floor($diff / (365*60*60*24));
            
            if($diff < 0) {
                // die($diff . "  fds " . strtotime($today) . " " .strtotime($date_of_birth));
                header('Location: errors.php?error=InvalidBirthDate');
            }

            if($years>=100) {
                header('Location: errors.php?error=AgeIsGreaterThan100Years');
            }
        }

        // Phone Number
        if(empty($phone_number)) {
            header('Location: errors.php?error=PhoneNumberIsEmpty');
        } else {
            $len = strlen($phone_number);
            if($len != 10) {
                header('Location: errors.php?error=LastNameLengthShouldbeEqualTo10');
            }
            if(!is_numeric($phone_number)){
                header('Location: errors.php?error=LastNameShouldOnlyContainNumberOnly');
            }
        }

        // Email        //Email check database contains the email or not
        if(empty($email)) {
            header('Location: errors.php?error=EmailIsEmpty');
        } else {
            $len = strlen($email);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                header('Location: errors.php?error=EmailAddressIsNotValid');
            }

            $sql = "SELECT * FROM `user_information` WHERE `email` = '$email';";
            if(!($result = $connection->query($sql))) {
                // die($connection->error);
                header('Location: errors.php?error=UnknownError');die();   
            }

            if($result->num_rows > 0) {
                $_SESSION['message_email_signup'] = "YES";
                header('Location: index.php');
                die();
            }
        }

        // Password
        if(empty($user_password)) {
            header('Location: errors.php?error=PasswordIsEmpty');
        } else {
            $len = strlen($user_password);
            if($len < 6) {
                header('Location: errors.php?error=PasswordLengthShouldBeGreaterThan6');
            }

            //Check Format
            // if(!is_numeric($phone_number)){
            //     header('Location: errors.php?error=LastNameShouldOnlyContainNumberOnly');
            // }
        }

        $date_of_birth=date("y-m-d",strtotime($date_of_birth));

        // Encrypt the password before adding into the database
        $password_hash=password_hash($user_password, PASSWORD_DEFAULT);


        // Genetate the hash for the the checking email
        $str = rand();
        $hash = md5($str);

        $sql = "INSERT INTO `user_information` (`first_name`, `middle_name`, `last_name`, `birth_date`, `phone`, `email`, `password`, `token`, `email_verification`, `mobile_verification`, `photo_name`) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$date_of_birth', '$phone_number', '$email', '$password_hash', '$hash', '0', '0', NULL)";

        if(!$connection->query(($sql))) {
            echo $connection->error;
            // header('Location: errors.php?error=UnknowErrorOccur');
        }

        $id = mysqli_insert_id($connection);

        // Send to Verfication sent php
        $subject = "Verify your Account";
        $message = "<h2>Let's verify Consultancy</h2><br><a href= 'http://localhost/project/user/server.php?id=$id&token=$hash'>Click here To verify Your Account</a>";
        $receiver = $email;
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $sender_email;                     
            $mail->Password   = $sender_password;                               
            $mail->SMTPSecure = 'tls';         
            $mail->Port       = 587;
            $mail->setFrom($sender_email);
            $mail->addAddress($receiver);     
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
            header('Location: verification_sent.php');
        } catch (Exception $e) {
            // if failed for the any reason delete the user
            $sql = "DELETE FROM `user_information` WHERE `user_information`.`id` = $id";
            $connection->query($sql);
            header('Location: errors.php?error=ErrorOnOurSideSorryForInconvnice');
        }
    }


    // POST request for the sign_in user
    if(isset($_POST['sign_in'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];


        // Email
        if(empty($email)) {
            header('Location: errors.php?error=EmailIsEmpty');
        } else {


            $len = strlen($email);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                header('Location: errors.php?error=EmailAddressIsNotValid');
            }
        }

        // Password
        if(empty($password)) {
            header('Location: errors.php?error=PasswordIsEmpty');
        } else {
            $len = strlen($password);
            if($len < 6) {
                header('Location: errors.php?error=PasswordLengthShouldBeGreaterThan6');
            }

            //Check Format
            // if(!is_numeric($phone_number)){
            //     header('Location: errors.php?error=LastNameShouldOnlyContainNumberOnly');
            // }
        }

        //Check database start Session log in and Redirect to home page
        $sql = "SELECT `email_verification` FROM `user_information` where `user_information`.`email` = '$email'";

        $result = $connection->query($sql);

        if($result->num_rows == 0) {
            // Check such user exist or not
            header('Location: errors.php?error=EitherEmailAdrressorPasswordIsAreWrong');
        } else {
            $row = $result->fetch_assoc();

            if($row['email_verification'] == 0) {
                // Check email is verified or not
                header('Location: errors.php?error=EmailIsNotverified');
            } else {
                $result = $connection->query("SELECT * from `user_information` where `email` = '$email'");
                $result = $result->fetch_assoc();

                $password_hash = (isset($result['password']) ? $result['password'] : '');
                $check = password_verify($password, $password_hash);
                
                if($check) {

                    session_start();
                    $_SESSION['email'] = $email;
                    $_SESSION['id'] = $result['id'];
                    $_SESSION['first_name'] = $result['first_name'];

                    // if user said then set the cookies
                    if(isset($_POST['remember_me'])) {
                        setcookie("user_login",$email,time() + 60*60*24*7);
                        setcookie('user_password',$password,time() + 60*60*24*7);
                    }
                    // if successful request redirect to the home page 
                    header('Location: home_page.php');
                    
                } else {
                    header('Location: errors.php?error=EitherEmailAdrressorPasswordIsAreWrong');
                }
            }
        }

    }

    // Get request for the verifying user email
    if(isset($_GET['id']) && isset($_GET['token'])) {
        $id = $_GET['id'];
        $token = $_GET['token'];
        $sql = "SELECT * FROM `user_information` where `user_information`.`id` = $id";

        if(!($result = $connection->query($sql)) or ($result->num_rows == 0) ) {
            // Check email is used or not 
            header('Location: errors.php?error=NoSuchAccount');
        }

        $sql = "UPDATE `user_information` SET `email_verification` = '1' WHERE `user_information`.`id` = $id AND `user_information`.`token` = '$token'";
        if(!($result = $connection->query($sql)) or ($result->num_rows == 0) ) {
            // If the any data is tempered
            header('Location errors.php?error=VerificationFailedTryPleaseSeeToken');
        }

        header('Location: email_verified.php');   
    }


    // Apply the document Request for the user accepting the POST Request
    if(isset($_POST['apply_document'])) {

        function error() {
            header('Location: errors.php?error=InvalidServiceAccess');
        }

        session_start();
        if(!isset($_SESSION['id'])) {
            header('Location: errors.php?error=InvalidAccess');
        }

        if((!isset($_POST['document_id'])) or (!is_numeric($_POST['document_id']))) {
            // echo "document Is is fine \n";
            error();
        } 

        if((!isset($_POST['required_proof'])) or (!is_numeric($_POST['required_proof']))) {
            // echo "required proof is fine \n";
            error();
        }
        $user_id = $_SESSION['id'];
        $document_id = $_POST['document_id'];
        $sql = "SELECT * FROM `documents` WHERE `id` = $document_id";

        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
             error(); 
        }
        if($result->num_rows == 0) { 
            // echo $connection->error;
            error();
        }

        $row = mysqli_fetch_assoc($result);
        $document_name = $row['document_name'];

        $table_name = 'document_' . $document_id;
        $count = 0;
        $required_proof_array = array();

        $sql = "SELECT * FROM `$table_name`";
        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
             error();
        }

        $required_proof_numbers = $result->num_rows;
        
        if($required_proof_numbers != $_POST['required_proof']) { 
            // echo $connection->error;
            error(); 
        }

        $error = 0;
        $error_count = 0; 
        
        while($row = mysqli_fetch_assoc($result)) {
            $required_proof_array[$count] = $row['required_document'];
            $count++;

            $string = "proof_id_" . $row['required_document'];
            if($_FILES[$string]['error'] != "UPLOAD_ERR_OK") {
                // echo "Problem in while loop";
                error();
            }

            $file = $_FILES["proof_id_" . $row['required_document']]['tmp_name'];
            $size = $_FILES["proof_id_" . $row['required_document']]['size'];

            $mime = mime_content_type($file);

            $flag = 0;
            $message = "";
            if ($mime != "image/jpeg" ) {
                echo "$mime " . $row['required_document'];
                $error = 1;
                $flag = 1;
                $message = $message . "You file extension must be jpeg \n";
                
            } 
            if ($size > 1000000*5) { 
                $error = 1;
                $flag = 1;
                $message = $message . "File too large! \n";
            }
            if($flag == 1) {
                $error_count++;
                echo "<br>" . $row['required_document'] . "<br>";
                echo $message;
                $_SESSION['error_' .  $row['required_document']]  = $message;
            }
        }
        if($error == 1 or $error_count > 0) {
            header("Location: document.php?document_id=$document_id");
            die();
        }

        // echo "Everything is Fine";

        $sql = "INSERT INTO `requests` (`user_id`, `document_id`, `time`, `status`, `officer_id`) VALUES ('$user_id', '$document_id', current_timestamp(), 'pending', NULL);";

        if(!($connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');
        }
        $request_id = mysqli_insert_id($connection);

        $sql = "SELECT * FROM `$table_name`";
        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
             error();
        }
        
        while($row = mysqli_fetch_assoc($result)) {
            

            $string = "proof_id_" . $row['required_document'];
            if($_FILES[$string]['error'] != "UPLOAD_ERR_OK") {
                // echo "Problem in while loop";
                error();
            }

            $file = $_FILES["proof_id_" . $row['required_document']]['tmp_name'];
            $filename = $request_id . "_" . $row['required_document'];

            $destination =  '../Uploads/'.$filename. ".jpeg";

            if (!move_uploaded_file($file, $destination)) {
                header('Location: errors.php?error=UnknownErrorOccured');
            }
        }
        // echo "Request Made successfully";

        $subject = "Service Request";
        $message = "<p>Your Request Id is $request_id for the document $document_name Visit your profile page for the update</p>";
        $receiver = $_SESSION['email'];
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $sender_email;                     
            $mail->Password   = $sender_password;                               
            $mail->SMTPSecure = 'tls';         
            $mail->Port       = 587;
            $mail->setFrom($sender_email);
            $mail->addAddress($receiver);     
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();

            $_SESSION['user_document_applied'] = "$document_name";


            header('Location: home_page.php');
        } catch (Exception $e) {

        }
    }

    //get request Document id
    // check Document id

    // Document load data store into session variable

    if(isset($_POST['update_profile'])) {

        session_start();

        $user_id = $_POST['id'];

        if($user_id != $_SESSION['id']) {
            header('Location: errors.php?error=InvalidServiceRequest');
        }
        include('config.php');

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $middle_name = $_POST['middle_name'];
        $date_of_birth = $_POST['birth_date'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $user_password = $_POST['password'];

        $sql = "SELECT * FROM `user_information` WHERE `email` = '$email';";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError');die();   
        }

        $email_changed = false;

        if($result->num_rows == 1) {

            $row = mysqli_fetch_assoc($result);
            if($row['id'] != $user_id) {
                $_SESSION['error_message_email_change'] = $email;
                header("Location: profile.php?id=$user_id");
                die();
            }
        } else {
            $email_changed = true;
        }

        if(empty($user_password)) {
            header('Location: errors.php?error=PasswordIsEmpty');die();
        } else {
            $len = strlen($user_password);
            if($len < 6) {
                header('Location: errors.php?error=PasswordLengthShouldBeGreaterThan6');die();
            }

            //Check Format
            // if(!is_numeric($phone_number)){
            //     header('Location: errors.php?error=LastNameShouldOnlyContainNumberOnly');
            // }
        }

        $date_of_birth=date("y-m-d",strtotime($date_of_birth));
        $password_hash=password_hash($user_password, PASSWORD_DEFAULT);
        $str = rand();
        $hash = md5($str);

        $sql = "";

        // if email is changed then verify email id again 
        if($email_changed == true) {
            $sql = "UPDATE `user_information` SET `first_name` = '$first_name',`middle_name` = '$middle_name',`last_name` = '$last_name',`birth_date` = '$date_of_birth',
                `phone` = '$phone_number',`email` = '$email',`password` = '$password_hash',`token` = '$hash',`email_verification` = '0'  WHERE `id` = '$user_id';";
        } else {
            $sql = "UPDATE `user_information` SET `first_name` = '$first_name',`middle_name` = '$middle_name',`last_name` = '$last_name',`birth_date` = '$date_of_birth',
                `phone` = '$phone_number',`password` = '$password_hash'  WHERE `id` = '$user_id';";
        }


        
        if(!$connection->query(($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknowErrorOccur');
        }


        
        if($email_changed) {
            // Send to Verfication sent php
            $subject = "Verify your Account";
            $message = "<h2>Let's verify Consultancy</h2><br><a href= 'http://localhost/project/user/server.php?id=$user_id&token=$hash'>Click here To verify Your Account</a>";
            $receiver = $email;
            try{
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;                                   
                $mail->Username   = $sender_email;                     
                $mail->Password   = $sender_password;                               
                $mail->SMTPSecure = 'tls';         
                $mail->Port       = 587;
                $mail->setFrom($sender_email);
                $mail->addAddress($receiver);     
                
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->send();

                //logout user
                session_destroy();
                header('Location: verification_sent.php'); die();

            } catch (Exception $e) {
                
            }
        } else {

            $_SESSION['user_profile_updated'] = "YES";
            // redirect user profile 
            header('Location: profile.php');
        }
    }
    

    // update user picture 
    if(isset($_POST['update_picture'])) {

        $user_id = $_POST['id'];

        // Check php session that user if logged in or not 
        if($user_id != $_SESSION['id']) {
            header('Location: errors.php?error=InvalidServiceRequest');
        }
        include('config.php');



        if($_FILES['profile_pic']['error'] != "UPLOAD_ERR_OK") {
            header('Location: errors.php?error=InvalidAccess');die();

            // die($_FILES['profile_pic']['error']);
        }

        $file = $_FILES['profile_pic']['tmp_name'];
        $size = $_FILES['profile_pic']['size'];

        $mime = mime_content_type($file);

        $flag = 0;
        $message = "";

        // only jpeg file is accepted
        if ($mime != "image/jpeg" ) {
            $flag = 1;
            $message = $message . "You file extension must be jpeg\n";
            
        } 

        // accept upto the 5MB
        if ($size > 1000000*5) { 
            $error = 1;
            $flag = 1;
            $message = $message . "File too large! \n";
        }

        if($flag == 1) {
            $_SESSION['message_update_picture'] = $message;
            header("Location: profile.php?profile_id=$user_id");
            die();
        }

        $filename = 'user_'. $user_id . "_".  rand(0,100000);

        // store profile photo name
        $sql = "UPDATE `user_information` SET `photo_name` = '$filename' WHERE `id` = '$user_id'";

        if(!$connection->query($sql)) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $destination =  '../Uploads/'.$filename. ".jpeg";

        // move uploaded file to the server
        if (!move_uploaded_file($file, $destination)) {
            header('Location: errors.php?error=UnknownErrorOccured');
        }
        $_SESSION['user_profile_updated'] = "YES";
        header('Location: profile.php');
    }

    // Forget password
    if(isset($_POST['forget_password'])) {
        $email = $_POST['email'];
        include('config.php');
        $error = "";

        if(empty($email)) {
            header('Location: errors.php?error=EmailIsEmpty');die();
        } else {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                header('Location: errors.php?error=EmailAddressIsNotValid');die();
            }
        }

        $sql = "SELECT * FROM `user_information` WHERE `email` = '$email';";
        if(!($result = $connection->query($sql))) {
            // die($connection->error);
            header('Location: errors.php?error=UnknownError');die();   
        }

        if($result->num_rows == 0) {
            $error = $email . " is not registerd yet!!";
            $_SESSION['forgot_password_error'] = $error;
            header('Location: index.php');die();
        }

        $row = mysqli_fetch_assoc($result);

        $user_id = $row['id'];
        $str = rand();
        $hash = md5($str);

        $name = $row['id'] . "_user";
        
        $sql = "INSERT INTO `password_recover_hash` VALUES('$name' , '$hash') ON DUPLICATE KEY UPDATE `hash` = '$hash';";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError');die();   
        }


        $subject = "Request for recovering password";
        $message = "Click Below link for Recovering password <br> <a href='http://localhost/project/user/recover_password.php?id=$user_id&type=user&hash=$hash'>Recover Password</a>";
        $receiver = $email;
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $sender_email;                     
            $mail->Password   = $sender_password;                               
            $mail->SMTPSecure = 'tls';         
            $mail->Port       = 587;
            $mail->setFrom($sender_email);
            $mail->addAddress($receiver);     
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
            header('Location: login.php');

        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
          }

    }


    // To recover password
    if(isset($_POST['recover_password'])) {
        $id = $_POST['id'];
        $hash = $_POST['hash'];

        if(!is_numeric($id)) {
            // echo $id;die();
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $user_password = $_POST['password'];

        if(empty($user_password)) {
            header('Location: errors.php?error=PasswordIsEmpty');die();
        } else {
            $len = strlen($user_password);
            if($len < 6) {
                header('Location: errors.php?error=PasswordLengthShouldBeGreaterThan6');die();
            }

            //Check Format
            // if(!is_numeric($phone_number)){
            //     header('Location: errors.php?error=LastNameShouldOnlyContainNumberOnly');
            // }
        }

        $password_hash=password_hash($user_password, PASSWORD_DEFAULT);

        $name = $id . "_user";
        $sql = "SELECT * FROM `password_recover_hash` WHERE `name` = '$name';";

        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        if($result->num_rows == 0) {
            // echo "NO such data";die();
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $sql = "UPDATE `user_information` SET `password` = '$password_hash' WHERE `id` = $id;";

        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        session_start();
        $_SESSION['password_changed'] = "Yes";

        header('Location: login.php');

    }
?>