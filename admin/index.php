<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA Admin Home</h2>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="well">
			<h3>
				<div class="pull-right">
					<a href="event.php" class="btn btn-xs btn-info">
						Manage <i class="glyphicon glyphicon-edit"></i>
					</a>
				</div>
				Events
			</h3>
			<p>Add and edit metadata for years and events</p>
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			<h3>
				<a href="quote.php" class="btn pull-right btn-xs btn-info">
					Manage <i class="glyphicon glyphicon-edit"></i>
				</a>
				Quotes
			</h3>
			<p>Add, edit, remove and modify quotes</p>
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			<h3>
				<a href="uploader.php" class="btn pull-right btn-xs btn-info">
					Upload <i class="glyphicon glyphicon-edit"></i>
				</a>
				Update System
			</h3>
			<p>Upload CSV for upcoming year</p>
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			<h3>
				<a href="users.php" class="btn pull-right btn-xs btn-info">
					Manage <i class="glyphicon glyphicon-edit"></i>
				</a>
				Users
			</h3>
			<p>Manage Regular and Administrative Users</p>
		</div>
	</div>
	<!--
	<div class="col-md-4">
		<div class="well">
			Something 7
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			Something 8
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			Something 9
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			Something 10
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			Something 11
		</div>
	</div>
	<div class="col-md-4">
		<div class="well">
			Something 12
		</div>
	</div>
	-->
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>