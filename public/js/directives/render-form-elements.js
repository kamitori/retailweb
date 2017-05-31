'use strict';

angular.module('app')
    .directive('renderFormElements', ['$compile', function($compile) {
        return {
            restrict: 'E',
            replace: true,
            template: '',
            scope: {
                data: '='
            },
            controller: ['$scope', function($scope) {
                var type = $scope.data.type;
                $scope.element = '';
                if (type) {
                    type = type.toLowerCase();
                    var elementOptions = {
                        label:      '',
                        labelWidth:  2,
                        labelAttr:  '',
                        labelStyle: '',
                        labelClass: '',

                        input:      '',
                        inputWidth: 10,
                        inputAttr:  '',
                        inputStyle: '',
                        inputClass: '',

                    };

                    for(var i in $scope.data) {
                        if (['name', '$$hashKey', 'value'].indexOf(i) != -1) continue;
                        elementOptions[ i ] = $scope.data[ i ];
                    }


                    if ($scope.data.label) {
                        elementOptions.label = '<label class="col-sm-'+ elementOptions.labelWidth +' '+ elementOptions.labelClass +' control-label" style="'+ elementOptions.labelStyle +'" '+ elementOptions.labelAttr +'>'+ $scope.data.label +'</label>';
                    }

                    switch(type) {
                        case 'text':
                        case 'checkbox':
                        case 'hidden':
                        case 'radio':
                        case 'email':
                        case 'password':
                        case 'number':
                            elementOptions.input = '<input type="'+ type +'" name="{{ data.name }}" ng-model="data.value" class="form-control '+ elementOptions.inputClass +'" style="'+  elementOptions.inputStyle +'" '+ elementOptions.inputAttr +' value="{{ data.value }}">';
                            break;
                        case 'textarea':
                            elementOptions.input = '<textarea name="{{ data.name }}" ng-model="data.value" class="form-control '+ elementOptions.inputClass +'" style="'+  elementOptions.inputStyle +'" '+ elementOptions.inputAttr +'>{{ data.value }}</textarea>';
                            break;
                        case 'select':
                            var options = '';
                            if ($scope.data.options && typeof $scope.data.options == 'object') {
                                for (var i in $scope.data.options) {
                                    options += '<option value="'+ $scope.data.options[i].value +'" '+ ($scope.data.options[i].value == $scope.data.value ? 'selected="selected"' : '') +' >'+ $scope.data.options[i].text +'</option>';
                                }
                            }
                            elementOptions.input = [
                                                    '<select class="form-control" name="{{ data.name }}" '+ elementOptions.inputClass +'" style="'+  elementOptions.inputStyle +'" '+ elementOptions.inputAttr +' ng-model="data.value">',
                                                        '<option value="">---Select---</option>',
                                                        options,
                                                    '</select>'
                                                ].join('\n');

                            break;
                        case 'file':
                            elementOptions.input = '<input name="{{ data.name }}" ui-jq="filestyle" type="file" data-icon="false" data-classButton="btn btn-default" style="'+  elementOptions.inputStyle +'" '+ elementOptions.inputAttr +' data-classInput="form-control inline v-middle input-s '+ elementOptions.inputClass +'" ng-model="data.value">';
                            break;
                        case 'image-upload':
                            elementOptions.input = [
                                                    '<img class="'+ elementOptions.inputClass +'" style="'+ elementOptions.inputStyle +'" ngf-src="data.file || \''+ $scope.data.value +'\'">',
                                                    '<input ngf-select ng-model="data.file" type="file" name="{{ data.name }}" accept="image/*" '+ elementOptions.inputAttr +' >',
                                                    ].join('\n');
                            break;
                        case 'date-picker':
                            elementOptions.input = '<input ui-jq="datepicker" class="datepicker form-control" style="width:100%" min-date="minDate" datepicker-options="endDatePickerOptions" data-date-format="yyyy-mm-dd" ng-model="data.value" value="{{data.value}}" >';
                            break;
                        case 'text-editor':
                            elementOptions.input = [
                                                '<div class="btn-toolbar m-b-sm btn-editor" data-role="editor-toolbar" data-target="#editor">',
                                                    '<div class="btn-group dropdown" dropdown>',
                                                        '<a class="btn btn-default" dropdown-toggle tooltip="Font"><i class="fa fa-font"></i><b class="caret"></b></a>',
                                                        '<ul class="dropdown-menu">',
                                                            '<li><a href data-edit="fontName Serif" style="font-family:\'Serif\'">Serif</a></li>',
                                                            '<li><a href data-edit="fontName Sans" style="font-family:\'Sans\'">Sans</a></li>',
                                                            '<li><a href data-edit="fontName Arial" style="font-family:\'Arial\'">Arial</a></li>',
                                                        '</ul>',
                                                    '</div>',
                                                    '<div class="btn-group dropdown" dropdown>',
                                                        '<a class="btn btn-default" dropdown-toggle data-toggle="dropdown" tooltip="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>',
                                                        '<ul class="dropdown-menu">',
                                                            '<li><a href data-edit="fontSize 5" style="font-size:24px">Huge</a></li>',
                                                            '<li><a href data-edit="fontSize 3" style="font-size:18px">Normal</a></li>',
                                                            '<li><a href data-edit="fontSize 1" style="font-size:14px">Small</a></li>',
                                                        '</ul>',
                                                    '</div>',
                                                    '<div class="btn-group">',
                                                        '<a class="btn btn-default" data-edit="bold" tooltip="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>',
                                                        '<a class="btn btn-default" data-edit="italic" tooltip="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>',
                                                        '<a class="btn btn-default" data-edit="strikethrough" tooltip="Strikethrough"><i class="fa fa-strikethrough"></i></a>',
                                                        '<a class="btn btn-default" data-edit="underline" tooltip="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>',
                                                    '</div>',
                                                    '<div class="btn-group">',
                                                        '<a class="btn btn-default" data-edit="insertunorderedlist" tooltip="Bullet list"><i class="fa fa-list-ul"></i></a>',
                                                        '<a class="btn btn-default" data-edit="insertorderedlist" tooltip="Number list"><i class="fa fa-list-ol"></i></a>',
                                                        '<a class="btn btn-default" data-edit="outdent" tooltip="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>',
                                                        '<a class="btn btn-default" data-edit="indent" tooltip="Indent (Tab)"><i class="fa fa-indent"></i></a>',
                                                    '</div>',
                                                    '<div class="btn-group">',
                                                        '<a class="btn btn-default" data-edit="justifyleft" tooltip="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>',
                                                        '<a class="btn btn-default" data-edit="justifycenter" tooltip="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>',
                                                        '<a class="btn btn-default" data-edit="justifyright" tooltip="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>',
                                                        '<a class="btn btn-default" data-edit="justifyfull" tooltip="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>',
                                                    '</div>',
                                                    '<div class="btn-group dropdown" dropdown>',
                                                        '<a class="btn btn-default" dropdown-toggle tooltip="Hyperlink"><i class="fa fa-link"></i></a>',
                                                        '<div class="dropdown-menu">',
                                                            '<div class="input-group m-l-xs m-r-xs">',
                                                                '<input class="form-control input-sm" id="LinkInput" placeholder="URL" type="text" data-edit="createLink"/>',
                                                                '<div class="input-group-btn">',
                                                                    '<button class="btn btn-sm btn-default" type="button">Add</button>',
                                                                '</div>',
                                                            '</div>',
                                                        '</div>',
                                                        '<a class="btn btn-default" data-edit="unlink" tooltip="Remove Hyperlink"><i class="fa fa-cut"></i></a>',
                                                    '</div>',
                                                    '<div class="btn-group">',
                                                        '<a class="btn btn-default" tooltip="Insert picture (or just drag & drop)" id="pictureBtn"><i class="fa fa-picture-o"></i></a>',
                                                        '<input type="file" data-edit="insertImage" style="position:absolute; opacity:0; width:41px; height:34px" />',
                                                    '</div>',
                                                    '<div class="btn-group">',
                                                        '<a class="btn btn-default" data-edit="undo" tooltip="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>',
                                                        '<a class="btn btn-default" data-edit="redo" tooltip="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>',
                                                    '</div>',
                                                '</div>',
                                                '<div ui-jq="wysiwyg" ng-model="data.value" class="form-control" style="overflow:scroll;height:200px;max-height:200px" contenteditable="true">',
                                                    '{{ data.value }}',
                                                '</div>'
                                            ].join('\n');
                            break;
                    }

                    if ($scope.data.validate) {
                        var nameForm = $scope.data.name +'Form';
                        if ($scope.data.validate.requiredMessage) {
                            elementOptions.input += '<span ng-show="'+ nameForm +'.{{ data.name }}.$error.required">'+ $scope.data.validate.requiredMessage +'</span>';
                        }
                        if ($scope.data.validate.patternMessage) {
                            elementOptions.input += '<span ng-show="'+ nameForm +'.{{ data.name }}.$error.pattern">'+ $scope.data.validate.patternMessage +'</span>';
                        }
                        $scope.element =  ['<div class="form-group">',
                                                '<div ng-form="'+ nameForm +'">',
                                                    elementOptions.label,
                                                    '<div class="col-sm-'+ elementOptions.inputWidth +'">',
                                                        elementOptions.input,
                                                    '</div>',
                                                '</div>',
                                            '</div>'].join('\n');
                    } else {
                        $scope.element =  ['<div class="form-group">',
                                                elementOptions.label,
                                                '<div class="col-sm-'+ elementOptions.inputWidth +'">',
                                                    elementOptions.input,
                                                '</div>',
                                            '</div>'].join('\n');
                    }

                }
            }],
            link: function($scope, element, attrs) {
                element.replaceWith($compile($scope.element)($scope));
            }
        };

    }]);

angular.module('app')
    .directive('contenteditable', function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                function read() {
                    ngModel.$setViewValue(element.html());
                }
                ngModel.$render = function() {
                    element.html(ngModel.$viewValue || "");
                };
                element.bind("blur change", function() {
                    scope.$apply(read);
                });
            }
        };
    });
