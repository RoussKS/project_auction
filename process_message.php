<?php
require_once('init.php');
$user = current_user();
$dbc = db_connect();

$errors = array();
if(!is_numeric($_POST['new-message-user']) || $_POST['new-message-user'] == '0') {
	$errors[] = 'The recipient is not valid.';
}
if(str_replace(' ','',$_POST['new-message-subject']) == '') {
	$errors[] = 'You must set a title.';
}
if(str_replace(' ','',$_POST['new-message-body']) == '') {
	$errors[] = 'You must write a message.';
}

if(count($errors) > 0) {
	$_SESSION['error'] = implode('<br/>',$errors);
} else {
	$query = sprintf("INSERT INTO message (from_user_id,to_user_id,auction_id,subject,body,created) 
				VALUES (%s,%s,%s,'%s','%s',now())",
				$user->id, $dbc->real_escape_string($_POST['new-message-user']),'NULL',
				$dbc->real_escape_string($_POST['new-message-subject']),$dbc->real_escape_string($_POST['new-message-body'])) ;
	$dbc->query($query);
	$_SESSION['success'] = 'Your message has been set';
}

header("Location: message.php");
exit(0);