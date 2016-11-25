

<?php


include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();


if (login_check($mysqli) == 2) {
  
       header('Location: ../admin.php');

} 
else if (login_check($mysqli) <= 0 ){
   
    header('Location: ../error.php?error=not_authorized_page');
}



?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=2">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>



    <link rel="stylesheet" href="css/Stylesheet.css">
    <link rel="stylesheet" href="css/Login.css">
    <link rel="stylesheet" href="css/user.css">

    
</head>

<body>

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
            <a href="HomePage.html" id="Name">Hair Salon</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

                <li>    <a href="HomePage.html">Home  </a>      </li>
                <li class="active">                  <a href="Services.html">Services</a>      </li>
                <li>                  <a href="#">Page 2</a>      </li>
                <li>                  <a href="#">Page 3</a>      </li>

            </ul>

            <ul id="Logout" class="nav navbar-nav navbar-right">
  
                <li> <a href="includes/logout_function.php">Logout</a></li>
               
                  
               
            </ul>

        </div>

        <!--

        <div id="LoginForm" class="modal">

            <div class="modal-content animate" action="action_page.php">

                <div class="imgcontainer">
                    <span onclick="document.getElementById('LoginForm').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <img src="img/avatar.png" alt="Avatar" class="avatar">

                </div>


                <div class="form-group">
                    <label id="email" class="control-label col-sm-2" for="usr">Email:</label>
                    <div class="col-sm-10">
                        <input type="text" style="height: 3%" placeholder="Enter your Email" class="form-control" id="usr">
                    </div>
                </div>

                <div class="form-group">
                    <label id="pass"class="control-label col-sm-2" for="pwd">Password:</label>
                    <div class="col-sm-10">
                        <input type="password"  style="height: 3%" class="form-control" placeholder="Enter your Password"id="pwd">
                    </div>
                </div>
                <div class="row">

                    <a href="#"><input  id="remember" type="checkbox" checked="checked"> Remember me</a>
                    <a href="#"><button id="submitButton" type="submit" >Login</button></a>

                </div>


            </div>

        </div>

        -->
    </div>
</nav>



    <div class="transbox">

        <img src="http://www.sleekhairsalon.com/sleek_hair_salon_lady.jpg" alt="lady" style="margin:50px 0px; padding:10px; width:450px;height:350px;" align="right";>
        <div class="letters" >




            <table>
                <h1>Services & Price List</h1> <br>



                <tr><td class="first">Wash and Blow Dry</td><td class="first">€5</td></tr>
                <tr><td class="first">Men's Shave</td><td class="first">€20</td></tr>
                <tr><td class="first">Men's Hair Cut and Style</td><td class="first">€15</td></tr>
                <tr><td class="first">Women's Hair Cut</td><td class="first">€15</td></tr>
                <tr><td class="first">Women's Hair Style</td><td class="first">€20</td></tr>
                <tr><td class="first">Men's hair coloring</td><td class="first">€20</td></tr>
                <tr><td class="first">Women's hair coloring</td><td class="first">€30</td></tr>
                <tr><td class="first">Highlights</td><td class="first">starting from €40</td></tr>
                <tr><td class="first">Permanent Wave</td><td class="first">€50</td></tr>
                <tr><td class="first">Brazilian Keratin Treatment</td><td class="first">€40</td></tr>
            </table>


        </div>
    </div>

</body>
</html>
