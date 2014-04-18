<?php

class Request {
	protected $args = array();
	protected $path = array();
	public $verb;
	public $type; // output format
	public $view; // view object

	public function __construct() {
		$this->verb = strtolower($_SERVER['REQUEST_METHOD']);

		// grab and parse pathinfo
		$pathinfo = pathinfo($_SERVER['REDIRECT_URL']);
		$this->path = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
		$this->path = explode('/', $this->path);
		$this->path = array_slice($this->path, 3); // remove _/ela/api

		// handle various of input methods
		$this->parseIncomingParams();

		// initialise json as default format
		$this->type = 'json';
		if (isset($this->args['format'])) $this->type = $this->args['format'];
		if (isset($pathinfo['extension'])) $this->type = $pathinfo['extension'];

		$this->initialiseFormat();

		return true;
	}

	protected function parseIncomingParams() {
		$parameters = array();

		// first of all, pull the GET vars
		if (isset($_SERVER['QUERY_STRING'])) parse_str($_SERVER['QUERY_STRING'], $parameters);

		// now how about PUT/POST bodies? These override what we got from GET
		$body = file_get_contents("php://input");
		$content_type = false;
		if (isset($_SERVER['CONTENT_TYPE'])) $content_type = $_SERVER['CONTENT_TYPE'];
		
		$content_type = strtok($content_type, ';');

		switch ($content_type) {
			case "application/json":
				$body_params = json_decode($body);
				if ($body_params)
					foreach ($body_params as $param_name => $param_value)
						$parameters[$param_name] = $param_value;
				break;

			case "application/x-www-form-urlencoded":
				parse_str($body, $postvars);
				foreach ($postvars as $field => $value) $parameters[$field] = $value;
				break;

			default:
				// we could parse other supported formats here
				break;
		}
		$this->args = $parameters;
	}

	public function initialiseFormat() {
		switch ($this->type) {
			case 'html':
				$this->view = new HtmlView();
				break;
			case 'json':
			default:
				$callback = filter_var($this->getParameter('callback'), FILTER_SANITIZE_STRING);
				if ($callback) {
					$this->view = new JsonpView($callback);
				} else {
					$this->view = new JsonView();
				}
		}
	}

	public function getParameter($param, $default = '') {
		$value = $default;
		if (isset($this->args[$param])) $value = $this->args[$param];
		return $value;
	}

	public function getUrlElement($index, $default = '') {
		$index = (int) $index;
		$element = $default;
		if (isset($this->path[$index])) $element = $this->path[$index];
		return $element;
	}
}
