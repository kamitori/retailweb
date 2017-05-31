'use strict';
function printThisCode(str){
    $(".app-content ").addClass('hidden-print');
    $(".modal-body ").addClass('hidden-print');
    $(".modal-footer ").addClass('hidden-print');
    $(".btn ").addClass('hidden-print');
    $(".hide-data ").addClass('hidden-print');    
    $('.print-all').addClass('hidden-print');    
    $("#"+str).removeClass('hidden-print');
    $("#header_"+str).removeClass('hidden-print');
    $("#content_"+str).removeClass('hidden-print');
    window.print();
};
function printAllCode(){
    $(".app-content ").addClass('hidden-print');
    $(".modal-body ").addClass('hidden-print');
    $(".modal-footer ").addClass('hidden-print');
    $(".btn ").addClass('hidden-print');
    $(".hide-data ").addClass('hidden-print');
    $('.print-all').css('display','block !important');
    window.print();    
}
app.controller('VoucherController', ['$rootScope', '$scope', '$stateParams', '$http', '$location', 'Upload', 'toaster', 'dialogs', 'uiGridConstants', 'RW', function($rootScope, $scope, $stateParams, $http, $location, Upload, toaster, dialogs, uiGridConstants, RW) {
    $rootScope.title = 'vouchers';
    $scope.template = appHelper.baseURL + '/tpl/vouchers/';

    var action = $stateParams.action.toLowerCase(),
        formTitle = $rootScope.title;

    formTitle = 'vouchers';
    $scope.action = action;
    $scope.button = {
        title: 'List Voucher',
        link: '#/app/Vouchers/list'
    };
    $scope.button_add = {
                title: 'Add vouchers',
                link: '#/app/Vouchers/add'
    };    
    switch (action) {
        case 'list':
            $scope.template += 'all.html';
            break;
        case 'add':
            $scope.template += 'one.html';
            formTitle = 'Add Vouchers';
            break;
        case 'edit':
            $scope.template += 'one.html';
            formTitle = 'Edit Vouchers';
            break;
        default:
            $scope.template += 'all.html';
            break;
    }
    $scope.showPopup = function(which){
       var dlg = dialogs.create('/load-ten-voucher-code','customDialogCtrl',{},{size:'lg',keyboard: true,backdrop: false,windowClass: 'my-class'});
            dlg.result.then(function(name){                
            },function(){
                
            });
    };
    if (['add', 'edit'].indexOf(action) != -1) {
        $scope.form = {
            fields: [
            {
                type: 'hidden',
                name: 'id'
            }, {
                type: 'text',
                label: 'Voucher',
                name: 'name'
            }, {
                type: 'date-picker',
                label: 'Expries',
                name: 'expries'
            }, {
                type: 'select',
                label: 'Type',
                name: 'product_type',
                options: [{value:'all',text:'All Products'},{value:"list",text:'Some Product'},{value:"promo",text:'Promo code'}],
                value :'all'
            }, {
                type: 'select',
                label: 'Category', 
                name: 'category',
                options: []
            }, {
                type: 'select',
                label: 'Unit type',
                name: 'type',
                options: [{value:'%',text:'%'},{value:"$",text:'$'}],
                value :'%'
            }, {
                type: 'number',
                label: 'Value',
                name: 'value',
                value : 10
            } ,{
                type: 'select',
                label: 'Limited time',
                name:'limited',
                options: [{value:2,text:'Use 1 time'},{value:0,text:'Unlimited time use'}],
                value : 0
            }, {
                type: 'select',
                label: 'Active',
                name: 'active',
                options: [{value:1,text:'Active'},{value:0,text:'Inactive'}],
                value :1
            }, {
                type: 'number',
                label: 'Order',
                name: 'order_no',
                value : 1
            }],
            title: formTitle
        };
        $scope.beginDate = new Date();
        $scope.minDate = new Date('1/6/2016');
        $scope.endDatePickerOptions = {
            minDate: 'beginDate'
        };

        if (action === 'edit') {
            var id = $stateParams.id;
            $http.get(appHelper.adminURL('Vouchers/edit/' + id))
                .success(function(result) {
                    if (result.error === 0) {
                        $scope.form.fields[4].options = result.data.categoryOptions;
                        $scope.form = appHelper.populateForm($scope.form, result.data);
                    }else{
                        toaster.pop('error', 'Error', result.messages+'<br />');
                    }
                    $scope.form.allLoaded = true;
                }).error(function(result) {
                    dialogs.confirm(result.message, 'Do you want to create a new one?', {
                            size: 'md'
                        })
                        .result.then(function() {
                            $location.path('/app/Vouchers/add');
                        }, function() {
                            $location.path('/app/Vouchers/list');
                        });
                });
        }else if (action === 'delete') {   
            $location.path('/app/Vouchers/list');
            $scope.form.allLoaded = true;

        } else {

           $http.get(appHelper.adminURL('categories/get-options'))
                    .success(function(result) {
                        if (result.error === 0) {
                            console.log(result.data);
                            $scope.form.fields[4].options = result.data;
                        }
                        $scope.form.allLoaded = true;
                    });
                    
           $http.get(appHelper.adminURL('Vouchers/generator/'))
                .success(function(result) {
                    for (var i in $scope.form.fields) {
                        if ($scope.form.fields[i].name == 'name') {
                            $scope.form.fields[i].value = result.messages;
                        }
                        if($scope.form.fields[i].name == 'expries'){
                            var oneWeekfromnow = new Date();
                            oneWeekfromnow.setDate(oneWeekfromnow.getDate() + 7);

                            var year = oneWeekfromnow.getFullYear();
                            var month = oneWeekfromnow.getMonth() + 1;
                            var day = oneWeekfromnow.getDate();
                            
                            var mindate = year+'-'+month+'-'+day;
                            $scope.form.fields[i].value = mindate;
                        }
                    }
                    $scope.form.allLoaded = true;
                });

        }
        $scope.getnewvoucher = function(){
            $http.get(appHelper.adminURL('Vouchers/generator/'))
                .success(function(result) {
                    for (var i in $scope.form.fields) {
                        if ($scope.form.fields[i].name == 'name') {
                            $scope.form.fields[i].value = result.messages;
                        }
                    }
                    $scope.form.allLoaded = true;
                })
        };
        $scope.save = function() {
            var file,
                data = {};
            for (var i in $scope.form.fields) {
                data[$scope.form.fields[i].name] = $scope.form.fields[i].value;
            }
            $scope.upload(file, data);
        };

        $scope.upload = function(file, data) {
            var config = {
                url: appHelper.adminURL('Vouchers/update'),
                fields: data
            };
            if (file && file.blobUrl) {
                config.file = file;
            }
            Upload.upload(config).success(function(result, status, headers, config) {
                if (result.error === 1) {
                    toaster.pop('error', 'Error', result.messages + '<br />');
                } else if (result.error === 0) {
                    $scope.form.fields[0].value = result.data.id;
                    toaster.pop('success', 'Message', result.message);
                    $location.path('/app/Vouchers/edit/' + result.data.id);
                }
            });
        };
    } else if (action === 'list') {
        $scope.gridOptions = RW.gridOptions($scope, {
            gridName: 'gridOptions', //*required
            rowHeight: 60,
            pageSizes: [50,100,250, 500]
            , pageSize: 50,
            columns: [{
                    name: 'Voucher',
                    field: 'name'
                },{
                    name: 'Value',
                    field: 'value',
                },{
                    name: 'Type',
                    field: 'type'
                },{
                    name: 'Expries',
                    field: 'expries'
                }, {
                    name: 'Category',
                    field: 'category'
                },{
                    name: 'Active',
                    field: 'active',
                    enableSorting: false,
                    enableFiltering: false,
                },{
                    name: 'Limited',
                    field: 'limited',
                    enableSorting: false,
                    enableFiltering: false,
                },{
                    name: 'Code Type',
                    field: 'product_type',
                    enableSorting: false,
                    enableFiltering: false,
                }],
            list: appHelper.adminURL('Vouchers/list'),
            edit: '#/app/Vouchers/edit',
            delete: appHelper.adminURL('Vouchers/deleteVoucher'),
            deleteConfirm: 'Are you sure you want to delete this category?' //optional
        });
    }
}]).controller('customDialogCtrl',['$scope','$modalInstance','$http',function($scope,$modalInstance,$http,data){                
        $scope.cancel = function(){
            $modalInstance.dismiss('Canceled');
        };        
        $scope.getCode = function(evt){

            var str = $("#current_value").val() +':'+($("#current_type").val() == '%' ? 'phan-tram' : 'value')+':'+$("#current_expries").val();

            $http.get(appHelper.adminURL('Vouchers/get-ten-code/' + str ))
                .success(function(result) {                    
                    $("#content_div").html('');
                    if (result.error == 0) {                
                        var contents = result.messages;
                        var str = '';
                        var print_div = '';
                        str +='<div class="form-group input-group-lg col-md-12 hide-data">';
                            str +='<label class="control-label col-md-8">Print all</label>';
                            str +='<input class="col-md-4 btn btn-default btn btn-success" type="button" value="Print all code" onclick=printAllCode() />';
                        str +='</div>';
                        for(var i=0;i<contents.length;i++){
                            str +='<div class="form-group input-group-lg col-md-12 hide-data">';
                                str +='<label class="control-label col-md-12" style="text-align:center;font-size:25px;">'+contents[i]+'</label>';
                                // str +='<input class="col-md-4 btn btn-default btn" type="button" value="Print this code" onclick=printThisCode(\"'+contents[i]+'\") />';                                    
                            str +='</div>';

                            print_div +='<div class="otherdata print-all modal-content" style="clear:both;margin-bottom:40px;height:130px;" id="'+contents[i]+'">';
                                print_div +='<div class="modal-header ng-scope modal-header-temp"  id="header_'+contents[i]+'">';
                                    print_div +='<h4 class="modal-title col-md-12">';
                                        print_div +='<span style="width:50%;float:left">';                                            
                                            print_div +='<span class="glyphicon glyphicon-star"></span>';
                                            print_div +=' Voucher Code';
                                        print_div +='</span>';
                                    
                                        print_div +='<span style="width:45%;float:left;text-align:right">';
                                            print_div +='Expried on:'+result.dates;
                                        print_div +='</span>';
                                    print_div +='</h4>';
                                    print_div +='<div class="col-md-12">&nbsp </div>';
                                print_div +='</div>';
                                

                                print_div +='<div class="modal-body ng-scope print-all" style="clear:both;height:65px;line-height:75px;font-size:35px;text-align:center;clear:both;color:red" id="content_'+contents[i]+'">';                                  
                                    print_div +='<div class="form-group input-group-lg col-md-12">';
                                        print_div +='<label class="control-label col-md-12">'+contents[i]+'</label>';
                                    print_div +='</div>';                                    
                                    print_div +='<div class="col-md-12">&nbsp;</div>';
                                print_div +='</div>';
                            print_div +='</div>';
                            print_div +='<div class="modal-footer otherdata" style="clear:both;border-bottom:1px solid black;margin-bottom:50px;"></div>';

                        }
                        $("#content_div").html(str);
                        $("#print_div").html(print_div);
                    }
                });      

        };
    }]).run(['$templateCache','$http',function($templateCache,$http){
        var oneWeekfromnow = new Date();
        oneWeekfromnow.setDate(oneWeekfromnow.getDate() + 7);

        var year = oneWeekfromnow.getFullYear();
        var month = oneWeekfromnow.getMonth() + 1;
        var day = oneWeekfromnow.getDate();
        
        var mindate = year+'-'+month+'-'+day;

        var str = '';
        str +='<div class="modal-header">';
            str +='<h4 class="modal-title">';
                str +='<span class="glyphicon glyphicon-star"></span>';
                str +=' Voucher Code';
            str +='</h4>';
        str +='</div>';
        str +='<div class="modal-body">';
            str +='<ng-form name="nameDialog" novalidate role="form">';
                str +='<div class="form-group input-group-lg" ng-class="">';
                    str +='<label class="control-label" for="course">Value:</label>';
                    str +='<input type="text" class="form-control" value="10" id="current_value" >';
                    str +='<span class="help-block">Enter the value for this voucher.</span>';
                str +='</div>';
                str +='<div class="form-group input-group-lg" ng-class="">';
                    str +='<label class="control-label" for="course">Unit type:</label>';
                    str +='<select class="form-control" id="current_type">';
                        str +='<option value="%" selected>%</option>';
                        str +='<option value="$">$</option>';
                    str +='</select>';
                str +='</div>';
                str +='<div class="form-group input-group-lg" ng-class="">';
                    str +='<label class="control-label" for="course">Expries:</label>';
                    str +='<input ui-jq="datepicker" class="datepicker form-control" style="width:100%" data-date-format="yyyy-mm-dd" id="current_expries" value="'+mindate+'" >';                                
                str +='</div>';
            str +='</ng-form>';
        str +='</div>';
        str +='<div id="content_div" style="clear:both"></div>';
        str +='<div class="modal-footer"  style="clear:both">';
            str +='<button type="button" class="btn btn-default" ng-click="cancel()">Cancel</button>';
            str +='<button type="button" class="btn btn-primary" ng-click="getCode()">Generate 10 voucher code</button>';
        str +='</div>';
        str +='<div id="print_div" style="clear:both"></div>';
        $templateCache.put('/load-ten-voucher-code', str);
    }]);
