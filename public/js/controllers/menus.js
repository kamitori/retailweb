'use strict';

app.controller('MenuController', ['$rootScope', '$scope', '$http', 'toaster', 'dialogs', '$compile', function($rootScope, $scope, $http, toaster, dialogs, $compile) {
    $rootScope.title = 'Menu';
    $scope.baseURL = appHelper.baseURL;

    $scope.menu = {
        html: '',
        group: 'header',
        parent: {},
        parentOptions: [],
        form: {
            id: 0,
            name: '',
            group_name: 'header',
            link: '',
            parent_id: 0,
            order_no: 1
        }
    };

    $http.get(appHelper.adminURL('menus'))
                .success(function(result) {
                    if (result.error === 0) {
                        $scope.menu.parent = result.data.parent;
                        if ($scope.menu.parent.header) {
                            $scope.menu.parentOptions = $scope.menu.parent.header;
                        }
                    }
                    $scope.menu.allLoaded = true;

                    $scope.menu.html = [
                                    '<li class="dd-item dd3-item" data-id="13">',
                                        '<div class="dd-handle dd3-handle">Drag</div>',
                                        '<div class="dd3-content" ng-click="alert(12)">Item 13</div>',
                                    '</li>',
                                    '<li class="dd-item dd3-item" data-id="14">',
                                        '<div class="dd-handle dd3-handle">Drag</div>',
                                        '<div class="dd3-content">Item 14</div>',
                                    '</li>',
                                    '<li class="dd-item dd3-item" data-id="15">',
                                        '<div class="dd-handle dd3-handle">Drag</div>',
                                        '<div class="dd3-content">Item 15</div>',
                                        '<ol class="dd-list">',
                                            '<li class="dd-item dd3-item" data-id="16">',
                                                '<div class="dd-handle dd3-handle">Drag</div>',
                                                '<div class="dd3-content">Item 16</div>',
                                            '</li>',
                                            '<li class="dd-item dd3-item" data-id="17">',
                                                '<div class="dd-handle dd3-handle">Drag</div>',
                                                '<div class="dd3-content">Item 17</div>',
                                            '</li>',
                                            '<li class="dd-item dd3-item" data-id="18">',
                                                '<div class="dd-handle dd3-handle">Drag</div>',
                                                '<div class="dd3-content">Item 18</div>',
                                            '</li>',
                                        '</ol>',
                                    '</li>'
                                ].join("\n");
                });

    $scope.changeGroup = function(group) {
        $scope.menu.group = group;
        if ($scope.menu.parent[ group ]) {
            $scope.menu.parentOptions = $scope.menu.parent[ group ];
        } else {
            $scope.menu.parentOptions = [{
                value: 0,
                text: '*Root'
            }];
        }
    };

    $scope.changeOrder = function() {
        console.log(1212);
    };

    $scope.reset = function() {
        $scope.menu.form = {
            id: 0,
            name: '',
            group_name: 'header',
            link: '',
            parent_id: 0,
            order_no: 1
        };
        $scope.changeGroup('header');
    }

    $scope.reorder = function() {

    };

    $scope.save = function() {
        $http.post(appHelper.adminURL('menus/update'), $scope.menu.form)
            .success(function(result) {
                if (result.error === 0) {
                    $scope.menu.parent = result.data.parent;
                    if ($scope.menu.parent[ $scope.menu.group ]) {
                        $scope.menu.parentOptions = $scope.menu.parent[ $scope.menu.group ];
                    }
                    $scope.menu.form.id = result.data.id;
                    toaster.pop('success', 'Message', result.message);
                } else if (result.error === 1) {
                    toaster.pop('error', 'Error', result.messages.join('<br />'));
                }
            });
    };
}]);

angular.module('app')
    .directive('nestable', ['$compile', function($compile){
        return {
            restrict: 'A',
            require: 'ngModel',
            compile: function(element){
                return function(scope, element, attrs, $ngModel) {
                    scope.$watch(
                        function(scope) {
                            return scope.$eval(attrs.nestable);
                        },
                        function(value) {
                            element.html(value);
                            $compile(element.contents())(scope);
                            element.bind('change', function() {
                                console.log(element);
                                // $ngModel.$setViewValue(element.nestable('serialize'));
                                // scope.$apply();
                            });
                                console.log(element);
                        }
                    );
                };
            }
        }
    }]);