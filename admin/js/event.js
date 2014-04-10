angular.module('ela-event', []).

controller('event-edit', ['$scope', 'API', function ($scope, API) {

	// Program Years
	$scope.prgmYears = [];
	var PrgmYear = new API('programYear', 'programYearID');
	PrgmYear.all().then(function (data) {
		$scope.prgmYears = data;
		$scope.activePrgmYear = data[data.length-1]; // set default as last
	});

	// Years
	$scope.years = [];
	var Year = new API('year', 'yearID');
	Year.all().then(function (data) { $scope.years = data; });

	// Events
	$scope.events = [];
	var Event = new API('event', 'eventID');
	Event.all().then(function (data) { $scope.events = data; });

	// Save Controllers
	$scope.saveYear = Year.set.bind(Year);
	$scope.saveEvent = Event.set.bind(Event);
}]).

factory('API', ['$http', function ($http) { // TODO: improve with browser data cashe
	var cleanup = function (result) { return result.data; }
	var service = function(table, identifier) {
		this.base = table;
		this.id = identifier;
	};
	service.prototype = {
		all: function () {
			return $http.get('db/' + this.base).then( cleanup );
		},
		get: function (itemID) {
			return $http.get('db/' + this.base + '/' + itemID).then( cleanup );
		},
		set: function (item) {
			return $http.put('db/' + this.base + '/' + item[ this.id ], item).then( cleanup );
		},
		rem: function (item) {
			return $http.delete('db/' + this.base + '/' + item[ this.id ]).then( cleanup );
		},
		add: function (item) {
			return $http.post('db/' + this.base, item).then( cleanup );
		}
	};
	return service;
}]).

directive('colEditor', function () {
	return {
		replace: true,
		scope: {
			colField: '=',
			saveCb: '&'
		},
		templateUrl: 'tpl/event.colEditor.tpl.html',
		link: function (scope, elem, attrs) {
			var origional = null;
			scope.active = false;
			scope.start_editing = function () {
				origional = angular.copy(scope.colField);
				scope.active = true;
			};
			scope.done_editing = function () {
				if (scope.active && scope.colField != origional) scope.saveCb();
				scope.active = false;
			};
			scope.undo_editing = function () {
				scope.colField = origional;
				scope.done_editing();
			};
		}
	};
}).

directive('editEscape', function () {
	var ESCAPE_KEY = 27;
	return function (scope, elem, attrs) {
		elem.bind('keydown', function (event) {
			if (event.keyCode === ESCAPE_KEY) 
				scope.$apply(attrs.editEscape);
		});
	};
}).

directive('editFocus', ['$timeout', function ($timeout) {
	return function (scope, elem, attrs) {
		scope.$watch(attrs.editFocus, function (newVal) {
			if (newVal) $timeout(function () {
				elem[0].focus();
			}, 0, false);
		});
	};
}]);
