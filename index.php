<?php // Main page of website. It shows 9 most recent auctions

require_once('init.php');
require_once('layout.php');
$dbc = db_connect();

render_head('Project Auction');
// Render header & side menu
render_main();

// SQL query to Fetch first 9 auctions with corresponding image
$query = "SELECT auction.auction_id AS id, auction.*, category.*, user.*, image.* FROM auction 
	LEFT JOIN category ON auction.category_id=category.category_id
	LEFT JOIN user ON auction.created_by=user.user_id
	LEFT JOIN (
		SELECT MIN(image_id) AS iid,auction_id AS aid FROM image
		GROUP BY auction_id
	) AS t ON t.aid = auction.auction_id
	LEFT JOIN image ON image.image_id = t.iid
	WHERE now() <= end_time
	ORDER BY end_time ASC
	LIMIT 0,9";
$result = $dbc->query($query);

if(!$result || $result->num_rows == 0) {
	echo '<div class="row-fluid row-top_margin">
			<div class="span4 offset1">There are no active auctions.<br/></div>
		</div>';
} else {
	echo'<div class="row-fluid" id="index-auctions">';
	while($row = $result->fetch_array(MYSQLI_BOTH)){
		echo'<div class="span4 offset1 index-auction">
				<img src="uploads/'.$row['filename'].'" class="index-auction-image" alt="auction image" />
				<a href="auction.php?id='.$row['id'].'">
					<div class="auction-title">'.$row['title'].'</div>
					<div class="auction-body">'.$row['body'].'</div>
					<div class="auction-user">posted by '.$row['username'].'</div>
				</a>
			</div>';
	}
	echo'</div>';
}
echo'<div class="row-fluid row-top_margin">
		<div class="span12 text-center"><a href="create_auction.php">Create a new Auction</a></div>
	</div>';


render_footer();
mysqli_close($dbc);

?>