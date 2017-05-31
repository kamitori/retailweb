<form action="{{ baseURL }}/user/signin" method="POST" accept-charset="utf-8" id="form_signin">
	<input type="hidden" id="_link" value="{{_link_}}" />
    <div class="product_left shadow" style="width:100%">
        <div>
			<div class="modal-header">
				<h4 class="modal-title">Sign in</h4>
			</div>
			<div class="modal-body row">
				<div class="form-group col-xs-12">
                    <label class="control-label col-xs-2 col-xs-offset-1 text-right">Email</label>
                    <div class="col-xs-8">
                        <input type="text" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-xs-2 col-xs-offset-1 text-right">Password</label>
                    <div class="col-xs-8">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>
                <div class="col-xs-10 col-xs-offset-2 text-center">
					<button class="btn btn-success" type="button" onclick="signin()">Sign in</button>
					&nbsp;&nbsp;or&nbsp;&nbsp;
					<button class="btn btn-facebook" type="button" onclick="checkLoginState()"><i class="fa fa-facebook"></i> Sign In with Facebook</button>
                </div>
                <div class="col-xs-12 text-center" style="color:#f00;margin-top: 20px;" id="error">
                </div>
			</div>
		</div>
    </div>
</form>