<?php

require_once('init.php');
require_once('layout.php');
$dbc = db_connect();

$category_id = 0;
if(isset($_GET['id'])) {
	$category_id = $_GET['id'];
}

// Fetch 9 first auctions
$query = sprintf("SELECT auction.auction_id AS id, auction.*, category.*, user.*, image.* FROM auction
				LEFT JOIN category ON auction.category_id=category.category_id
				LEFT JOIN user ON auction.created_by=user.user_id
				LEFT JOIN (
				SELECT MIN(image_id) AS iid,auction_id AS aid FROM image
				GROUP BY auction_id
				) AS t ON t.aid = auction.auction_id
				LEFT JOIN image ON image.image_id = t.iid
				WHERE now() <= end_time
				AND auction.category_id=%s
				ORDER BY end_time ASC
				LIMIT 0,9",
				$dbc->real_escape_string($category_id)
			);
$result = $dbc->query($query);

$query2 = sprintf("SELECT * FROM category
				WHERE category_id=%s",
				$dbc->real_escape_string($category_id)
			);
$result2 = $dbc->query($query2);
$cat = $result2->fetch_array(MYSQLI_BOTH);
			
render_head('Project Auction | '.$cat['name']);
render_main();

if(!$result || $result->num_rows == 0) {
	echo'<div class="row-fluid row-top_margin">
			<div class="span4 offset1">There are no auctions<br/></div>
		</div>';
} else {
	echo '<div class="row-fluid row-top_margin	" id="index-auctions">';
	while($row = $result->fetch_array(MYSQLI_BOTH)){
		echo '<div class="span4 offset1 index-auction">
				<img src="uploads/'.$row['filename'].'" class="index-auction-image" alt="auction image" />
				<a href="auction.php?id='.$row['id'].'">
					<div class="auction-title">'.$row['title'].'</div>
					<div class="auction-body">'.$row['body'].'</div>
					<div class="auction-user">created by '.$row['username'].'</div>
				</a>
			</div>';
	}
	echo '</div>';
}
echo '<div class="row-fluid row-top_margin">
		<div class="span12 text-center"><a href="create_auction.php">Create a new Auction</a></div>
	</div>';

render_footer();
mysqli_close($dbc);
?>
