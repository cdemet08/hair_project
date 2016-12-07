<?php


include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();


/*
if (login_check($mysqli) <= 0 ){
   
    header('Location: ../error.php?error=not_authorized_page');
}
*/


//fix 

$userID = 12 ; 
// $userID = getUserId($mysqli);

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




/**
 *  structure the array
    0 : day
    1 : hour 
    2 : idAppointment
    3 : name barber
    4 : type appointment

 */
$allAppointmentArray = array(array());
$allAppointmentLength = 0;

//true if is before the today
$today_day_con = checkBeforeToday($date_today,$app_date) ;


if (strcmp($today_day_con, "true" ) === 0) {  //show the appointment


    //ean ine off i imera mas
    if( strcmp($off_day, "false") === 0 ) {
        
        $sql = "SELECT a.idAppointment ,a.dateTimeApp , a.typeAppointment, b.name AS barberName FROM `Appointment` AS a INNER JOIN `Barber` AS b ON a.idBarber = b.idBarber";

        $result = $mysqli->query($sql);

        $getResult = $result->num_rows;
        $allAppointmentLength = $result->num_rows;

        //create array 2d
        $appointmentArray = array(array());



        if ($result->num_rows > 0) {
            // output data of each row

            $getAllAppointment = 0;
            while($row = $result->fetch_assoc()) {

                $dateTimeTemp = $row["dateTimeApp"];

                $dateOnly = substr($dateTimeTemp, 0, 10);

                $hourOnly = substr($dateTimeTemp, 10, 15);

                $allAppointmentArray[$getAllAppointment][0] = $dateOnly;
                $allAppointmentArray[$getAllAppointment][1] = $hourOnly;
                $allAppointmentArray[$getAllAppointment][2] = $row["idAppointment"];
                $allAppointmentArray[$getAllAppointment][3] = $row["barberName"];
                $allAppointmentArray[$getAllAppointment][4] = $row["typeAppointment"];
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

                $getAllAppointment = $getAllAppointment + 1 ;
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


// $app_date 


$days = array('Monday', 'Tuesday', 'Wednesday','Thursday','Friday','Saturday','Sunday');

//$dayWork  = date('l', strtotime($app_date));

/*
    Structure for type
    all type                              0
wash_and_dry   Unisex Wash and Blow Dry | 1
men_shave      Men's Shave              | 2
men_cut        Men's Hair Cut and Style | 3
women_cut      Women's Hair Cut         | 4
women_style   Women's Hair Style       |  5
women_color   Women's hair coloring    |  6
close admin time                          7

*/
$dayAppointmentType = array(0,0,0,0,0,0,0,0);
$monthAppointmentType = array(0,0,0,0,0,0,0,0);
$weekAppointmentType = array(0,0,0,0,0,0,0,0);
$allAppointmentType = array(0,0,0,0,0,0,0,0);



// the 4 barber statistic
/*
    Scooby Doo
    Trump
    Smurfette
    Popeye
*/
$allAppointmentBarber = array(0,0,0,0);
$monthAppointmentBarber=array(
    0 =>array(0,0,0,0),
    1=>array(0,0,0,0),
    2=>array(0,0,0,0),
    3=>array(0,0,0,0),
    4=>array(0,0,0,0),
    5=>array(0,0,0,0),
    6=>array(0,0,0,0),
    7=>array(0,0,0,0,),
    8=>array(0,0,0,0),
    9=>array(0,0,0,0),
    10=>array(0,0,0,0),
    11=>array(0,0,0,0),
    12=>array(0,0,0,0));


/**
    8esi 0 oloi sinolika
    Scooby Doo
    Trump
    Smurfette
    Popeye
*/
$dayAppointmentBarber = array(0,0,0,0,0);



//fix this
for ($i=0; $i < $allAppointmentLength; $i++){

    $day_show = $allAppointmentArray[$i][0] ; // $dateOnly;
    $hour_show = $allAppointmentArray[$i][1] ; // $hourOnly;
    $allAppointmentArray[$i][2]; // $row["idAppointment"];
    $barber_name_show = $allAppointmentArray[$i][3] ;// $row["barberName"];
    $type_show = $allAppointmentArray[$i][4] ;// $row["typeAppointment"];

    $allAppointmentType[0] = $allAppointmentType[0] +1;

    if(strcmp("wash_and_dry",$type_show) === 0 ){
        $allAppointmentType[1] = $allAppointmentType[1] +1;

    }
    else if(strcmp("men_shave",$type_show) === 0 ){
        $allAppointmentType[2] = $allAppointmentType[2] +1;

    }
    else if(strcmp("men_cut",$type_show) === 0 ){
        $allAppointmentType[3] = $allAppointmentType[3] +1;

    }
    else if(strcmp("women_cut",$type_show) === 0 ){
        $allAppointmentType[4] = $allAppointmentType[4] +1;

    }
    else if(strcmp("women_style",$type_show) === 0 ){
        $allAppointmentType[5] = $allAppointmentType[5] +1;

    }
    else if(strcmp("women_color",$type_show) === 0 ){
        $allAppointmentType[6] = $allAppointmentType[6] +1;

    }
    else if(strcmp("close_admin",$type_show) === 0 ){
        $allAppointmentType[7] = $allAppointmentType[7] +1;

    }


    //barber
    if(strcmp("Scooby Doo",$barber_name_show) === 0 ){
        $allAppointmentBarber[0] = $allAppointmentBarber[0] +1;

    }
    else if(strcmp("Trump",$barber_name_show) === 0 ){
        $allAppointmentBarber[1] = $allAppointmentBarber[1] +1;

    }
    else if(strcmp("Smurfette",$barber_name_show) === 0 ){
        $allAppointmentBarber[2] = $allAppointmentBarber[2] +1;

    }
    else if(strcmp("Popeye",$barber_name_show) === 0 ){
        $allAppointmentBarber[3] = $allAppointmentBarber[3] +1;

    }


    //this day only
    if(strcmp($day_show, $app_date) === 0 ){


        //barber only for today
        if(strcmp("Scooby Doo",$barber_name_show) === 0 ){
            $dayAppointmentBarber[1] = $dayAppointmentBarber[1] +1;
            echo  $dayAppointmentBarber[1];
        }
        else if(strcmp("Trump",$barber_name_show) === 0 ){
            $dayAppointmentBarber[2] = $dayAppointmentBarber[2] +1;

        }
        else if(strcmp("Smurfette",$barber_name_show) === 0 ){
            $dayAppointmentBarber[3] = $dayAppointmentBarber[3] +1;
            echo  $dayAppointmentBarber[3];
        }
        else if(strcmp("Popeye",$barber_name_show) === 0 ){
            $dayAppointmentBarber[4] = $dayAppointmentBarber[4] +1;
            echo  $dayAppointmentBarber[4];
        }

        $dayAppointmentBarber[0] = $dayAppointmentBarber[0] + 1;


        if(strcmp("wash_and_dry",$type_show) === 0 ){
            $dayAppointmentType[1] = $dayAppointmentType[1] +1;

        }
        else if(strcmp("men_shave",$type_show) === 0 ){
            $dayAppointmentType[2] = $dayAppointmentType[2] +1;

        }
        else if(strcmp("men_cut",$type_show) === 0 ){
            $dayAppointmentType[3] = $dayAppointmentType[3] +1;

        }
        else if(strcmp("women_cut",$type_show) === 0 ){
            $dayAppointmentType[4] = $dayAppointmentType[4] +1;

        }
        else if(strcmp("women_style",$type_show) === 0 ){
            $dayAppointmentType[5] = $dayAppointmentType[5] +1;

        }
        else if(strcmp("women_color",$type_show) === 0 ){
            $dayAppointmentType[6] = $dayAppointmentType[6] +1;

        }
        else if(strcmp("close_admin",$type_show) === 0 ){
            $dayAppointmentType[7] = $dayAppointmentType[7] +1;

        }

        $dayAppointmentType[0] = $dayAppointmentType[0] + 1;



    }



}   //end form








//fclose($myfile);

$mysqli->close();



?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="js/yahooCalendar.js"></script>
    <script src="js/charts.js"></script>

    <link rel="stylesheet" href="css/user.css">


   <link rel="stylesheet" href="css/admin.css">


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

                <li ><a href="admin.php"><font size="5">Home  </font></a>      </li>
                <li><a href="#service"><font  size="5">Services</font></a>      </li>
                <li><a href="#offers"><font   size="5">Offers</a></font>      </li>
                <li><a href="#book"><font     size="5">Book Now</a></font>      </li>
                <li><a href="#charts"><font     size="5">Charts</a></font>      </li>

            </ul>

            <ul id="Logout" class="nav navbar-nav navbar-right">
  

                <li  class="dropdown" >
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" >
                        <span class="glyphicon glyphicon-user"></span> My Account</button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                        <button    class="btn btn-sm btn-info" id="settingsButton" onclick="document.getElementById('accountSettings').style.display='block'" type="text" data-toggle="modal" data-target="#gridSystemModal">Account Settings</button></li>


                    </ul>

                </li>
                <li> <a href="includes/logout_function.php">Logout</a></li>
            </ul>

        </div>

       
    </div>
</nav>

<form id="accountSettings"  class="modal">

    <div class="modal-content" action="action_page.php">
        <div class="modal-header" >
            <div class="col-sm-offset-4 col-sm-8">
                <span onclick="document.getElementById('accountSettings').style.display='none'" class="close" title="Close Modal">&times;</span>
                <img src="img/avatar.png" alt="Avatar" class="avatar" >
            </div>
        </div>

        <div class="modal-body" style="padding-top: 50px">
            <div class="row">
                <div class="col-sm-2">
                    <label id="email" class="control-label" for="usr">Current Email:</label>
                </div>
                <div class="col-sm-8">
                    <input type="text" name="email" style="height: 50px" placeholder="Email" class="form-control" id="usr">
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2">
                    <label id="email" class="control-label" for="usr">New Email:</label>
                </div>
                <div class="col-sm-8">
                    <input type="text" name="email" style="height: 50px" placeholder="Email" class="form-control" id="usr">
                </div>

            </div>
            <div class="row">
                <div  class="col-sm-12"></div>
            </div>
                <div class="row">

                <div class="col-sm-4 col-sm-offset-3">
                    <button id="newPassword" type="button">Change Email</button>
                </div>
             </div>


        </div>
    </div>

</form>


<div class="container-fluid" id="Second_Part" style="background-image: url(img/calendarBack.jpg); padding-top: 30px">




    <h1 class="headerstyle"><a name="book">BOOK NOW</h1>
        <div class="Calendar" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <div  class="yui3-skin-sam yui3-g">

                        <div id="leftcolumn" class="yui3-u">
                            <div id="mycalendar"></div>
                        </div>




                        <script src="js/adminCalendar.js"></script>

                    </div>
                </div>

                <div id="date_hours" class="col-sm-2">
                    <!-- TODO show day and workday   -->
                    Date: <span id="selecteddate"></span>  <?= $dayWork ?> <?= $app_date ?>

                </div>

                <div class="col-sm-7">
                    <div id="cal" style="height:500px; overflow-x:scroll;overflow-x:auto; overflow-y:scroll; " >
                        <table class="table1" id="tableAppoint" cellpadding="5" cellspacing="2">
                            <thead>
                            <tr>
                                <th><br>#</th>
                                <th><br>Available <br>Barber/Hairdresser <br></th>
                                <th style="text-indent: 1em;">Hours<br></th>
                                <th style="text-indent: 1em;"> Choose</th>

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
                                                $show_image_calendar =  '<img src="img/trump.png" style="width: 65px">' ;
                                                $show_image_calendar_array[$show_image_calendar_array_count] = $show_image_calendar;
                                            }
                                            if(strcmp($barber_str, "Scooby Doo")  === 0) {
                                                $show_image_calendar = ' <img src="img/scooby.png" style="width: 50px">';
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

                                                <div id="button_div_app" >
                                                    <button id=<?= $idButton ?> type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#close_appointment" onClick="take_id_app(this.id);" style= "  background-color: inherit; font-size: 30px;  font-family: inherit; color: white; margin-left: 30%; margin-top: 1%; ">Submit</button>
                                                </div>
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

                         <form id="appointment_form"  action="includes/appointment_save.php" method="post" >
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
                                          <input type="radio" name="img_select" value="6" />
                                          <img src="img/popey.png" style="width: 52px">
                                        </label>
                                        <label id="label_img_trump">
                                          <input type="radio" name="img_select" value="8" />
                                          <img src="img/trump.png" style="width: 65px">
                                        </label>
                                        <label id="label_img_scooby">
                                          <input type="radio" name="img_select" value="2" />
                                          <img src="img/scooby.png" style="width: 50px">
                                        </label>
                                         <label id="label_img_smurfette">
                                          <input type="radio" name="img_select" value="7" />
                                          <img src="img/smurfette.gif" style="width: 47px">
                                        </label>
                                       
                                     </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label id="lbl_Register_sex" class="control-label" > Cut:</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="form-control" name="id_price_select" id="id_barber_select" style="width: 220px">
                                                
                                                <option value="close_time_admin">Close time</option>
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

                                <input type="hidden" name="userid" value=<?=$userID ?> >
                                <input type="hidden" id="time_to_close_id" name="time_to_close" value="" >
                                <input type="hidden" id="day_to_close_id" name="day_to_close" value=<?= $app_date ?> >
                                <input type="hidden" name="previous_page" value=<?= $_SERVER['REQUEST_URI']?> >



                                <div class="modal-footer">
                                    <input type="submit" name="submit_appointment" value="Submit" onclick="appointmenForUser(this.form)"; class="btn btn-default" data-dismiss="modal" style="margin-right: 30%" >
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Delete From List</button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>

            </div>

        </div>
    </div>


    <h1 class="headerstyle"><a name="offers">Offers</h1> <br>
    <div id="offers" class="container-fluid" >


        <div class="row">
            <div class="col-sm-5">



                <table>

                    <tr><td class="first">Men's Hair Cut and Style only €5 until 5/12/2016</td> </tr>
                    <tr><td class="first">Women's Hair Style only €10 until 5/12/2016</td> </tr>
                    <tr><td> <h2 style="color:white";> Birthday Discount </h2>
                            <p class="first"";>It's your special day! Come in any time within one week of your birthday and we will give you 30% off one service of your choice!
                            </p></td></tr>
                </table>

            </div>

            <div class="col-sm-7" >
                <table>
                    <tr><td class="first">Monday</td> <td class="first">9:00-18:00</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Tuesday</td> <td class="first">9:00-18:00</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Wednesday</td> <td class="first">9:00-18:00</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Thursday</td> <td class="first">closed</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Friday</td> <td class="first">9:00-18:00 </td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Saturday</td> <td class="first">9:00-18:00</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first">Sunday</td> <td class="first">open for special events</td><td class="first"><input type="text" size="5px"></td></tr>
                    <tr><td class="first"></tr>
                </table
            </div>


        </div>

        <div class="row">
            <div class="col-sm-3     col-sm-offset-7">
                <button  class="btn btn-sm btn-info" id="changeButton" type="button">Change Hours</button>
            </div>
        </div>

    </div>


    <h1 id="headerstyle"><a name="service"> Services & Price List</a></h1>
     <div id="servicesContainer1"  class="container-fluid" >
        <div class="row">
            <div class="col-sm-6">
                <table>

                    <?php

                    $length_price =  $price_array[0][0];

                    for($i=1; $i <= $length_price; $i++) {

                        ?>
                        <tr><td class="first"> <?= $price_array[$i][2] ?> </td><td class="first"> €<?= $price_array[$i][1] ?></td><td> <input type="text" name="price<?=$i ?>" size="4px"></td> </tr>


                        <?php
                    }

                    ?>




                </table>

            </div>




        </div>
         <div class="row">
             <div class="col-sm-5 col-sm-7">
                 <button   class="btn btn-sm btn-info" id="changeButton" type="button">Change Prices</button>
             </div>
         </div>

    </div>

    <h1 id="headerstyle"><a name="charts">Charts </a></h1>

        <div  class="container-fluid">
            <div class="row">

                <div class="col-sm-4">
                    <h2 style="color: white; padding-top: 15px">Appointments by Staff</h2>
                    <div id="chart1" style="padding-top: 15px" ></div>
                </div>

               <div id="monthChart" class="col-sm-4">


                 <h2 style="color: white">Appointments by Month by Staff</h2>
                   <h3 style="color: white">Select Year & Month:</h3>
                   <input type="text" id="year">
                   <select id="selectedMonth">
                       <option value="1">January</option>
                       <option value="2">February</option>
                       <option value="3">March</option>
                       <option value="4">April</option>
                       <option value="5">May</option>
                       <option value="6">June</option>
                       <option value="7">July</option>
                       <option value="8">August</option>
                       <option value="9">September</option>
                       <option value="10">October</option>
                       <option value="11">November</option>
                       <option value="12">December</option>
                   </select>
                 <button type="button" onclick="monthChart()" >Submit</button>

               </div>


                <div id="typeMonthChart" class="col-sm-4">


                    <h2 style="color: white">Appointments by Month by Service Type</h2>
                    <h3 style="color: white">Select Year & Month:</h3>
                    <input type="text" id="yearType">
                    <select id="selectedMonthType">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <button type="button" onclick="typeChart()" >Submit</button>

                </div>


            </div>


             <div class="row">

                     <div id="typeByYear"  class="col-sm-4">
                         <h2 style="color: white" >Appointments by Year by Service Type</h2>
                         <h3 style="color: white" > </br>Select Year:</h3>
                         <input type="text" id="yearForType">
                         <button type="button" onclick="yearTypeChart()" >Submit</button>

                </div>
                 <div id="BarberYearChart"  class="col-sm-4">
                     <h2 style="color: white" >Appointments by Year by Staff</h2>
                     <h3 style="color: white" > </br>Select Year:</h3>
                     <input type="text" id="yearForBarber">
                     <button type="button" onclick="yearBarberChart()" >Submit</button>

                 </div>

                 <div id="BarberType"  class="col-sm-4">
                     <h2 style="color: white" >Average Service Type by Staff</h2>
                     <h3 style="color: white" > </br>Select Barber:</h3>
                     <input type="text" id="barberName">
                     <button type="button" onclick="type_Barber()" >Submit</button>

                 </div>
        </div>
    </div>


<!--Script for Chart: All appointments by barbers  -->
<script type="text/javascript">

   // function allAppointmentsChart() {
        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages': ['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Topping');
            data.addColumn('number', 'Slices');
            data.addRows([
                ['Scoopy Doo', <?php echo $allAppointmentBarber[0]?>],
                ['Trump', <?php echo $allAppointmentBarber[1]?>],
                ['Smurfette', <?php echo $allAppointmentBarber[2]?>],
                ['Popey', <?php echo $allAppointmentBarber[3]?>],



            ]);


            // Set chart options
            var options = {
                'title': 'Average Appointments for every  Member of Staff',
                'width':400,
                'height': 300
            };

            // Instantiate and draw our chart, passing in some options.




            var chart = new google.visualization.PieChart(document.getElementById('chart1'));
            chart.draw(data, options);
        }
  //  }
</script>

<!--Script for Chart:  Month appointments by barbers  -->
<script type="text/javascript">


        function monthChart() {
            var jArray= <?php echo json_encode($monthAppointmentBarber ); ?>;
            var allAppointments=<?php echo json_encode($allAppointmentArray); ?>;
            var year  = document.getElementById('year').value;
            var month = parseInt(document.getElementById("selectedMonth").value);
            var i,str;
            var barber=[];

            barber[0]=0;
            barber[1]=0;
            barber[2]=0;
            barber[3]=0;

            for(i=0; i<allAppointments.length; i++){
                str=allAppointments[i][0].split("-");

                if(str[0] == year && month == parseInt(str[1])){
                    if(allAppointments[i][3] == "Scooby Doo"){
                        barber[0]=barber[0]+1;
                    }

                    if(allAppointments[i][3] == "Trump"){
                        barber[1]=barber[1]+1;
                    }
                    if(allAppointments[i][3] == "Smurfette"){
                        barber[2]=barber[2]+1;
                    }
                    if(allAppointments[i][3] == "Popeye"){
                        barber[3]=barber[3]+1;
                    }
                }
            }//end for







            // Load the Visualization API and the corechart package.
            google.charts.load('current', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {


                var name="January";

                switch (month){
                    case 1: name="January";
                        break;
                    case 2: name="February";
                        break;
                    case 3: name="March";
                        break;
                    case 4: name="April";
                        break;
                    case 5: name="May";
                        break;
                    case 6: name="June";
                        break;
                    case 7: name="July";
                        break;
                    case 8: name="August";
                        break;
                    case 9: name="September";
                        break;
                    case 10: name="October";
                        break;
                    case 11: name="November";
                        break;
                    case 12: name="December";
                        break;
                }

                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                    ['Scooby Doo', barber[0]],
                    ['Trump', barber[1]],
                    ['Smurfette', barber[2]],
                    ['Popeye',barber[3]]



                ]);


                // Set chart options
                var options = {
                    'title': 'Average Appointments for every  Member of Staff for the month '+name,
                    'width': 400,
                    'height': 300
                };

                // Instantiate and draw our chart, passing in some options.


                var idiv = document.createElement('DIV');

                idiv.id = 'chart_div';
                document.getElementById('monthChart').appendChild(idiv);

                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        }
    </script>



    <!-- type Chart by Month -->
<script type="text/javascript">

        function typeChart() {

            var allAppointments=<?php echo json_encode($allAppointmentArray); ?>;
            var year  = document.getElementById('yearType').value;
            var month = parseInt(document.getElementById("selectedMonthType").value);
            var i,str;
            var type=[];

            type[0]=0;
            type[1]=0;
            type[2]=0;
            type[3]=0;
            type[4]=0;
            type[5]=0;

            for(i=0; i<allAppointments.length; i++){
                str=allAppointments[i][0].split("-");

                if(str[0] == year && month == parseInt(str[1])){

                    if(allAppointments[i][4] == "wash_and_dry"){
                        type[0]=type[0]+1;
                    }
                    if(allAppointments[i][4] == "men_shave"){
                        type[1]=type[1]+1;
                    }
                    if(allAppointments[i][4] == "men_cut"){
                        type[2]=type[2]+1;
                    }
                    if(allAppointments[i][4] == "women_cut"){
                        type[3]=type[3]+1;
                    }
                    if(allAppointments[i][4] == "women_style"){
                        type[4]=type[4]+1;
                    }
                    if(allAppointments[i][4] == "women_color"){
                        type[5]=type[5]+1;
                    }
                }
            }//end for







            // Load the Visualization API and the corechart package.
            google.charts.load('current', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {


                var name="January";

                switch (month){
                    case 1: name="January";
                        break;
                    case 2: name="February";
                        break;
                    case 3: name="March";
                        break;
                    case 4: name="April";
                        break;
                    case 5: name="May";
                        break;
                    case 6: name="June";
                        break;
                    case 7: name="July";
                        break;
                    case 8: name="August";
                        break;
                    case 9: name="September";
                        break;
                    case 10: name="October";
                        break;
                    case 11: name="November";
                        break;
                    case 12: name="December";
                        break;
                }

                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                    ['Wash and dry', type[0]],
                    ['Men shave', type[1]],
                    ['Men cut', type[2]],
                    ['Women cut',type[3]],
                    ['Women style',type[4]],
                    ['Women color',type[5]]



                ]);


                // Set chart options
                var options = {
                    'title': 'Average Appointments for every  type of service for the month '+name,
                    'width': 400,
                    'height': 300
                };

                // Instantiate and draw our chart, passing in some options.


                var idiv = document.createElement('DIV');

                idiv.id = 'chart_div1';
                document.getElementById('typeMonthChart').appendChild(idiv);

                var chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
                chart.draw(data, options);
            }
        }
    </script>

 <!-- year Type Chart -->
<script type="text/javascript">


    function yearTypeChart() {

        var allAppointments=<?php echo json_encode($allAppointmentArray); ?>;
        var year  = document.getElementById('yearForType').value;
        var i,str;
        var type=[];

        type[0]=0;
        type[1]=0;
        type[2]=0;
        type[3]=0;
        type[4]=0;
        type[5]=0;

        for(i=0; i<allAppointments.length; i++){
            str=allAppointments[i][0].split("-");

            if(str[0] == year){

                if(allAppointments[i][4] == "wash_and_dry"){
                    type[0]=type[0]+1;
                }
                if(allAppointments[i][4] == "men_shave"){
                    type[1]=type[1]+1;
                }
                if(allAppointments[i][4] == "men_cut"){
                    type[2]=type[2]+1;
                }
                if(allAppointments[i][4] == "women_cut"){
                    type[3]=type[3]+1;
                }
                if(allAppointments[i][4] == "women_style"){
                    type[4]=type[4]+1;
                }
                if(allAppointments[i][4] == "women_color"){
                    type[5]=type[5]+1;
                }
            }
        }//end for



        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages': ['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {




            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Topping');
            data.addColumn('number', 'Slices');
            data.addRows([
                ['Wash and dry', type[0]],
                ['Men shave', type[1]],
                ['Men cut', type[2]],
                ['Women cut',type[3]],
                ['Women style',type[4]],
                ['Women color',type[5]]



            ]);


            // Set chart options
            var options = {
                'title': 'Average Appointments for every  type of service for the year '+year,
                'width': 400,
                'height': 300
            };

            // Instantiate and draw our chart, passing in some options.


            var idiv = document.createElement('DIV');

            idiv.id = 'chart_div2';
            document.getElementById('typeByYear').appendChild(idiv);

            var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
            chart.draw(data, options);
        }
    }




</script>

<!--Chart for Type By Barber -->
    <script type="text/javascript">
        function type_Barber(){
            var allAppointments=<?php echo json_encode($allAppointmentArray); ?>;
            var barber=document.getElementById('barberName').value;

            var i,str;
            var arr=[];

            arr[0]=0;
            arr[1]=0;
            arr[2]=0;
            arr[3]=0;
            for(i=0; i<allAppointments.length; i++) {


                if(allAppointments[i][3] == barber){
                    if(allAppointments[i][4] == "wash_and_dry"){
                        arr[0]=arr[0]+1;
                    }
                    if(allAppointments[i][4] == "men_shave"){
                        arr[1]=arr[1]+1;
                    }
                    if(allAppointments[i][4] == "men_cut"){
                        arr[2]=arr[2]+1;
                    }
                    if(allAppointments[i][4] == "women_cut"){
                        arr[3]=arr[3]+1;
                    }
                    if(allAppointments[i][4] == "women_style"){
                        arr[4]=arr[4]+1;
                    }
                    if(allAppointments[i][4] == "women_color"){
                        arr[5]=arr[5]+1;
                    }
                } //end if


            }//end for

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {




                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                    ['Wash and dry', arr[0]],
                    ['Men shave', arr[1]],
                    ['Men cut', arr[2]],
                    ['Women cut',arr[3]],
                    ['Women style',arr[4]],
                    ['Women color',arr[5]]



                ]);


                // Set chart options
                var options = {
                    'title': 'Average Type of service by member of Staff '+ barber  ,
                    'width': 400,
                    'height': 300
                };

                // Instantiate and draw our chart, passing in some options.


                var idiv = document.createElement('DIV');

                idiv.id = 'chartBarberType';
                document.getElementById('BarberType').appendChild(idiv);

                var chart = new google.visualization.PieChart(document.getElementById('chartBarberType'));
                chart.draw(data, options);
            }


        }
    </script>


<!--year appointments for  staff  -->
<script type="text/javascript">


        function yearBarberChart() {

            var allAppointments=<?php echo json_encode($allAppointmentArray); ?>;
            var year  = document.getElementById('yearForBarber').value;
            var i,str;
            var barber=[];

            barber[0]=0;
            barber[1]=0;
            barber[2]=0;
            barber[3]=0;

            for(i=0; i<allAppointments.length; i++){
                str=allAppointments[i][0].split("-");

                if(str[0] == year) {
                    if (allAppointments[i][3] == "Scooby Doo") {
                        barber[0] = barber[0] + 1;
                    }

                    if (allAppointments[i][3] == "Trump") {
                        barber[1] = barber[1] + 1;
                    }
                    if (allAppointments[i][3] == "Smurfette") {
                        barber[2] = barber[2] + 1;
                    }
                    if (allAppointments[i][3] == "Popeye") {
                        barber[3] = barber[3] + 1;
                    }

                }
            }//end for







            // Load the Visualization API and the corechart package.
            google.charts.load('current', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {




                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([
                    ['Scooby Doo', barber[0]],
                    ['Trump', barber[1]],
                    ['Smurfette', barber[2]],
                    ['Popeye',barber[3]]



                ]);


                // Set chart options
                var options = {
                    'title': 'Average Appointments for every  Barber for the year '+year,
                    'width': 400,
                    'height': 300
                };

                // Instantiate and draw our chart, passing in some options.


                var idiv = document.createElement('DIV');

                idiv.id = 'chart_div3';
                document.getElementById('BarberYearChart').appendChild(idiv);

                var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
                chart.draw(data, options);
            }
        }
    </script>


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


  document.getElementById('time_to_close_id').value = cellData[2];

  

  document.getElementById('lbl_show_the_time').innerHTML = cellData[2];

}


</script>
</div>



</body>
</html>
