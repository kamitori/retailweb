'use strict';

app.controller('PageController', ['$rootScope', '$scope', '$stateParams', '$http', '$location', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, $location, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Page';
    $scope.template = appHelper.baseURL + '/tpl/pages/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    formTitle = 'Page';
    $scope.action = action;
    $scope.button = {
        title: 'List Page',
        link: '#/app/pages/list'
    };
    $scope.button_add = {
        title: 'Add Page',
        link: '#/app/pages/add'
    };
    switch (action) {
        case 'list':
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Add Page';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Page';
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
                type: 'text',
                label: 'Name',
                name: 'name',
                inputAttr: 'required',
                validate: {
                    requiredMessage: 'Name must not be empty!',
                }
            }, {
                type: 'select',
                label: 'Category',
                name: 'category_id',
                options:[]
            }, {
                type: 'text-editor',
                label: 'Summary',
                name: 'summary'
            }, {
                type: 'text-editor',
                label: 'Content',
                name: 'content'
             
            }, {
                type: 'number',
                label: 'Order',
                name: 'order_no'
            }, {
                type: 'image-upload',
                label: 'Image',
                name: 'image',
                value: 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image',
                inputAttr: 'ngf-validate="{size: {max: \'2MB\', min: \'10B\'} }" name="file"',
                inputStyle: 'max-width: 200px;',
                validate: {
                    patternMessage: 'Image must be valid!',
                }
            }, {
                type: 'text',
                label: 'Meta title',
                name: 'meta_title'
            }, {
                type: 'text',
                label: 'Meta Desciption',
                name: 'meta_description',
            }

            ],
            title: formTitle
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('pages/edit/' + id))
                .success(function(result) {
                    if (result.error === 0) {
                        $scope.form.fields[2].options = result.data.categoryOptions;
                        delete(result.data.categoryOptions);
                        $scope.form = appHelper.populateForm($scope.form, result.data);
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/pages/add');
                        }, function() {
                            $location.path('/app/pages/list');
                        });
                });
        } else {
            $http.get(appHelper.adminURL('categories/get-options'))
                    .success(function(result) {
                        if (result.error === 0) {
                            $scope.form.fields[2].options = result.data;
                        }
                        $scope.form.allLoaded = true;
                    });
        }

        $scope.save = function() {
            var file,
                data = {};
            for (var i in $scope.form.fields) {
                if ($scope.form.fields[i].name == 'image')
                    file = $scope.form.fields[i].file;
                else
                    data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
            }
            $scope.upload(file, data);
        };
         $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('pages/update'),
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
                    $location.path('/app/pages/edit/' + result.data.id);
                }
            });
        };

    } else if (action === 'list') {
        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 100,
            columns: [{
                    name: 'Name',
                    field: 'name',
                }, {
                    name: 'Order',
                    field: 'order_no'
                }],
            list: appHelper.adminURL('pages/list'),
            edit: '#/app/pages/edit',
            delete: appHelper.adminURL('pages/delete'),
            deleteConfirm: 'Are you sure you want to delete this category?' //optional
        });
    }
}]);
