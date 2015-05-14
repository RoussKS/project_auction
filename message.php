<?php

require_once('init.php');
require_once('layout.php');

render_head('Messages');
render_main();

$user = current_user();

if(!$user->is_logged_in) {
	echo '<div class="row-fluid row-top_margin">
			<div class="span4 offset1">Please <a href="login.php">login</a> to check your messages.</div>
		</div>';
	render_footer();
	exit(0);
}

echo '<div class="row-fluid">
		<ul class="nav nav-pills message-area-selector">
			<li class="active"><a href="#">Incoming</a></li>
			<li><a href="#">Outgoing</a></li>
			<li><a href="#">Create a message</a></li>
		</ul>
	</div>';

$dbc = db_connect();
/*
Fetch messages and JOIN user table @ from_user_id to fetch sender's name
Recipient's name is current user and already use it from $user object
*/
$query = sprintf("SELECT message.message_id,message.subject,message.created,message.is_seen,user.username FROM message 
		LEFT JOIN user ON user.user_id = message.from_user_id
		WHERE to_user_id = %s
		ORDER BY message.created DESC", 
	$dbc->real_escape_string($user->id));
$result = $dbc->query($query);

echo'<div class="row-fluid messages-wrap hide" id="received-messages-wrap">
		<div class="row-fluid" style="border-bottom:solid 1px #999;">
			<div class="span4">From</div>
			<div class="span5">Title</div>
			<div class="span3">Sent</div>
		</div>';
while($row = $result->fetch_array(MYSQLI_BOTH)) {
	$style = ($row['is_seen'] == '0') ? ' style="font-weight:bold;"' : '';
 	echo'<div class="row-fluid" id="message-'.$row['message_id'].'">
 			<a href="#" class="message-handle">
				<div class="span4"'.$style.'>'.$row['username'].'</div>
				<div class="span5"'.$style.'>'.$row['subject'].'</div>
				<div class="span3"'.$style.'>'.$row['created'].'</div>
			</a>
		</div>';
}
echo'</div>';

/*
Fetch messages that current user has sent 
Use LEFT JOIN on user table @ to_user_id to fetch the receiver name, I am the sender
*/
$query = sprintf("SELECT message.message_id,message.subject,message.created,message.is_seen,user.username FROM message 
	LEFT JOIN user ON user.user_id = message.to_user_id
	WHERE from_user_id = %s
	ORDER BY message.created DESC", 
$dbc->real_escape_string($user->id));
$result = $dbc->query($query);

echo'<div class="row-fluid messages-wrap hide" id="sent-messages-wrap">
		<div class="row-fluid" style="border-bottom:solid 1px #999;">
			<div class="span4">To</div>
			<div class="span5">Title</div>
			<div class="span3">Sent</div>
		</div>';
while($row = $result->fetch_array(MYSQLI_BOTH)) {
	$seen = ($row['is_seen'] == '0') ? ' seen' : '';
 	echo'<div class="row-fluid" id="message-'.$row['message_id'].'">
 			<a href="#" class="message-handle'.$seen.'">
				<div class="span4">'.$row['username'].'</div>
				<div class="span5">'.$row['subject'].'</div>
				<div class="span3">'.$row['created'].'</div>
			</a>
		</div>';
}
echo'</div>';

// New message markup
$result = $dbc->query("SELECT user_id,username FROM user WHERE user_id != " .$user->id);

echo'<div class="row-fluid messages-wrap hide" id="new-message-wrap">
	<div class="row-fluid" style="border-bottom:solid 1px #999;padding-top:15px;">
		<form name="newmessageform" action="process_message.php" onsubmit="return validate_new_message();" method="POST">
			<div class="row-fluid" id="new-message-user-wrap">
				<div class="span2 offset2"><label>To:</label></div>
				<div class="span4">
					<select name="new-message-user" id="new-message-user">
						<option value="0">-- Choose User --</option>';
while($row = $result->fetch_array(MYSQLI_BOTH)) {
	echo '<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
}
echo '				</select>
				</div>
				<div class="help-block hide">This field can not be empty</div>
			</div>
			<div class="row-fluid" id="new-message-subject-wrap">
				<div class="span2 offset2"><label>Title:</label></div>
				<div class="span4"><input type="text" name="new-message-subject" id="new-message-subject" /></div>
				<div class="help-block hide">This field can not be empty</div>
			</div>
			<div class="row-fluid" id="new-message-body-wrap">
				<div class="span2 offset2"><label>Message:</label></div>
				<div class="span4"><textarea name="new-message-body" id="new-message-body"></textarea></div>
				<div class="help-block hide">This field can not be empty</div>
			</div>
			<div class="row-fluid" id="new-message-body-wrap">
				<div class="span4 offset4"><input type="submit" value="Send" /></div>
			</div>
		</form>
	</div>';


echo'</div><!-- messages-wrap -->
	<div class="well" id="messages-content-wrap">
	</div> <!-- messages-content -->
';

render_footer();
mysqli_close ($dbc);
?>