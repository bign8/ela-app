<?php

// For Debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Initialize App
session_start();
ob_start("ob_gzhandler");
require_once(__dir__ . DIRECTORY_SEPARATOR . 'ela.php');

// Perform routing for app
$app = new ELA();
switch ( isset($_REQUEST['action']) ? $_REQUEST['action'] : 'none' ) {
	case 'login':
		$result = $app->login( $_POST['user'], $_POST['pass'] );
		if ($result) die(header('Location: index.php#')); // lose post request
		array_push($app->status, 'login-error');
		break;

	case 'logout':
		$app->logout();
		die(header('Location: index.php')); // lose query string
		break;

	case 'profile':
		$result = $app->save_profile( $_POST );
		if ($result) die(header('Location: index.php#profile')); // lose post request
		array_push($app->status, 'profile-error');
		break;
}

// For perfect striped frames
$ela_include = function($path) use ($app, &$dumb_counter) {
	$dumb_counter++;
	echo "<div class=\"color color-$dumb_counter\">";
	require($path);
	echo "</div>";
	$dumb_counter %= 2;
};
