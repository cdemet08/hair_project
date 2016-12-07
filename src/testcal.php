

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

// create variable and array for all
$resultVar=0;

$dateOnly = "";

//create array for appointment in one day 
//$appointmentArray;


//$sql = "SELECT idUser, idBarber,dateTimeApp FROM Appointment";

$sql = "SELECT a.idAppointment ,a.dateTimeApp , b.name AS barberName FROM `Appointment` AS a INNER JOIN `Barber` AS b ON a.idBarber = b.idBarber";

$result = $mysqli->query($sql);



if ($result->num_rows > 0) {
// output data of each row
    while($row = $result->fetch_assoc()) {

      $dateTimeTemp = $row["dateTimeApp"];

      $dateOnly = substr($dateTimeTemp, 0, 10);

      $hourOnly = substr($dateTimeTemp, 10, 18);


      
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
   

//TODO check ean ine kiriaki na min klini rantevou 
$dayWork  = date('l', strtotime($app_date));



//create the array to save the available appointment 

$barberNumberAll = 4;

$startHourday = "09:00:00 UTC";
$startHalfHourday = "09:30:00 UTC";
$endHourday = "18:00:00 UTC";
$halfHourday = "00:30:00 UTC";

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
  



  for($i = 0; $i < $resultVar; $i++){

    $appointmentTime = strtotime($appointmentArray[$i][2]); //hour only 

    if($appointmentTime >= $timeSave and $appointmentTime <= $timeNextSave){

      $appointmentBarberSameTime = $appointmentBarberSameTime + 1;



    }


  }


  // free barber for appointment 
  if($appointmentBarberSameTime < 4){
    $freeAppointmentArray[$freeAppointmentVar][0] = $stringHour;
    $freeAppointmentArray[$freeAppointmentVar][1] = $stringNextHour;

    $freeAppointmentVar = $freeAppointmentVar + 1 ;
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

  $timeNextSaveTemp = $hoursTemp . ":" . $minutesTemp . ":00 UTC";

  $timeNextSave = strtotime($timeNextSaveTemp,0);


  //ean ine prin
  if($timeSave >= $endWork){

    $nextHalf = false;
    //break; 
  }


}



$mysqli->close();



?>


<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>User</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <script src="js/forgotJavaScript.js"></script>
    <script src="js/yahooCalendar.js"></script>

    <script type="text/JavaScript" src="js/forms.js"></script> 


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

                <li><a href="HomePage.html">Home  </a>      </li>
                <li class="active">                  <a href="Services.html">Services</a>      </li>
                <li>                  <a href="#">Page 2</a>      </li>
                <li>                  <a href="#">Page 3</a>      </li>

            </ul>

            <ul id="Logout" class="nav navbar-nav navbar-right">
                <li> <a href="includes/logout_function.php">    <span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>

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


<div id="bookBar" style="background-image: url(img/calendarBack.jpg)">
    <p id="bookNow">BOOK NOW</p>

</div>


<div class="Calendar" style="background-image:  url(img/calendarBack.jpg) ">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">
                <div  class="yui3-skin-sam yui3-g">

                    <div id="leftcolumn" class="yui3-u">
                        <div id="mycalendar"></div>
                    </div>




                    <script src="js/Calendar.js"></script>

                </div>
            </div>

            <div id="date_hours" class="col-sm-2">
                 Date: <span id="selecteddate"></span><br> <?= $freeAppointmentVar ?>
                Available Hours:
            </div>

            <div class="col-sm-7">
                <div style="overflow-x:scroll;overflow-x:auto;">
                    <table class="table1">
                        <thead>
                        <tr>
                            <th><br>#</th>
                            <th>Barber/<br>Hairdresser</th>
                            <th><br>Hours</th>
                            <th><br>Choose</th>

                        </tr>
                        </thead>
                         <tbody>                 

                          <?php 

                          for($i=0; $i < $freeAppointmentVar; $i++){
                            $timeStart = $freeAppointmentArray[$i][0] ;
                            $timeEnd = $freeAppointmentArray[$i][1] ;
                          ?>

                          <tr>
                              <td> <?= $i +1  ?></td>
                              <td>Christopher,Trump,Andri,Kiriakos </td>
                              <td> <?= $timeStart . "-" . $timeEnd ?> </td>
                              <td><input type="checkbox" > </td>
                          </tr>
                          
                          
                          <?php //end for 
                          }
                          ?>

                        </tbody>
                    </table>
                </div>
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







<div id="servicesContainer"  class="container-fluid" style="background-image: url(img/calendarBack.jpg)">
                <table>
                <h1 style="color: white">Services & Price List</h1> <br>



                <tr><td class="first">Wash and Blow Dry</td><td class="first">€5</td></tr>
                <tr><td class="first">Men's Shave</td><td class="first">€20</td></tr>
                <tr><td class="first">Men's Hair Cut and Style</td><td class="first">€15</td></tr>
                <tr><td class="first">Women's Hair Cut</td><td class="first">€15</td></tr>
                <tr><td class="first">Women's Hair Style</td><td class="first">€20</td></tr>
                <tr><td class="first">Men's hair coloring</td><td class="first">€20</td></tr>
                <tr><td class="first">Women's hair coloring</td><td class="first">€30</td></tr>
                <tr><td class="first">Highlights</td><td class="first">€40</td></tr>
                <tr><td class="first">Permanent Wave</td><td class="first">€50</td></tr>
                <tr><td class="first">Brazilian Keratin Treatment</td><td class="first">€40</td></tr>
            </table>


        </div>




<div class="container-fluid" id="Contacts" style="background-image: url(img/calendarBack.jpg)">
    <div class="row">
        <div class="col-sm-3" id="gmap_canvas"> <a href='https://embedmaps.net'>www.embedmaps.net</a></div>

        <div class="col-sm-3">
            <a href="#"><img src="img/logo.jpg" width="280px" height="100px" style="margin-left : -14px; margin-top: 200px;"></a>
        </div>
        <div class="col-sm-6" style="color:white;margin-top: 200px;">
            <span style=" margin-left : -20px;"><strong>Phone: 22 525031 <br></strong></span>
            <span style=" margin-left : -20px;"><strong>Email: info@unihome.com <br> </strong></span>
            <span style=" margin-left : -20px;"><strong>Address: 18 Markou Drakou Strovolos, Nicosia, 2020<br></strong></span>
            <span style=" margin-left : -20px;"><strong>Facebook: https://www.facebook.com/giannakis.giannaki <br></strong></span>
            <span style=" margin-left : -20px;"><strong>Twitter: https://twitter.com/TheJohn <br></strong></span>

        </div>



        </div>

    </div>









<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&key= AIzaSyArBTKEJX7IoqSZyx4Y71TNl1SWMtI3Nzg '></script>
<script type='text/javascript' src='https://embedmaps.com/google-maps-authorization/script.js?id=d866bb1ff7eb3fc6d8329da9b3773027f9a04807'></script>
<script type='text/javascript'>function init_map(){var myOptions = {
        zoom:11,center:new google.maps.LatLng(35.1463067,33.40800360000003),
        mapTypeId: google.maps.MapTypeId.ROADMAP};
        map = new google.maps.Map(document.getElementById('gmap_canvas'), myOptions);
        marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(35.1463067,33.40800360000003)});
        infowindow = new google.maps.InfoWindow({content:'University of Cyprus<br>1 Panepistimiou Avenue Aglantzia,<br>2109 Nicosia<br>'});
        google.maps.event.addListener(marker, 'click', function(){infowindow.open(map,marker);});infowindow.open(map,marker);}
    google.maps.event.addDomListener(window, 'load', init_map);</script>





</body>
</html>
