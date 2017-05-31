<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="author" content="Anvy Developers">
        <title>BanhMiSub.com</title>
        {{ assets.outputCss() }}
    </head>
    <body>
        <div class="header shapdow-bot">
            {{ partial('blocks/header') }}
        </div>
        <div class="main-content container">
            <!-- Static navbar -->
            {{ partial('blocks/nav') }}
            {{ content() }}
            
            <!-- Footer -->
            {{ partial('blocks/footer')}}
        </div>
        <!-- /container -->
        {{ partial('blocks/search_product')}}

        <!-- Loading -->
        <div class="loading" style="display:none;">
            <div class="loading-message">
                <span class="logo"></span>
                <span>Loading menu...</span>
            </div>
        </div>

        {{ assets.outputJs() }}
        {{ assets.outputJs('pageJS') }} 
        
    </body>
</html>