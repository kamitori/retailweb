// lazyload config

angular.module('app')
    /**
   * jQuery plugin config use ui-jq directive , config the js and css files that required
   * key: function name of the jQuery plugin
   * value: array of the css js file located
   */
  .constant('JQ_CONFIG', {
      easyPieChart:   [   appHelper.bowerComponents('jquery.easy-pie-chart/dist/jquery.easypiechart.min.js')],
      sparkline:      [   appHelper.bowerComponents('jquery.sparkline/dist/jquery.sparkline.retina.js')],
      plot:           [   appHelper.bowerComponents('flot/jquery.flot.js'),
                          appHelper.bowerComponents('flot/jquery.flot.pie.js'),
                          appHelper.bowerComponents('flot/jquery.flot.resize.js'),
                          appHelper.bowerComponents('flot.tooltip/js/jquery.flot.tooltip.js'),
                          appHelper.bowerComponents('flot.orderbars/js/jquery.flot.orderBars.js'),
                          appHelper.bowerComponents('flot-spline/js/jquery.flot.spline.js')],
      moment:         [   appHelper.bowerComponents('moment/moment.js')],
      screenfull:     [   appHelper.bowerComponents('screenfull/dist/screenfull.min.js')],
      slimScroll:     [   appHelper.bowerComponents('slimscroll/jquery.slimscroll.min.js')],
      sortable:       [   appHelper.bowerComponents('html5sortable/jquery.sortable.js')],
      nestable:       [   appHelper.bowerComponents('nestable/jquery.nestable.js'),
                          appHelper.bowerComponents('nestable/jquery.nestable.css')],
      filestyle:      [   appHelper.bowerComponents('bootstrap-filestyle/src/bootstrap-filestyle.js')],
      slider:         [   appHelper.bowerComponents('bootstrap-slider/bootstrap-slider.js'),
                          appHelper.bowerComponents('bootstrap-slider/bootstrap-slider.css')],
      chosen:         [   appHelper.bowerComponents('chosen/chosen.jquery.min.js'),
                          appHelper.bowerComponents('bootstrap-chosen/bootstrap-chosen.css')],
      TouchSpin:      [   appHelper.bowerComponents('bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js'),
                          appHelper.bowerComponents('bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css')],
      wysiwyg:        [   appHelper.bowerComponents('bootstrap-wysiwyg/bootstrap-wysiwyg.js'),
                          appHelper.bowerComponents('bootstrap-wysiwyg/external/jquery.hotkeys.js')],
      dataTable:      [   appHelper.bowerComponents('datatables/media/js/jquery.dataTables.min.js'),
                          appHelper.bowerComponents('plugins/integration/bootstrap/3/dataTables.bootstrap.js'),
                          appHelper.bowerComponents('plugins/integration/bootstrap/3/dataTables.bootstrap.css')],
      vectorMap:      [   appHelper.bowerComponents('bower-jvectormap/jquery-jvectormap-1.2.2.min.js'),
                          appHelper.bowerComponents('bower-jvectormap/jquery-jvectormap-world-mill-en.js'),
                          appHelper.bowerComponents('bower-jvectormap/jquery-jvectormap-us-aea-en.js'),
                          appHelper.bowerComponents('bower-jvectormap/jquery-jvectormap-1.2.2.css')],
      footable:       [   appHelper.bowerComponents('footable/dist/footable.all.min.js'),
                          appHelper.bowerComponents('footable/css/footable.core.css')],
      fullcalendar:   [   appHelper.bowerComponents('moment/moment.js'),
                          appHelper.bowerComponents('fullcalendar/dist/fullcalendar.min.js'),
                          appHelper.bowerComponents('fullcalendar/dist/fullcalendar.css'),
                          appHelper.bowerComponents('fullcalendar/dist/fullcalendar.theme.css')],
      daterangepicker:[   appHelper.bowerComponents('moment/moment.js'),
                          appHelper.bowerComponents('bootstrap-daterangepicker/daterangepicker.js'),
                          appHelper.bowerComponents('bootstrap-daterangepicker/daterangepicker-bs3.css')],
      datepicker:       [   appHelper.bowerComponents('bootstrap-datepicker/dist/js/bootstrap-datepicker.js'),
                          appHelper.bowerComponents('bootstrap-datepicker/dist/css/bootstrap-datepicker.css')],
      tagsinput:      [   appHelper.bowerComponents('bootstrap-tagsinput/dist/bootstrap-tagsinput.js'),
                          appHelper.bowerComponents('bootstrap-tagsinput/dist/bootstrap-tagsinput.css')]

    }
  )
  // oclazyload config
  .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
      // We configure ocLazyLoad to use the lib script.js as the async loader
      $ocLazyLoadProvider.config({
          debug:  true,
          events: true,
          modules: [
              {
                  name: 'ngGrid',
                  files: [
                      appHelper.bowerComponents('ng-grid/build/ng-grid.min.js'),
                      appHelper.bowerComponents('ng-grid/ng-grid.min.css'),
                      appHelper.bowerComponents('ng-grid/ng-grid.bootstrap.css')
                  ]
              },
              {
                  name: 'ui.grid',
                  files: [
                      appHelper.bowerComponents('angular-ui-grid/ui-grid.min.js'),
                      appHelper.bowerComponents('angular-ui-grid/ui-grid.min.css'),
                      appHelper.bowerComponents('angular-ui-grid/ui-grid.bootstrap.css')
                  ]
              },
              {
                  name: 'ui.select',
                  files: [
                      appHelper.bowerComponents('angular-ui-select/dist/select.min.js'),
                      appHelper.bowerComponents('angular-ui-select/dist/select.min.css')
                  ]
              },
              {
                  name:'angularFileUpload',
                  files: [
                    appHelper.bowerComponents('angular-file-upload/angular-file-upload.min.js')
                  ]
              },
              {
                  name:'ui.calendar',
                  files: [appHelper.bowerComponents('angular-ui-calendar/src/calendar.js')]
              },
              {
                  name: 'ngImgCrop',
                  files: [
                      appHelper.bowerComponents('ngImgCrop/compile/minified/ng-img-crop.js'),
                      appHelper.bowerComponents('ngImgCrop/compile/minified/ng-img-crop.css')
                  ]
              },
              {
                  name: 'angularBootstrapNavTree',
                  files: [
                      appHelper.bowerComponents('angular-bootstrap-nav-tree/dist/abn_tree_directive.js'),
                      appHelper.bowerComponents('angular-bootstrap-nav-tree/dist/abn_tree.css')
                  ]
              },
              {
                  name: 'toaster',
                  files: [
                      appHelper.bowerComponents('angularjs-toaster/toaster.js'),
                      appHelper.bowerComponents('angularjs-toaster/toaster.css')
                  ]
              },
              {
                  name: 'textAngular',
                  files: [
                      appHelper.bowerComponents('textAngular/dist/textAngular-sanitize.min.js'),
                      appHelper.bowerComponents('textAngular/dist/textAngular.min.js')
                  ]
              },
              {
                  name: 'vr.directives.slider',
                  files: [
                      appHelper.bowerComponents('venturocket-angular-slider/build/angular-slider.min.js'),
                      appHelper.bowerComponents('venturocket-angular-slider/build/angular-slider.css')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular',
                  files: [
                      appHelper.bowerComponents('videogular/videogular.min.js')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.controls',
                  files: [
                      appHelper.bowerComponents('videogular-controls/controls.min.js')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.buffering',
                  files: [
                      appHelper.bowerComponents('videogular-buffering/buffering.min.js')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.overlayplay',
                  files: [
                      appHelper.bowerComponents('videogular-overlay-play/overlay-play.min.js')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.poster',
                  files: [
                      appHelper.bowerComponents('videogular-poster/poster.min.js')
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.imaads',
                  files: [
                      appHelper.bowerComponents('videogular-ima-ads/ima-ads.min.js')
                  ]
              },
              {
                  name: 'xeditable',
                  files: [
                      appHelper.bowerComponents('angular-xeditable/dist/js/xeditable.min.js'),
                      appHelper.bowerComponents('angular-xeditable/dist/css/xeditable.css')
                  ]
              },
              {
                  name: 'smart-table',
                  files: [
                      appHelper.bowerComponents('angular-smart-table/dist/smart-table.min.js')
                  ]
              }
          ]
      });
  }])
;
