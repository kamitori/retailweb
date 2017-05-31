'use strict';

app.controller('OptionController', ['$rootScope', '$scope', '$stateParams', '$http', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Option';
    $scope.template = appHelper.baseURL + '/tpl/options/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    $scope.action = action;
    $scope.button = {
        title: 'List option',
        link: '#/app/options/list'
    };
    $scope.button_add = {
        title: 'Add option',
        link: '#/app/options/add'
    };
    switch (action) {
        case 'list':
            
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Edit Option';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Option';
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
                inputAttr: 'ng-patern="/^[a-zA-Z0-9]{4,10}$/" required="true"',
                validate: {
                    requiredMessage: 'Name must not be empty!',
                    patternMessage: 'Name must be string!'
                }
            }, {
                type: 'select',
                label: 'Group',
                name: 'option_group',
				options: []
            }, {
                type: 'number',
                label: 'Price',
                name: 'price'
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
            }],
            title: formTitle,
			sold_by: '',
			oum: '',
			listUnit: [],
			unit: [],
			unitdetail: []
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('options/edit/' + id))
                .success(function(result) {
                    if (result.error === 0) {
						//console.log(result.data);
                        $scope.form.fields[2].options = result.data.group;

                        $scope.form.unit = result.data.unit;
						
						$scope.form.listUnit = result.data.listUnit;

						$scope.form.sold_by = result.data.sold_by;
						$scope.selectUnit(result.data.sold_by);
						$scope.form.oum = result.data.oum;
						
                        $scope.form = appHelper.populateForm($scope.form, result.data);
						//console.log($scope.form);
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/options/add');
                        }, function() {
                            $location.path('/app/options/list');
                        });
                });
        } else {
            $http.get(appHelper.adminURL('configs/get-option-group'))
				.success(function(result) {
					//console.log('result.data'+result.data);
					if (result.error === 0) {
						$scope.form.fields[2].options = result.data;
					}
					$scope.form.allLoaded = true;
			});
            $http.get(appHelper.adminURL('configs/get-list-unit'))
				.success(function(result) {
					//console.log('result.data'+result.data);
					if (result.error === 0) {
						$scope.form.listUnit = result.data.listUnit;
						$scope.form.unit = result.data.unit;
					}
					$scope.form.allLoaded = true;
			});
        }

        $scope.save = function() {
            // if (optionForm.$valid) {
            var file,
                data = {};
            for (var i in $scope.form.fields) {
                if ($scope.form.fields[i].name == 'image') {
                    /*if (!$scope.form.fields[i].file.$valid && $scope.form.fields[i].file.$error) {
                        return false;
                    }*/
                    file = $scope.form.fields[i].file;
                } else {
                    data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
                }
            }
			
			data['sold_by'] = $scope.form.sold_by;
			data['oum'] = $scope.form.oum;
			
            $scope.upload(file, data);
            // }
        };

        $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('options/update'),
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
                    $location.path('/app/options/edit/' + result.data.id);
                }
            });
        };
		
		$scope.selectUnit = function(name) {
			//console.log('name:'+name);
			var list_unit = $scope.form.listUnit;			
			var unit_detail = new Array();
			var arr_detail = new Array();
			for (var i in list_unit)
			{
				if(list_unit[i]['name'] == name)
				{
					unit_detail = list_unit[i]['data'];
					break;
				}
			}			
			for (var i in unit_detail)
			{
				var tmp = new Array();
				tmp['value'] = unit_detail[i];
				tmp['text'] = unit_detail[i];
				arr_detail.push(tmp);
			}
			$scope.form.unitdetail = arr_detail;
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
                    field: 'price'
                }, {
                    name: 'Sold By',
                    field: 'sold_by'
                }, {
                    name: 'OUM',
                    field: 'oum'
                }, {
                    name: 'Preview',
                    field: 'image',
                    enableSorting: false,
                    enableFiltering: false,
                    cellClass: 'text-center',
                    cellTemplate: '<img ng-if="row.entity.image" style="height: 100px;" src="{{ row.entity.image }}" />'
                }, {
                    name: 'Group',
                    field: 'option_group_text'
                }
            ],
            list: appHelper.adminURL('options/list'),
            edit: '#/app/options/edit',
            delete: appHelper.adminURL('options/delete'),
            deleteConfirm: 'Are you sure you want to delete this option?' //optional
        });
    }
}]);
