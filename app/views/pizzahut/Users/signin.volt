<form action="" method="POST" accept-charset="utf-8" id="form_signin">
<div class="row main_wrap">
	<div class="col-md-9">
		<div class="white_block">
			<div class="white_block_title">Sign in to your account</div>
			<div class="block">
				<p>Email address<span class="red">*</span></p>
				<input name="email" class="form-control" type="text" placeholder="">
			</div>
			<div class="block">
				<p>Password<span class="red">*</span> &nbsp;&nbsp;<a href="#">Forgot your password?</a></p>
				<input name="password" class="form-control" type="password" placeholder="">
			</div>
			<div class="block">
				<p>
					<input name="remember" type="checkbox" name="" value="">
					Remember Me
				</p>
				<button class="btn btn-danger btn-lg" type="submit">Sign in</button>
				&nbsp;&nbsp;or&nbsp;&nbsp;
				<button class="btn btn-facebook btn-lg" type="button" onclick="checkLoginState()"><i class="fa fa-facebook"></i> Sign In with Facebook</button>
			</div>
			<div id="status" style="color:red;font-weight: bold;display:{% if message!='' %} block {%else%} none {% endif %}">{{ message }}</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="right_title">
			With your account you can...
		</div>
		<ul class="right_list">
			<li>Save your favourites for faster ordering</li>
			<li>Speed through checkout with saved delivery addresses and payment information</li>
			<li>Get exclusive deals, right in your inbox</li>
		</ul>
	</div>
</div>
</form>

<div class="modal fade" id="modal_fb">
	<form action="/user/create-account" method="POST" accept-charset="utf-8" id="form_create_account">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Create Account</h4>
			</div>
			<div class="modal-body" style="background:#fff !important;">
				<div class="white_block" style="margin-top: 0px; ">
						<input type="hidden" name="first_name" id="first_name">
						<input type="hidden" name="last_name" id="last_name">
						<input type="hidden" name="email" id="email">
						<input type="hidden" name="re_email" id="re_email">
						<div class="block">
							<p>Your birthday<span class="red">*</span></p>
							<div class="row">
								<div class="col-md-4">
									<select required aria-required="true" name="month" id="" class="select2 form-control select_month" data-placeholder="--Month--">
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
								</div>
								<div class="col-md-4 col-md-offset-1">
									<select required aria-required="true" name="day" id="" class="select2 form-control select_day" data-placeholder="--Day--">
										<option value=""></option>
									</select>
								</div>
							</div>
						</div>
						<div class="block">
							<p>Phone number<span class="red">*</span></p>
							<input required aria-required="true" name="phone" maxlength="20" class="form-control" type="tel" title="Phone must be number"pattern="[0-9]+" placeholder="xxxxxxxxxx">
						</div>
						<div class="block">
							<p>Password<span class="red">*</span></p>
							<input name="password" id="password"  required aria-required="true"  class="form-control" type="password" placeholder="Password">
						</div>
						<div class="block">
							<p>Retype password<span class="red">*</span></p>
							<input name="re_password" id="re_password"  required aria-required="true" onchange="check_reinput('password')" class="form-control" type="password" placeholder="Retype password">
						</div>
						<div class="block">
							<input type="checkbox" name="subscribe" value="">
							Email Opt In (by opting in you will receive emails, promotions and special offers from BanhMiSub.)
						</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-invert btn-lg" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger btn-lg">Create Account</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</form>
</div><!-- /.modal -->
<style>
	#modal_fb *{
		font-family: 'UnitedSans' !important;
	}
</style>
