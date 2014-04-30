<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA FAQ Manager</h2>
</div>

<div data-ng-controller="faq-edit">
	<table class="table" data-ng-cloak>
		<thead>
			<tr>
				<th class="col-sm-2">Title</th>
				<th class="col-sm-10">Body</th>
				<th>Del</th>
			</tr>
		</thead>
		<tbody>
			<tr class="add">
				<td colspan="3">
					<form data-ng-submit="add_item('new_item')" class="add">
						<div class="col-sm-2">
							<input type="text" class="form-control" placeholder="New Title" data-ng-model="new_item.title">
						</div>
						<div class="col-sm-10">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="New Body" data-ng-model="new_item.body">
								<span class="input-group-btn">
									<button class="btn btn-success" type="submit">+</button>
								</span>
							</div>
						</div>
					</form>
				</td>
			</tr>
			<tr data-ng-repeat="faq in faqs | orderBy:['title','body']" data-ng-class="{'info': faq.is_new}">
				<td data-col-editor data-col-field="faq.title" data-save-cb="set_item(faq)">-</td>
				<td data-col-editor data-col-field="faq.body" data-save-cb="set_item(faq)">-</td>
				<td><button type="button" class="close" aria-hidden="true" data-ng-click="rem_item(faq)">&times;</button></td>
			</tr>
		</tbody>
	</table>
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>