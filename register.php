<?php

require_once ('init.php'); // Provides the database credentials and also loads helpers.php that has the db connection function
require_once ('layout.php'); // Provides website's layout. Header, footer, side menu and all header info

$dbc = db_connect(); // included in helpers.php loaded by init.php

render_head ('Register'); // layout.php included
render_main (); // layout.php included

$user = current_user(); // function set in helpers.php that uses User class presented in the user.php file

if ($user->is_logged_in) { // If already logged user, returns a message of already registered with the appropriate username
	echo '<div class="row-fluid row-top_margin">
			<div class="span6 offset1">You are a registered user with username: <a href="profile.php"><b>' .$user->username. '</b></a></div>
		</div>';
} else {
?>
<!-- HTML Form for entries -->
<div class="row-fluid">
	<div class="span3 offset2"><p>Complete this form to register.</p>
	<p>Fields with (*) are mandatory.</div>
</div>
<div class="row-fluid row-top_margin"></div>
<form class="form-horizontal" name="register_form" action="process_reg.php" method="post" onsubmit="return validate_register();"> <!-- simple javascript validation message that uses bootstraps implented js -->
	<div class="row-fluid" id="username-wrap">
		<div class="span2 offset2">Username:</div>
		<div class="span5"><input type="text" name="username" size="20" id="username" placeholder="Username" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="password-wrap">
		<div class="span2 offset2">Password:</div>
		<div class="span5"><input type="password" name="password" size="20" id="password" placeholder="******" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="password1-wrap">
		<div class="span2 offset2">Repeat password:</div>
		<div class="span5"><input type="password" name="password1" size="20" id="password1" placeholder="******" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="first_name-wrap">
		<div class="span2 offset2">First Name:</div>
		<div class="span5"><input type="text" name="first_name" size="20" id="first_name" placeholder="First Name" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="last_name-wrap">
		<div class="span2 offset2">Surname:</div>
		<div class="span5"><input type="text" name="last_name" size="20" id="last_name" placeholder="Surname" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="email-wrap">
		<div class="span2 offset2">Email:</div>
		<div class="span5"><input type="text" name="email" size="20" id="email" placeholder="your@email.com" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="city-wrap">
		<div class="span2 offset2">City/Town:</div>
		<div class="span5"><input type="text" name="city" size="20" id="city" placeholder="City/Town" />(*)</div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid">
		<div class="span2 offset2">Address</div>
		<div class="span5"><input type="text" name="address" size="20" placeholder="Address" /></div>
	</div>
	<div class="row-fluid">
		<div class="span2 offset2">Phone Number</div>
		<div class="span5"><input type="text" name="phone" size="20" placeholder="Phone Number" /></div>
	</div>
	<br />
	<div class="row-fluid">
		<button type="submit" name="submit" class="btn btn-primary offset4">Register</button>
	</div>
	</div>
</form>

<?php
}
render_footer();
mysqli_close($dbc);

?>