<form action="" method="POST" accept-charset="utf-8" id="form_create_account">
	<div class="row main_wrap">
		<div class="main_wrap_title">Create an account for faster ordering & special offers</div>
		<div class="col-md-9">
			<div>
				<span class="ball">1</span>
				<span style="font-size: 135%; font-family: 'UnitedSans';">Tell us about yourself <span style="color:#fed75f">*required</span></span>
			</div>
			<div class="white_block">
				<div class="block">
					<p>Full name<span class="red">*</span></p>
					<input name="first_name" required aria-required="true" maxlength="50" class="form-control" type="text" placeholder="First name">
					<input name="last_name" required aria-required="true" maxlength="50" style="margin-top:20px;" class="form-control" type="text" placeholder="Last name">
				</div>
				<div class="block">
					<p>Email address<span class="red">*</span></p>
					<input type="email" onchange="check_user(this)" required aria-required="true" maxlength="100"  name="email" id="email" class="form-control" placeholder="e.g. you@account.com">
				</div>
				<div class="block">
					<p>Confirm email address<span class="red">*</span></p>
					<input type="email" required aria-required="true" maxlength="100"  onchange="check_reinput('email')" name="re_email" id="re_email" class="form-control" placeholder="e.g. you@account.com">
				</div>
				<div class="block">
					<p>Phone number<span class="red">*</span></p>
					<input required aria-required="true" name="phone" maxlength="20" class="form-control" type="tel" title="Phone must be number"pattern="[0-9]+" placeholder="xxxxxxxxxx">
				</div>
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
					<input type="checkbox" name="subscribe" value="">
					Email Opt In (by opting in you will receive emails, promotions and special offers from BanhMiSub.)
				</div>
			</div>

			<div style="margin-top:40px;">
				<span class="ball">2</span>
				<span style="font-size: 135%; font-family: 'UnitedSans';">Secure your account <span style="color:#fed75f">*required</span></span>
			</div>
			<div class="white_block">
				<div class="block">
					<p>Password<span class="red">*</span></p>
					<input name="password" id="password"  required aria-required="true"  class="form-control" type="password" placeholder="Password">
				</div>
				<div class="block">
					<p>Retype password<span class="red">*</span></p>
					<input name="re_password" id="re_password"  required aria-required="true" onchange="check_reinput('password')" class="form-control" type="password" placeholder="Retype password">
				</div>
				<!-- <div class="block">
					<p>Security answer<span class="red">*</span></p>
					<select name="" id="" class="select2 form-control" data-placeholder="--Select question--">
						<option value=""></option>
						<option value="1">Lorem ipsum dolor sit amet.</option>
						<option value="2">Aspernatur cupiditate itaque dolor? Amet.</option>
						<option value="3">Voluptatem velit deleniti facere mollitia.</option>
						<option value="4">Dignissimos doloremque, quidem nemo deserunt.</option>
					</select>
				</div>
				<div class="block">
					<p>Security answer<span class="red">*</span></p>
					<input class="form-control" type="text" placeholder="Answer">
				</div> -->
				<div class="block">
					I agree to the <a href="#">Terms of Use</a> and understand that my information will be used as described in the <a href="#">BanhMiSub Privacy Policy</a>.
				</div>
				<div class="block">
					<button class="btn btn-lg btn-danger" type="submit">Create account</button>
					&nbsp;&nbsp;&nbsp;
					<button class="btn btn-lg btn-default" type="reset">Cancel</button>
				</div>
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