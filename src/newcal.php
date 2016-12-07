

<html lang="en-us">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="js/forgotJavaScript.js"></script>
    <script src="js/yahooCalendar.js"></script>

    <title>Hair Salon</title>
    <link rel="stylesheet" href="css/Stylesheet.css">
    <link rel="stylesheet" href="css/Login.css">
    <link rel="stylesheet" href="css/Calendar.css">

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
            <a id="Name">Hair Salon</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

                <li class="active">    <a href="#">Home  </a>      </li>
                <li>                  <a href="#">Page 1</a>      </li>
                <li>                  <a href="#">Page 2</a>      </li>
                <li>                  <a href="#">Page 3</a>      </li>

            </ul>

            <ul id="Login" class="nav navbar-nav navbar-right">
                <li ><a href="#"><span class="glyphicon glyphicon-log-in"></span>
                    <button id="LoginButton"onclick="document.getElementById('LoginForm').style.display='block'" type="button" data-toggle="modal" data-target="#gridSystemModal">Login</button></a></li>
                <li><a href="register.php"><span class="glyphicon glyphicon-registration-mark"></span>Register</a></li>
            </ul>

        </div>



        <form id="LoginForm"  class="modal" action="includes/login_function.php" method="post" name="login_form">

            <div class="modal-content" action="action_page.php">
                <div class="modal-header" >
                    <div class="col-sm-offset-4 col-sm-8">
                        <span onclick="document.getElementById('LoginForm').style.display='none'" class="close" title="Close Modal">&times;</span>
                        <img src="img/avatar.png" alt="Avatar" class="avatar" >

                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <label id="email" class="control-label" for="usr">Email:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" name="email" style="height: 5%" placeholder="Email" class="form-control" id="usr">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label id="pass"class="control-label " for="password">Password:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="password" name="password" style="height: 5%" class="form-control" placeholder="Password" id="password">
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a href="#"><input  id="remember" type="checkbox" checked="checked"> Remember me</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5">
                            <a href="#" onclick="myaction()" type="button" data-toggle="modal" data-target="#gridSystemModal">
                                Forgot Password?</a>
                        </div>
                        <div class="col-sm-7">

                            <input type="button" value="Login" onclick="formhash(this.form, this.form.password);" />



                        </div>
                    </div>
                </div>

            </div><!--modal content -->

        </form>

        <form id="ForgotForm" class="modal">
            <div class="modal-content" action="action_page.php">
                <div class="modal-header" >
                    <div class="col-sm-offset-4 col-sm-8">
                        <span onclick="document.getElementById('ForgotForm').style.display='none'" class="close" title="Close Modal">&times;</span>
                        <img src="img/avatar.png" alt="Avatar" class="avatar" >
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <label id="lbl_forgot_email" class="control-label" for="usr">Email:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" style="height: 2%" placeholder="Email" class="form-control" id="forgot_email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-7 col-sm-offset-5" >
                            <a href="#"><button id="resetButton"  type="submit" >Reset</button></a>
                        </div>
                    </div>

                </div>
            </div>
        </form>



    </div>
</nav>




<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox" >

        <div class="item active" >
            <img src="img/hairSalon.jpg" alt="HairSalon"  >
            <div class="carousel-caption">
                <h3>HairSalon</h3>
                <p>The atmosphere in Hair Salon has a touch of Relaxing </p>
            </div>
        </div>



        <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>


</div>

<div id="bookBar" style="background-image: url(img/calendarBack.jpg)">
<p id="bookNow">BOOK NOW</p>

</div>


<div class="Calendar" style="background-image:  url(img/calendarBack.jpg) ">



    <div  class="yui3-skin-sam yui3-g">

            <div id="leftcolumn" class="yui3-u">
                <div id="mycalendar"></div>
            </div>



        <div id="rightcolumn" class="yui3-u">
            <div class="row">
                <div id="date_hours" class="col-sm-2">
                        Date: <span id="selecteddate"></span>
                        Available Hours:
                </div>
                <div class="col-sm-10">
                    <div class="table-responsive">
                        <table id="table1" class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Barber/Hairdresser</th>
                                <th>Hours</th>
                                <th>For</th>
                                <th>Choose</th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr class="Chris">
                                <td>1</td>
                                <td>Christopher</td>
                                <td>13:00-14:00<br>14:00-15:00</td>
                                <td>Ktenisma<br>dyed hair</td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div><button id="bookButton"type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Sumbit</button></div>
                    <div id="myModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h4 class="modal-title">Rantevou: </h4>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>




    </div>

    <script src="js/Calendar.js"></script>

</div>









</body>

</html>
