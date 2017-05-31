'use strict';

app.controller('BannerController', ['$rootScope', '$scope', '$stateParams', '$http', '$location', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, $location, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Banner';
    $scope.template = appHelper.baseURL + '/tpl/banners/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    formTitle = 'Banner';
    $scope.action = action;
    $scope.button = {
        title: 'List banner',
        link: '#/app/banners/list'
    };
    $scope.button_add = {
                title: 'Add banner',
                link: '#/app/banners/add'
    };
    switch (action) {
        case 'list':
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Add Banner';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Banner';
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
                type: 'image-upload',
                label: 'Image',
                name: 'image',
                value: 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image',
                inputAttr: 'ngf-validate="{size: {max: \'2MB\', min: \'10B\'} }" name="file" required',
                inputStyle: 'max-width: 200px;',
                validate: {
                    requiredMessage: 'Image must not be empty!',
                    patternMessage: 'Image must be valid!',
                }
            }, {
                type: 'text',
                label: 'Link',
                name: 'link'
            }, {
                type: 'select',
                label: 'Position',
                name: 'position',
                options: [{value:1,text:'Home left'},{value:2,text:'Home right'},{value:3,text:'Main backgound'},{value:4,text:'Extra'},{value:5,text:'Small Banner in Cart'}],
                value: 1
            }, {
                type: 'number',
                label: 'Order',
                name: 'order_no'
            }],
            title: formTitle
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('banners/edit/' + id))
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
                            $location.path('/app/banners/add');
                        }, function() {
                            $location.path('/app/banners/list');
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
                    if (action == 'add') {
                        if (!$scope.form.fields[i].file ) {
                            toaster.pop('error', 'Error', 'Image must not be empty!');
                            return false;
                        }
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
                url: appHelper.adminURL('banners/update'),
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
                    $location.path('/app/banners/edit/' + result.data.id);
                }
            });
        };
    } else if (action === 'list') {
        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 60,
            columns: [{
                    name: 'Banner',
                    field: 'image',
                    enableSorting: false,
                    enableFiltering: false,
                    cellClass: 'text-center',
                    cellTemplate: '<img ng-if="row.entity.image" style="height: 100px;" src="{{ row.entity.image }}" class="image-thumbs" />'
                },{
                    name: 'Position',
                    field: 'position'
                },{
                    name: 'Link',
                    field: 'link'
                },{
                    name: 'Order',
                    field: 'order_no'
                }],
            list: appHelper.adminURL('banners/list'),
            edit: '#/app/banners/edit',
            delete: appHelper.adminURL('banners/delete'),
            deleteConfirm: 'Are you sure you want to delete this category?' //optional
        });
    }
}]);
