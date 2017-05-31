<!DOCTYPE html>
<html lang="en" ng-app="app">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="">
		<meta name="author" content="Anvy Developers">
		<title>POS BANHMISUB.COM</title>		
		{{ assets.outputCss() }}				
	</head>
<body>
	{{ partial('/blocks/pos-nav-menu') }}
	<section id="content" class="container">
		{{ partial('/blocks/cashPos') }}
		{{ partial('/blocks/listOrder') }}
		{{ partial('/blocks/close-register') }}
	</section>
	<script type="text/javascript">
		var moreitems = {{listItems}};
		var moreusers = {{listUsers}};
	</script>		
	{{ assets.outputJs() }}		
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/angular/js/autocomplete.js"></script>
	<script type="text/javascript" src="/themes/vendhq/js/jquery/lib/angular/js/app.js"></script>
	<input type="hidden" id="sale-order-description" value="{{currentOrder['description']}}" />
	<input type="hidden" id="currentOrderId" value="{{currentOrder['id']}}" />	
</body>
</html>