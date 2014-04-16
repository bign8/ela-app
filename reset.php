<?php 
	require_once(__dir__ . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'index.php');

	if (!isset($_REQUEST['hash']) || !$app->valid_reset($_REQUEST['hash'])) die(header('Location: .#login'));
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>ELA - App: Reset Password</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href="css.css" rel="stylesheet">
</head>
<body data-spy="scroll" data-target=".navbar">
	<?php include('tpl/nav.tpl.html'); ?>

	<div id="wrap">
		<?php $ela_include('tpl/reset.frame.html'); ?>

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
</body>
</html>