<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include( to_path('tpl', 'header.tpl.html') );

	$admins = $admin->getAdmins();
	$users = $admin->getUsers();
?>

<div class="page-header clearfix">
	<h2>ELA User Manager</h2>
</div>

<div class="row">
	<div class="col-md-6">
		<h2>Administrator Manager</h2>
		<div class="well">
			<form role="form" method="post" class="form-horizontal">
				<fieldset>
					<legend>Add New Administrator</legend>
					<div class="form-group">
						<label for="user" class="col-sm-2 control-label">Username</label>
						<div class="col-sm-10">
							<input type="text" class="form-control input-sm" id="user" placeholder="Username" name="user" required>
						</div>
					</div>
					<div class="form-group">
						<label for="pass" class="col-sm-2 control-label">Password</label>
						<div class="col-sm-10">
							<input type="password" class="form-control input-sm" id="pass" placeholder="Password" name="pass" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-primary" name="action" value="add-admin">Add Admin</button>
						</div>
					</div>
				</fieldset>
			</form>
			<table class="table">
				<thead>
					<tr>
						<th>Administrator</th>
						<th></th>
						<th>Delete</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($admins as $admin): ?>
						<tr>
							<td>
								<?php echo $admin['user']; ?>
							</td>
							<td>
								<form method="post">
									<input type="hidden" name="admin" value="<?php echo $admin['admin']; ?>">
									<div class="input-group">
										<input type="password" name="pass" placeholder="New Password" class="form-control input-sm" required>
										<span class="input-group-btn">
											<button type="submit" name="action" value="pw-admin" class="btn btn-warning btn-sm">Change</button>
										</span>
									</div>
								</form>
							</td>
							<td>
								<form method="post">
									<input type="hidden" name="admin" value="<?php echo $admin['admin']; ?>">
									<button type="submit" name="action" value="rem-admin" class="close" aria-hidden="true">&times;</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="col-md-6">
		<h2>User Manager</h2>
		<table class="table">
			<tbody>
				<?php foreach ($users as $user): ?>
					<tr>
						<td>
							<?php echo $user['first'] . ' ' . $user['last']; ?><br/>
							<span class="text-muted"><?php echo $user['user']; ?></span>
							<form method="post" action="users.php">
								<input type="hidden" name="accountno" value="<?php echo $user['accountno']; ?>">
								<input type="submit" name="action" value="emulate" class="btn btn-default btn-xs">
							</form>
						</td>
						<td>
							<form method="post">
								<input type="hidden" name="accountno" value="<?php echo $user['accountno']; ?>">
								<div class="input-group">
									<input type="password" name="pass" placeholder="New Password" class="form-control input-sm" required>
									<span class="input-group-btn">
										<button type="submit" name="action" value="pw-user" class="btn btn-warning btn-sm">Change</button>
									</span>
								</div>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
	</div>
</div>

<?php include( to_path('tpl', 'footer.tpl.html') ); ?>