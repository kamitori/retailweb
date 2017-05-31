'use strict';

/* Controllers */
// signin controller
app.controller('SigninFormController', ['$scope', '$http', '$state', '$auth', function($scope, $http, $state, $auth) {
    $scope.admin = {};
    $scope.authError = null;
    $scope.login = function() {
        $scope.authError = null;
        // Try to login
        $auth.submitLogin($scope.admin);
        $scope.$on('auth:login-success', function(ev, data) {
            $state.go('app.dashboard');
        });
        $scope.$on('auth:login-error', function(ev, data) {
            $scope.authError = data.message;
        });
    };
}]);
