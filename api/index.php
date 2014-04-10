<?php

// http://blog.brunoscopelliti.com/building-a-restful-service-with-angularjs-and-php-backend-setup
// http://www.lornajane.net/posts/2012/building-a-restful-php-server-understanding-the-request
// https://github.com/joindin/joind.in/blob/apiv2-removal/src/api-v2/public/index.php

include 'inc/Autoloader.php';
include 'inc/Request.php';

set_exception_handler(function(Exception $e) use (&$request) {
	header("Status: " . $e->getCode(), false, $e->getCode());
	$request->view->error( $e->getMessage() );
});

// Initialize objects
$db = new PDO( 'sqlite:' . implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'db.sqlite3')) );
$request = new Request();

// Dumb authentication
session_start();
if (!isset($_SESSION['user'])) throw new Exception('Authentication Required', 401);

// Process request
$controller_name = ucfirst( $request->getUrlElement(0) ) . 'Controller';
if (class_exists($controller_name)) {
	$controller = new $controller_name($request, $db);
	$action_name = strtolower($request->verb) . 'Action';

	if (method_exists($controller_name, $action_name)) {
		$result = $controller->$action_name();
		$request->view->success( $result );
	} else throw new Exception('Unsupported method', 405);
} else throw new Exception('Invalid endpoint', 404);
