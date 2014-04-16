<?php

require_once(implode(DIRECTORY_SEPARATOR, array( __DIR__, 'PHPMailer', 'PHPMailerAutoload.php' )));
require_once(implode(DIRECTORY_SEPARATOR, array( __DIR__, 'config.php' )));

class Mailer extends PHPMailer {
	function __construct() {
		parent::__construct();
		$this->setFrom(config::defaultEmail, config::defaultFrom);

		// For dev purposes
		$this->isSMTP();                        // Set mailer to use SMTP
		$this->Host       = 'smtp.gmail.com';   // Specify main and backup server
		$this->Username   = config::gmail_user; // SMTP username
		$this->Password   = config::gmail_pass; // SMTP password
		$this->SMTPAuth   = true;               // Enable SMTP authentication
		$this->SMTPSecure = 'tls';              // SMTP authentication type
		$this->Port       = 587;                // SMTP com port
	}

	public function notify($subject, $html) {

		// Add Custom Footer to messages
		// $html .= file_get_contents(__DIR__ . '/foot.html');

		$this->addAddress(config::notifyEmail, config::notifyName);
		$this->Subject = $subject;
		$this->msgHTML($html);
		return $this->send();
	}

	public function sendMsg($subject, $html, $to, $name = '') {

		// Add Custom Footer to messages
		// $html .= file_get_contents(__DIR__ . '/foot.html');

		$this->addAddress($to, $name);
		$this->Subject = $subject;
		$this->msgHTML($html);
		return $this->send();
	}
}