<?php
// Helping functions called from anywhere

$_dbc = null;
$_user = null;

// Checking and connecting to SQL
function db_connect() {
	global $dbs,$dbu,$dbp,$dbn,$_dbc;

	if(is_null($_dbc)) {
		// For a different server testing
		if($_SERVER['SERVER_PORT'] == '8881'){
			$_dbc = mysqli_connect($dbs, $dbu, 'root', $dbn, 8889);
		} else {
			$_dbc = mysqli_connect($dbs, $dbu, $dbp, $dbn);	
		}
		
		// Setting Unicode utf8
		$_dbc->set_charset("utf8");
		$_dbc->select_db($dbn);
		mysqli_query ($_dbc, "set character_set_client='utf8'");
		mysqli_query ($_dbc, "set character_set_results='utf8'");
		mysqli_query ($_dbc, "set collation_connection='utf8_general_ci'"); 
	}
	return $_dbc;
}

function current_user() {
	global $_user;
	if(is_null($_user)) {
		$_user = new User();
	}
	return $_user;
}

function create_hash($str) {
	return md5($str);
}
