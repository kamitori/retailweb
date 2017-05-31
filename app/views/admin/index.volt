<!DOCTYPE html>
<html lang="en" data-ng-app="app">
<head>
    <meta charset="utf-8" />
    <title ng-bind="title">Retail Web</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    {{ assets.outputCss() }}
</head>
<body ng-controller="AppCtrl">
    <div class="app" id="app" ng-class="{'app-header-fixed':app.settings.headerFixed, 'app-aside-fixed':app.settings.asideFixed, 'app-aside-folded':app.settings.asideFolded, 'app-aside-dock':app.settings.asideDock, 'container':app.settings.container}" ui-view></div>
    <toaster-container toaster-options="{'position-class': 'toast-top-right', 'close-button': true, 'prevent-duplicates': true, 'body-output-type': 'trustedHtml'}"></toaster-container>
    <script type="text/javascript">
        var appHelper = {
            baseURL: '{{ baseURL }}',
            adminURL: function(path) {
                if (typeof path == 'undefined') {
                    path = '';
                } else {
                    path = '/'+ path;
                }
                return this.baseURL + '/admin'+ path;
            },
            bowerComponents: function(path) {
                return this.baseURL + '/bower_components/'+ path;
            },
            populateForm: function (form, data) {
                var fieldPosition = {};
                for(var i in form.fields) {
                    fieldPosition[ form.fields[i].name ] = i;
                }
                for(var i in data) {
                    if (fieldPosition[i]) {
                        if (data[i] == null) {
                            data[i] = '';
                        }
                        form.fields[ fieldPosition[i] ].value = data[i];
                    }
                }
                return form;
            },
            getDataSearch: function(columns, filterOptions) {
                var search = {};
                if (typeof columns == 'object') {
                    for (var i in columns) {
                        if (columns[i].filters[0].term) {
                            search[columns[i].field] = columns[i].filters[0].term;
                        }
                    }
                } else {
                    search = filterOptions;
                }
                return search;
            }
        };
    </script>
    {{ assets.outputJs() }}
    {{ assets.outputJs('controllers') }}
</body>
</html>
