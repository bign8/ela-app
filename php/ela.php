<?php

require_once(__dir__ . DIRECTORY_SEPARATOR . 'secure_pass.php');
require_once(__dir__ . DIRECTORY_SEPARATOR . 'mailer.php');

/**
* ELA Main class
*/
class ELA {
	public $status;
	protected $db;

	function __construct() {
		$this->db = new PDO('sqlite:' . implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'db.sqlite3')));
		$this->status = array();
	}

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
		$user = $this->db->prepare("SELECT accountno,first,last,company,title,city,state,bio,gradYear,phone,email FROM user WHERE accountno=?;");
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
				'gif' => "image/gif",
				'jpg' => "image/jpeg",
				'jpg' => "image/jpg",
				'jpg' => "image/pjpeg",
				'png' => "image/x-png",
				'png' => "image/png"
			))) {
				$pass = false;
				array_push($this->status, 'image-type-error');
			} else {
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
		$html .= "	<tr><td>First</td><td>{$old['first']}</td><td>{$data['first']}</td></tr>\r\n";
		$html .= "	<tr><td>Last</td><td>{$old['last']}</td><td>{$data['last']}</td></tr>\r\n";
		$html .= "	<tr><td>Title</td><td>{$old['title']}</td><td>{$data['title']}</td></tr>\r\n";
		$html .= "	<tr><td>Company</td><td>{$old['company']}</td><td>{$data['company']}</td></tr>\r\n";
		$html .= "	<tr><td>City</td><td>{$old['city']}</td><td>{$data['city']}</td></tr>\r\n";
		$html .= "	<tr><td>State</td><td>{$old['state']}</td><td>{$data['state']}</td></tr>\r\n";
		$html .= "	<tr><td>Phone</td><td>{$old['phone']}</td><td>{$data['phone']}</td></tr>\r\n";
		$html .= "	<tr><td>Email</td><td>{$old['email']}</td><td>{$data['email']}</td></tr>\r\n";
		$html .= "	<tr><td>Bio</td><td>{$old['bio']}</td><td>{$data['bio']}</td></tr>\r\n";
		$html .= "</table>\r\n";
		if ($pass) $pass = $mail->notify('ELA Profile Update: ' . $data['first'] . ' ' . $data['last'], $html);

		return $pass;
	}

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

	public function get_image( $user ) {
		return $user['last'] . ',%20' . $user['first'] . '.jpg';
	}

	public function random_quote() {
		$ret = $this->db->query("SELECT * FROM quote ORDER BY RANDOM() LIMIT 1;")->fetch( PDO::FETCH_ASSOC );
		if ( is_null($ret['author']) ) $ret['author'] = 'Anonymous';
		return $ret;
	}
}
