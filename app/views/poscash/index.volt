<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="author" content="Anvy Developers">
        <title>BanhMiSub.com</title>
        <link href="http://fonts.googleapis.com/css?family=Roboto:400,900" rel="stylesheet" type="text/css" media="all">
        {{ assets.outputCss() }}
    </head>
    <body>
        <div class="hidden">
            <img src="{{baseURL}}/themes/banhmisub/images/BmiSUB_logo.png" alt="logo" />
            <img src="{{baseURL}}/themes/banhmisub/images/BmiSUB_logo_border.png" alt="logo border" />
        </div>
        <div class="header shapdow-bot bartop" id="header">
            {{ partial('blocks/header') }}
        </div>
        <div class="main-content">
            {{ content() }}
        </div>
        <!-- /container -->

        <!-- Footer -->
        <div class="footer">
            {{ partial('blocks/footer')}}
        </div>
        <!-- Loading -->
        <div class="loading" style="display:block;">
            <div class="loading-bg">
                <div class="logo-center" style="display:none;top: 372px;left: 650px;">
                    <div class="loading-logo" style="width: 310px;">
                    </div>
                </div>
            </div>
        </div>

        {{ assets.outputJs() }}
        <!-- {{ assets.outputJs('pageJS') }}  -->
        
    </body>
</html>