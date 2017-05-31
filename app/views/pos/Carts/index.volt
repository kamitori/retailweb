<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="author" content="Anvy Developers">
        <title>BanhMiSub.com</title>
        <link href="http://fonts.googleapis.com/css?family=Roboto:400,900" rel="stylesheet" type="text/css" media="all">
        {{ assets.outputCss() }}
    </head>
 	<body>
 		<form method="POST" action="{{ baseURLPos }}/orders/create-order">
 		<div id="header_cart" class="bartop">
 			<div class="top_left">
 					<div class="block">Pizza Cart:</div>
 					<div class="block white-text"><span class="amount">{{ cart['quantity'] }}</span> Item In Cart</div>
 					<div class="block">
 						<a href="javascript:history.back()">
 						<i class="fa fa-shopping-cart"></i> Hide cart <i class="fa fa-caret-down"></i>
 						</a>
 					</div>
 			</div>
			<div class="user_info">
				<div class="row">
					<div class="col-xs-5">
						<table>
							<tr>
								<td>Sub total</td>
								<td class="text-right">$<span class="total_price">{{ cart['total']|format }}</span></td>
							</tr>
							<tr>
								<td>GST/HST</td>
								<td class="text-right">$0.00</td>
							</tr>
							<tr>
								<td>PST</td>
								<td class="text-right">$0.00</td>
							</tr>
							<tr>
								<td>Delivery</td>
								<td class="text-right">$0.00</td>
							</tr>
							<tr>
								<td>Discount</td>
								<td class="text-right">$0.00</td>
							</tr>
						</table>
					</div>
					<div class="col-xs-7">
						{% if session_user != false %}
						<img src="/themes/pizza-pizza/images/avatar.jpg" alt="" class="avatar">
						&nbsp;&nbsp;&nbsp;
						<a class="btn btn-default" href="{{baseURLPos}}/user/logout">Log out</a>
						<span class="name">{{session_user['first_name']}} {{session_user['last_name']}}</span>
						{% endif %}
					</div>
				</div>
			</div>
			<div class="total">
				$<span class="total_price">{{ cart['total']|format }}</span>
			</div>
			<!-- <div class="bottom_left">
				<h3 class="uppercase">Select:</h3>
				<label><input type="radio" name="chk_type_cart" id="chk_delivery" value="delivery" checked>Delivery</label>
				<label><input type="radio" name="chk_type_cart" id="chk_pickup" value="pickup">Pickup</label>
			</div> -->
			<div class="coupon">
				<span class="title">Coupons:</span>
				<input type="text" id="coupon_code" value="" placeholder="Coupon Code" class="form-control">
				<button class="btn btn-success">Apply</button>
			</div>
 		</div>
 		
 		<div id="checkout_cart" class="row">
 			<div class="col-xs-8">
				<div id="cart_tab">
					<div id="cart_tab_nav">
						<ul>
							<li data-id="pickup"  class="active">
								Address
							</li>
							<li data-id="payment">
								Information
							</li>
							<li data-id="delivery">
								Date/Time Delivery
							</li>
						</ul>
					</div>
					<div id="cart_tab_content">
						<div class="content active" id="pickup">
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Address</label>
								</div>
								<div class="col-xs-8">
									<input type="text" name="address" value="" placeholder="Address" class="form-control" required>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Town/City</label>
								</div>
								<div class="col-xs-8">
									<input type="text" name="town_city" value="" placeholder="Town/City" class="form-control" required>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Province/State</label>
								</div>
								<div class="col-xs-8">
									<select class="form-control" name="province" required>
										{% for province in provinces %}
										<option value="{{province.key}}-{{ province.name }}">{{ province.name }}</option>
										{% endfor %}
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Postal code</label>
								</div>
								<div class="col-xs-8">
									<input type="text" name="postal_code" value="" placeholder="Postal code" class="form-control">
								</div>
							</div>
						</div>
						<div class="content" id="payment">
							<!-- <div class="radio_input">
								<input type="radio" id="check1" name="payment" value="pay_at_door" checked/>
								<label for="check1">Pay at door</label>
							</div>
							<div class="radio_input">
								<input type="radio" id="check2" name="payment" value="pay_online"/>
								<label for="check2">Pay online</label>
							</div>
							<select name="" class="form-control" id="payment_option">
								<option value="">Lorem ipsum dolor sit.</option>
								<option value="">Voluptatum, neque commodi cum!</option>
								<option value="">Officiis numquam delectus nostrum.</option>
								<option value="">Consectetur dignissimos at ut?</option>
							</select> -->
							<br/>
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Your name</label>
								</div>
								<div class="col-xs-8">
									<input type="text" name="name" value="" placeholder="Your name" class="form-control">
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4 text-right">
									<label for="">Phone number</label>
								</div>
								<div class="col-xs-8">
									<input type="text" name="phone_number" value="" placeholder="Phone number" class="form-control">
								</div>
							</div>
						</div>
						<div class="content form-inline" id="delivery">
							<label for="">Select time to delevery:</label><br/>
							<input id="select_day_cart" type="text" onchange="create_hour(this,'#select_hour_cart');" class="form-control" style="width:150px;" data-date-start-date="+0d">
							</select>
							<select id="select_hour_cart" class="form-control" style="width:150px;">
							</select>
						</div>
					</div>
				</div>
 			</div>
 			<div class="col-xs-4">
 				<div class="" id="cart_submit">
 					{% if session_user == false %}
 					<div id="cart_info">
 					<p>Sign in to continue or <a href="#" onclick="create_account_popup();">register new account</a></p>
 					<p><button class="btn btn-success btn-block btn-lg" onclick="sign_in_popup();" type="button">Sign in</button></p>
 					<p class="text-center">or</p>
 					<button class="btn btn-success btn-block btn-lg" type="submit">Continue as guest</button>
					</div>
 					{% else %}
 					<div id="cart_info">
 						Hello, {{session_user['first_name']}}! Click next to order.
 					</div>
 					<button class="btn btn-success btn-block btn-lg" type="submit">Next</button>
 					{% endif %}
 				</div>
 			</div>
 		</div>
 		</form>
 		<div id="list_product_cart">
 			<div class="scroller">
 				{% for index,item in cart['items'] %}
 				<div class="item" id="item-{{ index }}">
	 				<div class="block thumb">
	 					<img src="{{item['image']}}" alt="">
	 				</div>
	 				<div class="block info">
	 					<span class="product_name">{{item['name']}}</span>
	 					<p>{{item['description']}}</p>
	 				</div>
	 				<div class="block price">
	 					$<span>{{ item['sell_price'] }}</span>
	 				</div>
	 				<div class="block quantity">
	 					<div class="input_number">
							<button class="btn down">-</button>
							<input type="text" name="" value="{{item['quantity']}}" maxlength="3" data-cart-key='{{index}}' onchange="update_quantity(this)">
							<button class="btn up">+</button>
						</div>
						<div><button class="btn" onclick="delete_cart('{{index}}')" data-cart-key='{{index}}'>Delete</button></div>
						{% if item['options']|length %}
						<div><button class="btn" onclick="edit_cart(this)" data-cart-key='{{index}}' data-product-id="{{item['_id']}}" data-quantity="{{item['quantity']}}" data-price="{{ item['sell_price'] }}">Option</button></div>
						{% endif %}
	 				</div>
	 				<div class="block amount">
	 					$<span class="item-total">{{ item['total']|format }}</span>
	 				</div>
	 			</div>
	 			{% endfor%}
 			</div>
 		</div>
 		<div class="popup_item popup-shapdow" style="display:none;">
		    <div class="header_line barpopup">
		        <div class="close_popup" onclick="closePopup();">x</div>
		    </div>
		    <div class="popup_content">
		        <div class="productitem" style="visibility:hidden; height:0px;margin:0;padding: 0;min-height:0;">
		            <div class="popup_title" style="visibility:hidden; height:0px;margin:0;padding: 0;min-height:0;">
		                <h2>Banh mi SUB</h2>
		                <p class="description_item"></p>
		            </div>
		            <div class="popup_image" style="visibility:hidden; height:0px;margin:0;padding: 0;min-height:0;">
		                <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="Banh mi" />
		            </div>
		        </div>
		        <div class="popup_prices" style="visibility:hidden; height:0px;margin:0;padding: 0;min-height:0;">
		            <div class="popup_amount">
		                <button class="btn down mainbt" onclick="downQty('popup_qty_main')">-</button>
		                <input class="popup_qty" type="text" value="1" id="popup_qty_main" />
		                <button class="btn up mainbt" onclick="upQty('popup_qty_main')">+</button>
		                <input id="sell_price_popup_qty_main" value="0" type="hidden" />
		            </div>
		            <div class="popup_price">
		                $10.00
		            </div>
		            <div class="popup_add_bt">
		                
		                <input class="item_id" type="hidden" value="" />
		            </div>
		        </div>
		        <div class="tabs">
		        </div>
		        <div class="text-center" style="margin-top:15px;">
		        	<button class="btn" onclick="saveEditCart(this)" id="save_edit_cart">Save</button>
		        </div>
		    </div>
		</div>
        {% if session_user == false %}
            {{ partial('frontend/Users/signin')}}
            {{ partial('frontend/Users/createAccount')}}
        {% endif %}
     	<script type="text/javascript">
            var appHelper = {
                baseURL: '{{ baseURLPos }}',
				JT_URL: '{{ JT_URL }}'
            };
        </script>
    	{{ assets.outputJs() }}
    	{{ assets.outputJs('pageJS') }}
 	</body>
</html>