'use strict';

app.factory('RW', ['$timeout', '$http', 'dialogs', 'toaster', function($timeout, $http, dialogs, toaster) {
    var gridOptions = function(scope, options) {
        options = angular.extend({
                            useExternalFiltering : true,
                            useExternalPagination : true,
                            useExternalSorting : true
                        }, options);
        if (typeof options.gridName == 'undefined') {
            dialogs.error('Error', '<b>gridName</b> must be defined.<br />');
            return false;
        }

        var lastCell,
            filterOptions = {},
            list;


        if (typeof options.paginationOptions == 'undefined') {
            options.paginationOptions = {
                                        pageNumber: 1,
                                        pageSize: 50,
                                        sort: null,
                                        sortName: null
                                    };
        }
        if (typeof options.columns == 'undefined') {
            options.columns = [];
        }
        if (options.delete || options.edit) {
            lastCell = '';
            if (options.edit) {
                lastCell = '<a href="'+ options.edit +'/{{ row.entity.id }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a> '
            }
            if (options.delete) {
                lastCell += '<a ng-click="grid.appScope.delete(row)" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
            }
            options.columns.push({
                name: '',
                field: 'id',
                enableSorting: false,
                enableFiltering: false,
                headerCellClass: 'text-center',
                cellClass: 'text-center',
                cellTemplate: lastCell,
                width: 80
            });
        }
        if (typeof options.rowHeight == 'undefined') {
            options.rowHeight = 50;
        }

        scope.$on("$destroy", function(event) {
            if (angular.isDefined(scope.filterTimeout)) {
                $timeout.cancel(scope.filterTimeout);
            }
        });

        list = function(columns) {
            var data = {
                pagination: options.paginationOptions
            };

            filterOptions = data.search = appHelper.getDataSearch(columns, filterOptions);

            $http.post(options.list, data)
                .success(function(result) {
                    scope[ options.gridName ].data = result.data;
                    scope[ options.gridName ].totalItems = result.total;
                });
        }

        return {
            enableFiltering: true,
            rowHeight: options.rowHeight,
            paginationPageSizes: [50, 100, 150, 200],
            paginationPageSize: 100,
            useExternalFiltering: options.useExternalFiltering,
            useExternalPagination: options.useExternalPagination,
            useExternalSorting: options.useExternalSorting,
            columnDefs: options.columns,
            onRegisterApi: function(gridApi) {
                list();
                scope.gridApi = gridApi;
                if (options.useExternalSorting) {
                    scope.gridApi.core.on.sortChanged(scope, function(grid, sortColumns) {
                        if (sortColumns.length == 0) {
                            options.paginationOptions.sort = null;
                            options.paginationOptions.sortName = null;
                        } else {
                            options.paginationOptions.sort = sortColumns[0].sort.direction;
                            options.paginationOptions.sortName = sortColumns[0].field;
                        }
                        list(this.grid.columns);
                    });
                }
                if (options.useExternalPagination) {
                    scope.gridApi.pagination.on.paginationChanged(scope, function(newPage, pageSize) {
                        options.paginationOptions.pageNumber = newPage;
                        options.paginationOptions.pageSize = pageSize;
                        list();
                    });
                }
                if (options.useExternalFiltering) {
                    scope.gridApi.core.on.filterChanged(scope, function() {
                        var columns = this.grid.columns;
                        if (angular.isDefined(scope.filterTimeout)) {
                            $timeout.cancel(scope.filterTimeout);
                        }
                        scope.filterTimeout = $timeout(function() {
                            list(columns);
                        }, 500);
                    });
                }
            },
            appScopeProvider: {
                delete: function(row) {
                    if (options.delete) {
                        if (typeof row.entity.id == 'undefined') {
                            dialogs.error('Error', '<b>Id</b> must be defined.<br />');
                            return false;
                        }
                        options.delete += '/'+ row.entity.id;
                        if (typeof options.deleteConfirm == 'undefined') {
                            options.deleteConfirm = 'Are you sure you want to delete this record?';
                        }
                        dialogs.confirm('Confirm', options.deleteConfirm, {size: 'md'})
                            .result.then(function() {
                                $http.get(options.delete)
                                        .success(function(result) {
                                            console.log(result)
                                            if (result.error === 0) {
                                                var index = scope[ options.gridName ].data.indexOf(row.entity);
                                                scope[ options.gridName ].data.splice(index, 1);
                                                toaster.pop('success', 'Message', result.message);
                                            } else if (result.error === 1) {
                                                toaster.pop('error', 'Error', result.message);
                                            }
                                        })
                                        .error(function(result) {
                                            var message = result.message || result;
                                            dialogs.error('Error', message);
                                        });
                            }, function() {

                            });
                    }
                }
            }
        };
    }
    return {
        gridOptions: gridOptions
    };
}]);