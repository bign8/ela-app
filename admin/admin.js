angular.module('ela-admin', [
	'ela-event',
	'ela-faq',
	'ela-quiz',
	'ela-quote',
	'ela-user',
]);

// ELA Event Module (/admin/event.php)
angular.module('ela-event', ['ela-admin-helpers']).

controller('event-edit', ['$scope', 'API', function ($scope, API) {

	// Program Years
	var PrgmYear = new API('programYear', 'programYearID', function() {
		$scope.activePrgmYear = $scope.prgmYears[ $scope.prgmYears.length - 1 ];
	});
	$scope.prgmYears = PrgmYear.list;

	// Years
	var Year = new API('year');
	$scope.years = Year.list;

	// Events
	var Event = new API('event');
	$scope.events = Event.list;

	// API Save Calls
	$scope.saveYear = Year.set.bind(Year);
	$scope.saveEvent = Event.set.bind(Event);
	$scope.remEvent = Event.rem.bind(Event);
}]);

// ELA Quote Module (/admin/quote.php)
angular.module('ela-quote', ['ela-admin-helpers']).

controller('quote-edit', ['$scope', 'API', function ($scope, API) {
	var Quote = new API('quote');
	$scope.quotes = Quote.list;

	// API Calls
	$scope.update = Quote.set.bind(Quote);
	$scope.remove_item = Quote.rem.bind(Quote);
	$scope.add_item = function () {
		Quote.add($scope.new_quote);
		$scope.new_quote = {};
	};
}]);

// ELA FAQ (/admin/faq.php)
angular.module('ela-faq', ['ela-admin-helpers']).

controller('faq-edit', ['$scope', 'API', function ($scope, API) {
	var FAQ = new API('faq');
	$scope.faqs = FAQ.list;

	// API Calls
	$scope.set_item = FAQ.set.bind(FAQ);
	$scope.rem_item = FAQ.rem.bind(FAQ);
	$scope.add_item = function (item) {
		FAQ.add($scope[item]);
		$scope[item] = {};
	}
}]);

// ELA Quiz (/admin/quiz.php)
angular.module('ela-quiz', ['ela-admin-helpers', 'ui.bootstrap']).

controller('user-quiz', ['$scope', 'API', '$http', function ($scope, API, $http) {

	// Initial (load all users)
	var User = new API('user', 'accountno');
	$scope.users = User.list;
	$scope.view = 'tile';

	// Load events
	$scope.events = [];
	$http.get('../api/quiz/').then(function (res) {
		$scope.events = res.data.data;
		$scope.shuffle();
	});

	// Load users as desired
	$scope.$watch('myEvent', function (value) {
		if (!value || !value.eventID) {
			$scope.users = User.list;
			$scope.shuffle();
		} else {
			$http.get('../api/quiz/' + value.eventID).then(function (res) {
				$scope.users = res.data.data;
				$scope.shuffle();
			});
		}
	});

	// Controls
	$scope.show_me = function (user) {
		alert(user.first + ' ' + user.last + '\n' + user.title + '\n' + user.company);
	};
	$scope.shuffle = function () {
		angular.forEach($scope.users, function (value) {
			value.random = Math.random();
		});
	};

	// Pagination
	$scope.limits = [8,16,32,64,128];
	$scope.limit = $scope.limits[0];
	$scope.page = 1;
}]);

// ELA User (/admin/users.php)
angular.module('ela-user', ['ela-admin-helpers']).

controller('user-edit', ['$scope', 'API', function ($scope, API) {
	$scope.search = '';
	var User = new API('user');
	$scope.users = User.list;
}]);

// ELA Helper Module
angular.module('ela-admin-helpers', []).

factory('API', ['$http', function ($http) { // TODO: improve with browser data cashe
	var cleanup = function (result) { clear_new.call(this); return result.data; };
	var rem_obj = function (item) { this.list.splice(this.list.indexOf(item), 1); };
	var clear_new = function() {
		var len = (this.list || []).length;
		for (var i = 0; i < len; i++) this.list[i].is_new = false;
	};
	var add_obj = function (item, data) {
		item.is_new = true;
		item[ this.id ] = data.success.data;
		this.list.unshift(item);
	};
	var service = function(table, identifier, cb) {
		this.list = [];
		this.table = table;
		this.id = identifier || (table + 'ID'); // standard convention (tablename + ID, ie: faq = faqID)
		this.all().then(angular.extend.bind(undefined, this.list)).then(cb);
	};
	service.prototype = {
		all: function () {
			return $http.get('db/' + this.table).then( cleanup.bind(this) );
		},
		get: function (itemID) {
			return $http.get('db/' + this.table + '/' + itemID).then( cleanup.bind(this) );
		},
		set: function (item) {
			return $http.put('db/' + this.table + '/' + item[ this.id ], item).then( cleanup.bind(this) );
		},
		rem: function (item) {
			return $http.delete('db/' + this.table + '/' + item[ this.id ]).then( cleanup.bind(this) ).then(rem_obj.bind(this, item));
		},
		add: function (item) {
			return $http.post('db/' + this.table, item).then( cleanup.bind(this) ).then(add_obj.bind(this, item));
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
		templateUrl: 'tpl/colEditor.tpl.html',
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
}]).

filter('pagination', function () {
	return function (inputArray, selectedPage, pageSize) {
		var start = (selectedPage-1) * pageSize;
		return inputArray.slice(start, start + pageSize);
	};
});