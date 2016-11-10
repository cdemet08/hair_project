
<?php

include_once 'db_config.php';   // As functions.php is not included
if ($mysqli->connect_error) {
    header("Location: ../error.php?err=Unable to connect to MySQL");
    exit();
}

