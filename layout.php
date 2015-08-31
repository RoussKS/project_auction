<?php

// Head & Title
function render_head($title = null) {
	echo '<!DOCTYPE html>
	<html>
	<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/normalize.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/app.js"></script>
	<title>' . $title . '</title>
	</head>';
}
	// Header & Side Menu
function render_main(){
	$dbc = db_connect();
	$user = current_user();
	$page = $_SERVER['SCRIPT_NAME'];
	$active = ' class="active"';
	echo'<body>
	<div class="navbar navbar-inverse">
		<div class="navbar-inner">
		<div class="container"> <!-- Added for responsve collapse menu -->
		<!-- Buttons for responsive collapse menu -->
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</a>
			<a class="brand" href="index.php">Project Auction</a>
				<div class="nav-collapse collapse">
				<ul class="nav">
					<li'.(($page == 'http://www.roussks.eu/project_auction/create_auction.php') ? $active : '').'><a href="create_auction.php"><i class="icon-plus-sign icon-white"></i> Create an Auction</a></li>
					<li'.(($page == '/message.php') ? $active : '').'><a href="message.php"><i class="icon-envelope icon-white"></i> Messages</a></li>
					<li'.(($page == '/contact.php') ? $active : '').'><a href="contact.php"><i class="icon-asterisk icon-white"></i> Contact</a></li>
				</ul>
				<ul class="nav pull-right">
				<!-- <form class="navbar-search pull-left" action="search.php" method="get">
	  				<input type="text" class="search-query" placeholder="Search">
					</form>
				<li class="divider-vertical"></li> -->';
	if($user->is_logged_in) {
		echo'<li'.(($page == '/profile.php') ? $active : '').'><a href="profile.php"><i class="icon-user icon-white"></i> '.$user->username.'</a></li>
			<li><a href="logout.php"><i class="icon-off icon-white"></i> Logout</a></li>';
	} else {
		echo'<li><a href="login.php"><i class="icon-user icon-white"></i> Login</a></li>
			<li><a href="register.php"><i class="icon-pencil icon-white"></i> Register</a></li>';
	}
	echo'		</ul> <!-- nav-pull-right -->
	    		</div> <!-- nav-collapse -->
		</div> <!-- container -->
		</div> <!-- navbar-inner -->
	</div> <!-- navbar -->

	<div class="jumbotron subhead"><br/></div>
	<div class="container-fluid">
	<div class="row-fluid">
	
	<div id="side_menu" class="span2">
	<h4>Categories</h4>
		<ul>';
	// Fetching category names
	$query = mysqli_query($dbc, "SELECT * FROM category");
	while ($cat = mysqli_fetch_array($query, MYSQLI_BOTH)) {
		echo '<li><a href="category.php?id=' . $cat['category_id'] . '">' . $cat['name'] . '</a></li>';
	}
	echo '</ul>
	</div> <!-- side_menu -->

	<div id="main" class="span10">';

	// var_dump($_SESSION);

	// Show errors of last page
	if(isset($_SESSION['error'])) {
		echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
		unset($_SESSION['error']);
	}

	if(isset($_SESSION['warning'])) {
		echo '<div class="alert alert-warning">'.$_SESSION['warning'].'</div>';
		unset($_SESSION['warning']);
	}

	if(isset($_SESSION['info'])) {
		echo '<div class="alert alert-info">'.$_SESSION['info'].'</div>';
		unset($_SESSION['info']);
	}

	if(isset($_SESSION['success'])) {
		echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
		unset($_SESSION['success']);
	}
}

// Render footer
function render_footer(){
	$page = $_SERVER['SCRIPT_NAME'];
	$active = ' class="active"';
	echo '</div> <!-- main -->
	</div> <!-- row-fluid -->
	</div> <!-- container-fluid -->
	<div id="push"></div> <!-- for sticky footer -->
	<footer class="navbar">
<!--		<div class="nav-collapse collapse"> -->
<!--		<ul class="nav"> -->
<!--			<li'.(($page == '/contact.php') ? $active : '').'><a href="contact.php"><i class="icon-asterisk icon-white"></i> Contact</a></li> -->
<!--		</ul> -->
<!--		</div>	-->
<!--	<a href="policy.php">Policy</a> -->
<!--	<a href="faq.php">F.A.Q.</a></p> -->
		<p>Copyright @ Project Auction ' . date(Y) . '</p>
		<p>Developed by <a href="http://www.roussks.eu" target="_blank">RoussKS</a></p>
	</footer>

	</body>
	</html>';
}