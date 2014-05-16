"use strict";

// jQuery soft scroll
jQuery.fn.scroll_top = function (cb) {
	if ( this.offset() ) {
		jQuery('html, body').animate({
			scrollTop: parseInt( this.offset().top, 10 )
		}, 500, cb);
	}
};
jQuery('ul.nav a,a.navbar-brand').click( function (e){
	if ( e.target.hash ) {
		jQuery( e.target.hash ).scroll_top(function() {
			if (e.target.hash == '#login') $('#inputUser').focus();
			document.location.hash = e.target.hash;
		});
		e.preventDefault();
	}
});

// Image pre-loader : http://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded
jQuery('#user_image').change(function() {
	if (this.files && this.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#actual_user_image').attr('src', e.target.result);
			$('.img-loader .spinner').hide();
		};
		$('.img-loader .spinner').show();
		reader.readAsDataURL(this.files[0]);
	}
});
jQuery('#profile_reset').click(function () {
	document.profile.reset();
	$('#actual_user_image').attr('src', 'img/full/' + $('#user_last').val() + ', ' + $('#user_first').val() + '.jpg');
});

// angular application
angular.module('ela', [
	'ui.bootstrap'
]).

// need full user to open dialog
controller('meet', ['$scope', '$controller', function ($scope, $controller) {
	angular.extend(this, $controller('search', {$scope: $scope}));
	$scope.open_dlg = function (accountno) {
		for (var i = 0; i < $scope.users.length; i++) {
			if ($scope.users[i].accountno == accountno) return $scope.view($scope.users[i]);
		};
	};
}]).

controller('search', ['$scope', '$http', '$modal', function ($scope, $http, $modal) {

	// Initialize user data
	$scope.users = $scope.filtered_users = [];
	$http.get('api/search').then(function (res) {
		$scope.users = res.data.data;

		angular.forEach($scope.users, function (value) {
			value.bio = value.first + /*' ' + value.Last +*/ ' ' + value.bio;
		});
	});

	// Searching
	$scope.total_rows = function () {
		var tail = ($scope.filtered_users.length == $scope.users.length) ? '' : (' of ' + $scope.users.length);
		return 'Total: ' + $scope.filtered_users.length + tail;
	};

	// Pagination
	$scope.limits = [5,10,25,50,100];
	$scope.limit = $scope.limits[0];
	$scope.page = 1;

	// Order By
	$scope.fields = [
		{field: ['first','last'], disp: 'First Name'},
		{field: ['last','first'], disp: 'Last Name'},
		{field: ['title','first','last'], disp: 'Title'},
		{field: ['company','first','last'], disp: 'Company'},
		{field: ['city','state','first','last'], disp: 'City'},
		{field: ['state','city','first','last'], disp: 'State'}
	];
	$scope.field = $scope.fields[0].field;
	$scope.sort_order = false;

	// Details
	$scope.view = function (user) {
		var instance = $modal.open({
			templateUrl: 'tpl/note.tpl.html',
			controller: ['$scope', 'person', '$modalInstance', function ($scope, person, $modalInstance) {
				$scope.person = person;
				$scope.ok = function () { $modalInstance.close($scope.person); };
				$scope.cancel = function () { $modalInstance.dismiss('cancel'); };
			}],
			resolve: {
				person: function () { return angular.copy(user); }
			},
			windowClass: 'person-notes'
		});
		instance.result.then(function (person) {
			angular.extend(user, person);
			$http.post('api/note', person).then(function (res) {
				if (res.data.success) user.noteID = res.data.data;
			});
		});
	};
}]).

filter('pagination', function () {
	return function (inputArray, selectedPage, pageSize) {
		var start = (selectedPage-1) * pageSize;
		return inputArray.slice(start, start + pageSize);
	};
}).

directive('textAutoScale', function () {
	return {
		restrict: 'C',
		link: function(scope, element, attrs) {
			element.on('keyup', function (e) {
				e.target.style.height = "1px";
    			e.target.style.height = (25+e.target.scrollHeight)+"px";
			});
			scope.$watch(function () { return element.is(':visible'); }, function () { element.keyup(); });
		}
	}
});