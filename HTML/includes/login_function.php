<?php
include_once 'db_connect.php';
include_once 'functions.php';
 

sec_session_start(); // Our custom secure way of starting a PHP session.
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.
    

    $login_result = login($email, $password, $mysqli);

    if ($login_result == 1) {
        // Login success 
        header('Location: ../user.php');
    } else if ($login_result == 2){
        //admin page success
        header('Location: ../admin.php');

    }
    // no user exist
    else if ($login_result == -1){
        // email dont exist
        header('Location: ../index.php?error=login_failure');


    }
    else if ($login_result == 0){
        // Password is not correct
        header('Location: ../index.php?error=login_failure');


    }
    else {
        // other wrong in login
        header('Location: ../index.php?error=login_failure');

    }


} else {
        
        header('Location: ../index.php?error=invalide_request');

}