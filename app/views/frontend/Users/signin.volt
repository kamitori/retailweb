<div class="modal fade" id="modal_signin">
	<form action="{{ baseURL }}/user/signin" method="POST" accept-charset="utf-8" id="form_signin">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Sign in</h4>
			</div>
			<div class="modal-body row">
				<div class="form-group col-xs-12">
                    <label class="control-label col-xs-2 col-xs-offset-1 text-right">Email</label>
                    <div class="col-xs-8">
                        <input type="text" name="email" class="form-control" placeholder="Email">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-xs-2 col-xs-offset-1 text-right">Password</label>
                    <div class="col-xs-8">
                        <input type="password" name="password" class="form-control" placeholder="Password">
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
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</form>
</div><!-- /.modal -->
<style>
	.modal h4,.modal label,.modal #error{
		font-family: 'GothamMediumRegular';
	}

	#modal_fb .modal-dialog{
		width:940px;
	}

</style>


<div class="modal fade" id="modal_fb">
	<form action="{{ baseURL }}/user/signin" method="POST" accept-charset="utf-8" id="form_create_account">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Create Account</h4>
			</div>
			<div class="modal-body">
                    <input type="hidden" name="first_name" id="first_name" class="form-control" placeholder="Firstname">
                    <input type="hidden" name="last_name" id="last_name" class="form-control" placeholder="Lastname">
                    <input type="hidden" name="email" id="email" class="form-control" placeholder="Email">
                    <input type="hidden" name="re_email" id="re_email" class="form-control" placeholder="Re-email" onchange="check_reinput(this,'email')">

				<div class="row">
					<div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Your birthday<span class="red"> *</span></label>
	                    <div class="col-xs-8">
               				<select style="width:58%;margin-right: 1.5%;display:inline-block;" required aria-required="true" name="month" id="" class="select2 form-control select_month" data-placeholder="--Month--">
								<option value="" class=""></option>
								<option value="1" label="January">January</option>
								<option value="2" label="February">February</option>
								<option value="3" label="March">March</option>
								<option value="4" label="April">April</option>
								<option value="5" label="May">May</option>
								<option value="6" label="June">June</option>
								<option value="7" label="July">July</option>
								<option value="8" label="August">August</option>
								<option value="9" label="September">September</option>
								<option value="10" label="October">October</option>
								<option value="11" label="November">November</option>
								<option value="12" label="December">December</option>
							</select>
                       		<select style="width:38%;display:inline-block;" required aria-required="true" name="day" id="" class="select2 form-control select_day" data-placeholder="--Day--">
								<option value=""></option>
							</select>

	                    </div>
	                </div>
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Phone number<span class="red"> *</span></label>
	                    <div class="col-xs-8">
	                        <input type="text" name="phone" class="form-control" placeholder="xxxxxxxxxx">
	                    </div>
	                </div>
				</div>
				<div class="row">
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Password<span class="red"> *</span></label>
	                    <div class="col-xs-8">
	                        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
	                    </div>
	                </div>
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Re-password<span class="red"> *</span></label>
	                    <div class="col-xs-8">
	                        <input type="password" name="re_password"  id="re_password" class="form-control" placeholder="Password" onchange="check_reinput(this,'password')">
	                    </div>
	                </div>
	            </div>
				<div class="row">
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Address</label>
	                    <div class="col-xs-8">
	                        <input type="text" name="address" class="form-control" placeholder="">
	                    </div>
	                </div>
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Town/City</label>
	                    <div class="col-xs-8">
	                        <input type="text" name="town_city" class="form-control" placeholder="">
	                    </div>
	                </div>
	            </div>
				<div class="row">
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Province/State</label>
	                    <div class="col-xs-8">
	                        <select class="form-control" name="province">
								{% for province in provinces %}
								<option value="{{province.key}}-{{ province.name }}">{{ province.name }}</option>
								{% endfor %}
							</select>
	                    </div>
	                </div>
	                <div class="form-group col-xs-6">
	                    <label class="control-label col-xs-4 text-right">Postal code</label>
	                    <div class="col-xs-8">
	                        <input type="text" name="postal_code" class="form-control" placeholder="">
	                    </div>
	                </div>
	            </div>
				<div class="row">
	                <input type="hidden" name="facebook_id" id="facebook_id">
	                <div class="form-group col-xs-12  text-justify">
	                    <label>Email Opt In (by opting in you will receive emails, promotions and special offers from BanhMiSub.) <input type="checkbox" name="subscribe"></label>
	                </div>
	                <div class="col-xs-12 text-justify">
	                	<label>I agree to the <a href="#">Terms of Use</a> and understand that my information will be used as described in the <a href="#">BanhMiSub Privacy Policy</a>.</label>
	                </div>
	                <div class="col-xs-12 text-center">
						<button class="btn btn-success" type="button" onclick="create_account(this)">Create account</button>
	                </div>
	                <div class="col-xs-12 text-center" style="color:#f00;margin-top: 20px;" id="error">
	                </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</form>
</div><!-- /.modal -->