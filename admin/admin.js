angular.module('ela-admin', [
	'ela-search',
	'ela-event',
]);

angular.module('ela-event', ['ela-admin-helpers']).

controller('event-edit', ['$scope', 'API', function ($scope, API) {

	// Program Years
	var PrgmYear = new API('programYear', 'programYearID', function() {
		$scope.activePrgmYear = $scope.prgmYears[ $scope.prgmYears.length - 1 ];
	});
	$scope.prgmYears = PrgmYear.list;

	// Years
	var Year = new API('year', 'yearID');
	$scope.years = Year.list;

	// Events
	var Event = new API('event', 'eventID');
	$scope.events = Event.list;

	// API Save Calls
	$scope.saveYear = Year.set.bind(Year);
	$scope.saveEvent = Event.set.bind(Event);
}]);

angular.module('ela-quote', ['ela-admin-helpers']).

controller('quote-edit', ['$scope', 'API', function ($scope, API) {
	var Quote = new API('quote', 'quoteID');
	$scope.quotes = Quote.list;

	// API Calls
	$scope.update = Quote.set.bind(Quote);
	$scope.remove_item = Quote.rem.bind(Quote);
	$scope.add_item = function () {
		Quote.add($scope.new_quote);
		$scope.new_quote = {};
	};
}]);

angular.module('ela-admin-helpers', []).

factory('API', ['$http', function ($http) { // TODO: improve with browser data cashe
	var cleanup = function (result) { return result.data; };
	var rem_obj = function (item) { this.list.splice(this.list.indexOf(item), 1); };
	var add_obj = function (item, data) {
		item[ this.id ] = data.success.data;
		this.list.unshift(item);
	};
	var service = function(table, identifier, cb) {
		this.list = [];
		this.base = table;
		this.id = identifier;
		this.all().then(angular.extend.bind(undefined, this.list)).then(cb);
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
			return $http.delete('db/' + this.base + '/' + item[ this.id ]).then( cleanup ).then(rem_obj.bind(this, item));
		},
		add: function (item) {
			return $http.post('db/' + this.base, item).then( cleanup ).then(add_obj.bind(this, item));
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