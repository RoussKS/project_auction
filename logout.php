<?php

require_once('init.php');

$user = current_user();
if($user->is_logged_in) {
	$user->logout();
} else {
	$_SESSION['error'] = 'You are not logged in';
	header('Location: index.php'.$redirect_page);
	exit(0);
}
?>
