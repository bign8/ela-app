<?php

class JsonView extends ApiView {
	public function render($content) {
		$this->header();
		echo $this->build($content);
		return true;
	}

	// print header data
	protected function header() {
		header('Content-Type: application/json; charset=utf8');
	}

	// Builds output, can be used by JSON and JSONP
	protected function build($content) {
		return json_encode($content, JSON_NUMERIC_CHECK);
	}
}
