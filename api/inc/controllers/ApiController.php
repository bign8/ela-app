<?php

abstract class ApiController {
	protected $request;
	protected $db;

	public function __construct(Request $req, $db) {
		$this->request = $req;
		$this->db = $db;

		if (isset($_SESSION['user']) && $_SESSION['user'] != null) {
			$this->user = (object) $_SESSION['user'];
		} elseif (isset($_SESSION['admin'])) {
			$this->user = (object) $_SESSION['admin'];
		}
		// validate session?
	}
}
