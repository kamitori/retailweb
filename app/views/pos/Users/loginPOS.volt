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
		<div id="form_login">
			<form action="{{ baseURLPos }}/user/signinpos" method="POST">
				<input type="hidden" id="_link" value="{{_link_}}" />
				<div class="row">
					<div class="col-xs-12 text-center" style="margin-bottom:10px;color:#E0B041;">
						{{message}}
					</div>
				</div>
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
				<div class="row">
					<div class="col-xs-3 col-xs-offset-1 text-right"></div>
					<div class="col-xs-7">
						<button class="btn btn-lg btn-bms">Login</button>
					</div>
				</div>
				<div class="row text-center">
				</div>
			</form>
		</div>
	</div>
	{{ assets.outputJs() }}
</body>
</html>