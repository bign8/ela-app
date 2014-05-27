<?php
	require_once(__dir__ . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'index.php');
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>ELA - App</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href="css.css" rel="stylesheet">
</head>
<body data-spy="scroll" data-target=".navbar">
	<?php include('tpl/nav.tpl.html'); ?>

	<div id="wrap" data-ng-app="ela">
		<?php if ($auth) $ela_include('tpl/meet.frame.html'); ?>
		<?php $ela_include('tpl/home.frame.html'); ?>
		<?php if (!$auth) $ela_include('tpl/login.frame.html'); ?>
		<?php $ela_include('tpl/upstream.frame.html'); ?>
		<?php $ela_include('tpl/faq.frame.html'); ?>
		<?php if ($auth) $ela_include('tpl/search.frame.html'); ?>
		<?php if ($auth) $ela_include('tpl/myELA.frame.html'); ?>
		<?php if ($auth) $ela_include('tpl/profile.frame.html'); ?>
	</div><!-- ./wrap -->

	<div id="footer" class="navbar navbar-default navbar-fixed-bottom">
		<div class="container">
			<p class="pull-right social">
				<a href="http://www.linkedin.com/groups?gid=4403897" target="blank">
					<i class="fa fa-linkedin-square"></i>
				</a>
			</p>
			<p class="text-muted credit">
				&copy; <a href="http://upstreamacademy.com">Upstream Academy</a> 
				<?php date_default_timezone_set('America/Denver'); echo date('Y'); ?>
			</p>
		</div>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.14/angular.min.js"></script>
	<script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.10.0.min.js"></script>
	<script src="js.js"></script>
</body>
</html>