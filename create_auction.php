<?php

require_once('init.php'); // Provides the database credentials and also loads helpers.php that has the db connection function
require_once('layout.php'); /// Provides website's layout. Header, footer, side menu and all header info

$dbc = db_connect(); /// included in helpers.php loaded by init.php

render_head('Auction Creation'); // layout.php included
render_main(); // layout.php included

$user = current_user(); // function set in helpers.php that uses User class presented in the user.php file
if($user->is_logged_in){ 

?>
<!-- enctype for acceptable file upload -->
<div class="row-fluid">
<form class="form-horizontal" method="post" action="process_auction.php" enctype="multipart/form-data" name="create_auction_form" onsubmit="return validate_create_auction();">
<div class="row-fluid" id="category_id-wrap">
	<div class="span2"><b>Item Category</b></div>
	<div class="span4">
		<select name="category_id" id="category_id">
		<option value="15">- Choose Category -</option>
<?php

// Fetches the category names from db for display in order for a user to select it. It is an obligatory field
$query = mysqli_query($dbc, "SELECT * FROM category");
while ($cat = mysqli_fetch_array($query, MYSQLI_BOTH)) {
	echo '<option value="' . $cat['category_id'] . '">' . $cat['name'] . '</option>';
}
?>
		</select>
	</div>
	<div class="help-block hide">Choose a category</div>
</div>

<div class="row-fluid" id="title-wrap">
	<div class="span2"><b>Auction Title</b></div> <!-- User sets the auction title, obligatory field -->
	<div class="span4"><input type="text" name="title" size="20" id="title" /></div>
	<div class="help-block hide">This field can not be empty</div> <!-- Field cannot be empty message -->
</div>
<div class="row-fluid" id="body-wrap">
	<div class="span2"><b>Auction Description</b></div> <!-- User sets the item description, obligatory field -->
	<div class="span4"><textarea name="body" id="body" cols="20" rows="5"></textarea></div>
	<div class="help-block hide">This field can not be empty</div>
</div>
<div class="row-fluid" id="is_new-wrap">
	<div class="span2"><b>New/Used Item</b></div> <!-- User selects the items condition, new/used, obligatory field -->
	<div class="span4"><select name="is_new" id="is_new">
	<option value="2">- Choose Option -</option>
	<option value="0">New</option>
	<option value="1">Used</option>
	</select>
	</div>
	<div class="help-block hide">This field can not be empty</div>
</div>
<div class="row-fluid">
	<div class="span2"><b>Starting bid</b></div> <!-- User sets the min bid/starting price of auction, optional field -->
	<div class="span4 input-append">	
	<input class="span3" id="appendedInput" type="text" placeholder="0000.00" name="min_bid" />
	<span class="add-on">€</span>
	</div>
</div>
<div class="row-fluid">
	<div class="span2"><b>Buyout price</b></div> <!-- User sets the buyout price, optional field -->
	<div class="span4 input-append">	
	<input class="span3" id="appendedInput" type="text" placeholder="0000.00" name="buyout_price" />
	<span class="add-on">€</span>
	</div>
</div>
<div class="row-fluid" id="duration-wrap">
	<div class="span2"><b>Auction duration</b></div>  <!-- User sets auction's duration, obligatory field 1d/2d/5d/1w options-->
	<div class="span4"><select name="duration" id="duration">
	<option value="4">- Choose duration -</option>
	<option value="0">1 Day</option>
	<option value="1">2 Days</option>
	<option value="2">5 Days</option>
	<option value="3">1 Week</option>
	</select>
	</div>
	<div class="help-block hide">This field can not be empty</div>
</div>
<div class="row-fluid">
	<div class="span2"><b>Upload an image</b></div> <!-- Adding an image file of the item, optional field -->
	<input type="hidden" name="MAX_FILE_SIZE" value="200000" /> <!-- HTML limit 200KB-->
	<div class="span4"><input type="file" name="filename" /></div>
</div>
<br />
<div class="row-fluid">
	<div class="span2 offset2">
	<button type="submit" name="submit" class="btn btn-success">Create Auction</button> <!-- Create auction button -->
	</div>
</div>
</form>
</div>

<?php
} else { // End if logged in ?>
	<div class="row-fluid row-top_margin">
		<div class="span4 offset1"><a href="login.php">Login</a> to create a new auction</div> <!-- if not logged in, then login to create auction -->
	</div>
<?php }
	
render_footer(); // layout.php included

mysqli_close($dbc);