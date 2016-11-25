<?php



$error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_URL );

?>





<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">

<?php 

	if (strcmp("login_failure", $error) === 0 ){

?>
		<title>Login failure.</title>

<?php 
	}
	else if (strcmp("not_authorized_page", $error) === 0 ){

?>
		<title>Not authorized page.</title>


<?php 

	}
	else {
?>
		<title>Page Not Found</title>
<?php 
	}

?>


<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/error.css">




    
</head>


<body>


<?php 

	if (strcmp("login_failure", $error) === 0 ){

?>
		<title>Login failure.</title>

<?php 
	}
	else if (strcmp("not_authorized_page", $error) === 0 ){

?>
	
		<h1>Page not authorized</h1>
		<p>Sorry, but you are not authorized to access this page.Please go to <a href="/index.php"> index.php </a> to login. </p>
		<p>Or wait 5 sec and you go there.</p>



<?php 

	}
	else {
?>

		<h1>Page Not Found</h1>
	    <p>Sorry, but the page you were trying to view does not exist.</p>


<?php 
	}

?>


    
    

	<script language="javascript" type="text/javascript">

		window.setTimeout('window.location="index.php"; ',5000);

	</script>

</body>
</html>
