<!DOCTYPE html>
<html lang="en" ng-app="app">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="">
		<meta name="author" content="Anvy Developers">
		<title>POS BANHMISUB.COM</title>
		<!-- <link rel="stylesheet" href="/themes/vendhq/css/bootstrap.css"> -->
		<!-- <link rel="stylesheet" href="/themes/vendhq/css/style.css"> -->
		<link rel="stylesheet" href="/themes/vendhq/js/jquery/lib/angular/css/autocomplete.css">
		<link rel="stylesheet" href="/themes/vendhq/js/jquery/lib/alert/css/jAlert-v3.css">
		{{ assets.outputCss() }}				
	</head>
<body>
	{{ partial('/blocks/pos-nav-menu') }}
	<section id="content" class="container">
		{{ partial('/blocks/cashPos') }}
		{{ partial('/blocks/listOrder') }}
		{{ partial('/blocks/close-register') }}
	</section>
	{{ assets.outputJs() }}
	<script type="text/javascript">
		var moreitems = {{listItems}}
	</script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/core/jquery.min.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/action/main.js"></script>
	<script src="/themes/vendhq/js/jquery/lib/angular/js/angular.min.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/angular/js/autocomplete.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/angular/js/app.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/alert/js/jAlert-v3.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/alert/js/jAlert-functions.js"></script>
	<input type="hidden" id="sale-order-description" value="{{currentOrder['description']}}" />
	<input type="hidden" id="currentOrderId" value="{{currentOrder['id']}}" />	
</body>
</html>