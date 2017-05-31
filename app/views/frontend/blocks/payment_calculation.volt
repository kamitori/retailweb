<style type="text/css">

.payment_calculation {
    font-family: 'Avenir Next LT Pro Bold';
    src: url('{{baseURL}}/fonts/Avenir/AvenirNextLTPro-Bold.woff') format('woff');
    font-weight: normal;
    font-style: normal;
    font-size: 20px;
	width: 100%;
	padding: 0;
	margin: 0;    
}
.payment_calculation td{
	padding:2.5% 1%;
}
.bold{
	font-weight: 600;
}
.center{
	text-align: center !important;
}

.payall{
	background-color: #f00;
	color: #fff;
	text-align: center;
}
.payall a{
	color: #fff;
}
.payment_calculation .disabled{
	background-color: #999;
}
.payment_calculation .payment_method{
	font-size: 16px;
	text-align: center;
}
.payment_calculation .amount{
	text-align: right;
}
.payment_calculation .input_amount_tender{
	text-align: right;
    width: 95%;
    height: 150%;
    border: none;
    background: transparent;
    padding-right: 5%;
    font-size: 1.5em;
}
.payment_calculation .amount_tender_text{
	font-size: 1.5em;
}
.btn-removecoupon,.btn-removepromo, .btn-payment-method{
	font-size: 1em;
	border: none;
	background: transparent;
	width: 100%;
}
#coupon_code, #promo_code{
	background: transparent;
	border: none;
	float: left;
	text-align: center;
	width: 100%;
}
#coupon_value, #error_coupon, #promo_value, #error_promo{
	top: 0px;
    width: 100%;
    text-align: center;
    float: left;
}
 #error_coupon, #error_promo{
 	color: red;
 }
.credit_labl{
	padding-bottom: 5px!important;
}
.credit_labl span{
	font-family: Arial;
	font-weight: 100;
	cursor: pointer;
	margin-top: 15px;
	display: block;
	float: left;
	width: 50%;
}
.credit_labl span label{
	cursor: pointer;
}

@media (max-width: 1024px){
	.credit_labl span{
		width: 100%;
	}
}

</style>

<table class="payment_calculation" cellpadding="0" cellspacing="0" class="bold" border="1">
	<tr>
		<td width="20%" class="payment_method">Payment Method</td>
		<td width="10%">&nbsp;</td>
		<td width="35%" style="text-align:center">Amount Tendered</td>
		<td width="35%" class="amount">Total Owing = $<span id="total_owning">{% if cart['main_total'] is defined %}<?php echo number_format((double)$cart['main_total'],2) ?>{% endif %}</span></td>		
	</tr>
	<!-- if items['voucher_code'] == "" -->
	<tr>
		<td class="payment_method">
			PROMO CODE
		</td>
		<td class="bt_apply_promo" align="center">
			{% if cart['promo_code'] ==''  %}
				<button type="button" class="btn btn-default btn-promo btn-payment-method" onclick="applyPromo(1)">Apply</button>
			{%else%}
				<button type="button" class="btn btn-default btn-removepromo" onclick="removePromo()">Clear</button>
			{% endif %}
		</td>
		<td class="amount">
			<input class="promo_input" type="text" placeholder="Promo Code" id="promo_code" value="{% if cart['promo_code'] is defined %}{{cart['promo_code']}}{%endif%}" {% if cart['main_total_promo'] is defined and cart['main_total_promo'] >0 %} disabled="disabled" {%endif%}>
			<div id="error_promo"></div>
			<div id="promo_value">
				{% if cart['promo_code'] !='' %}
					Total promo (-<?php echo number_format((double)$cart['main_total_promo'],2);?>)
				{%endif%}
			</div>
			<input data-payment-method="Promo code" id="discount_value_input" class="" type="hidden" value="{% if cart['main_total_promo'] is defined %}{{cart['main_total_promo']}}{%endif%}">
		</td>
		<td class="amount " id="discount_value" style="font-size: 1.5em;">
			{% if cart['main_total_promo'] is defined and cart['promo_code'] !='' %}
				<?php echo number_format((double)$cart['main_total_promo'],2);?>
			{%endif%}
		</td>
	</tr>
	<tr>
		<td class="payment_method">
			VOUCHER
		</td>
		<td class="bt_apply_voucher" align="center">
			{% if cart['discount_total'] is not defined or cart['discount_total'] ==0  %}
				<button type="button" class="btn btn-default btn-coupon btn-payment-method" onclick="applyCoupon(1)">Apply</button>
			{%else%}
				<button type="button" class="btn btn-default btn-removecoupon" onclick="removeCoupon()">Clear</button>
			{% endif %}
		</td>
		<td class="amount">
			<input class="coupon_input" type="text" placeholder="Coupon Code" id="coupon_code" value="{% if cart['voucher_code'] is defined %}{{cart['voucher_code']}}{%endif%}" {% if cart['discount_total'] >0 %} disabled="disabled" {%endif%}>
			<div id="error_coupon"></div>
			<div id="coupon_value">
				{% if cart['discount_total'] >0 %}
					Discount ({{cart['voucher_value']}}{{cart['voucher_type']}})
				{%endif%}
			</div>
			<input data-payment-method="Coupon code" id="discount_value_input" class="" type="hidden" value="{% if cart['discount_total'] is defined %}{{cart['discount_total']}}{%endif%}">
		</td>
		<td class="amount " id="discount_value" style="font-size: 1.5em;">
			{% if cart['discount_total'] is defined %}
				<?php echo number_format((double)$cart['discount_total'],2);?>
			{%endif%}
		</td>
	</tr>
	<tr>
		<td class="payment_method">CASH</td>
		<td class="disabled">&nbsp;</td>
		<td class="amount">
			<input data-payment-method="Cash" class="input_amount_tender" type="text" placeholder="0.00" value="{% if cart['payment_method']['Cash'] is defined %}{{cart['payment_method']['Cash']}}{%endif%}">
		</td>
		<td class="amount amount_tender_text">{% if cart['payment_method']['Cash'] is defined %}{{cart['payment_method']['Cash']}}{%else%}0.00{%endif%}</td>		
	</tr>
	<tr>
		<td class="payment_method credit_labl">
			CREDIT<br>
			<p>
				<span>
					<input type="radio" {%if credit['type'] is defined and credit['type']!='Master Card'%}checked="true"{%endif%} {%if credit['type'] is not defined %}checked="true"{%endif%} value="Visa Cart" name="credit_cart"  onclick="updatePaymentMethodCredit('Visa Cart');" id="visa_cart" />
					<label for="visa_cart">Visa Cart</label>
				</span>
				<span>
					<input type="radio" value="Master Card" name="credit_cart" onclick="updatePaymentMethodCredit('Master Card');" id="master_card" {%if credit['type'] is defined and credit['type']=='Master Card'%}checked="true"{%endif%} />
					<label for="master_card" >Master Card</label>
				</span>
			</p>
		</td>
		<td class="payall">
			<div class="payall_bt" onclick="setAmountTenderToAll(this)">ALL</div>
		</td>
		<td class="amount">
			<input data-payment-method="Visa Cart" class="input_amount_tender credit_cart_value" type="text" placeholder="0.00" value="{%if credit['value'] is defined %}{{credit['value']}}{%endif%}">
		</td>
		<td class="amount amount_tender_text">{%if credit['value'] is defined %}{{credit['value']}}{%else%}0.00{%endif%}</td>		
	</tr>
	<tr>
		<td class="payment_method">DEBIT</td>
		<td class="payall">
			<div class="payall_bt"  onclick="setAmountTenderToAll(this)">ALL</div>
		</td>
		<td class="amount">
			<input data-payment-method="Debit" class="input_amount_tender" type="text" placeholder="0.00" value="{% if cart['payment_method']['Debit'] is defined %}{{cart['payment_method']['Debit']}}{%endif%}">
		</td>
		<td class="amount amount_tender_text">{% if cart['payment_method']['Debit'] is defined %}{{cart['payment_method']['Debit']}}{%else%}0.00{%endif%}</td>		
	</tr>
	<tr id="on_account_line" style="display:table-row;">
		<td class="payment_method">ON ACCOUNT</td>
		<td class="payall">
			<div class="payall_bt"  onclick="setAmountTenderToAll(this)">ALL</div>
		</td>
		<td class="amount">
	        <input id="tender_onaccount" data-payment-method="On Account" class="input_amount_tender" type="text" placeholder="0.00" value="{% if cart['payment_method']['On Account'] is defined %}{{cart['payment_method']['On Account']}}{%endif%}">
	        <div class="dely_time" style="position:relative;margin-top:-78px;margin-left:108%;width:60%;height:73px;">
				<p class="dely_time_p">(Delivery Time)</p>
	            <div class='input-group date' id='datetimepicker1' style="margin-top: 1%;">
	                <input id="time_delivery" type='text' class="form-control" value="{{datetimepicker}}" style="font-size: 20px;height: 70px;margin: 0;" />
	                <span class="input-group-addon" style="height: 70px; width: 22%;">
	                    <span class="glyphicon glyphicon-calendar" style="font-size: 30px;"></span>
	                </span>
	            </div>
		        <script type="text/javascript">
		            $(function () {
		                $('#datetimepicker1').datetimepicker();
		            });
		        </script>
			</div>
		</td>
		<td class="amount amount_tender_text">
			
			{% if cart['payment_method']['On Account'] is defined %}{{cart['payment_method']['On Account']}}{%else%}0.00{%endif%}
		</td>		
	</tr>
	<tr>
		<td class="payment_method" style="background-color:#000; color:#fff;padding: 0;margin: 0;">
			<a id="finalize_payment_btn" href="javascript:void(0)" onclick="finalizeCheck()" style="display:none; color:#fff;">FINALIZE</a>
		</td>
		<td>&nbsp;</td>
		<td class="amount">Change Due:</td>
		<td id="change_due" class="amount" style="font-size:1.5em">0.00</td>		
	</tr>
	<input type="hidden" value="{{ cart['free_item_qty'] }}" id="free_item_qty" />
</table>
