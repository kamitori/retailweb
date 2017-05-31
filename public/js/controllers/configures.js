'use strict';

app.controller('ConfiguresController', ['$rootScope', '$scope', '$stateParams', '$http', '$location', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, $location, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'Configures';
    $scope.template = appHelper.baseURL + '/tpl/configures/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    formTitle = 'Configures';
    $scope.action = action;
    $scope.button = {
        title: 'List configs',
        link: '#/app/configures/list'
    };
    $scope.button_add = {
        title: 'Add config',
        link: '#/app/configures/add'
    };
    switch (action) {
        case 'list':
            
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Add Config';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Config';
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
                label: 'Key',
                name: 'cf_key',
                inputAttr: 'ng-patern="/^[a-zA-Z0-9]{2,10}$/" required="true"',
                validate: {
                    requiredMessage: 'Key must not be empty!',
                    patternMessage: 'Key must be string!'
                }
            }, {
                type: 'text-editor',
                label: 'Value',
                name: 'cf_value',
                validate: {
                    requiredMessage: 'Value must not be empty!'
                }
            }, {
                type: 'select',
                label: 'Status',
                name: 'status',
                options: [{value:1,text:'On'},{value:0,text:'Off'}],
                value: 1
            }],
            title: formTitle
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('configs/edit/' + id))
                .success(function(result) {
                    //console.log(result);
                    if (result.error === 0) {

                        $scope.form = appHelper.populateForm($scope.form, result.data);
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/configures/add');
                        }, function() {
                            $location.path('/app/configures/list');
                        });
                });
        } else {
            $scope.form.allLoaded = true;
        }

        $scope.save = function() {
            
            // if (configureForm.$valid) {
            var file,
                data = {};
            for (var i in $scope.form.fields) {
                data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
            }
            $scope.upload(file, data);
            // }
        };

        $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('configs/update'),
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
                    $location.path('/app/configures/edit/' + result.data.id);
                }
            });
        };

    } else if (action === 'list') {

        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 100,
            enableCellEditOnFocus: true,
            columns: [{
                    name: 'ID',
                    field: 'id',
                    visible: false
                },
                {
                    name: 'Key',
                    field: 'cf_key',
                    cellTemplate: '<a href="#/app/configures/edit/{{ row.entity.id }}" class="namelink">{{ row.entity.cf_key }}</a>',
                    filter: {
                        placeholder: 'Search by Key'
                    }
                },
                {
                    name: 'Value',
                    field: 'cf_value',
                    cellTemplate: '<div ng-bind-html="row.entity.cf_value"></div>'                 
                }, {
                    name: 'Status',
                    field: 'status',
                    cellTemplate: '<div class="{{row.entity.status == 1 ? \'status-on\' : \'status-off\'}}">{{row.entity.status == 1 ? "On" : "Off"}}</div>',
                    width: '10%'
                }
            ],
            list: appHelper.adminURL('configs/list'),
            edit: '#/app/configures/edit',
            delete: appHelper.adminURL('configs/delete'),
            deleteConfirm: 'Are you sure you want to delete this config?' //optional
        });
        $scope.getCurrentFocus = function(){
          var rowCol = $scope.gridApi.cellNav.getFocusedCell();
          if(rowCol !== null) {
              $scope.currentFocused = 'Row Id:' + rowCol.row.entity.id + ' col:' + rowCol.col.colDef.id;
          }
        }
    }
}]);
