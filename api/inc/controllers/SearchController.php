<?php

class SearchController extends ApiController {
	public function getAction() {
		$action = $this->request->getUrlElement(1);

		$data = array();
		if ($action == '') {
			$sth  = $this->db->prepare("SELECT accountno, first, last, company, title, city, state, bio, gradYear, phone, email, user, noteID, srcID, destID, note, img FROM user p LEFT JOIN (SELECT * FROM note WHERE srcID=?) n ON p.accountno = n.destID;");
			$sth->execute( array( $this->user->accountno ) );
			$data = $sth->fetchAll( PDO::FETCH_ASSOC );
		} else {
			throw new Exception('Unsupported method', 405);
		}

		return $data;
	}
}
