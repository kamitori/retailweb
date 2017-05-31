<div class="nav_cart row">
	<div class="col-xs-3 step here" data-id="order_summary">
		<span>Order Summary</span>
		<span class="end"></span>
	</div>
	<div class="col-xs-3 step" data-id="checkout">
		<span>Checkout</span>
		<span class="end"></span>
	</div>
	<div class="col-xs-3 step complete" data-id="details">
		<span>Details</span>
		<span class="end"></span>
	</div>
	<div class="col-xs-3 step" data-id="payment">
		<span>Payment</span>
		<span class="end"></span>
	</div>
</div>
<div class="main_cart" id="order_summary">
	<div class="row" id="head">
		<div class="col-xs-6">
			<div class="col-xs-5">
				<button class="btn btn-carryout">Carryout From</button>
			</div>
			<div class="col-xs-7">
				<span id="address">1440 52nd St NE Calgary AB T2A 4T8</span>
				<a href="#" id="change_address">Change</a>
			</div>
		</div>
		<div class="col-xs-6" style="line-height: 45px;">
				<div class="col-xs-5 text-justify">
					<a href="#" style="font-size: 18px;">Continue shopping</a>
				</div>
				<div class="col-xs-2 text-justify" style="font-size: 18px;">Or</div>
				<div class="col-xs-5 text-justify"><button class="btn btn-lg btn-danger btn-block uppercase">Checkout</button></div>	
		</div>
	</div>

	<div class="row" id="list_product">
		<div class="item">
			<div class="col-xs-2">
				<img class="img-thumb" src="http://pos.banhmisub.com/images/products/test%20011.02-10-15.jpg" alt="">
			</div>
			<div class="col-xs-6">
				<span class="uppercase">THE ULTIMATE BANH MI</span>
			</div>
			<div class="col-xs-2 quantity">
				QTY
				<br/>
				<div class="input_number">
					<input type="text" name="" value="1" maxlength="2" />
					<span class="spinner">
						<i class="fa fa-caret-up up"></i>
						<i class="fa fa-caret-down down"></i>
					</span>
				</div>
				<br/>
				<a href="#">Delete</a>
			</div>
			<div class="col-xs-2 text-right price">
				$5.99
			</div>
		</div>
		<div class="item">
			<div class="col-xs-2">
				<img class="img-thumb" src="http://pos.banhmisub.com/images/products/test%20011.02-10-15.jpg" alt="">
			</div>
			<div class="col-xs-6">
				<span class="uppercase">THE ULTIMATE BANH MI</span>
			</div>
			<div class="col-xs-2 quantity">
				QTY
				<br/>
				<div class="input_number">
					<input type="text" name="" value="1" maxlength="2" />
					<span class="spinner">
						<i class="fa fa-caret-up up"></i>
						<i class="fa fa-caret-down down"></i>
					</span>
				</div>
				<br/>
				<a href="#">Delete</a>
			</div>
			<div class="col-xs-2 text-right price">
				$5.99
			</div>
		</div>
		<div class="item">
			<div class="col-xs-2">
				<img class="img-thumb" src="http://pos.banhmisub.com/images/products/test%20011.02-10-15.jpg" alt="">
			</div>
			<div class="col-xs-6">
				<span class="uppercase">THE ULTIMATE BANH MI</span>
			</div>
			<div class="col-xs-2 quantity">
				QTY
				<br/>
				<div class="input_number">
					<input type="text" name="" value="1" maxlength="2" />
					<span class="spinner">
						<i class="fa fa-caret-up up"></i>
						<i class="fa fa-caret-down down"></i>
					</span>
				</div>
				<br/>
				<a href="#">Delete</a>
			</div>
			<div class="col-xs-2 text-right price">
				$5.99
			</div>
		</div>
	</div>

	<div  class="row" id="random_product">
		<div class="col-xs-5 ">
			<h3 class="text-left">Boneless Bites - 8 Piece</h3>
			<p>
				Crispy, breaded, 100% white meat chicken breast pieces tossed in your choice of sauce.
			</p>
		</div>
		<div class="col-xs-3 text-center">
			<img class="img-thumb" src="http://pos.banhmisub.com/images/products/test%20011.02-10-15.jpg" alt="">
		</div>
		<div class="col-xs-4">
			<div class="select_product ">
				<select name="" id="" class="selectpicker">
					<option value="">Lorem ipsum dolor sit amet.</option>
					<option value="">Totam accusantium at repellendus explicabo!</option>
					<option value="">Placeat delectus voluptates, inventore beatae.</option>
				</select>
			</div>
			<div class="input_number">
				<input type="text" name="" value="1" maxlength="2" />
				<span class="spinner">
					<i class="fa fa-caret-up up"></i>
					<i class="fa fa-caret-down down"></i>
				</span>
				<button class="btn btn-danger uppercase">Add to Order</button>
			</div>
		</div>
	</div>

	<div  class="row" id="total">
		<div class="col-xs-6 col-xs-offset-6">
			<div class="col-xs-12">
				<span class="uppercase">Coupon Code</span class="uppercase">
			</div>
			<div class="col-xs-12 line">
				<input type="text" name="" value="" class="form-control" placeholder="Enter your code" style="width:70%;display: inline;">
				<button class="btn btn-default uppercase" id="submit_coupon">Apply</button>
			</div>
			<div class="col-xs-6 line text-left uppercase">
				Subtotal
			</div>
			<div class="col-xs-6 line text-right">
				$17.97
			</div>
			<div class="col-xs-6 line text-left uppercase">
				Tax
			</div>
			<div class="col-xs-6 line text-right">
				$1.10
			</div>
			<div class="col-xs-6 line text-left uppercase">
				Your Total
			</div>
			<div class="col-xs-6 line text-right" id="total_amount">
				$19.07
			</div>
			<div class="col-xs-12 line">
				<p>Additional charge may apply based on payment type.</p>
			</div>
			<div class="col-xs-12 col-xs-offset-6 line text-right">
				<p>
					<button class="btn btn-danger btn-lg uppercase">Checkout</button>
				</p>
				<p>
					<a href="#" class="small">Continue Shopping</a>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="main_cart" id="checkout">
	<div class="row">
		<div class="col-xs-8">
			<div class="row" id="head">
				<div class="col-xs-6">
					<h2 class="text-left">Not got an account?</h2>
				</div>
				<div class="col-xs-10">
					<div class="col-xs-6">
						<button class="btn btn-danger uppercase">Continue as a guest</button>
					</div>
					<div class="col-xs-6">
						<h4 class="text-left"><a href="#">Create an account</a></h4>
					</div>
				</div>
			</div>

			<div class="row" id="signin">
				<div class="col-xs-6">
					<h2 class="text-left">Done this before?</h2>
				</div>
				<div class="col-xs-10">
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
				</div>
			</div>
		</div>

		<div class="col-xs-4">
			<div id="my_order">
				<div class="title">
					My Order
				</div>
				<div class="list_product">
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="product_name">Garlic Bread - Regular Size(4)</span>
							<span class="note">With No Cheese</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$17.97</span>
						</div>
					</div>
				</div>
				<div class="total">
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Subtotal</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$17.97</span>
						</div>
					</div>
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Tax</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$1.10</span>
						</div>
					</div>
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Your Total</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$19.07</span>
						</div>
					</div>
				</div>
			</div>

			<div id="carryout">
				<button class="btn">Carryout from</button>
				<p class="address">1440 52nd St NE Calgary AB T2A 4T8</p>
				<p><a href="#">Get directions to our store</a></p>
			</div>
		</div>
	</div>
</div>

<div class="main_cart" id="details">
	<div class="row">
		<div class="col-xs-8">
			<div class="row" id="head">
				<h3 class="text-left">Your store is currently closed</h3>
				<p class="red">Your order will be processed on the date selected below.</p>
				<div class="col-xs-4">
					<span class="uppercase lead">Now</span> <input type="radio" name="ship_time" id="ship_time_now" value="0" checked onchange="ship_time()"/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span class="uppercase lead">Later</span> <input type="radio" name="ship_time" id="ship_time_later" value="1" onchange="ship_time()"/>
				</div>
				<div class="col-xs-4 ship_later">
					<select name=""class="form-control select2" id="select_day_ship" onchange="create_hour(this,'#select_time_ship')" data-placeholder="Select Date">
					</select>
				</div>
				<div class="col-xs-4 ship_later">
					<select name="" class="form-control select2" id="select_time_ship" data-placeholder="Select Time">
						<option value="0" label="11:00 AM">11:00 AM</option>
						<option value="1" label="11:15 AM">11:15 AM</option>
						<option value="2" label="11:30 AM">11:30 AM</option>
						<option value="3" label="11:45 AM">11:45 AM</option>
						<option value="4" label="12:00 PM">12:00 PM</option>
						<option value="5" label="12:15 PM">12:15 PM</option>
						<option value="6" label="12:30 PM">12:30 PM</option>
						<option value="7" label="12:45 PM">12:45 PM</option>
						<option value="8" label="01:00 PM">01:00 PM</option>
						<option value="9" label="01:15 PM">01:15 PM</option>
						<option value="10" label="01:30 PM">01:30 PM</option>
						<option value="11" label="01:45 PM">01:45 PM</option>
						<option value="12" label="02:00 PM">02:00 PM</option>
						<option value="13" label="02:15 PM">02:15 PM</option>
						<option value="14" label="02:30 PM">02:30 PM</option>
						<option value="15" label="02:45 PM">02:45 PM</option>
						<option value="16" label="03:00 PM">03:00 PM</option>
						<option value="17" label="03:15 PM">03:15 PM</option>
						<option value="18" label="03:30 PM">03:30 PM</option>
						<option value="19" label="03:45 PM">03:45 PM</option>
						<option value="20" label="04:00 PM">04:00 PM</option>
						<option value="21" label="04:15 PM">04:15 PM</option>
						<option value="22" label="04:30 PM">04:30 PM</option>
						<option value="23" label="04:45 PM">04:45 PM</option>
						<option value="24" label="05:00 PM">05:00 PM</option>
						<option value="25" label="05:15 PM">05:15 PM</option>
						<option value="26" label="05:30 PM">05:30 PM</option>
						<option value="27" label="05:45 PM">05:45 PM</option>
						<option value="28" label="06:00 PM">06:00 PM</option>
						<option value="29" label="06:15 PM">06:15 PM</option>
						<option value="30" label="06:30 PM">06:30 PM</option>
						<option value="31" label="06:45 PM">06:45 PM</option>
						<option value="32" label="07:00 PM">07:00 PM</option>
						<option value="33" label="07:15 PM">07:15 PM</option>
						<option value="34" label="07:30 PM">07:30 PM</option>
						<option value="35" label="07:45 PM">07:45 PM</option>
						<option value="36" label="08:00 PM">08:00 PM</option>
						<option value="37" label="08:15 PM">08:15 PM</option>
						<option value="38" label="08:30 PM">08:30 PM</option>
						<option value="39" label="08:45 PM">08:45 PM</option>
						<option value="40" label="09:00 PM">09:00 PM</option>
						<option value="41" label="09:15 PM">09:15 PM</option>
						<option value="42" label="09:30 PM">09:30 PM</option>
						<option value="43" label="09:45 PM">09:45 PM</option>
						<option value="44" label="10:00 PM">10:00 PM</option>
						<option value="45" label="10:15 PM">10:15 PM</option>
						<option value="46" label="10:30 PM">10:30 PM</option>
						<option value="47" label="10:45 PM">10:45 PM</option>
					</select>
				</div>
			</div>

			<div class="row" id="info">
				<h3 class="text-left">Your Detail <span class="red">require*</span></h3>
				<div class="col-xs-10">
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
						<p>Delivery driver instructions</p>
						<input required aria-required="true" name="note" class="form-control" type="text" placeholder="Example: Don't ring doorbell">
					</div>
					<div class="block">
						<p>Send me deals by</p>
						<input type="checkbox" name="" value=""> Email
					</div>
					<div class="block">
						<button class="btn btn-danger btn-lg">Continue</button>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-4">
			<div id="my_order">
				<div class="title">
					My Order
				</div>
				<div class="list_product">
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="product_name">Garlic Bread - Regular Size(4)</span>
							<span class="note">With No Cheese</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$17.97</span>
						</div>
					</div>
				</div>
				<div class="total">
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Subtotal</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$17.97</span>
						</div>
					</div>
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Tax</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$1.10</span>
						</div>
					</div>
					<div class="row item">
						<div class="col-xs-8 text-left">
							<span class="uppercase">Your Total</span>
						</div>
						<div class="col-xs-4 text-right">
							<span class="price">$19.07</span>
						</div>
					</div>
				</div>
			</div>

			<div id="carryout">
				<button class="btn">Carryout from</button>
				<p class="address">1440 52nd St NE Calgary AB T2A 4T8</p>
				<p><a href="#">Get directions to our store</a></p>
			</div>
		</div>
	</div>
</div>