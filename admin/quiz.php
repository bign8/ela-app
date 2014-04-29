<?php 
	require_once('admin.class.php');
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA User Quizzer</h2>
</div>

<div data-ng-controller="user-quiz">

	<div class="form-group row">
		<div class="col-md-2 hidden-xs">
			<div class="btn-group">
				<a type="button" class="btn btn-default" ng-class="{active:view=='tile'}" ng-click="view='tile'">
					<span class="glyphicon glyphicon-th-large"></span>
				</a>
				<a type="button" class="btn btn-default" ng-class="{active:view=='list'}" ng-click="view='list'">
					<span class="glyphicon glyphicon-list"></span>
				</a>
			</div>
			<a type="button" class="btn btn-default" ng-click="shuffle()">
				<span class="glyphicon glyphicon-refresh"></span>
			</a>
		</div>

		<div class="col-md-3">
			<div class="input-group">
				<label class="input-group-addon" for="myEvent">Event: </label>
				<select class="form-control" ng-model="myEvent" id="myEvent" 
					ng-options="e.name group by e.programYear for e in events | orderBy:['-programYear','name']"
				>
					<option value="">&mdash; all events &mdash;</option>
				</select>
			</div>
		</div>

		<div class="col-md-5">
			<div class="input-group">
				<label class="input-group-addon" for="query">Search: </label>
				<input type="text" id="query" class="form-control" ng-model="search_str">
			</div>
		</div>

		<div class="hidden-xs col-md-2" ng-hide="filtered.length < 8">
			<div class="input-group">
				<span class="input-group-addon">Page Size: </span>
				<select data-ng-model="limit" class="form-control" id="search_page" data-ng-options="x for x in limits">
					<option value="" disabled>Per Page...</option>
				</select>
			</div>
		</div>
	</div>

	<div data-ng-switch="view">
		<div data-ng-switch-when="tile">
			<div class="row">
				<span class="col-md-3" data-ng-repeat="user in (filtered_users = (users | filter:search_str)) | orderBy:'random' | pagination:page:limit">
					<div class="thumbnail" data-ng-click="show_me(user)">
						<img data-ng-src="http://upstreamacademy.com/images/ELAApp/{{user.img}}" class="img-rounded" height="100" 
							data-ng-attr-title="{{user.first}} {{user.last}}"
							data-ng-attr-alt="{{user.first}} {{user.last}}" alt="John Doe" />
					</div>
				</span>
			</div>
			<div class="text-center clearfix" data-ng-show="num_pages !== 1"><!-- pager -->
				<span 
					data-pagination
					data-total-items="filtered_users.length"
					data-items-per-page="limit"
					data-page="page"
					data-max-size="5"
					data-boundary-links="true"
					data-previous-text="&lsaquo;"
					data-next-text="&rsaquo;"
					data-first-text="&laquo;"
					data-last-text="&raquo;"
					data-num-pages="num_pages">
				</span>
			</div>
		</div>
		<div data-ng-switch-when="list">
			<table class="table" data-ng-cloak>
				<thead>
					<tr>
						<th>Contact</th>
						<th style="width:75%" class="hidden-xs">
							<small class="pull-right text-right" style="font-weight:normal" data-ng-bind="total_rows()"></small>
							Bio
						</th>
					</tr>
				</thead>
				<tbody>
					<tr data-ng-repeat="user in (filtered_users = (users | filter:search_str)) | pagination:page:limit"
						data-ng-click="view(user)" >
						<td>
							<div class="center-cropped pull-left img-rounded">
								<img data-ng-src="http://upstreamacademy.com/images/ELAApp/{{user.img}}" class="img-rounded" height="100" 
								data-ng-attr-title="{{user.first}} {{user.last}}"
								data-ng-attr-alt="{{user.first}} {{user.last}}" alt="John Doe" />
							</div>
							<div class="pull-right">
								<a data-ng-href="tel:{{user.phone}}" data-ng-show="user.phone"><i class="glyphicon glyphicon-phone-alt"></i></a>
								<a data-ng-href="mailto:{{user.email}}" data-ng-show="user.email"><i class="glyphicon glyphicon-envelope"></i></a>
							</div>
							<strong>
								<span data-ng-bind="user.first">John</span>&nbsp;
								<span data-ng-bind="user.last">Doe</span>&nbsp;
								<i class="glyphicon glyphicon-comment" data-ng-show="user.note"></i>
							</strong><br/>
							<small data-ng-bind="user.title">Intern</small><br/>
							<span class="text-muted">
								<span data-ng-bind="user.company">Temporary INC.</span><br/>
								<span data-ng-bind="user.city">Two Dot</span>,&nbsp;<span data-ng-bind="user.state">MT</span>
							</span>
						</td>
						<td data-ng-bind="user.bio" class="hidden-xs">was an amazing ...</td>
					</tr>
				</tbody>
			</table>
			<div class="text-center" data-ng-show="num_pages !== 1"><!-- pager -->
				<span 
					data-pagination
					data-total-items="filtered_users.length"
					data-items-per-page="limit"
					data-page="page"
					data-max-size="5"
					data-boundary-links="true"
					data-previous-text="&lsaquo;"
					data-next-text="&rsaquo;"
					data-first-text="&laquo;"
					data-last-text="&raquo;"
					data-num-pages="num_pages">
				</span>
			</div>
		</div>
	</div>
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>
