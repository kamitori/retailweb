'use strict';

app.controller('ProductController', ['$rootScope', '$scope', '$stateParams', '$http', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Product';
    $scope.template = appHelper.baseURL + '/tpl/products/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    $scope.action = action;
    $scope.button = {
        title: 'List product',
        link: '#/app/products/list'
    };
    $scope.button_add = {
        title: 'Add product',
        link: '#/app/products/add'
    };
    switch (action) {
        case 'list':
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Edit Product';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Product';
            break;
        default:
            $scope.template += 'all.html';
            break;
    }
    if (['add', 'edit'].indexOf(action) != -1) {
        $scope.form = {
            fields: [{
                type: 'hidden',
                name: '_id'
            }, {
                type: 'text',
                label: 'Name',
                name: 'name',
                inputAttr: 'ng-patern="/^[a-zA-Z0-9]{4,10}$/" required="true"',
                validate: {
                    requiredMessage: 'Name must not be empty!',
                    patternMessage: 'Name must be string!'
                }
            }, {
                type: 'select',
                label: 'Category',
                name: 'category',
                inputAttr: 'required="true"',
                options: [],
                validate: {
                    requiredMessage: 'Category must not be empty!'
                }
            }, {
                type: 'number',
                label: 'Price',
                name: 'sell_price'
            }, {
                type: 'textarea',
                label: 'Description',
                name: 'description',
                inputStyle: 'resize: vertical',
                inputAttr: 'ng-patern="/^[a-zA-Z0-9]{25,}$/" required="true" rows="5"',
                validate: {
                    requiredMessage: 'Description must not be empty!',
                    patternMessage: 'Description must be string!'
                }
            },/* {
                type: 'image-upload',
                label: 'Image',
                name: 'image',
                value: 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image',
                inputAttr: 'ngf-validate="{size: {max: \'2MB\', min: \'10B\'} }" name="file"',
                inputStyle: 'max-width: 200px;',
                validate: {
                    patternMessage: 'Image must be valid!',
                }
            }, */{
                type: 'text',
                label: 'Meta title',
                name: 'meta_title'
            }, {
                type: 'text',
                label: 'Meta Desciption',
                name: 'meta_description'
            }],
            title: formTitle,
			options: [],
			productOptions: []
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('products/edit/' + id))
                .success(function(result) {
                    if (result.error === 0) {
                        $scope.form.fields[2].options = result.data.categoryOptions;
                        $scope.form.options = result.data.options;
                        $scope.form.productOptions = result.data.productOptions;
                        $scope.form = appHelper.populateForm($scope.form, result.data);
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/products/add');
                        }, function() {
                            $location.path('/app/products/list');
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
                if ($scope.form.fields[i].name == 'image') {
                    file = $scope.form.fields[i].file;
                } else {
                    data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
                }
            }

            data.options = $scope.form.options;

            $scope.upload(file, data);
        };

        $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('products/update'),
                fields: data
            };
            if (file && file.blobUrl) {
                config.file = file;
            }
            Upload.upload(config).success(function(result, status, headers, config) {
                if (result.error === 1) {
                    toaster.pop('error', 'Error', result.messages.join('<br />'));
                } else if (result.error === 0) {
                    $scope.form.fields[0].value = result.data._id;
                    toaster.pop('success', 'Message', result.message);
                    $location.path('/app/products/edit/' + result.data._id);
                }
            });
        };

		$scope.addOption = function() {
            if (typeof $scope.form.selectedOption == 'undefined') {
                return false;
            }
            $scope.form.options.push({
                'deleted': false,
                'product_id': $scope.form.selectedOption._id,
                'code': $scope.form.selectedOption.code,
                'sku': $scope.form.selectedOption.sku,
                'name': $scope.form.selectedOption.name,
                'unit_price': $scope.form.selectedOption.unit_price,
                'quantity': 1,
                'type': '',
                'required': false,
                'same_parent': false,
            });
		};

        $scope.removeOption = function(optionId) {
            $scope.form.options[ optionId ].deleted = true;
        };

    } else if (action === 'list') {
        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 100,
            columns: [{
                    name: 'Name',
                    field: 'name',
                }, {
                    name: 'Price',
                    field: 'sell_price'
                }, {
                    name: 'Preview',
                    field: 'image',
                    enableSorting: false,
                    enableFiltering: false,
                    cellClass: 'text-center',
                    cellTemplate: '<img ng-if="row.entity.image" style="height: 100px;" src="{{ row.entity.image }}" />'
                }, {
                    name: 'Description',
                    field: 'description'
                }, {
                    name: 'Category',
                    field: 'category'
                }
            ],
            list: appHelper.adminURL('products/list'),
            edit: '#/app/products/edit',
            delete: appHelper.adminURL('products/delete'),
            deleteConfirm: 'Are you sure you want to delete this product?' //optional
        });
    }
}]);
