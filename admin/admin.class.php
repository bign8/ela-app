<?php

function to_path() { return realpath(implode(DIRECTORY_SEPARATOR, func_get_args())); }

require_once( to_path(__DIR__, '..', 'php', 'secure_pass.php' ));

class Admin {
	private $status = array();

	function __construct() {
		if (session_id() == '') session_start();
		$this->db = new PDO('sqlite:' . to_path(__DIR__, '..', 'db.sqlite3'));
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
	public function getUsers() {
		$this->requiresAdmin();
		return $this->db->query("SELECT * FROM user ORDER BY first, last;")->fetchAll(PDO::FETCH_ASSOC);
	}
	public function emulate($userID) {
		$this->requiresAdmin();
		$sth  = $this->db->prepare("SELECT * FROM user WHERE accountno=? LIMIT 1;");
		if (
			$sth->execute(array( $userID )) &&
			false !== ($user = $sth->fetch(PDO::FETCH_ASSOC))
		) {
			unset($user['pass']);
			$_SESSION['user'] = $user;
			die(header('Location: ../index.php'));
		}
	}
	public function getAdmins() {
		$this->requiresAdmin();
		return $this->db->query("SELECT admin, user FROM admin;")->fetchAll( PDO::FETCH_ASSOC );
	}
	public function pw_admin($admin, $new_pass) {
		$this->requiresAdmin();
		$check = $this->db->prepare("UPDATE admin SET pass=? WHERE admin=?;")->execute(array( create_hash($new_pass), $admin ));
		$this->status['pw_admin'] = $check;
		return $check;
	}
	public function add_admin($user, $pass) {
		$this->requiresAdmin();
		$check = $this->db->prepare("INSERT INTO admin (user,pass) VALUES (?,?);")->execute(array($user, create_hash($pass)));
		$this->status['add_admin'] = $check;
		return $check;
	}
	public function rem_admin($admin) {
		$this->requiresAdmin();
		$check = $this->db->prepare("DELETE FROM admin WHERE admin=?;")->execute(array($admin));
		$this->status['rem_admin'] = $check;
		return $check;
	}
	public function pw_user($accountno, $pass) {
		$this->requiresAdmin();
		$check = $this->db->prepare("UPDATE user SET pass=? WHERE accountno=?;")->execute(array(create_hash($pass), $accountno));
		$this->status['pw_user'] = $check;
		return $check;
	}
}

$admin = new Admin();
switch ( isset($_REQUEST['action']) ? $_REQUEST['action'] : null ) {
	case 'login':     $admin->login($_POST['user'], $_POST['pass']); break;
	case 'logout':    $admin->logout(); break;
	case 'emulate':   $admin->emulate($_POST['accountno']); break;
	case 'pw-admin':  $admin->pw_admin($_POST['admin'], $_POST['pass']); break;
	case 'add-admin': $admin->add_admin($_POST['user'], $_POST['pass']); break;
	case 'rem-admin': $admin->rem_admin($_POST['admin']); break;
	case 'pw-user':   $admin->pw_user($_POST['accountno'], $_POST['pass']); break;
}
