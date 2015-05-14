<?php

/*
Used in asynchronous & javascript queries
result in JSON
eg { "result":"Success", "error":"" }
*/

require_once('init.php');

function get_message($id) {
	$dbc = db_connect();
	$result = $dbc->query(sprintf("SELECT * FROM message WHERE message_id=%s", $dbc->real_escape_string($id)));
	if($result->num_rows == 0) {
		return '{ "error":"No message found with this ID" }';
	} else {
		$row = $result->fetch_array(MYSQLI_BOTH);
		$dbc->query("UPDATE message SET is_seen = 1 WHERE message_id = ".$id);
		return '{ "subject":"'.str_replace('"','&quot;',$row['subject']).'", "body":"'.str_replace('"','&quot;',$row['body']).'" }';
	}
}

$response = '{ "error":"No action supplied" }';

switch($_REQUEST['action']) {
	case 'get_message': $response = get_message($_REQUEST['id']);break;
}
echo $response;