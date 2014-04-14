<?php

class QuizController extends ApiController {
	public function getAction() {
		$action = $this->request->getUrlElement(1);

		$data = array();

		if ($action == "") {
			$data = $this->db->query("SELECT * FROM event e LEFT JOIN programYear p ON e.programYearID=p.programYearID ORDER BY programYear, name;")->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$sth = $this->db->prepare("SELECT u.* FROM attendee a LEFT JOIN user u ON a.userID = u.accountno WHERE a.eventID=?;");
			if (!$sth->execute(array( $action ))) throw new Exception('Unsupported method', 405);
			$data = $sth->fetchAll(PDO::FETCH_ASSOC);
			for ($i=0; $i < sizeof($data); $i++) unset($data[$i]['pass']);
		}

		return $data;
	}
}
