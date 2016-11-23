
<html lang="en-us">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=2">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <title>Hair Salon</title>
    <link rel="stylesheet" href="css/Stylesheet.css">
    <link rel="stylesheet" href="css/Login.css">
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
                       <button id="LoginButton" onclick="document.getElementById('LoginForm').style.display='block'" style="width:auto;" >Login</button></a></li>
                   <li><a href="#"><span class="glyphicon glyphicon-registration-mark"></span>
                       <button id="RegisterButton" onclick="document.getElementById('id02').style.display='block'" style="width:auto;" >Register</button> </a></li>
               </ul>

           </div>


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
                            <img src="http://victoriassalon.com/wp-content/uploads/2015/02/rev.jpg" alt="HairSalon" width="100%" height="60%">
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





</body>
</html>

