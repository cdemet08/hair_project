<?php
include_once 'db_config.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name 
    $secure = SECURE;

    // This stops JavaScript being able to access the session id.
    $httponly = true;

    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }

    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

    // Sets the session name to the one set above.
    session_name($session_name);

    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}



function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT idUser, password,adminuser
        FROM User WHERE email = ? LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $db_password,$db_adminuser);
        $stmt->fetch();
        


        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted. We are using
                // the password_verify function to avoid timing attacks.
                if (strcmp($db_password, $password) === 0 and strcmp($db_adminuser,"N") === 0) {


                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                  

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['email'] = $email;
                    $_SESSION['login_string'] = $db_password . $user_browser;

                    //$_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);
                    // Login successful.

                    return 1;
                    //return true;
                } 
                else if (strcmp($db_password, $password) === 0 and strcmp($db_adminuser,"Y") === 0) {
                    //admin 
                      // Password is correct!

                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['email'] = $email;
                    $_SESSION['login_string'] = $db_password . $user_browser;

                    
                    //admin
                     return 2;
                }
                else {
                    /*
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");

                                    */

                    return 0;
                    //return false;
                }
            }
        } else {
            return -1;
            // No user exists.
            // false;
        }
    }
}


function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (1 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts 
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}




function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['email'], $_SESSION['login_string'])) {
 

        $user_id = $_SESSION['user_id'];
        $email = $_SESSION['email'];
        $login_string = $_SESSION['login_string'];

 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT email,password,adminuser
                                      FROM User
                                      WHERE idUser = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($db_email, $db_password,$db_adminuser);
                $stmt->fetch();
                
                $login_check =  $db_password . $user_browser;
               
                 if(strcmp($login_check, $login_string) === 0) {


                    if (strcmp($db_adminuser,"Y") === 0){
                        //is admin
                        return 2;
                    }
                    else {


                    }

                    // Logged In!!!! 
                    //check if is admin or user; and return the correct 



                    return 1;
                } else {
                    // Not logged in 
                    return 0;
                }

                
            } else {
                // Not logged in 
                return -1;
            }
        } else {
            // Not logged in 
            return -2;
        }
    } else {
        // Not logged in 
        return -2;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}