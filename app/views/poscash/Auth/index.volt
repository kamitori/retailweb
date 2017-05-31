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
	<div id="div_login">
		<div class="text-center" id="logo_login">
			<img src="{{baseURL}}/themes/banhmisub/images/BmiSUB_logo.png" alt="">
		</div>
		<div id="form_login">
			<form action="{{baseURL}}/poscash/auth/authorize" method="POST">
				<div class="row">
					<div class="col-xs-3 col-xs-offset-1 text-right">
						<label for="">Username:</label>
					</div>
					<div class="col-xs-7">
						<input type="text" name="username" value="" placeholder="Username" class="form-control" required >
					</div>
				</div>
				<div class="row">
					<div class="col-xs-3 col-xs-offset-1 text-right">
						<label for="">Password:</label>
					</div>
					<div class="col-xs-7">
						<input type="password" name="password" value="" placeholder="Password" class="form-control" required>
					</div>
				</div>
				<div class="col-md-12 text-center red">
					<strong>
					{% if message is defined %}
						{{message}}
					{% endif %}
					</strong>
				</div>
				<div class="row">
					<div class="col-xs-3 col-xs-offset-1 text-right"></div>
					<div class="col-xs-7">
						<button class="btn btn-lg btn-bms">Login</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<style>
		body{
			overflow: hidden;
		}
	</style>
	{{ assets.outputJs() }}
</body>
</html>