# project_auction
Online Auctions Project

This was started as a Dissertation Project for University.
Working copy, but more work needed for production.

init.php file needs to be created that would look like this:
<?php
session_start();
/*
This script is called at the start if every .php page to start default values etc
The variables here are globally accessible by all the scripts of the website
*/
ini_set ('display errors', 1);
error_reporting (E_ALL & ~E_NOTICE);
date_default_timezone_set("Europe/Helsinki");

require_once('helpers.php');
require_once('user.php');

// database parameters
$dbn = ''; 	// Database Name
$dbs = '';			// Database Server
$dbu = '';				// Database Username
$dbp = '';			// Database Password
