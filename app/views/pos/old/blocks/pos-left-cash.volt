<div class="col-md-6 col-xs-6" id="left">
	<div class="tier-1" style="height:60px;">
		<div style="width:100%;float:left;" ng-controller="MyCtrl">
		 	<autocomplete ng-model="result" attr-placeholder="type to search items..." click-activation="true" data="items" on-type="doSomething" on-select="doSomethingElse">
		  	</autocomplete>
		</div>
	</div>
	<div class="tier-2" id="item-checkout" style="clear:both">
		{% if(arrListItem) %}
			{% for item in arrListItem %}
				<div class="row" id="<?php echo preg_replace('/\s+/','-',$item['productName']);?>">
					<div class="quantity col-md-1" data-quantity="{{item['quantity']}}" data-price="{{item['price']}}" onclick=_editQuantity("{{item['quantity']}}","<?php echo preg_replace('/\s+/','-',$item['productName']);?>")>
						<span class="badge">
							{% if (item['quantity']) %}
								{{item['quantity']}}
							{% else  %}
								0
							{% endif %}
						</span>
					</div>
					<div class="info col-md-5">
						<div class="name">
							{% if (item['productName']) %}
								{{item['productName']}}
							{% else  %}
								N/A
							{% endif %}
						</div>
					</div>
					<div class="list col-md-1">
						<i class="glyphicon glyphicon-th-list"></i>
					</div>
					<div class="col-md-2 price">
						<span class="badge">
							{% if (item['unitprice']) %}
								{{item['unitprice']}}
							{% else  %}
								$0.00
							{% endif %}
						</span>
					</div>
					<div class="col-md-2 amount">
						<span class="badge">
							{% if (item['totalprice']) %}
								{{item['totalprice']}}
							{% else  %}
								@$0.00
							{% endif %}
						</span>
					</div>
					<div class="col-md-1 delete" data-id="{{item['id']}}" onclick="deleteItem(this)">
						<i class="glyphicon glyphicon-remove-sign"></i>
					</div>
				</div>			
			{% endfor %}			
		{% endif %}
	</div>
	<div class="tier-3">
		<div class="row">
			<div class="col-md-9">
				<div class="col-md-11" 
				style="padding-left:0 !important" ng-controller="MyCtrl" id="search_customer" >
					<autocomplete ng-model="result" attr-placeholder="Add a Customer" click-activation="true" data="users" on-type="doSomethingUsers" on-select="doSomethingElseUsers">
			  		</autocomplete>					
				</div>
				<div class="col-md-1" style="line-height:32px">
					<a class="custom-btn-add" onclick="_AddNewCustomer()" id="btn-add-customer" href="javascript:void(0)" >+</a>
				</div>				
			</div>
			<div class="col-md-3" id="customer_name" style="padding-left:0;
				{% if(currentOrder['customerId'] > 0) %}
					display:block
				{% else %}
					display:none
				{% endif %}
			">
				<span class="badge col-md-9 display_name_customer" id="_customer_display_name">{{currentOrder['customerName']}}</span>
				<span class="col-md-1 pull-right delete" onclick="removeCustomer(this)" id="deleted_customer">
					<i class="glyphicon glyphicon-remove-sign"></i>
				</span>
			</div>
		</div>
	</div>
	<div class="tier-4">
		<div class="row">
			<div class="col-md-6 text-left">
				Sub-total
			</div>
			<div class="col-md-6 text-right">
				<span id="sub-total">{{subtotal}}</span>
				<input id="hiddensubtotal" type="hidden" value="{{hiddensubtotal}}" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-left">
				Tax (NZ GST)
			</div>
			<div class="col-md-6 text-right">
				<span id="sub-tax">{{tax}}</span>
			</div>
		</div>
		<div class="row" id="total">
			<div class="col-md-6 text-left">
				TOTAL
			</div>
			<div class="col-md-6 text-right">
				<span id="_total">{{total}}</span>
			</div>
		</div>
		<div class="row" id="to_pay">
			<div class="col-md-6 text-left">
				TO PAY
			</div>
			<div class="col-md-6 text-right">
				<span id="to-pay">{{topay}}</span>
			</div>
		</div>
	</div>
	<div class="tier-5">
		<div class="row">
			<div class="col-md-2 text-left">
				<button class="btn btn-invert btn-lg" onclick="VoidOrder()">Void</button>
			</div>
			<div class="col-md-8 text-center">
				<button class="btn btn-invert btn-lg" id="clickPark" onclick="parkOrder()">Park (F5)</button>
				<button class="btn btn-invert btn-lg" onclick="NotesOrder()">Note</button>
				<button class="btn btn-invert btn-lg" onclick="AddCaculator()">Discount</button>
			</div>
			<div class="col-md-2 text-right">
				<button class="btn btn--success btn-lg" onclick="payorder()">Pay</button>
			</div>
		</div>
	</div>
</div>
{% if(arrListItem) %}
	<div id="printonly">
		<div class="col-md-12 col-xs-12">
			<h3>
				Banh my Sub
			</h3>
			<h1 style="border-bottom-style: dashed;padding-top:5px;"></h1>
		</div>
		<div class="col-md-12 col-xs-12">
			<h4>Receipt/Tax Invoice</h4>
			<div>Invoiced#:{{currentOrder['code']}}</div>
			<div>{{currentOrder['created_at']}}</div>
			<div>Serve By: {{currentOrder['userName']}}</div>
			<div id="sales_list_container" style="padding:0 !important;margin:0 !important">
		        <div class="table-wrapper" style="padding-left:0 !important;padding-right:0 !important">
		           <table class="item_list table-padded" id="register-sale-list" style="width:100% !important;border:none">            
		              <tbody>
		              	{% for item in arrListItem %}
		              		<tr style="border-top:3px solid">
			              		<td style="width:10%;border:none">{{item['quantity']}}</td>
			              		<td style="width:50%;border:none">{{item['productName']}}</td>
			              		<td style="width:20%;border:none;text-align:right">{{item['unitprice']}}</td>
			              		<td style="width:20%;border:none;text-align:right">{{item['totalprice']}}</td>
			              	</tr>
		              	{% endfor %}
		              	<tr style="border-top:3px solid">
		              		<td style="border:none">&nbsp;</td>
		              		<td colspan=2 style="border:none">Subtotal</td>
		              		<td style="width:20%;border:none;text-align:right">{{subtotal}}</td>
		              	</tr>
		              	<tr>
		              		<td style="border:none">&nbsp;</td>
		              		<td colspan=2 style="border:none;border-top:3px solid">Tax</td>
		              		<td style="width:20%;border:none;border-top:3px solid;text-align:right">{{tax}}</td>
		              	</tr>
		              	<tr style="">
		              		<td style="border:none">&nbsp;</td>
		              		<td colspan=2 style="border:none;border-top:3px solid">Total</td>
		              		<td style="width:20%;border:none;border-top:3px solid;text-align:right">{{total}}</td>
		              	</tr>
		              	<tr style="">
		              		<td style="border:none">&nbsp;</td>
		              		<td colspan=2 style="border:none;border-top:3px solid">To Pay</td>
		              		<td style="width:20%;border:none;border-top:3px solid;text-align:right">{{topay}}</td>
		              	</tr>
		              	<tr style="">
		              		<td style="border:none">&nbsp</td>
		              		<td colspan=3 style="border:none;border-top:3px solid;text-align:center"><h4>Customer Copy</h4></td>
		              	</tr>
		              </tbody>
		           </table>
		        </div>
		    </div>
		</div>
		<div class="col-md-12 col-xs-12">		
			<h1 style="border-bottom-style: dashed;padding-top:5px;"></h1>
		</div>
		<div class="col-md-12 col-xs-12">
			<h3>
				Thank you. See you again.
			</h3>
		</div>
	</div>	
{% endif %}