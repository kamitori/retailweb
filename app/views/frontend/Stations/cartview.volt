<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="author" content="Anvy Developers">
	<title>BanhMiSub.com</title>
	<link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="/themes/banhmisub/css/cart.css">
	<link rel="stylesheet" href="/css/simple-slideshow-styles.css">
	<style type="text/css" media="screen">
		#viewcart_box_left{
			width:40%;
			float:right;
			margin:0;
		}
		#viewcart_box_right{
			width:60%;
			float:right;
			margin:0;
			padding-left:0.5%;
			padding-right:0.5%;
		}
		@media only screen and (max-width: 940px){
			#viewcart_box_left{
				width:100% !important;
				float: none !important;
			}
			#viewcart_box_right{
				width:100% !important;
				float: none !important;
			}
		}
	</style>
</head>
<body>
	<div class="main_box">
		<div style="" id="viewcart_box_right">
		</div>
		<div style="" id="viewcart_box_left">
			<div class="bss-slides slide1" tabindex="1" autofocus="autofocus">
				{% for index, item in arrbanner_small %}
			    <figure>
			      <img src="/{{item}}" style="width:100%;" />
			    </figure>
			    {% endfor %}
			</div>
		</div>
	</div>
	<div class="main-slides" style="display:none; height:auto;">
		<div class="bss-slides slide2" tabindex="2">
			{% for index, item in arrbanner_full %}
		    <figure>
		      <img src="/{{item}}" style="width:100%;" />
		    </figure>
		    {% endfor %}
		</div>
	</div>
	<input type="hidden" id="hidden_count" value="0" />

	<div class="thank_you" style="display:none;">
		<p>Thank you for your business.</p>
		<p>Your order number is </p>
		<p class="last_your_order_code"></p>
	</div>
	<input id="idss" value="{{idss}}" type="hidden" />
	
	<script src="/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="/themes/banhmisub/js/cartview.js"></script>
	<script src="/js/hammer.js"></script>
	<script src="/js/better-simple-slideshow.js"></script>
	<script>
	var opts1 = {
	    auto : {
	        speed : 5000, 
	        pauseOnHover : true
	    },
	    fullScreen : false, 
	    swipe : true
	};
	makeBSS('.slide1', opts1);

	var opts2 = {
	    auto : {
	        speed : 5000, 
	        pauseOnHover : true
	    },
	    fullScreen : false, 
	    swipe : true
	};
	makeBSS('.slide2', opts2);
	</script>

</body>
</html>