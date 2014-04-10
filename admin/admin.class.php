<?php

require_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'php', 'secure_pass.php' )));

class Admin {
	private $status = array();

	function __construct() {
		session_start();
		$this->db = new PDO('sqlite:' . implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'db.sqlite3')));
	}

	public function __get($name) {
		if (array_key_exists($name, $this->status)) 
			return $this->status[$name];
		return null;
	}

	public function requiresAdmin($redirect = true) {
		$test = isset($_SESSION['admin']);
		if ( !$test && $redirect ) die(header('Location: login.php'));
		return $test;
	}

	public function login($user, $pass) {
		$sth = $this->db->prepare("SELECT * FROM admin WHERE user=? LIMIT 1;");
		$check = (
			$sth->execute(array( $user )) && 
			false !== ($admin = $sth->fetch( PDO::FETCH_ASSOC )) &&
			validate_password($pass, $admin['pass'])
		);
		if ($check) {
			unset($admin['pass']);
			$_SESSION['admin'] = $admin;
		}
		$this->status['login_status'] = $check;
		return $check;
	}
	public function logout() {
		unset($_SESSION['admin']);
	}
}

$admin = new Admin();
switch ( isset($_REQUEST['action']) ? $_REQUEST['action'] : null ) {
	case 'login':  $admin->login($_POST['user'], $_POST['pass']); break;
	case 'logout': $admin->logout(); break;
}
