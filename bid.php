<?php
/*
if a bid is buyout
$_POST['buyout'] = 1;
$_POST['auction_id'] = 2;

if it is a simple BID
$_POST['amount'] = 100;
$_POST['auction_id'] = 2;
*/

require_once('init.php');
$dbc = db_connect();

// var_dump($_POST);

if(isset($_POST['id'])) {
	$auction_id = $_POST['id'];
} else {
	header('Location: index.php');
}

$is_buyout = 0;
$amount = 0;

if($_POST['buyout']) {
	$is_buyout = 1;
	// Checking if there is a row with buyout = 1 for the particular auction_id
	$query = sprintf("SELECT * FROM bid WHERE auction_id=%s AND is_buyout = 1",$auction_id);
	$result = mysqli_query($dbc,$query);
	
	if($result->num_rows > 0) {
		// There already exists a buyout --> show message that auction is closed
		$_SESSION['error'] = 'Auction has ended';
		header ("Location: auction.php?id=" . $_POST['id']);
	}
	$query = sprintf ("SELECT buyout_price FROM auction WHERE auction_id=%s",
			$dbc->real_escape_string($auction_id)
			);
	$result = mysqli_query($dbc, $query);
	$row = $result->fetch_array(MYSQLI_BOTH);
	$amount = $row['buyout_price'];
} else {
	// If it is not yet buyout, I check for the bid with the highest amount (to make comparisons)
	$query = sprintf("SELECT * FROM bid
				WHERE auction_id=%s
				ORDER BY bid.amount DESC
				LIMIT 0,1",
				$dbc->real_escape_string($auction_id)
				);

	$result = mysqli_query($dbc,$query);

	$bid = $result->fetch_array(MYSQLI_BOTH);
	
	$query2 = sprintf ("SELECT * FROM auction
				WHERE auction_id=%s",
				$dbc->real_escape_string($auction_id)
				);
	$result2 = mysqli_query($dbc,$query2);
	$bid2 = $result2->fetch_array(MYSQLI_BOTH);

	if($bid['is_buyout'] == 1) {
		$_SESSION['error'] = 'Auction has ended';
		header ("Location: auction.php?id=" . $_POST['id']);
		exit(0);
	}

	if ($_POST['amount'] < $bid2['min_bid']) { //Checks if user's bid is lower than the starting/min bid and gives error message`
		$_SESSION['error'] = 'Your bid was lower than the starting bid';
		header ("Location: auction.php?id=" .$_POST['id']);
		exit(0);
	}
	
	if ($_POST['amount'] <= $bid['amount']) { // Checks if user's bid is lower than current highest bid and gives error message  
		$_SESSION['error'] = 'Your bid was lower than the current highest bid';
		header ("Location: auction.php?id=" . $_POST['id']);
		exit(0);
	}

	$amount = $_POST['amount'];
}


$user = current_user();

// If everything is ok, the bid is inserted to the database table and becomes the highest one

$query = sprintf ("INSERT INTO bid (auction_id, from_user_id, amount, is_best, is_approved, is_buyout)
				VALUES (%s, $user->id, ".$amount.", ".$is_buyout.", ".$is_buyout.", ".$is_buyout.")",
				$dbc->real_escape_string($auction_id)
				);
$result = mysqli_query($dbc, $query);

if($is_buyout == 1) { // if buyout bid is selected update end status of auction
	$dbc->query(sprintf("UPDATE auction SET has_ended = 1 WHERE auction_id = ",$dbc->real_escape_string($auction_id)));
}

$_SESSION['success'] = 'Your bid was placed'; // show success message of bid placed.
header ("Location: auction.php?id=" . $_POST['id']);
exit(0);

?>