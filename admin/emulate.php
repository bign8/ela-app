<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
	$users = $admin->getUsers();
?>

<div class="page-header clearfix">
	<h2>ELA User Emulator</h2>
</div>

<p>Choose <code>emulate</code> on any user to emulate them.</p>

<table class="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Title</th>
			<th>Company</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($users as $user): ?>
			<tr>
				<td><?php echo $user['first'] . ' ' . $user['last']; ?></td>
				<td><?php echo $user['title']; ?></td>
				<td>
					<?php echo $user['company']; ?>
					<form method="post" class="pull-right">
						<input type="hidden" name="accountno" value="<?php echo $user['accountno']; ?>">
						<input type="submit" name="action" value="emulate" class="btn btn-default btn-xs">
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<!-- <pre>
<?php
	print_r($users);
?>
</pre> -->

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>