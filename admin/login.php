<?php
	require_once('admin.class.php');
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
	if ($admin->login_status) die(header('Location: index.php'));
?>

<div class="page-header clearfix">
	<h2>ELA Admin Login</h2>
</div>

<div class="col-md-6 col-md-offset-3">
	<?php if ($admin->login_status === false): ?>
		<div class="alert alert-danger">
			<strong>Oh snap!</strong> Change a few things up and try authenticating again.
		</div>
	<?php endif; ?>
	<form role="form" method="post" action="login.php">
		<div class="form-group">
			<label for="user">Username</label>
			<input autofocus type="text" class="form-control" id="user" placeholder="Username" name="user" 
				value="<?php echo isset($_REQUEST['user']) ? $_REQUEST['user'] : ''; ?>">
		</div>
		<div class="form-group">
			<label for="pass">Password</label>
			<input type="password" class="form-control" id="pass" placeholder="Password" name="pass" 
				value="<?php echo isset($_REQUEST['pass']) ? $_REQUEST['pass'] : ''; ?>">
		</div>
		<button type="submit" class="btn btn-default" name="action" value="login">Submit</button>
	</form>
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>