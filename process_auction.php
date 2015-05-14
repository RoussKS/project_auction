<?php
/*
This process checks the entries added by the user to the create_auction.php (included), just before posting the created auction
It returns false errors (using the visual look of warnings of bootstrap framework),
else the auction creation is completed smoothly. The submitted data are recorded in the database and the auction appears on main site.
*/

require_once('init.php');  // database credentials & db connection function (as explained in create_auction.php)

$dbc = db_connect(); // db_connect is set on helpers.php called by init.php
$user = current_user();
$pattern = "/\d{1,15}(\.(\d{1,2}))?$/"; // Pattern for preg match. It checks that a numeric value is a positive number with maximum two decimal points (eg 12.56)

trim($_POST['title']);
trim($_POST['body']);
trim($_POST['min_bid']);
trim($_POST['buyout_price']);

$error = ''; /// Setting error variable

if ($_POST['category_id'] == 15) { // Checks that auction category is set by user, if not -> error message
	$error .= 'Category is not set!<br />';
}
if (empty($_POST['title']) == true) { // Checks that auction title is set by user, if not -> error message
	$error .= 'Auction title is not set!<br />';
}
if (empty($_POST['body']) == true) { // Checks that auction description/body is set by user, if not -> error message
	$error .= 'Auction description is not set!<br />';
}
if ($_POST['is_new'] == 2) { // Checks that auction item used/new status is set by user, if not -> error message
	$error .= 'New/Used item state is not set!<br />';
}
if (empty ($_POST['min_bid']) == false) { // In case the min_bid entry is not empty (optional field) ->
	if (is_numeric($_POST['min_bid']) == false) { // Checks if min_bid price is actually numeric
		$error .= 'Starting bid is not numeric!<br />'; // error of not numeric
	}
	if (preg_match($pattern, $_POST['min_bid']) == false) { // Checks the numeric to be in accordance with the pattern using preg_match
		$error .= 'Starting bid is not an proper numeric!<br />'; // error of not PROPER numeric
	}
}
if (empty ($_POST['buyout_price']) == false) { // In case the buyout_price entry is not empty (optional field) ->
	if (is_numeric($_POST['buyout_price']) == false) { // Checks if buyout price is actually numeric
		$error .= 'Buyout Price is not numeric!<br />'; // error of not numeric
	} 
	if (preg_match($pattern, $_POST['buyout_price']) == false){ // Checks the numeric to be in accordance with the pattern using preg_match
		$error .= 'Buyout Price is not a proper numeric!<br />'; // error of not PROPER  numeric
	}
	if ($_POST['buyout_price'] <= $_POST['min_bid']) { // Checks if buyout price is lower than min _bid (if not empty)
		$error .= 'Buyout price must be higher than Starting bid!<br />'; // error message of buyout lower than min bid (needs to be higher)
	}
}

if ($_POST['duration'] == 4) { // Checks if auction duration is set by user, if not -> error message
	$error .= 'Auction duration is not set!<br />';
}
// Checking filename (for possible uploaded file, needs to be an image) (optional field)
if (is_uploaded_file($_FILES['filename']['tmp_name']) == true) { // If HTTP/POST file uploaded -->
	if (getimagesize($_FILES['filename']['tmp_name']) == false) { // If it is not an image file -> error message
		$error .= 'This is not an image file!<br />';
	}
	// errors of size (if it is image file)
	switch ($_FILES['filename']['error']) {
		case 1: $error .= 'The image size is more than 200KB!<br />'; // php.ini limit set (200KB)
		break;
		case 2: $error .=  'The image size is more than 200KB!<br />'; // html form limit set (200KB)
		break;
		case 3: $error .=  'The image file was partially uploaded!<br />';
		break;
	}
}
if($error != '') { // if error exist, show error message
	$_SESSION['error'] = $error;
	header ('Location: create_auction.php');
	exit(0);
}

// SQL Injection protection
$category_id = mysqli_real_escape_string($dbc, $_POST['category_id']);
$created_by = mysqli_real_escape_string($dbc, $user->id);
$title = mysqli_real_escape_string($dbc, $_POST['title']);
$body = mysqli_real_escape_string($dbc, $_POST['body']);
$is_new = mysqli_real_escape_string($dbc, $_POST['is_new']);
$min_bid = mysqli_real_escape_string($dbc, $_POST['min_bid']);
$buyout_price = mysqli_real_escape_string($dbc, $_POST['buyout_price']);
$duration = mysqli_real_escape_string($dbc, $_POST['duration']);

// Extra checking, I set time intervals in order to change end_time of SQL
switch ($duration) {
	case "0": $duration = '+ INTERVAL 1 DAY';
	break;
	case "1": $duration = '+ INTERVAL 2 DAY';
	break;
	case "2": $duration = '+ INTERVAL 5 DAY';
	break;
	case "3": $duration = '+ INTERVAL 7 DAY';
	break;
}
//setting sql query
$query = "INSERT INTO auction (category_id, created_by, title, body, 
							is_new, buyout_price, end_time, created, min_bid)
				VALUES ('$category_id', '$created_by', '$title', '$body', 
					'$is_new', '$buyout_price', now() ".$duration.", now(), '$min_bid')";
$result = mysqli_query($dbc, $query);

/*
any image file uploaded, is uploaded to a directory and not in SQL table. Only the filename is entered in the database table
this was done to avoid enlarging the capacity of sql database through image uploads directly to it.
Additionally we rename it to avoid duplicate names
*/

$auction = mysqli_insert_id($dbc);
$datetime = date('Y-m-d-H-m-s'); // create date/time stamp
$filename = $auction.$datetime.$_FILES['filename']['name']; // Final rename of file with auction_id + timestamp for unique name.
if (move_uploaded_file ($_FILES['filename']['tmp_name'], "uploads/" . $filename)) {
	$filename = mysqli_real_escape_string($dbc, $filename);
	// query for adding filename to SQL + the auction id it refers to
	$query2 = "INSERT INTO image (auction_id, filename, description)
		VALUES ('$auction', '$filename', '$filename')";
	$result2 = mysqli_query($dbc, $query2);
}
mysqli_close ($dbc); // Closing database
$_SESSION['success'] = 'Your auction is created successfully';
header('Location: auction.php?id=' . $auction . ''); // redirection to the created auction
exit(0);