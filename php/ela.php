<?php
require_once(__dir__ . DIRECTORY_SEPARATOR . 'secure_pass.php');
require_once(implode(DIRECTORY_SEPARATOR, array( __dir__, 'PHPMailer', 'PHPMailerAutoload.php' )));

/**
* ELA Main class
*/
class ELA {
	public $status;
	protected $db;

	function __construct() {
		$this->db = new PDO('sqlite:' . __dir__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db.sqlite3');
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
		$user = $this->db->prepare("SELECT accountno, first, last, company, title, city, state, bio, gradYear, email FROM user WHERE accountno=?;");
		$pass = $user->execute(array( $_SESSION['user']['accountno'] ));
		$old_data = $user->fetch( PDO::FETCH_ASSOC );

		// Init mail object
		$mail = new PHPMailer;
		$mail->setFrom(config::defaultEmail, config::defaultFrom);
		$mail->isHTML(true);

		// Upload photo
		if (	
			$pass &&
			isset($_FILES['image']) &&
			!$_FILES['image']['error']
		) {
			// Build image path
			$start = __dir__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
			$new = DIRECTORY_SEPARATOR . ucfirst($data['last']) . ', ' . ucfirst($data['first']) . '.jpg';
			$image_path = $start . 'orig' . $new;

			// Verify appropriate mime types
			$allowed_types = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
			$pass = in_array($_FILES['image']["type"], $allowed_types);
			if (!$pass) array_push($this->status, 'image-type-error');

			// Upload the dang thing
			if ($pass) $pass = move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
		}

		// Update settings
		if ($pass) {
			$sth = $this->db->prepare("UPDATE user SET first=?, last=?, title=?, company=?, city=?, state=?, bio=? WHERE accountno=?;");
			$pass = $sth->execute(array( $data['first'], $data['last'], $data['title'], $data['company'], $data['city'], $data['state'], $data['bio'], $_SESSION['user']['accountno'] ));
		}
		
		// Re-assign session data
		if ($pass) {
			$user->execute(array( $_SESSION['user']['accountno'] ));
			$_SESSION['user'] = $user->fetch( PDO::FETCH_ASSOC );
		}

		// Generate Email HTML
		$html = "They changed some stuff...";

		// Email profile change to UA...
		$this->addAddress('nwoods@azworld.com', 'Nathan Woods');
		$this->Subject   = 'ELA Profile Update: ' . $data['first'] . ' ' . $data['last'];
		$this->Body      = $html;
		$this->AltBody   = strip_tags($html);
		if (isset($image_path) && file_exists($image_path)) $mail->addAttachment($image_path);
		if ($pass) $pass = $this->send();

		// Cleanup and respond accordingly
		if (isset($image_path) && file_exists($image_path)) unlink($image_path); // delete image
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
