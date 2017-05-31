'use strict';

app.controller('AdminController', ['$rootScope', '$scope', '$stateParams', '$http', '$location', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, $location, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Admin';
    $scope.template = appHelper.baseURL + '/tpl/admins/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    formTitle = 'Admin';
    $scope.action = action;
    $scope.button = {
        title: 'List Admin',
        link: '#/app/admins/list'
    };
    $scope.button_add = {
        title: 'Add Admin',
        link: '#/app/admins/add'
    };
    switch (action) {
        case 'list':
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Add Admin';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Admin';
            break;
        default:
            $scope.template += 'all.html';
            break;
    }

    if (['add', 'edit'].indexOf(action) != -1) {
        $scope.form = {
            fields: [{
                type: 'hidden',
                name: 'id'
            }, {
                type: 'email',
                label: 'Email',
                name: 'email',
                inputAttr: 'required',
                validate: {
                    requiredMessage: 'Email must not be empty!',
                }
            }, {
                type: 'text',
                label: 'Name',
                name: 'name',
                inputAttr: 'required',
                validate: {
                    requiredMessage: 'Name must not be empty!',
                }
            }, {
                type: 'password',
                label: 'Password',
                name: 'password',
                inputAttr: 'placeholder="password"'
            }, {
                type: 'password',
                label: 'Confirm password',
                name: 'password_confirm',
                inputAttr: 'placeholder="password"'
            }],
            title: formTitle
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('admins/edit/' + id))
                .success(function(result) {
                    if (result.error === 0) {
                        $scope.form = appHelper.populateForm($scope.form, result.data);
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/admins/add');
                        }, function() {
                            $location.path('/app/admins/list');
                        });
                });
        } else {
            $scope.form.allLoaded = true;
        }

        $scope.save = function() {
            // if (categoryForm.$valid) {
            var file,
                data = {};
            for (var i in $scope.form.fields) {
                if ($scope.form.fields[i].name == 'image') {
                    if (!$scope.form.fields[i].file ) {
                        toaster.pop('error', 'Error', 'Image must not be empty!');
                        return false;
                    }
                    file = $scope.form.fields[i].file;
                } else {
                    data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
                }
            }
            $scope.upload(file, data);
            // }
        };

        $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('admins/update'),
                fields: data
            };
            if (file && file.blobUrl) {
                config.file = file;
            }
            Upload.upload(config).success(function(result, status, headers, config) {
                if (result.error === 1) {
                    toaster.pop('error', 'Error', result.messages.join('<br />'));
                } else if (result.error === 0) {
                    $scope.form.fields[0].value = result.data.id;
                    toaster.pop('success', 'Message', result.message);
                    $location.path('/app/admins/edit/' + result.data.id);
                }
            });
        };
    } else if (action === 'list') {
        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 100,
            columns: [{
                    name: 'Email',
                    field: 'email',
                }, {
                    name: 'Name',
                    field: 'name'
                }],
            list: appHelper.adminURL('admins/list'),
            edit: '#/app/admins/edit',
            delete: appHelper.adminURL('admins/delete'),
            deleteConfirm: 'Are you sure you want to delete this category?' //optional
        });
    }
}]);
