<?php 
	require_once(implode(DIRECTORY_SEPARATOR, array( __DIR__, 'php', 'ela-admin.php' )));
	$admin->requiresAdmin();
	include('tpl' . DIRECTORY_SEPARATOR . 'header.tpl.html');
?>

<div class="page-header clearfix">
	<h2>ELA Event Manager</h2>
</div>

<div data-ng-app="ela-event">
	
	<div data-ng-controller="event-edit" data-ng-cloak>
		<div class="col-md-12">
			<div class="col-md-6" style="position:absolute;bottom:0;right:0">
				<select 
					class="form-control" 
					data-ng-options="v.programYear for v in prgmYears | orderBy:'programYear'" 
					data-ng-model="activePrgmYear"
				>
					<option value="" disabled> -- CHOOSE ONE -- </option>
				</select>
			</div>
			<h3>Program Year <small>Choose one</small></h3>
		</div>
		<div class="col-md-6">
			<h3>Year In Program <small ng-show="activePrgmYear.programYear">During {{activePrgmYear.programYear}}</small></h3>
			<table class="table" ng-show="filteredYears.length">
				<thead>
					<tr>
						<th class="col-xs-2">Year</th>
						<th class="col-xs-5">Workbook</th>
						<th class="col-xs-5">Calendar</th>
					</tr>
				</thead>
				<tbody>
					<tr data-ng-repeat=" year in filteredYears = (years | filter:{programYearID:activePrgmYear.programYearID}) | orderBy:'year' ">
						<td>Year {{ year.year }}</td>
						<td data-col-editor data-col-field="year.workbook" data-save-cb="saveYear(year)">-</td>
						<td data-col-editor data-col-field="year.calendar" data-save-cb="saveYear(year)">-</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<h3>Events <small ng-show="activePrgmYear.programYear">In {{activePrgmYear.programYear}}</small></h3>
			<table class="table" ng-show="filteredEvents.length">
				<thead>
					<tr>
						<th class="col-xs-3">Name</th>
						<th class="col-xs-4">Date</th>
						<th class="col-xs-5">Link</th>
					</tr>
				</thead>
				<tbody>
					<tr data-ng-repeat=" event in filteredEvents = (events | filter:{programYearID:activePrgmYear.programYearID}) | orderBy:'name' ">
						<td>{{ event.name }}</td>
						<td data-col-editor data-col-field="event.date" data-save-cb="saveEvent(event)">-</td>
						<td data-col-editor data-col-field="event.link" data-save-cb="saveEvent(event)">-</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include('tpl' . DIRECTORY_SEPARATOR . 'footer.tpl.html'); ?>