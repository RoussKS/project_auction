// Javascript / jQuery scripts for user errors
$(document).ready(function() {
	//Initial script. It will execute when the page is fully loaded.
	
	$('#received-messages-wrap,#sent-messages-wrap').each(function() {
		$(this).on('click', '.row-fluid', function() {
			$('.message-handle.selected').removeClass("selected");
			$(this).find('a').addClass("selected");
			show_message($(this).attr('id').substr(8));
			return false;
		})
	});

	if($('.message-area-selector').size() > 0) {
		$('.message-area-selector').on('click', 'li', function() {
			$('.messages-wrap').hide();
			$(this).parent().find('li').removeClass('active');
			$(this).addClass('active');
			if($(this).html().indexOf('Incoming') > -1) {
				$('#received-messages-wrap').show();
			} else if ($(this).html().indexOf('Outgoing') > -1) {
				$('#sent-messages-wrap').show();
			} else {
				$('#new-message-wrap').show();
			}
		});
	}
});

function check_login() { // login check
	$('.help-block').hide();

    var success = true;

	if($('#username').val().replace(/ /g,'').length == 0) {
		$('#username-wrap .help-block').show();
		success = false;
	}

	if($('#password').val().replace(/ /g,'').length == 0) {
		$('#password-wrap .help-block').show();
		success = false;
	}

	return success;
}

function validate_create_auction() { // create auction check
	$('.help-block').hide();
	var success = true;
	
	if($('#category_id').val()==15) {
		$('#category_id-wrap .help-block').show();
		success = false;
	}

	if($('#title').val().replace(/ /g,'').length == 0) {
		$('#title-wrap .help-block').show();
		success = false;
	}

	if($('#body').val().replace(/ /g,'').length == 0) {
		$('#body-wrap .help-block').show();
		success = false;
	}

	if($('#is_new').val()==2) {
		$('#is_new-wrap .help-block').show();
		success = false;
	}
	
	if($('#duration').val()==4) {
		$('#duration-wrap .help-block').show();
		success = false;
	}

	
	return success;
}

function validate_register() { // registration check
	$('.help-block').hide();
	var success = true;

	if($('#username').val().replace(/ /g,'').length == 0) {
		$('#username-wrap .help-block').show();
		success = false;
	}
	
	if($('#password').val().replace(/ /g,'').length == 0) {
		$('#password-wrap .help-block').show();
		success = false;
	}
	
	if($('#password1').val().replace(/ /g,'').length == 0) {
		$('#password1-wrap .help-block').show();
		success = false;
	}

	if($('#first_name').val().replace(/ /g,'').length == 0) {
		$('#first_name-wrap .help-block').show();
		success = false;
	}

	if($('#last_name').val().replace(/ /g,'').length == 0) {
		$('#last_name-wrap .help-block').show();
		success = false;
	}

	if($('#email').val().replace(/ /g,'').length == 0) {
		$('#email-wrap .help-block').show();
		success = false;
	}

	if($('#city').val().replace(/ /g,'').length == 0) {
		$('#city-wrap .help-block').show();
		success = false;
	}
	
	return success;
}

function validate_new_message() { // new message check
	$('.help-block').hide();
	var success = true;
	return success;

	if($('#new-message-user').val() == '0') {
		$('#new-message-user-wrap .help-block').show();
		success = false;
	}

	if($('#new-message-subject').val().replace(/ /g,'').length == 0) {
		$('#new-message-subject-wrap .help-block').show();
		success = false;
	}

	if($('#new-message-body').val().replace(/ /g,'').length == 0) {
		$('#new-message-body-wrap .help-block').show();
		success = false;
	}

	return success;
}

function toggle_messages_area(area) {
	switch(area){
		case "received": break;
	}
}

function show_message(id) { // calls asynch.php (not included in the attached files for asynchronous sql message table entries fetching)
	$.post("async.php?action=get_message&id="+id,function(data,dataType) {
		var obj = jQuery.parseJSON(data.replace(/\[/g,'&#91;').replace(']','&#93;'));
		$('#messages-content-wrap').html('<div class="show-message-subject"><b>'+obj.subject+'</b></div><br /><div class="show-message-body">'+obj.body+'</div>');
	});
}