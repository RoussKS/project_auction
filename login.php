<?php
require_once('init.php');
require_once('layout.php');

$user = current_user();
if($user->is_logged_in) {
	header('Location: index.php');
} 

if(isset($_POST['submit'])){
	$user->login($_POST['username'],$_POST['password']);

	if($user->error != null) {
		$_SESSION['error'] = $user->error;
	}

}

render_head('Login');
render_main();

?>
<!-- <div class="row-fluid" style="margin-top:30px;"></div> -->
<div class="row-fluid row-top_margin">
<form class="form-horizontal" name="loginform" action="login.php" method="post" onsubmit="return check_login();">
	<div class="row-fluid" id="username-wrap">
		<div class="span1 offset2">Username:</div>
		<div class="span4"><input type="text" name="username" id="username" /></div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid" id="password-wrap">
		<div class="span1 offset2">Password:</div>
		<div class="span4"><input type="password" name="password" id="password" /></div>
		<div class="help-block hide">This field can not be empty</div>
	</div>
	<div class="row-fluid">
			<label class="checkbox offset3"><input type="checkbox"/>Keep me logged in</label>
	</div>
	<div class="row-fluid">
			<button type="submit" name="submit" class="btn btn-primary offset3">Login</button>
	</div>
</form>
</div>
<?php
render_footer();
?>