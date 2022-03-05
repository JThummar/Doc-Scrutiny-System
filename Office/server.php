<?php
    require_once('../PHPMailer/PHPMailerAutoload.php');
    $mail = new PHPMailer(true);

    include('config.php');
    session_start();


    // For sign up
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
            
            // $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                header('Location: errors.php?error=EmailAddressIsNotValid');
            }

            $sql = "SELECT * FROM `office_employee_information` WHERE `email` = '$email';";
            if(!($result = $connection->query($sql))) {
                // die($connection->error);
                header('Location: errors.php?error=UnknownError');die();   
            }

            if($result->num_rows > 0) {
                $_SESSION['office_message_email_signup'] = "YES";
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
        $password_hash=password_hash($user_password, PASSWORD_DEFAULT);
        $str = rand();
        $hash = md5($str);

        $sql = "INSERT INTO `office_employee_information` (`first_name`, `middle_name`, `last_name`, `birth_date`, `phone`, `email`, `password`, `token`, `email_verification`, `mobile_verification`, `photo_name`,`verified`) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$date_of_birth', '$phone_number', '$email', '$password_hash', '$hash', '0', '0', NULL,'0')";

        if(!$connection->query(($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknowErrorOccur');
        }

        $officer_id = mysqli_insert_id($connection);

        // Send to Verfication sent php
        $subject = "Verify your Account";
        $message = "<h2>Let's verify Consultancy</h2><br><a href= 'http://localhost/project/office/server.php?officer_id=$officer_id&token=$hash'>Click here To verify Your Account</a>";
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
            $sql = "DELETE FROM `office_employee_information` WHERE `office_employee_information`.`id` = $officer_id";
            $connection->query($sql);
        }
    }

    // For sign in
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
        $sql = "SELECT `email_verification`,`verified` FROM `office_employee_information` where `office_employee_information`.`email` = '$email'";

        $result = $connection->query($sql);

        if($result->num_rows == 0) {
            header('Location: errors.php?error=EitherEmailAdrressorPasswordIsAreWrong');
        } else {
            $row = $result->fetch_assoc();

            if($row['email_verification'] == 0) {
                header('Location: errors.php?error=EmailIsNotverified');
            } else if ($row['verified'] == 0) {
                header('Location: errors.php?error=AccountIsNotVerifiedYet');
            } else {
                $result = $connection->query("SELECT * from `office_employee_information` where `email` = '$email'");
                $result = $result->fetch_assoc();

                $password_hash = (isset($result['password']) ? $result['password'] : '');
                $check = password_verify($password, $password_hash);
                
                if($check) {

                    session_start();
                    $_SESSION['officer_email'] = $email;
                    $_SESSION['officer_id'] = $result['id'];
                    $_SESSION['officer_first_name'] = $result['first_name'];
                    $_SESSION['master_user'] = $result['master_user'];

                    if(isset($_POST['remember_me'])) {
                        setcookie("office_login",$email,time() + 60*60*24*7);
                        setcookie('office_password',$password,time() + 60*60*24*7);
                    }

                    header('Location: home_page.php');                    
                } else {
                    header('Location: errors.php?error=EitherEmailAdrressorPasswordIsAreWrong');
                }
            }
        }

    }

    // For verification of the user
    if(isset($_GET['officer_id']) && isset($_GET['token'])) {
        
        $id = $_GET['officer_id'];
        $token = $_GET['token'];
        $sql = "SELECT * FROM `office_employee_information` where `office_employee_information`.`id` = $officer_id";

        if(!($result = $connection->query($sql)) or ($result->num_rows == 0) ) {
            header('Location: errors.php?error=NoSuchAccount');
        }

        $sql = "UPDATE `office_employee_information` SET `email_verification` = '1' WHERE `office_employee_information`.`id` = $id AND `office_employee_information`.`token` = '$token'";
        if(!($result = $connection->query($sql)) or ($result->num_rows == 0) ) {
            header('Location errors.php?error=VerificationFailedTryPleaseSeeToken');
        }

        header('Location: email_verified.php');   
    }

    // For accepting the request
    if(isset($_POST['accept'])) {
        session_start();
        if(!isset($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        if(!is_numeric($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        if($_SESSION['officer_id'] != $_POST['officer_id']){
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $officer_id = $_POST['officer_id'];
        $request_id = $_POST['request_id'];
        $user_id = $_POST['user_id'];
        $document_id = $_POST['document_id'];
        $message = $_POST['feedback_message'];

        $sql = "SELECT * FROM `requests` WHERE `user_id` = $user_id AND `request_id` = $request_id AND `document_id` = $document_id";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        if($result->num_rows == 0) {
            header('Location: errors.php?error=InvalidServiceAccess');die();
        }

        $sql = "UPDATE `requests` SET `status` = 'accepted', `officer_id` = '$officer_id' WHERE `requests`.`request_id` = $request_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $doucment_name = $_POST['document_name'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $subject = "Feedback for $doucment_name with Service Id $request_id";
        $message = "<p>HI, $first_name" . " " . $last_name . " Your Service Request $doucment_name with request id $request_id is Accepted" . "</p>" ."<p>" .$message . "</p>";
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
            $_SESSION['office_document_response'] = "$doucment_name Accepted!";
            header("Location: document_requests.php?document_id=$document_id");
        } catch (Exception $e) {

        }
    }

    // Document rejecting handler
    if(isset($_POST['reject'])) {
        session_start();
        if(!isset($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        if(!is_numeric($_SESSION['officer_id'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        if($_SESSION['officer_id'] != $_POST['officer_id']){
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $officer_id = $_POST['officer_id'];
        $request_id = $_POST['request_id'];
        $user_id = $_POST['user_id'];
        $document_id = $_POST['document_id'];
        $message = $_POST['feedback_message'];

        $sql = "SELECT * FROM `requests` WHERE `user_id` = $user_id AND `request_id` = $request_id AND `document_id` = $document_id";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        if($result->num_rows == 0) {
            header('Location: errors.php?error=InvalidServiceAccess');die();
        }

        $sql = "UPDATE `requests` SET `status` = 'rejected', `officer_id` = '$officer_id' WHERE `requests`.`request_id` = $request_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $doucment_name = $_POST['document_name'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $subject = "Feedback for $doucment_name with Service Id $request_id";
        $message = "<p>HI, $first_name" . " " . $last_name . " Your Service Request $doucment_name with request id $request_id is Rejected" . "</p>" ."<p>" .$message . "</p>";
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
            $_SESSION['office_document_response_reject'] = "$doucment_name Rejected!";
            header("Location: document_requests.php?document_id=$document_id");
        } catch (Exception $e) {

        }
    }


    // Update the proof
    if(isset($_POST['update_proof'])) {
        session_start();
        if(!isset($_SESSION['master_user'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        $sql = "UPDATE `proofs` SET `proof_name` = '$name', `proof_description` = '$description' WHERE `proofs`.`id` = '$id'";
        
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=InvalidService');
            die();
        }
        $_SESSION['office_updated_proof'] = $name;
        header('Location: add_proof.php');
    }

    // Adding the new proof
    if(isset($_POST['save_proof'])) {
        session_start();
        if(!isset($_SESSION['master_user'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $name = $_POST['name'];
        $description = $_POST['description'];

        $sql = "INSERT INTO `proofs` (`proof_name`, `proof_description`) VALUES ('$name', '$description')";
        
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=InvalidService');
            die();
        }
        $_SESSION['office_updated_proof'] = $name;
        header('Location: add_proof.php');
    }

     //Updating the document
    if(isset($_POST['update_document'])) {
        session_start();
        if(!isset($_SESSION['master_user'])){
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $total_proofs = $_POST['total_proofs'];

        $table_name = "document_$id";
        echo $table_name." ".$name." ".$description." ".$total_proofs;
        $sql = "UPDATE `documents` SET `document_name` = '$name', `document_description` = '$description' WHERE `documents`.`id` = '$id'";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError1');die();
        }

        $sql = "DROP TABLE `$table_name`;";

        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError1');die();
        }

        $sql = "CREATE TABLE `project`.`document_$id` ( `required_document` INT NOT NULL ) ENGINE = InnoDB;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError1');die();
        }

        for($i = 0;$i < $total_proofs;$i++) {
            $box_id = 'proof_' . $i;

            if(isset($_POST[$box_id])) {
                $value = $_POST[$box_id];
                $sql = "INSERT INTO $table_name VALUES($value)";
                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownError3');die();
                }
            }
        }

        $_SESSION['office_document_updated'] = "$name";

        header('Location: add_document.php');
    }

    // Adding the new Document
    if(isset($_POST['save_document'])) {
        session_start();
        if(!isset($_SESSION['master_user'])) {
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $name = $_POST['name'];
        $description = $_POST['description'];
        $total_proofs = $_POST['total_proofs'];

        $sql = "INSERT INTO `documents`(`document_name`,`document_description`) VALUES('$name','$description');";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError1');die();
        }

        $id = mysqli_insert_id($connection);

        $sql = "CREATE TABLE `project`.`document_$id` ( `required_document` INT NOT NULL ) ENGINE = InnoDB;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError2');die();
        }

        for($i = 0;$i < $total_proofs;$i++) {
            $box_id = 'proof_' . $i;

            if(isset($_POST[$box_id])) {
                $value = $_POST[$box_id];
                $sql = "INSERT INTO `document_$id`(`required_document`) VALUES($value)";
                if(!($result = $connection->query($sql))) {
                    header('Location: errors.php?error=UnknownError3');die();
                }
            }
        }

        $_SESSION['office_document_updated'] = "$name";
        header('Location: add_document.php');
    }


    // Updating the profile information
    if(isset($_POST['update_profile'])) {

        session_start();

        $user_id = $_POST['id'];

        if($user_id != $_SESSION['officer_id']) {
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

        $sql = "SELECT * FROM `office_employee_information` WHERE `email` = '$email';";
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

        $date_of_birth=date("y-m-d",strtotime($date_of_birth));
        $password_hash=password_hash($user_password, PASSWORD_DEFAULT);
        $str = rand();
        $hash = md5($str);

        $sql = "";
        if($email_changed == true) {
            $sql = "UPDATE `office_employee_information` SET `first_name` = '$first_name',`middle_name` = '$middle_name',`last_name` = '$last_name',`birth_date` = '$date_of_birth',
                `phone` = '$phone_number',`email` = '$email',`password` = '$password_hash',`token` = '$hash',`email_verification` = '0'  WHERE `id` = '$user_id';";
        } else {
            $sql = "UPDATE `office_employee_information` SET `first_name` = '$first_name',`middle_name` = '$middle_name',`last_name` = '$last_name',`birth_date` = '$date_of_birth',
                `phone` = '$phone_number',`password` = '$password_hash'  WHERE `id` = '$user_id';";
        }


        
        if(!$connection->query(($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknowErrorOccur');
        }


        
        if($email_changed) {
            // Send to Verfication sent php
            $subject = "Verify your Account";
            $message = "<h2>Let's verify Consultancy</h2><br><a href= 'http://localhost/project/office/server.php?officer_id=$user_id&token=$hash'>Click here To verify Your Account</a>";
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

                session_destroy();
                header('Location: verification_sent.php'); die();

            } catch (Exception $e) {
                
            }
        } else {
            $_SESSION['office_update_profile'] = "Profile Updated";
            header('Location: profile.php');
        }
    }
    
    // Update picture
    if(isset($_POST['update_picture'])) {

        session_start();
        $user_id = $_POST['id'];

        if($user_id != $_SESSION['officer_id']) {
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
        if ($mime != "image/jpeg" ) {
            $flag = 1;
            $message = $message . "You file extension must be jpeg \n";
            
        } 
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

        $filename = 'office_'. $user_id . "_" . rand(0,100000);

        $sql = "UPDATE `office_employee_information` SET `photo_name` = '$filename' WHERE `id` = '$user_id'";

        if(!$connection->query($sql)) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $destination =  '../Uploads/'.$filename. ".jpeg";

        if (!move_uploaded_file($file, $destination)) {
            header('Location: errors.php?error=UnknownErrorOccured');
        }

        $_SESSION['office_update_profile'] = "Profile Updated";
        header('Location: profile.php');
    }

     // Change the visibility
    if(isset($_GET['change_document_visibility']) && isset($_GET['document_id'])) {
        session_start();
        if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false) {
            header('Location: errors.php?error=InvalidAccessTryTOLogin');
            die();
        }

        if(!is_numeric(($_GET['document_id']))) {
            header('Location: errors.php?error=InvalidService');die();
        }

        include('config.php');
        $document_id = $_GET['document_id'];
        $to = $_GET['change_document_visibility'];

        $sql = "UPDATE `documents` SET `active` = '$to' WHERE `id` = $document_id";

        if(!($connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');
        }
        header('Location: add_document.php');
    }

    // Verigying the user
    if(isset($_GET['verify_user_id'])) {
        $officer_id = $_GET['verify_user_id'];
        session_start();
        if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false || !(is_numeric($officer_id))) {
            header('Location: errors.php?error=InvalidAccessTryTOLogin');
            die();
        }

        $sql = "SELECT * FROM `office_employee_information` WHERE `id` = $officer_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $row = mysqli_fetch_assoc($result);
        if(!($row['verified'] == 0 && $row['email_verification'] == 1)) {
            header('Location: errors.php?error=InvalidServiceRequest');die();
        }

        $sql = "UPDATE `office_employee_information` SET `verified` = 1 WHERE `id` = $officer_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }
        header('Location: verify_user.php');
    }

    // unVerigying the user
    if(isset($_GET['unverify_user_id'])) {
        $officer_id = $_GET['unverify_user_id'];
        session_start();
        if(!isset($_SESSION['officer_id'])  || !isset($_SESSION['master_user']) || $_SESSION['master_user'] == false || !(is_numeric($officer_id))) {
            header('Location: errors.php?error=InvalidAccessTryTOLogin');
            die();
        }

        $sql = "SELECT * FROM `office_employee_information` WHERE `id` = $officer_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        $row = mysqli_fetch_assoc($result);
        if(!($row['verified'] == 1 && $row['email_verification'] == 1)) {
            header('Location: errors.php?error=InvalidServiceRequest');die();
        }

        $sql = "UPDATE `office_employee_information` SET `verified` = 0 WHERE `id` = $officer_id;";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }
        header('Location: user_details.php');
    }

    // forget password
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

        $sql = "SELECT * FROM `office_employee_information` WHERE `email` = '$email';";
        if(!($result = $connection->query($sql))) {
            // die($connection->error);
            header('Location: errors.php?error=UnknownError');die();   
        }

        if($result->num_rows == 0) {
            $error = $email . " is not registerd yet!!";
            $_SESSION['office_forgot_password_error'] = $error;
            header('Location: index.php');die();
        }

        $row = mysqli_fetch_assoc($result);

        $office_id = $row['id'];
        $str = rand();
        $hash = md5($str);

        $name = $row['id'] . "_office";
        
        $sql = "INSERT INTO `password_recover_hash` VALUES('$name' , '$hash') ON DUPLICATE KEY UPDATE `hash` = '$hash';";
        if(!($result = $connection->query($sql))) {
            header('Location: errors.php?error=UnknownError');die();   
        }


        $subject = "Request for recovering password";
        $message = "Click Below link for Recovering password <br> <a href='http://localhost/project/office/recover_password.php?id=$office_id&type=office&hash=$hash'>Recover Password</a>";
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

    // Recover password
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

        $name = $id . "_office";
        $sql = "SELECT * FROM `password_recover_hash` WHERE `name` = '$name';";

        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        if($result->num_rows == 0) {
            // echo "NO such data";die();
            header('Location: errors.php?error=InvalidAccess');die();
        }

        $sql = "UPDATE `office_employee_information` SET `password` = '$password_hash' WHERE `id` = $id;";

        if(!($result = $connection->query($sql))) {
            // echo $connection->error;
            header('Location: errors.php?error=UnknownErrorOccured');die();
        }

        session_start();
        $_SESSION['office_password_changed'] = "Yes";

        header('Location: login.php');

    }
?>

