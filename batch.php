+6<?php

/*
This script is run on the server at set time intervals (10 seconds)
It sets has_ended=1 for each auction without buyout option that has expired but not finalised.
If there is a highest bidder, a message is sent.
*/

require_once('init.php');

$dbc = db_connect();

$query = "SELECT t.auction_id,t.title, MAX(t.amount) AS amount,t.created_by,t.from_user_id,user.first_name,user.last_name,t.bid_id FROM (
			SELECT auction.auction_id,auction.title,auction.created_by,bid.bid_id,bid.amount,bid.from_user_id FROM auction
			LEFT JOIN bid ON bid.auction_id = auction.auction_id
			WHERE auction.end_time < now()
			AND auction.has_ended = 0
			AND NOT auction.auction_id IN (
				SELECT auction_id FROM bid WHERE is_buyout = 1 GROUP BY auction_id 
			)
			ORDER BY auction.auction_id ASC, bid.amount DESC
		) AS t
		LEFT JOIN user ON user.user_id = t.from_user_id
		GROUP BY t.auction_id";
$result = $dbc->query($query);

$message_query = "INSERT INTO message (from_user_id,to_user_id,auction_id,subject,body,created) VALUES (%s,%s,%s,'%s','%s',now())";

while($row = $result->fetch_array(MYSQLI_BOTH)) {
	// Close the auction
	$result = $dbc->query(sprintf("UPDATE auction SET has_ended = 1 WHERE auction_id=%s", $row['auction_id']));
	echo $row['auction_id'];
	// Sending messages to auctioneer, highest bidder
	if(is_null($row['amount'])) {
		$query = sprintf($message_query,
				'1',$row['created_by'],$row['auction_id'], # 1 = from admin
				'[Auction ends without an offer] '.$row['title'],
				'Your auction "'.$row['title'].' has ended without an offer.');
		$result = $dbc->query($query);
		echo '<br/><br/>second ERROR: '.$result->error;
	} else {
		// Setting best bid to is_best, so auctioneer approves.
		$result = $dbc->query(sprintf("UPDATE bid SET is_best = 1 WHERE bid_id=%s", $row['bid_id']));
		echo '<br/><br/>second ERROR: '.$dbc->error;
		$query = sprintf($message_query,
				$row['from_user_id'],$row['created_by'],$row['auction_id'],
				'[Auction End] '.$row['title'],
				'Your auction "'.$row['title'].'" has ended with best bid &euro;'.$row['amount'].' by '.$row['last_name'].
					' '.$row['first_name'].'. Visit <a href="auction.php?id='.$row['auction_id'].'">auction page</a>'.
					' to approve the bid');
		$result = $dbc->query($query);
		echo '<br/><br/>third ERROR: '.$dbc->error;
	}
}