angular.module('ela-quote', []).

controller('quote-edit', ['$scope', '$http', function ($scope, $http) {

	// Initialize data
	$scope.quotes = [];
	$http.get('db/quote').then(function (res) {
		$scope.quotes = res.data;
	});

	// api calls
	$scope.add_item = function () {
		var new_quote = angular.copy($scope.new_quote);
		$http.post('db/quote', {
			author: $scope.new_quote.author.trim(),
			quote: $scope.new_quote.quote.trim()
		}).then(function (res) {
			new_quote.quoteID = res.data.success.data;
			$scope.quotes.unshift(new_quote);
		});
		$scope.new_quote = {};
	}
	function update (quote) {
		$http.put('db/quote/' + quote.quoteID, {
			author: quote.author.trim(),
			quote: quote.quote.trim()
		});
	}
	$scope.remove_item = function (quote) {
		$http.delete('db/quote/' + quote.quoteID).then(function () {
			$scope.quotes.splice($scope.quotes.indexOf(quote), 1);
		});
	};

	// Editing features
	$scope.start_editing = function (item, area) {
		$scope.editing = item;
		$scope.field = area;
		$scope.origional = angular.copy( item );
	};
	$scope.done_editing = function (item) {
		if ($scope.origional !== item && $scope.editing !== null) update(item);
		$scope.editing = null;
	};
	$scope.undo_editing = function (item) {
		$scope.quotes[$scope.quotes.indexOf(item)] = $scope.origional;
		$scope.done_editing($scope.origional);
	};
}]).

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