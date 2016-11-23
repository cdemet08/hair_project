


<?php

include_once 'includes/register.inc.php';
include_once 'includes/functions.php';


?>

<html>
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="css/Login.css">
    <link rel="stylesheet" href="css/Register.css">


    <script type="text/JavaScript" src="js/forms.js"></script>

    <title>Register</title>
</head>
<body>

 <?php

    if (!empty($error_msg)) {
        echo $error_msg;
    }
    
?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a  class="navbar-brand" ><img style="display: inline-block; height: 30px; margin-top: -5px"   src="img/logo.jpg"> </a>
            <a id="Name">Hair Salon</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

                <li class="active">    <a href="index.php">Home  </a>      </li>
                <li>                  <a href="#">Page 1</a>      </li>
                <li>                  <a href="#">Page 2</a>      </li>
                <li>                  <a href="#">Page 3</a>      </li>

            </ul>



        </div>
    </div>
</nav>


<form class="container" id="RegisterContainer" method="post" name="registration_form" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>">

    <div class="row">
        <div class="col-sm-offset-1 col-sm-11">
            <div class="imgcontainer">
                <img src="img/avatar.png" alt="Avatar" class="avatar" >
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_email" class="control-label" for="register_email">Email:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Email" class="form-control" id="register_email">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_pass" class="control-label" for="register_pass">Password:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Password" class="form-control" id="register_pass">
        </div>
    </div>

        <div class="row">
            <div class="col-sm-2">
                <label id="lbl_Register_confirm" class="control-label" for="register_confirm">Confirm Password:</label>
            </div>
            <div class="col-sm-10">
                <input type="text" style="height: 8%" placeholder="Confirm Password" class="form-control" id="register_confirm">
            </div>
        </div>

    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_name" class="control-label" for="register_name">Name:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Name" class="form-control" id="register_name">
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_last" class="control-label" for="register_last">Last Name:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Last Name" class="form-control" id="register_last">
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_age" class="control-label" for="register_last">Age:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Age" class="form-control" id="register_age">
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_phone" class="control-label" for="register_phone">Phone:</label>
        </div>
        <div class="col-sm-10">
            <input type="text" style="height: 8%" placeholder="Phone" class="form-control" id="register_phone">
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <label id="lbl_Register_sex" class="control-label" >Sex:</label>
        </div>
        <div class="col-sm-10">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="sex_button">Select
                    <span class="caret"></span></button>
                <ul class="dropdown-menu" id="sex_menu">
                    <li ><a href="#" style="color: white;">Male</a></li>
                    <li ><a href="#" style="color: white;">Female</a></li>

                </ul>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <a href="#"><button id="regButton" type="submit" >Register</button></a>
        </div>
    </div>

</form>

</body>
</html>
