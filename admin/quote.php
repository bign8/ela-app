<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA Quote Manager</h2>
</div>

<div data-ng-app="ela-quote">
	<div data-ng-controller="quote-edit">
		<table class="table" data-ng-cloak>
			<thead>
				<tr>
					<th class="col-sm-2">Author</th>
					<th class="col-sm-10">Quote</th>
					<th>Del</th>
				</tr>
			</thead>
			<tbody>
				<tr class="add">
					<td colspan="3">
						<form data-ng-submit="add_item()" class="add">
							<div class="col-sm-2">
								<input type="text" class="form-control" placeholder="New Author" data-ng-model="new_quote.author">
							</div>
							<div class="col-sm-10">
								<div class="input-group">
									<input type="text" class="form-control" placeholder="New Quote" data-ng-model="new_quote.quote">
									<span class="input-group-btn">
										<button class="btn btn-success" type="submit">+</button>
									</span>
								</div>
							</div>
						</form>
					</td>
				</tr>
				<tr data-ng-repeat="quote in quotes | orderBy:['author','quote']">
					<td data-col-editor data-col-field="quote.author" data-save-cb="update(quote)">-</td>
					<td data-col-editor data-col-field="quote.quote" data-save-cb="update(quote)">-</td>
					<td><button type="button" class="close" aria-hidden="true" data-ng-click="remove_item(quote)">&times;</button></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>