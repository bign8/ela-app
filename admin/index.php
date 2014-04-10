<?php 
	require_once(implode(DIRECTORY_SEPARATOR, array( __DIR__, 'php', 'ela-admin.php' )));
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA Admin Home</h2>
</div>

<p>This is your ELA anministration section.</p>
<ul>
	<li><a href="uploader.php">Upload a CSV</a> of new data.</li>
	<li><a href="quote.php">Create, Read, Update and Delete Quotes</a> that are used on the website.</li>
	<li><a href="event.php">Manage linked documents</a> to various events along with workbook content.s</li>
</ul>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>