<?php
	require_once('admin.class.php');
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');

	if ($admin->login_status) die(header('Location: index.php'));
	if ($admin->login_status === false) echo 'login error';
?>

<div class="page-header clearfix">
	<h2>ELA Admin Login</h2>
</div>

<form role="form" method="post">
	<div class="form-group">
		<label for="user">Username</label>
		<input type="text" class="form-control" id="user" placeholder="Username" name="user">
	</div>
	<div class="form-group">
		<label for="pass">Password</label>
		<input type="password" class="form-control" id="pass" placeholder="Password" name="pass">
	</div>
	<button type="submit" class="btn btn-default" name="action" value="login">Submit</button>
</form>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>