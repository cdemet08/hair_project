<?php


include_once 'functions.php';
include_once 'db_config.php';   // As functions.php is not included


sec_session_start(); // Our custom secure way of starting a PHP session.
 




    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    if ($mysqli->connect_error) {
        header("Location: ../error.php?err=Unable to connect to MySQL");
        exit();
    }


    $sql = "SELECT idUser, idBarber,dateTimeApp FROM Appointment";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["idUser"]. " - Name: " . $row["idBarber"]. " " . $row["dateTimeApp"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    

  

    $mysqli->close();



    


   





?>


