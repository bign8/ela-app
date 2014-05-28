<?php

require_once(__dir__ . DIRECTORY_SEPARATOR . 'secure_pass.php');
require_once(__dir__ . DIRECTORY_SEPARATOR . 'mailer.php');

/**
* ELA Main class
*/
class ELA {
	public $status;
	protected $db;

	const RESET_MISSMATCH = 'missmatch';
	const RESET_BAD_HASH = 'bad-hash';
	const RESET_SHORT = 'short';
	const DB_ERROR = 'db-error';

	function __construct() {
		$this->db = new PDO('sqlite:' . implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'db.sqlite3')));
		$this->status = array();
	}

	// USER FUNCTIONS

	public function login( $email, $password ) {
		$sth  = $this->db->prepare("SELECT * FROM user WHERE email=? OR user=?;");
		$pass = $sth->execute(array( $email, $email ));
		$user = $sth->fetch( PDO::FETCH_ASSOC );
		$pass = $pass ? validate_password( $password, $user['pass'] ) : false ;

		if ($pass) {
			unset($user['pass']);
			$_SESSION['user'] = $user;
		}
		return $pass;
	}
	public function logout() {
		unset( $_SESSION['user'] );
	}
	public function save_profile( $data ) {
		$mail = new Mailer;
		$pass = true;
		$user = $this->db->prepare("SELECT accountno,first,last,company,title,city,state,bio,gradYear,phone,email,img FROM user WHERE accountno=?;");
		$pass = $user->execute(array( $_SESSION['user']['accountno'] ));
		$old = $user->fetch(PDO::FETCH_ASSOC);

		// Validate Passwords
		if ($pass && isset($data['pass']) && isset($data['confirm'])) {
			if ($data['pass'] != $data['confirm']) {
				array_push($this->status, 'save-profile-pw');
				$pass = false;
			} elseif ($data['pass'] != '') {
				$sth = $this->db->prepare("UPDATE user SET pass=? WHERE accountno=?;");
				$pass = $sth->execute(array( create_hash($data['pass']), $_SESSION['user']['accountno'] ));
			}
		}

		// Upload photo
		if ( $pass && isset($_FILES['image']) && !$_FILES['image']['error'] ) {

			// Verify appropriate mime types
			if (false === $ext = array_search($_FILES['image']["type"], array(
				'gif0' => "image/gif",
				'jpg1' => "image/jpeg",
				'jpg2' => "image/jpg",
				'jpg3' => "image/pjpeg",
				'png4' => "image/x-png",
				'png5' => "image/png"
			))) {
				$pass = false;
				array_push($this->status, 'image-type-error');
			} else {
				$ext = subster($ext, 0, -1);
				$mail->addAttachment($_FILES['image']['tmp_name'], $data['last'].'-'.$data['first'].'.'.$ext);
			}
		}

		// Update settings
		if ($pass) {
			$sth = $this->db->prepare("UPDATE user SET first=?,last=?,title=?,company=?,city=?,state=?,bio=?,phone=?,email=? WHERE accountno=?;");
			$pass = $sth->execute(array( $data['first'], $data['last'], $data['title'], $data['company'], $data['city'], $data['state'], $data['bio'], $data['phone'], $data['email'], $_SESSION['user']['accountno'] ));
		}
		
		// Re-assign session data
		if ($pass) $pass = $user->execute(array( $_SESSION['user']['accountno'] ));
		if ($pass) $_SESSION['user'] = $user->fetch( PDO::FETCH_ASSOC );

		// Generate Email HTML
		$html  = "<p>The following changes have been made to an ela-app user</p>\r\n";
		$html .= "<table>\r\n";
		$html .= "	<tr><th>Attribute</th><th>Old Version</th><th>New Version</th></tr>\r\n";
		$html .= $this->render_row('First',  'first',   $old, $data);
		$html .= $this->render_row('Last',   'last',    $old, $data);
		$html .= $this->render_row('Title',  'title',   $old, $data);
		$html .= $this->render_row('Company','company', $old, $data);
		$html .= $this->render_row('City',   'city',    $old, $data);
		$html .= $this->render_row('State',  'state',   $old, $data);
		$html .= $this->render_row('Phone',  'phone',   $old, $data);
		$html .= $this->render_row('Email',  'email',   $old, $data);
		$html .= $this->render_row('Bio',    'bio',     $old, $data);
		$html .= "</table>\r\n";
		if ($pass) $pass = $mail->notify('ELA Profile Update: ' . $data['first'] . ' ' . $data['last'], $html);

		return $pass;
	}
	private function render_row( $title, $key, $old, $new ) {
		$out = "\t<tr" . ( $old[$key] != $new[$key] ? ' style="background-color:red;color:white;"' : '' );
		return $out . "><td>{$title}</td><td>{$old[$key]}</td><td>{$new[$key]}</td></tr>\r\n";
	}
	public function send_reset( $user ) {
		$getSTH = $this->db->prepare("SELECT accountno, email, (first || ' ' || last) name FROM `user` WHERE `email`=? OR `user`=? LIMIT 1;");
		if (
			!$getSTH->execute(array( $user, $user )) || 
			false === ($user = $getSTH->fetch( PDO::FETCH_ASSOC ))
		) return false;

		// DB
		$hash = sha1( $user['email'] . config::encryptSTR . uniqid() );
		$setSTH = $this->db->prepare("UPDATE `user` SET `resetHash`=?, `resetExpires`=date('now', '+3 DAYS') WHERE `accountno`=?;");
		if (!$setSTH->execute(array( $hash, $user['accountno'] ))) return false;
		
		// E-Mail
		$mail = new Mailer();
		$html = str_replace('{{HASH}}', $hash, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'reset_email.html'));
		return $mail->sendMsg('ELA-APP: Password Reset', $html, $user['email'], $user['name']);
	}
	public function valid_reset( $hash ) {
		$STH = $this->db->prepare("SELECT * FROM `user` WHERE `resetHash`=? AND `resetExpires` > CURRENT_TIMESTAMP LIMIT 0,1;");
		$STH->execute(array( $hash ));
		return count($STH->fetchAll()) == 1;
	}
	public function pass_reset( $pass, $confirm, $hash ) {
		if (strlen($pass) < 2) throw new Exception(self::RESET_SHORT);
		if ($pass != $confirm) throw new Exception(self::RESET_MISSMATCH);
		if (!$this->valid_reset($hash)) throw new Exception(self::RESET_BAD_HASH);
		$STH = $this->db->prepare("UPDATE user SET pass=?,resetHash=NULL,resetExpires=NULL WHERE resetHash=?;");
		if (!$STH->execute(array( create_hash($pass), $hash ))) throw new Exception(self::DB_ERROR);
		return true;
	}


	// App functions
	public function my_conference_info() {
		$sth = $this->db->prepare("SELECT * FROM (SELECT * FROM attendee WHERE userID=?) a LEFT JOIN event e ON a.eventID=e.eventID LEFT JOIN year y ON a.yearID=y.yearID ORDER BY y.year DESC;");
		$sth->execute(array( $_SESSION['user']['accountno'] ));
		return $sth->fetchAll( PDO::FETCH_ASSOC );
	}
	public function random_user() {
		$sth = $this->db->prepare("SELECT * FROM user WHERE accountno != ? ORDER BY RANDOM() LIMIT 1;");
		$sth->execute(array( $_SESSION['user']['accountno'] ));
		return $sth->fetch( PDO::FETCH_ASSOC );
	}
	public function random_quote() {
		$ret = $this->db->query("SELECT * FROM quote ORDER BY RANDOM() LIMIT 1;")->fetch( PDO::FETCH_ASSOC );
		if ( is_null($ret['author']) ) $ret['author'] = 'Anonymous';
		return $ret;
	}
	public function get_faq() {
		return $this->db->query("SELECT * FROM faq ORDER BY title;")->fetchAll( PDO::FETCH_ASSOC );
	}
}
