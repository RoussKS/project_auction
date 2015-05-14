<?php
/*
This .php sets user class
If a user is logged in, user.php makes user's data available in each page until he logs out, or the session ends
*/
class User {
	public $is_logged_in = false;
	// public variable to retain possible errors
	public $error = null;

	// I keep only the user sql table entries I need to show on the html div "you are connected as...user"
	public $id = -1;
	public $username = null;
	public $first_name = null;
	public $last_name = null;

	// construct function when we first set up a User object - $u = new User()
	function __construct(){
		$temporary_id = -1;

		// Check session for already existing user_id, if the user is already logged in
		// user_id is in session through process_login
		if(array_key_exists('user_id',$_SESSION)) {
			$temporary_id = $_SESSION['user_id'];
		} else if(isset($_COOKIE['user_id'])) {
			// Session ended but user has "remember me" checked
			$temporary_id = $_COOKIE['user_id'];
			// I fetch the id from cookie and renew the session
			$_SESSION['user_id'] = $temporary_id;
			$_SESSION['password'] = $_COOKIE['password'];
		}

		// If we have a current id now, the user is logged in
		// We fetch his sql user table entries again
		if($temporary_id != -1) {
			$dbc = db_connect();
			$query = sprintf("SELECT username,password,first_name,last_name FROM user WHERE user_id = %s",
							$dbc->real_escape_string($temporary_id));
			$result = $dbc->query($query);

			/*
			If we don't have returned rows from database or if it gets more than 1 users, that's suspicious
			so we send error message
			*/
			if(!$result || $result->num_rows == 0 || $result->num_rows > 1) {
				$_SESSION['error'] = 'Please login to continue';
				logout('login.php');
			}

			$row = $result->fetch_array(MYSQLI_BOTH);

			// Checking hashed session password with sql user table hashed password
			$hashed_password = create_hash($row['password']);
			if($hashed_password != $_SESSION['password']) {
				$_SESSION['error'] = 'Please login to continue';
				logout('login.php');
			}

			// everything ok part
			$this->id = $temporary_id;
			$this->username = $row['username'];
			$this->first_name = $row['first_name'];
			$this->last_name = $row['last_name'];
			$this->is_logged_in = true;
		}
	}

	// Function login. Shows error on problem, redirects if everything ok
	function login($username, $password, $redirect_page = 'index.php') {
		if($this->is_logged_in) {
			$this->error = "You are already logged in"; // already connected (if a logged user goes to login.php
			return;
		}
		$dbc = db_connect(); //fetch username and password of appropriate user id
		$query = sprintf("SELECT user_id FROM user WHERE username = '%s' AND password = '%s'",
							$dbc->real_escape_string($username),
							$dbc->real_escape_string($password)
						);
		$result = $dbc->query($query);

		if(!$result) {
			$this->error = $dbc->error;
			return;
		}

		if($result->num_rows > 1) { // duplicate entry
			$this->error = "There are more than 1 users with these details";
			return;
		}

		if($result->num_rows == 0) { // wrong login details
			$this->error = "Username/Password do not exist";
			return;
		}

		// Everything ok part
		$row = $result->fetch_array(MYSQLI_BOTH);
		$_SESSION['user_id'] = $row['user_id'];
		$_SESSION['password'] = create_hash($password);
		$_SESSION['success'] = 'You logged in successfully';
		$this->id = $row['user_id'];
		$this->is_logged_in = true;
		header('Location: '.$redirect_page);
		exit(0);
	}

	function logout($redirect_page = 'index.php') {
		unset($_SESSION['user_id']);
		unset($_SESSION['password']);
		unset($_COOKIE['user_id']);
		unset($_COOKIE['password']);
		setcookie('user_id', null, -1, '/');
		setcookie('password', null, -1, '/');
		$_SESSION['info'] = 'You logged out successfully'; // Successful logout message on
		header('Location: '.$redirect_page);
		exit(0);
	}
}