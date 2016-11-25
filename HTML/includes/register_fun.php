<?php


include_once 'db_connect.php';
include_once 'db_config.php';

$error_msg = "";

if (isset($_POST['email'], $_POST['p'] , $_POST['name'] , $_POST['lastname'] , $_POST['age'], $_POST['phone'] , $_POST['sex'] )) {
    // Sanitize and validate the data passed in

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);

    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);



    $temp_sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_STRING);
    

    $sex = "M";
    if (strcmp($temp_sex, "Male") !== 0 ){
        $sex = "W";

    }




    


    $prep_stmt = "SELECT idUser FROM User WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
    
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            // A user with this email address already exists
            $error_msg .= '<p class="error">A user with this email address already exists.</p>';
        }
    } else {
        $error_msg .= '<p class="error">Database error</p>';
    }
    


   


    $adminuser = "N";
   
    

    $driver = new mysqli_driver();
    $driver->report_mode = MYSQLI_REPORT_ALL;


    try {


    if (empty($error_msg)) {
       
    
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES (?, ?, ?, ? ,?, ?, ?, ?)")) {
            
            $insert_stmt->bind_param('sssiisss', $email, $name, $lastname, $phone ,$age , $sex , $adminuser ,$password);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {


                header('Location: ../error.php?err='.$email.'&?a='.$sex);
                exit();
            }
        }
        
        header('Location: ./user.php');
        exit();
    }

    } catch (mysqli_sql_exception $e) {

    echo $e->__toString();
}
    
}

