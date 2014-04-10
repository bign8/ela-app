<?php
	require_once(__dir__ . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'index.php');
	$auth = isset( $_SESSION['user'] );
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>ELA - App</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href="css.css" rel="stylesheet">
</head>
<body data-spy="scroll" data-target=".navbar">
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#home">ELA</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="#home">Home</a></li>
					<li><a href="#about">About</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#faq">FAQ</a></li>
							<?php if ($auth): ?><li><a href="#contacts">Contacts</a></li><?php endif; ?>
							<li><a href="#samples">Project Samples</a></li>
							<li class="divider"></li>
							<li class="dropdown-header">Guides</li>
							<li><a href="#hints">Hints + Tips</a></li>
							<li><a href="#desc">Guide Description</a></li>
							<li class="divider"></li>
							<li class="dropdown-header">External Links</li>
							<li><a href="http://ela.upstreamacademy.com/" target="blank">Progress Tracker</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php if ($auth): ?>
						<?php if (isset($_SESSION['admin'])): ?>
							<li><a href="admin/">Admin</a></li>
						<?php endif; ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php echo $_SESSION['user']['first'] . ' ' . $_SESSION['user']['last']; ?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#myela">My ELA</a></li>
								<li><a href="#profile">Profile</a></li>
								<li><a href="index.php?action=logout">Logout</a></li>
							</ul>
						</li>
					<?php else: ?>
						<?php if (isset($_SESSION['admin'])): ?>
							<li><a href="admin/">Admin</a></li>
						<?php endif; ?>
						<li><a href="#login">Login</a></li>
					<?php endif; ?>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>

	<div id="wrap" data-ng-app="ela">
		<?php if ($auth) $ela_include('tpl/meet.frame.html'); ?>

		<?php $ela_include('tpl/home.frame.html'); ?>

		<?php $ela_include('tpl/about.frame.html'); ?>

		<?php $ela_include('tpl/faq.frame.html'); ?>

		<?php if ($auth) $ela_include('tpl/search.frame.html'); ?>

		<?php $ela_include('tpl/samples.frame.html'); ?>

		<?php $ela_include('tpl/hints.frame.html'); ?>

		<?php $ela_include('tpl/desc.frame.html'); ?>

		<?php if (!$auth) $ela_include('tpl/login.frame.html'); ?>

		<?php if ($auth) $ela_include('tpl/myELA.frame.html'); ?>

		<?php if ($auth) $ela_include('tpl/profile.frame.html'); ?>

		<div style="min-height:60px;height:60px"></div><!-- same as a push -->
	</div><!-- ./wrap -->

	<div id="footer" class="navbar navbar-default navbar-fixed-bottom">
		<div class="container">
			<p class="pull-right social">
				<a href="http://www.linkedin.com/groups?gid=4403897" target="blank">
					<i class="fa fa-linkedin-square text-muted"></i>
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