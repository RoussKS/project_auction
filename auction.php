<?php
require_once('init.php');
require_once('layout.php');

$dbc = db_connect();

// Fetch id from URL
$auction_id = 0;
if(isset($_GET['id'])) {
	$auction_id = $_GET['id'];
}

/*
Setting query. LEFT JOIN tables for specific entries connection & connecting keys
sprintf: same as printf but do not show output on screet but set it as a variable.
User of real_escape_string to avoid SQL Injection
*/
$query = sprintf("SELECT auction.auction_id AS id, auction.*, category.*, user.*, image.* FROM auction
				LEFT JOIN category ON auction.category_id=category.category_id
				LEFT JOIN user ON auction.created_by=user.user_id
				LEFT JOIN (
					SELECT MIN(image_id) AS iid,auction_id AS aid FROM image
					GROUP BY auction_id
				) AS t ON t.aid = auction.auction_id
				LEFT JOIN image ON image.image_id = t.iid
				WHERE auction.auction_id=%s",
				$dbc->real_escape_string($auction_id)
			);
$result = mysqli_query($dbc,$query);

$query2 = sprintf("SELECT * FROM bid WHERE auction_id=%s
				ORDER BY bid.amount DESC
				LIMIT 0,1", $dbc->real_escape_string($auction_id)
				);
$result2 = mysqli_query($dbc,$query2);
$row2 = mysqli_fetch_array($result2, MYSQLI_BOTH);

if (!$result) {
    printf("Error: %s\n", mysqli_error($dbc));
    exit();
}

$row = mysqli_fetch_array($result, MYSQLI_BOTH);

$title = 'Auction not found';
if (!is_null($row)) {
	$title = 'Auction | ' . $row['title'];
}

render_head($title);
render_main();

if(is_null($row)) { //Not found auction (non-existent id)
	echo '<div class="row-fluid row-top_margin">
			<div class="span12 text-center">Auction not found.</div>
		</div>';
} else {
?>

<!-- HTML -->
<div class="row-fluid">
	<div class="span4 offset1">
		<?php echo '<img src="uploads/' .$row['filename'].'" class="auction-image" width="300" alt="auction image" />'; ?>
	</div>
	<div class="span6">
		<div class="row-fluid">
			<div class="span4">
			<b>Category: </b><?php echo $row['name']; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span4">
			<b>User</b>: <?php echo $row['username']; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
			<b>Title</b>: <?php echo $row['title']; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span8">
			<b>Description</b>: <?php echo $row['body']; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span4">
			<b>Condition</b>: <?php if ($row['is new']=0) {
							echo 'New';
						} else {
							echo 'Used';
						} ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span4">
			<b>Starting bid</b>: <?php echo $row['min_bid']; ?> €
			</div>
		</div>
<!-- Shows buyout price only if it is set -->
		<?php if ($row['buyout_price'] > 0) { 
			echo '<div class="row-fluid">
					<div class="span4"><b>Buyout Price</b>: ' . $row['buyout_price'] . ' €</div>
				</div>';
		} ?>
		<div class="row-fluid">
			<div class="span7">
			<b>Auction ends</b>: <?php echo $row['end_time']; ?>
			</div>
		</div>
		<?php 
		$user = current_user();
		if($user->is_logged_in){
			if($row['created_by'] == $user->id && $row['has_ended'] == '0') {
				// If user's auction and not ended, show best bid up until now
				echo'<div class="row-fluid">
						<div class="span4">
						<b>Highest bid at the moment</b>: '.$row2['amount'].'€
						</div>
					</div>';
			} else if($row['created_by'] == $user->id && $row['has_ended'] == '1') {
				// If user's auction and has ended, show is_best
				$bid = 'is_best bid query';
				
				if($bid['is_buyout'] == 1) {
					echo'<div class="row-fluid">
							<div class="span4">This Auction has ended.</div>
						</div>';
				} else if($bid['is_approved']) {
					// Show message of ended auction
				} else {
					// Show approved form
				}
			} else if($row['created_by'] != $user->id && $row['has_ended'] == '1') {
					echo'<div class="row-fluid">
							<div class="span4">This Auction has ended.</div>
						</div>';
			} else {
				// not current user's auctions
	 		?>
				<div class="row-fluid">
					<div class="span7">
					<b>Highest bid at the moment</b>: <?php echo $row2['amount']; ?> €
					</div>
				</div>
				<br />
				<div class="row-fluid">
					<div class="span7" id="auction-bid">
					<form class="form-inline" name="bid_form" method="post" action="bid.php">
					<?php echo '<input type="hidden" name="id" value="'.$auction_id.'" />'; ?>
					<input type="text" name="amount" value="0" />
					<span>
						<input type="submit" class="btn btn-success" value="Bid" />
					</span>
					</form>
					</div>
				</div>
				<div class="row-fluid">
				<?php if ($row['buyout_price'] > 0) { //Shows buyout price if buyout > 0 exists
				?>
					<div class="span3" id="auction-bid">
					<form name="buyout_form" method="post" action="bid.php">
					<input type="hidden" name="id" value="<?php echo $auction_id ?>" />
					<input type="hidden" name="buyout" value="1" />
					<input type="submit" class="btn btn-danger" value="Buyout" />
					</form>
					</div>
				</div>
				<?php 
				}
			} //else 
		} //end of if is-logged-in 
		else { ?>
			<div class="row-fluid row-top_margin">
				<br /><br />
				<div class="span9 text-center"><a href="login.php">Login</a> to bid!</div>
			</div>
		<?php } ?>
	</div> <!-- right content -->
</div> <!-- main content row -->
<!-- HTML END-->
<?php

} // If end

render_footer();
mysqli_close($dbc);
?>