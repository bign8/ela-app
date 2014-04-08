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

		// Upload photo
		if ( isset($_FILES['image']) && !$_FILES['image']['error'] ) {

			// Verify appropriate mime types
			$allowed_types = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
			$pass = in_array($_FILES['image']["type"], $allowed_types);
			if (!$pass) array_push($this->status, 'image-type-error');

			// Upload the dang thing
			$image_path = implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', 'img', 'orig', $data['last'].'-'.$data['first'].'.jpg' ));
			if ($pass) $pass = move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
			$mail->addAttachment($image_path);
		}

		// Update settings
		if ($pass) {
			$sth = $this->db->prepare("UPDATE user SET first=?, last=?, title=?, company=?, city=?, state=?, bio=? WHERE accountno=?;");
			$pass = $sth->execute(array( $data['first'], $data['last'], $data['title'], $data['company'], $data['city'], $data['state'], $data['bio'], $_SESSION['user']['accountno'] ));
		}
		
		// Re-assign session data
		if ($pass) $pass = $user->execute(array( $_SESSION['user']['accountno'] ));
		if ($pass) $_SESSION['user'] = $user->fetch( PDO::FETCH_ASSOC );

		// Generate Email HTML
		$html = "They changed some stuff...";
		if ($pass) $pass = $mail->notify('ELA Profile Update: ' . $data['first'] . ' ' . $data['last'], $html);

		// Cleanup and respond accordingly
		if ($mail->attachmentExists()) unlink($image_path); // delete image
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
