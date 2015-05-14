<?php

/*
This process checks the entries added by the user to the register.php (included) when registering
It returns false errors (using the visual look of warnings of bootstrap framework),
else the registration is completed smoothly. The submitted data are recorded in the database and user can login.
*/

require_once ('init.php'); // database credentials & db connection function (as explained in register.php)
$dbc = db_connect();

trim($_POST['username']);
trim($_POST['password']);
trim($_POST['password1']);
trim($_POST['first_name']);
trim($_POST['last_name']);
trim($_POST['email']);
trim($_POST['city']);
trim($_POST['address']);
trim($_POST['phone']);


$error = ''; // Set error variable

if (empty ($_POST['username']) == true) { // Check if username field is empty, true -> error message
	$error .= 'No username selected!<br />';
} else {
	if (preg_match("/^[a-zA-Z0-9_-]*$/", $_POST['username']) == false) { // Check username, letters and numbers only, else -> error message
		$error .= 'Username must consist of letters and numbers only!<br />';
	}
}

if (empty ($_POST['password']) == true) { // Check if password is set, if not -> error message
	$error .= 'No Password selected!<br />';
} else {
	if(!preg_match("/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{6,50}$/", $_POST['password'])) { // Check password from 6 to 50 chars, else -> error message
		$error .= 'Your password must be 6 digits at least. It must consist of 1 letter and 1 number at least!<br />';
	}
}

if (empty ($_POST['password1']) == true) { // Check if password repeat is set, if not -> error message
	$error .= 'No password repeat is set!<br />';
} else {
	if (($_POST['password']) != ($_POST['password1'])) { // Check if password & password repeat are the same, if not -> error message
		$error .= 'Password is different than password repeat<br />';
	}
}

if (empty ($_POST['first_name']) == true) { // Check if first name is set, if not -> error message
	$error .= 'No first name is set!<br />';
} else {
	if (!preg_match("/^[a-zA-Z]*$/", $_POST['first_name'])) { // Check name, only letters, if not -> error message
		$error .= 'Your first name must consist of letters only!<br />';
	}
}

if (empty ($_POST['last_name']) == true) { // Check if surname is set, if not -> error message
	$error .= 'No surname is set!<br />';
} else {
	if (!preg_match("/^[a-zA-Z]*$/", $_POST['$last_name'])) { // Check surname, only letters, else -> error message
		$error .= 'Your surname must consist of letters only!<br />';
	}
}

if (empty ($_POST['email']) == true) { // Check if email is set, if not -> error message
	$error .= 'No email is set!<br />';
} else {
	if (!filter_var ($_POST['email'], FILTER_VALIDATE_EMAIL)) { // check if validate email, if not -> error message
		$error .= 'Email is of invalid form!<br />';
	}
}

if (empty ($_POST['city']) == true) { // check if city/town is set, if not error -> message
	$error .= 'You have not set your city/town of residence!<br />';
} else {
	if (!preg_match("/^[a-zA-Z ]*$/",$_POST['city'])) { // Check town/city, only letters and white spaces, else -> error message
		$error .= 'Your city/town name must consist of letters and white spaces only!<br />';
	}
}

if (!empty ($_POST['address'])) { // Check address if not empty then (address field is optional) ->
	if (!preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['address'])) { // check address type, only letters,numbers & spaces, else -> error message
		$error .= 'Your address must consist of numbers, letters and white spaces only!<br />';
	}
}

if (!empty ($_POST['phone'])) { // Check phone if not empty then (phone field is optional) ->
	if (!preg_match("/^[26][0-9]{9}$/", $_POST['phone'])) { // Check phone type, must start from 2 or 6 and be at least 10 numbers (greek), else -> error message
		$error .= 'Your phone number must start from 2 or 6 and be 10 digits long!<br />';
	}
}
	
	
if($error != '') { // Show error message of each case
	$_SESSION['error'] = $error;
	header ('Location: register.php');
	exit(0);
}


// SQL entry if everything ok
// Protection from SQL Injection. 
$username = mysqli_real_escape_string($dbc, $_POST['username']);
$password = mysqli_real_escape_string($dbc, $_POST['password']);
$first_name = mysqli_real_escape_string($dbc, $_POST['first_name']);
$last_name = mysqli_real_escape_string($dbc, $_POST['last_name']);
$email = mysqli_real_escape_string($dbc, $_POST['email']);
$city = mysqli_real_escape_string($dbc, $_POST['city']);
$address = mysqli_real_escape_string($dbc, $_POST['address']);
$phone = mysqli_real_escape_string($dbc, $_POST['phone']);

// Setting query
$query = "INSERT INTO user (username, password, first_name, last_name, email, phone, address, city, created)
				VALUES ('$username', '$password', '$first_name', '$last_name', '$email', '$phone', '$address', '$city', now())";
$result = mysqli_query($dbc, $query);


mysqli_close ($dbc);

//success message
$_SESSION['success'] = 'Your registration is complete. Please log in with your personal username/password';
header('Location: login.php');
exit(0);