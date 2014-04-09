<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Quote Editor</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body data-ng-app="ela-event">
	
	<div class="container" data-ng-controller="event-edit" data-ng-cloak>
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
			<h2>Program Year <small>Choose one</small></h2>
		</div>
		<div class="col-md-6">
			<h2>Year In Program <small ng-show="activePrgmYear.programYear">During {{activePrgmYear.programYear}}</small></h2>
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
			<h2>Events <small ng-show="activePrgmYear.programYear">In {{activePrgmYear.programYear}}</small></h2>
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

	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.14/angular.min.js"></script>
	<script src="event.js"></script>
</body>
</html>