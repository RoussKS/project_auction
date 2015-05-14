<?php

require_once('init.php');
require_once('layout.php');

$dbc = db_connect();

render_head('User Profile');
render_main(); 

$user = current_user();
if($user->is_logged_in){ /* logged and not logged*/

$user_id = $user->id;
// Fetching user table
$query = sprintf("SELECT * FROM user WHERE user_id=%s",$user_id);
$result = mysqli_query($dbc,$query);
$row = $result->fetch_array(MYSQLI_BOTH);
?>
<div class="row">
	<div class="span3">Username: <b><?php echo $row['username'] ?></b></div>
</div>
<br />
<br />
<?php
// fetching his auctions
$query2 = sprintf("SELECT * FROM auction
				LEFT JOIN category ON auction.category_id=category.category_id
				WHERE created_by=%s 
				ORDER BY end_time ASC 
				LIMIT 0,9", $user_id
				);
$result2 = mysqli_query($dbc,$query2);
	if(!$result2 || $result2->num_rows == 0) {
		echo '<div class="span12 text-center">User has not created any auction.<br/></div>';
	} else {
		while ($row2 = $result2->fetch_array(MYSQLI_BOTH)) {
			echo '
	<div class="row">
		<div class="span9">
			<table width="400" border="1" cellspacing="0" cellpadding="3">
			<tr>
			<td width="30%"><b>Auction title</b>: '. $row2['title'] .'</td>
			<td width="30%"><b>Auction category</b>: '. $row2['name'] .'</td>
			<td width="30%"><b>Auction description</b>: '. $row2['body'] .'</td>
			</tr>
			</table>
		</div>
	</div>';
		}
	}
} else {
$_SESSION['warning'] = 'Login to check your profile';
header ('Location: login.php');
exit(0);
}
render_footer();
mysqli_close($dbc);
?>