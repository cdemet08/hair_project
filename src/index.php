<?php


include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();


$avtive_barber = 4;

$date_today = date("Y-m-d");
$app_date = filter_input( INPUT_GET, 'appointment', FILTER_SANITIZE_URL );

if (empty($_GET)) {
    $app_date = $date_today ;
}




$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
if ($mysqli->connect_error) {
    header("Location: ../error.php?err=Unable to connect to MySQL");
    exit();
}



//TODO check functionn to do
function getFromDbBarber($mysqli){

    //create array to save the price 2d
    $array_barber_fun = array(array());


    $sqlBarber = "SELECT name,lastname,email,male,age,workThere FROM Barber ";

    $resultBarber = $mysqli->query($sqlBarber);



    $i = 1 ;

    if ($resultBarber->num_rows > 0) {
        // output data of each row
        while($row = $resultBarber->fetch_assoc()) {

            if (strcmp("Y", $row["workThere"]) === 0){
                $array_barber_fun[$i][0] = $row["name"];
                $array_barber_fun[$i][1] = $row["lastname"];
                $array_barber_fun[$i][2] = $row["email"];
                $array_barber_fun[$i][3] = $row["male"];
                $array_barber_fun[$i][4] = $row["age"];
                $array_barber_fun[$i][5] = $row["workThere"];

                $i = $i +1 ;
            }


        }
    }

    $array_barber_fun[0][0] = $i - 1 ;

    return $array_barber_fun ;


}

/**

return true if the user give the day before the today

 */
function checkBeforeToday($day_to_check,$today_check){


    $today_time = strtotime($today_check);
    $check_time = strtotime($day_to_check);

    if ($check_time <= $today_time) {
        return "true" ;
    }
    else {
        return "false" ;

    }

}



function getFromDbPrice($mysqli){

    //create array to save the price 2d
    $array_price_fun = array(array());


    $sqlPrice = "SELECT name, price ,description FROM Price";

    $resultPrice = $mysqli->query($sqlPrice);

    $array_price_fun[0][0] = $resultPrice->num_rows;

    $i = 1 ;

    if ($resultPrice->num_rows > 0) {
        // output data of each row
        while($row = $resultPrice->fetch_assoc()) {

            $array_price_fun[$i][0] = $row["name"];
            $array_price_fun[$i][1] = $row["price"];
            $array_price_fun[$i][2] = $row["description"];

            $i = $i +1 ;

        }
    }

    return $array_price_fun ;

}



// end function


$price_array = getFromDbPrice($mysqli);

$barber_array = getFromDbBarber($mysqli);
$barberNumberAll = $barber_array[0][0];
// create variable and array for all
$resultVar=0;

$dateOnly = "";



//TODO check ean ine kiriaki na min klini rantevou
$days = array('Sunday','Monday', 'Tuesday', 'Wednesday','Thursday','Friday','Saturday');

$dayWork  = date('l', strtotime($app_date));

$off_day = "false"; // true if the day is off - relax mode

if( (strcmp($dayWork,$days[0]) === 0 ) OR (strcmp($dayWork,$days[4]) === 0 )){

    $off_day = "true";
}





//true if is before the today
$today_day_con = checkBeforeToday($date_today,$app_date) ;


if (strcmp($today_day_con, "true" ) === 0) {  //show the appointment


    //ean ine off i imera mas
    if( strcmp($off_day, "false") === 0 ) {
        $sql = "SELECT a.idAppointment ,a.dateTimeApp , b.name AS barberName FROM `Appointment` AS a INNER JOIN `Barber` AS b ON a.idBarber = b.idBarber";

        $result = $mysqli->query($sql);

        $getResult = $result->num_rows;

        //create array 2d
        $appointmentArray = array(array());


        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

                $dateTimeTemp = $row["dateTimeApp"];

                $dateOnly = substr($dateTimeTemp, 0, 10);

                $hourOnly = substr($dateTimeTemp, 10, 15);



                //echo $app_date;

                //isounte
                if(strcmp( $dateOnly, $app_date) === 0) {


                    $appointmentArray[$resultVar][0] = $row["idAppointment"]; // id appointment
                    $appointmentArray[$resultVar][1] = $dateOnly;             // day only
                    $appointmentArray[$resultVar][2] = $hourOnly;              // hour only
                    $appointmentArray[$resultVar][3] = $row["barberName"];        // barber name


                    $resultVar = $resultVar + 1 ;

                    //echo "id: " . $row["idUser"]. " -: " . $row["idBarber"]. " " . $row["dateTimeApp"]. "<br>";

                }

            } // end read while result

        }


        //create the array to save the available appointment


        $startHourday = "09:00";
        $startHalfHourday = "09:30";
        $endHourday = "18:00";
        $halfHourday = "00:30";

        $startWork = strtotime($startHourday,0);
        $endWork = strtotime($endHourday,0);
        $halfHour = strtotime($halfHourday,0);
        $startHalfWork = strtotime($startHalfHourday,0);

        $timeSave = $startWork;
        $timeNextSave = $startHalfWork;

        $nextHalf = true;


        $stringHour ="09:00";
        $stringNextHour = "09:30";

        // $freeAppointment
        $freeAppointmentVar = 0 ;

        while($nextHalf == true){


            //if we have 4 appointment in same time dont show in the table
            $appointmentBarberSameTime = 0;




            $barber_free_array = array($barberNumberAll);

            //set all barber in this array to check next for busy barber
            for ($y = 1; $y <= $barberNumberAll; $y++){

                $barber_free_array[$y-1] = $barber_array[$y][0];

            }


            for($i = 0; $i < $resultVar; $i++){


                $appointmentTime = strtotime($appointmentArray[$i][2],0); //hour only


                //fwrite($myfile, $appointmentTime . " timeSave:" . $timeSave . " \n");


                //barber he have appointment
                if($appointmentTime >= $timeSave AND $appointmentTime < $timeNextSave){

                    $appointmentBarberSameTime = $appointmentBarberSameTime + 1;
                    //fwrite($myfile, "barber:" . $appointmentBarberSameTime . " \n ");

                    for ($y = 0; $y < $barberNumberAll; $y++){

                        if ( strcmp($barber_free_array[$y], $appointmentArray[$i][3])  === 0){
                            $barber_free_array[$y] = "";
                        }

                    }

                }

                if($appointmentBarberSameTime == $barberNumberAll  ){

                    break;
                }

            } //end for


            // free barber for appointment
            if($appointmentBarberSameTime < $barberNumberAll){

                $freeAppointmentArray[$freeAppointmentVar][0] = $stringHour;
                $freeAppointmentArray[$freeAppointmentVar][1] = $stringNextHour;

                $barberFree = "";

                $counFree = 0 ;
                for ($y = 0; $y < $barberNumberAll; $y++){
                    if(strcmp($barber_free_array[$y], "")  !== 0 ){

                        if($counFree == 0 ){
                            $barberFree .=  $barber_free_array[$y] ;
                        }else {
                            $barberFree .= "," . $barber_free_array[$y] ;

                        }

                        $counFree = $counFree + 1 ;
                    } //end if

                } // end for

                $freeAppointmentArray[$freeAppointmentVar][2] = $barberFree;

                $freeAppointmentVar = $freeAppointmentVar + 1 ;
            }
            else{ //DEBUG use only

                //dont show in the site
                //fwrite($myfile, "4 barber one time:" . $appointmentBarberSameTime . "time:". $stringHour . " \n ");


            }


            $timeSave = $timeNextSave;

            $tempTime = $timeNextSave + $halfHour;
            $hoursTemp = $tempTime / 3600;
            $minutesTemp = ($tempTime % 3600) / 60;

            $hoursTemp = substr($hoursTemp, 0, 2);

            //string
            $stringHour = $stringNextHour;
            $stringNextHour = $hoursTemp . ":" . $minutesTemp;

            if(strlen($stringNextHour) <= 4 ){
                $stringNextHour = $stringNextHour . "0" ;
            }

            $timeNextSaveTemp = $hoursTemp . ":" . $minutesTemp . ":00";

            $timeNextSave = strtotime($timeNextSaveTemp,0);


            //ean ine prin
            if($timeSave >= $endWork){

                $nextHalf = false;
                //break;
            }



        } // end while



    }   // end else we have work mode - day


} //end else to show the -> with the result of appointment






//fclose($myfile);

$mysqli->close();



?>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">

    <title>Hair Salon</title>

    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <script src="js/yahooCalendar.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
<script src="js/popUp.js"></script>
    <script src="js/forgot.js"></script>
    <link rel="stylesheet" href="css/user.css">
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
            <a href="HomePage.html" id="Name">Hair Salon</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

                <li ><a href="index.php"><font size="5">Home  </font></a>      </li>
                <li><a href="#service"><font size="5">Services</font></a>      </li>
                <li><a href="#offers"><font size="5">Offers</a></font>      </li>
                <li><a href="#book"><font size="5">Book Now</a></font>      </li>
                <li><a href="#contact"><font size="5">Contact Us</a> </font>     </li>

            </ul>

            <ul id="Login" class="nav navbar-nav navbar-right">
                <li ><a href="#"><span class="glyphicon glyphicon-log-in"></span>
                        <button id="LoginButton" onclick="document.getElementById('LoginForm').style.display='block'" type="button" data-toggle="modal" data-target="#gridSystemModal">Login</button></a></li>
                <li><a href="register.php"><span class="glyphicon glyphicon-registration-mark"></span>Register</a></li>
            </ul>
        </div>

    </div>

</nav>

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
                    <input type="text" name="email" style="height: 50px" placeholder="Email" class="form-control" id="usr">
                </div>

            </div>
            <div class="row">
                <div class="col-sm-2">
                    <label id="pass"class="control-label " for="password">Password:</label>
                </div>
                <div class="col-sm-10">
                    <input type="password" name="password" style="height: 50px" class="form-control" placeholder="Password" id="password">
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
                    <input type="text" style="height: 50px" placeholder="Email" class="form-control" id="forgot_email">
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




<div id="myCarousel" class="carousel slide" data-ride="carousel"    >

    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
        <li data-target="#myCarousel" data-slide-to="4"></li>
    </ol>


    <div class="carousel-inner" role="listbox" >

        <div class="item active" >
            <img src="img/hairSalon.jpg" alt="HairSalon"  style="width: 100%; height=300px" >
            <div class="carousel-caption">
                <h3>HairSalon</h3>
                <p>The atmosphere in Hair Salon has a touch of Relaxing </p>
            </div>
        </div>

        <div class="item" >
            <img src="img/carusel1.jpg" alt="HairSalon"  width="100%"; height="300px">
            <div class="carousel-caption">

            </div>
        </div>
        <div class="item" >
            <img src="img/carusel2.jpg" alt="HairSalon"  width="100%"; height="300px" >
            <div class="carousel-caption">

            </div>
        </div>


        <div class="item" >
            <img src="img/carusel4.jpg" alt="HairSalon"  width="100%"; height="300px" >
            <div class="carousel-caption">

            </div>
        </div>
        <div class="item" >
            <img src="img/carusel5.jpg" alt="HairSalon"  width="100%"; height="300px" >
            <div class="carousel-caption">

            </div>
        </div>
        <div class="item" >
            <img src="img/carusel6.jpg" alt="HairSalon"  width="100%"; height="300px"  >
            <div class="carousel-caption">

            </div>
        </div>

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




<div class="container-fluid" id="Second_Part" style="background-image: url(img/calendarBack.jpg)">

    <h1 class="headerstyle" style="margin-top: 20px"><a name="offers">Offers</a></h1> <br>


    <div id="OFFERS"  class="container-fluid" >


        <div class="row">
            <div class="col-sm-8" style="color: white; font-size:25px">



                <p><strong>Men's Hair Cut and Style</strong> only €5 until 5/12/2016</p>

                <p><strong> Women's Hair Style </strong> only €1 until 5/12/2016 </p>
                <h1 style="color: darkcyan" >Birthday Discount</h1>
                <p><strong>It's your special day! Come in any time within one week of your</br> birthday and we will give you 30% off one service of your choice!</strong></p>

            </div>
            <div class="col-sm-4" >

                <table>

                    <tr><td class="first">Monday</td> <td class="first">9:00-18:00</td></tr>
                    <tr><td class="first">Tuesday</td> <td class="first">9:00-18:00</td></tr>
                    <tr><td class="first">Wednesday</td> <td class="first">9:00-18:00</td></tr>
                    <tr><td class="first">Thursday</td> <td class="first">closed</td></tr>
                    <tr><td class="first">Friday</td> <td class="first">9:00-18:00 </td></tr>
                    <tr><td class="first">Saturday</td> <td class="first">9:00-18:00</td></tr>
                    <tr><td class="first">Sunday</td> <td class="first">open for special events</td></tr>
                    <tr><td class="first"></tr>
                </table
            </div>

        </div>
    </div>


    <h1 class="headerstyle"  ><a name="service">Services & Price List</h1> <br>
    <div id="servicesContainer"  class="container-fluid" >
        <div class="row">
            <div class="col-sm-4">
                <table>


                    <?php

                    $length_price =  $price_array[0][0];

                    for($i=1; $i <= $length_price; $i++) {

                        ?>
                        <tr><td class="first"> <?= $price_array[$i][2] ?> </td><td class="first"> €<?= $price_array[$i][1] ?></td></tr>


                        <?php
                    }

                    ?>

                </table>
            </div>

            <div  class="col-sm-2" >

                <img id="popey2" class="card-img-top" src="img/popeyCard.png" alt="Card image cap">


            </div>
            <div  class="col-sm-2" >

                <img id="popey2" class="card-img-top" src="img/scoobyCard.png" alt="Card image cap">


            </div>
            <div  class="col-sm-2" >

                <img id="popey2" class="card-img-top" src="img/smurfetteCard.png" alt="Card image cap">


            </div>
            <div  class="col-sm-2" >

                <img id="popey2" class="card-img-top" src="img/trumpCard.png" alt="Card image cap">


            </div>


        </div>
    </div>


    <h1 class="headerstyle"><a name="book">BOOK NOW</h1>
        <div class="Calendar" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <div  class="yui3-skin-sam yui3-g">

                        <div id="leftcolumn" class="yui3-u">
                            <div id="mycalendar"></div>
                        </div>




                        <script src="js/indexCalendar.js"></script>

                    </div>
                </div>

                <div id="date_hours" class="col-sm-2">
                    <!-- TODO show day and workday   -->
                    Date: <span id="selecteddate"></span>  <?= $dayWork ?> <?= $app_date ?> <br/>
                </div>

                <div class="col-sm-7">
                    <div id="cal" style="height:500px; overflow-x:scroll;overflow-x:auto; overflow-y:scroll;overflow-y:inherit;" >
                        <table class="table1" id="tableAppoint" cellpadding="5" cellspacing="2">
                            <thead>
                            <tr>
                                <th><br>#</th>
                                <th><br>Available <br>Barber/Hairdresser <br></th>
                                <th style="text-indent: 1em;">Hours<br></th>

                            </tr>
                            </thead>
                            <tbody>

                            <?php


                            if (strcmp($today_day_con, "true" ) === 0) {  //show the appointment


                                //ean ine off i imera mas
                                if( strcmp($off_day, "false") === 0 ) {
                                    for($i=0; $i < $freeAppointmentVar; $i++){

                                        $timeStart = $freeAppointmentArray[$i][0] ;
                                        $timeEnd = $freeAppointmentArray[$i][1] ;
                                        $barberFree = $freeAppointmentArray[$i][2] ;
                                        $barber_show_array = explode(",",$barberFree);

                                        $show_image_calendar = "";

                                        $show_image_calendar_array = array();
                                        $show_image_calendar_array_count = 0;

                                        foreach ($barber_show_array as $barber_str) {

                                            //trump image
                                            if(strcmp($barber_str, "Trump")  === 0) {
                                                $show_image_calendar =  '<img src="img/trump.png" style="width: 65px"></img>' ;
                                                $show_image_calendar_array[$show_image_calendar_array_count] = $show_image_calendar;
                                            }
                                            if(strcmp($barber_str, "Scooby Doo")  === 0) {
                                                $show_image_calendar = ' <img src="img/scooby.png" style="width: 50px"></img>';
                                                $show_image_calendar_array[$show_image_calendar_array_count] = $show_image_calendar;

                                            }
                                            if(strcmp($barber_str, "Popeye")  === 0) {

                                                $show_image_calendar = ' <img src="img/popey.png" style="width: 52px"> </img>';
                                                $show_image_calendar_array[$show_image_calendar_array_count] = $show_image_calendar;
                                            }
                                            if(strcmp($barber_str, "Smurfette")  === 0) {
                                                $show_image_calendar = ' <img src="img/smurfette.gif" style="width: 47px"> </img>';
                                                $show_image_calendar_array[$show_image_calendar_array_count] = $show_image_calendar;
                                            }

                                            $show_image_calendar_array_count = $show_image_calendar_array_count + 1 ;


                                        } //end for

                                        $idButton = "bookButton_" . $i ;
                                        $idTableRow = "tableRow_" . $i ;
                                        ?>

                                        <tr id=<?= $idTableRow ?> >
                                            <td> <?= $i +1  ?></td>
                                            <td>

                                                <?php
                                                //print the image for that they dont have appointment
                                                foreach ($show_image_calendar_array as $show_image) {
                                                    //print image
                                                    print $show_image;
                                                }

                                                ?>

                                            </td>
                                            <td> <?= $timeStart . "-" . $timeEnd ; ?>  </td>
                                            <td>
                                                <button  class="popup" onclick="myPopUpFun<?= $idButton ?>()" >Submit
                                                    <span class="popuptext" id="myPopup<?= $idButton ?>">Login First</span>

                                                <script type='text/javascript'>
                                                    function myPopUpFun<?= $idButton ?>() {
                                                        var popup = document.getElementById('myPopup<?= $idButton ?>');
                                                        popup.classList.toggle('show');
                                                    }
                                                </script>
                                                </button>
                                            </td>
                                        </tr>


                                        <?php //end for
                                    } // end for

                                    if( $freeAppointmentVar == 0 ) {
                                        ?>  
                                            <tr>
                                                <td>  </td>                          
                                                <td> Sorry.We are full in this day. </td>
                                            </tr>

                                        <?php
                                    }

                                } //end first if to check if is off day
                                else {

                                ?>  
                                    <tr>
                                        <td>  </td>                          
                                        <td> Sorry.In this day we don't work. </td>
                                    </tr>
                                <?php 

                                }

                            } //end sec if to check if is before the day from today
                            else {

                                ?>  
                                    <tr>
                                        <td>  </td> 
                                        <td> Sorry,but you can't close appointment before today!! </td>
                                    </tr>
                                <?php 

                            }
                            ?>

                            </tbody>
                        </table>
                    </div>
                </div>



                <div id="close_appointment" class="modal fade" role="dialog">

                    <div class="modal-dialog">
                        <!-- Modal content-->

                        <form id="appointment_form"  name="appointment_form_name">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title"> Appointment: </h4>
                                </div>

                                <div class="modal-body">

                                    <div class="row" id="timeShowjs">
                                        <div class="col-sm-2">
                                            <label id="lbl_time_to_close" class="control-label" > Time:</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label id="lbl_show_the_time" class="control-label" > : </label>
                                        </div>

                                    </div>


                                    <div class="row" id="img_show" >
                                        <label id="label_img_popey"  >
                                            <input type="radio" name="fb" value="small" />
                                            <img src="img/popey.png" style="width: 52px"> </img>';
                                        </label>
                                        <label id="label_img_trump">
                                            <input type="radio" name="fb" value="small" />
                                            <img src="img/trump.png" style="width: 65px"></img>
                                        </label>
                                        <label id="label_img_scooby">
                                            <input type="radio" name="fb" value="small" />
                                            <img src="img/scooby.png" style="width: 50px"></img>
                                        </label>
                                        <label id="label_img_smurfette">
                                            <input type="radio" name="fb" value="small" />
                                            <img src="img/smurfette.gif" style="width: 47px"> </img>
                                        </label>

                                    </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label id="lbl_Register_sex" class="control-label" > Cut:</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="form-control" name="id_barber_select" id="id_barber_select" style="width: 220px">
                                                <?php

                                                $price_length_array = $price_array[0][0];

                                                for($i_price=1; $i_price<= $price_length_array; $i_price++){
                                                    ?>

                                                    <option value= <?= $price_array[$i_price][0] ?> > <?= $price_array[$i_price][2] ?> </option>

                                                    <?php
                                                }

                                                ?>


                                            </select>
                                        </div>

                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="" class="btn btn-default" data-dismiss="modal" style="margin-right: 50%" >Submit</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>

            </div>

        </div>
    </div>





    <h1 class="headerstyle" ><a name="contact">Contact Us</h1>
    <div class="container-fluid" id="Contacts">
        <div class="row">
            <div class="col-sm-9">
                <div  id="gmap_canvas">
                    <a href='https://embedmaps.net'>www.embedmaps.net</a></div>
            </div>
            <div class="col-sm-3" style="color:white;">
                <span  ><strong>Phone: 22 525031 <br></strong></span>
                <span  ><strong>Email: info@unihome.com <br> </strong></span>
                <span  ><strong>Address: 18 Markou Drakou Strovolos, Nicosia, 2020<br></strong></span>
                <span  ><strong>Facebook: https://www.facebook.com/giannakis.giannaki <br></strong></span>
                <span  ><strong>Twitter: https://twitter.com/TheJohn <br></strong></span>

            </div>

        </div>

    </div>


    <script src='https://maps.googleapis.com/maps/api/js?v=3.exp&key= AIzaSyArBTKEJX7IoqSZyx4Y71TNl1SWMtI3Nzg '></script>
    <script type='text/javascript' src='https://embedmaps.com/google-maps-authorization/script.js?id=d866bb1ff7eb3fc6d8329da9b3773027f9a04807'></script>
    <script type='text/javascript'>function init_map(){var myOptions = {
            zoom:15,center:new google.maps.LatLng(35.1463067,33.40800360000003),
            mapTypeId: google.maps.MapTypeId.ROADMAP};
            map = new google.maps.Map(document.getElementById('gmap_canvas'), myOptions);
            marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(35.1463067,33.40800360000003)});
            infowindow = new google.maps.InfoWindow({content:'University of Cyprus<br>1 Panepistimiou Avenue Aglantzia,<br>2109 Nicosia<br>'});
            google.maps.event.addListener(marker, 'click', function(){infowindow.open(map,marker);});infowindow.open(map,marker);}
        google.maps.event.addDomListener(window, 'load', init_map);</script>


</div>



<script type="text/javascript">
    /*

     */
    function take_id_app(clickedId)
    {

        var idClickButton = 0 ;
        //bookButton_
        if(clickedId.length >= 13 ){
            idClickButton = clickedId.substr(11, 13);
        }
        else {
            idClickButton = clickedId.substr(11, 12);
        }

        idClickButton = parseInt(idClickButton) + parseInt(1) ;

        var idTableRowScript = "tableRow_" + idClickButton ;

        var idTableScript = document.getElementById("tableAppoint");


        //<img src="img/scooby.png" style="width: 50px"></img>';

        var rowIndexTable = document.getElementById(idTableRowScript);


        //document.getElementById(idTableRowScript).cells.length;

        //document.getElementById(idTableRowScript).rowIndex;

        var cellData = new Array(3);

        cellData[0] = idTableScript.rows[idClickButton].cells[0].innerText; // id
        cellData[1] = idTableScript.rows[idClickButton].cells[1].innerHTML; // image
        cellData[2] = idTableScript.rows[idClickButton].cells[2].innerText; // time


        var setValue = new Array(4);
        var findFree = 0 ;



        document.getElementById('label_img_smurfette').style.display='none';
        document.getElementById('label_img_popey').style.display='none';
        document.getElementById('label_img_trump').style.display='none';
        document.getElementById('label_img_scooby').style.display='none';


        if(cellData[1].includes("scooby")){
            setValue[findFree] = "Scooby" ;
            document.getElementById('label_img_scooby').style.display='initial';
            findFree = parseInt(findFree) + parseInt(1) ;
        }
        if(cellData[1].includes("trump")){
            setValue[findFree] = "Trump" ;
            document.getElementById('label_img_trump').style.display='initial';
            findFree = parseInt(findFree) + parseInt(1) ;
        }
        if(cellData[1].includes("popey")){
            setValue[findFree] = "Popey" ;
            document.getElementById('label_img_popey').style.display='initial';
            findFree = parseInt(findFree) + parseInt(1) ;
        }
        if(cellData[1].includes("smurfette")){
            setValue[findFree] = "Smurfette" ;
            document.getElementById('label_img_smurfette').style.display='initial';
            findFree = parseInt(findFree) + parseInt(1) ;
        }



        var select_option_barber = document.getElementById("id_barber_select");
        var form_appointment = document.getElementById("appointment_form");




        document.getElementById('lbl_show_the_time').innerHTML = cellData[2];

    }
</script>



</body>
</html>
