<?php
namespace RW\Controllers\Admin;

class IndexController extends ControllerBase
{
    //To reset parent initialize, do not delete this method
    public function initialize()
    {

    }

    public function indexAction()
    {
        $this->assets
                ->collection('css')
                ->addCss('/bower_components/bootstrap/dist/css/bootstrap.css')
                ->addCss('/bower_components/font-awesome/css/font-awesome.css')
                ->addCss('/bower_components/simple-line-icons/css/simple-line-icons.css')
                ->addCss('/bower_components/animate.css/animate.css')
                ->addCss('/bower_components/angularjs-toaster/toaster.css')
                ->addCss('/bower_components/angular-dialog-service/dist/dialogs.min.css')
                ->addCss('/bower_components/angular-ui-grid/ui-grid.css')
                ->addCss('/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css')
                ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
                ->addCss('/css/font.css')
                ->addCss('/css/app.css')
                ->addCss('/css/style.css')
                ->addCss('/css/admin-voucher-print.css')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH . DS .'/css/admin.min.css')
                ->setTargetUri('/css/admin.min.css')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Cssmin());


        $this->assets
                ->collection('js')
                ->addJs('/bower_components/jquery/dist/jquery.min.js')
                ->addJs('/bower_components/angular/angular.js')
                ->addJs('/bower_components/angular-animate/angular-animate.js')
                ->addJs('/bower_components/angular-cookies/angular-cookies.js')
                ->addJs('/bower_components/angular-cookie/angular-cookie.js')
                ->addJs('/bower_components/ng-token-auth/dist/ng-token-auth.js')
                ->addJs('/bower_components/angular-resource/angular-resource.js')
                ->addJs('/bower_components/angular-sanitize/angular-sanitize.js')
                ->addJs('/bower_components/angular-ui-grid/ui-grid.js')
                ->addJs('/bower_components/angular-touch/angular-touch.js')
                ->addJs('/bower_components/angular-ui-router/release/angular-ui-router.js')
                ->addJs('/bower_components/ngstorage/ngStorage.js')
                ->addJs('/bower_components/angular-ui-utils/ui-utils.js')
                ->addJs('/bower_components/angular-bootstrap/ui-bootstrap-tpls.js')
                ->addJs('/bower_components/angularjs-toaster/toaster.js')
                ->addJs('/bower_components/oclazyload/dist/ocLazyLoad.js')
                ->addJs('/bower_components/angular-translate/angular-translate.js')
                ->addJs('/bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.js')
                ->addJs('/bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.js')
                ->addJs('/bower_components/angular-translate-storage-local/angular-translate-storage-local.js')
                ->addJs('/bower_components/angular-dialog-service/dist/dialogs.min.js')
                ->addJs('/bower_components/ng-file-upload/ng-file-upload-all.min.js')
                ->addJs('/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js')
                ->addJs('/js/app.js')
                ->addJs('/js/config.js')
                ->addJs('/js/config.lazyload.js')
                ->addJs('/js/config.router.js')
                ->addJs('/js/main.js')
                ->addJs('/js/services/ui-load.js')
                ->addJs('/js/filters/fromNow.js')
                ->addJs('/js/directives/setnganimate.js')
                ->addJs('/js/directives/ui-butterbar.js')
                ->addJs('/js/directives/ui-focus.js')
                ->addJs('/js/directives/ui-fullscreen.js')
                ->addJs('/js/directives/ui-jq.js')
                ->addJs('/js/directives/ui-module.js')
                ->addJs('/js/directives/ui-nav.js')
                ->addJs('/js/directives/ui-scroll.js')
                ->addJs('/js/directives/ui-shift.js')
                ->addJs('/js/directives/ui-toggleclass.js')
                ->addJs('/js/controllers/bootstrap.js')
                ->addJs('/js/directives/render-form-elements.js')
                ->addJs('/js/factories/rw-factory.js')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH. DS .'/js/admin.min.js')
                ->setTargetUri('/js/admin.min.js')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());

        $this->assets
                ->collection('controllers')
                ->addJs('/js/controllers/admins.js')
                ->addJs('/js/controllers/menus.js')
                ->addJs('/js/controllers/banners.js')
                ->addJs('/js/controllers/products.js')
                ->addJs('/js/controllers/product-categories.js')
                ->addJs('/js/controllers/pages.js')
                ->addJs('/js/controllers/configures.js')
                ->addJs('/js/controllers/voucher.js')
                ->addJs('/js/controllers/options.js')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH. DS .'/js/controllers.min.js')
                ->setTargetUri('/js/controllers.min.js')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());

        $this->view->baseURL = URL;
        $this->view->setViewsDir($this->view->getViewsDir() . '/admin/');
    }
}
