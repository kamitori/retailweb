<table class="table table-bordered" cellpadding="0" cellspacing="0">
	<thead>
		<tr class="info">
		    <td colspan="2">Product</td>
		    <td align="right">Price</td>
		    <td align="right">Qty</td>
		    <td align="right">Total</td>
	    </tr>
	</thead>
	{% set comid = -1 %}
	{% set total_cart = 0 %}
	{% for index, item in cart['items'] %}

	{% if item['combo_id'] is defined and comid != item['combo_id'] %}
	{% set comid = item['combo_id'] %}
		<tr class="">
		    <td colspan="2">
		        <h3>
		        	<b>
		        		Combo {{item['combo_id']+1}}: {{ combo_list[item['combo_id']]['name'] }} (discount {{item['discount']*100}}%)
		        	</b>
		        </h3>
		    </td>
		    <td></td>
		    <td  align="right">
		        <b>{{ combo_list[item['combo_id']]['quantity'] }}</b>
		    </td>
		    <td align="right">
		        <b>{{dinhdangtien(combo_list[item['combo_id']]['total'])}}</b>
		        {% set total_cart += combo_list[item['combo_id']]['total'] %}
		    </td>
		 </tr>
	{%endif%}
	{% if item['combo_id'] is defined %}
		<tr class="active iscombo">
			<td width="5%"></td>
		    <td>
		{% else %}
		<tr class="">
		    <td colspan="2">
	{% endif %}
			
    		<h3>
	        	<b>
	        		{{ item['name'] }}
	        		{% if item['combo_id'] is defined %} ({{ item['quantity'] }}) {% endif %}       		
	        	</b>
	        </h3>
	       {% if item['options']|length >0 %}
	            {% for opp in item['options'] %}
	                {% if opp['is_change']==1 %}
	                    <span class="option_list_se" data-idp="{{opp['_id']}}">
	                    {% if opp['group_type'] is defined and opp['group_type']!='Exc' and opp['isfinish']==0 %}
	                        <b>{{opp['quantity']}}</b>:
	                    {% endif %}
	                    {{opp['name']}}
	                    {% if opp['isfinish']==1 %}
	                        <b>({{finish_option[opp['_id']][opp['finish']]}})</b>,
	                    {% endif %}
	                    </span>
	                {% endif %}
	            {% endfor %}
	        {% endif %}   
	    </td>
	    <td align="right">
	        {{dinhdangtien(item['sell_price'])}}
	    </td>
	    <td align="right">
	    	{% if item['combo_id'] is not defined %}
	        <b>{{ item['quantity'] }}</b>
	        {%endif%}
	    </td>
	    <td align="right">
	    	{% if item['combo_id'] is not defined %}
	        	<b>{{dinhdangtien((item['sell_price']*item['quantity']))}} </b>
	        	{% set total_cart += item['sell_price']*item['quantity'] %}
	        {%endif%}
	        <br />
	        {% if item['total_promo'] is defined and item['quantity_promo'] is defined and item['total_promo']>0 %}
	        	<span style="font-size:12px;">Promo({{item['quantity_promo']}}pcs) </span>
	        	<span style="font-size:23px;color:red;padding-right:3%;"> - {{dinhdangtien((item['total_promo']))}}</span>
	        {%endif%}
	    </td>
	</tr>
	{% endfor %}

	{% if cart['discount_total'] is defined  and cart['discount_total']>0 %} 
	<tr class="">
		<td colspan="2">
		    <h3><b>Discount</b></h3>
	    </td>
	    <td align="right">-{{ dinhdangtien(cart['discount_total']) }}</td>
	    <td align="right">1</td>
	    <td align="right">-{{ dinhdangtien(cart['discount_total']) }}</td>
	</tr>
	{% endif %}

	<tr class="line ">
		<td colspan="3">Subtotal:</td>	    
	    <td align="right">
    	</td>
	    <td align="right">
	    	<b>
	    	{% if cart['total'] is defined %} {{dinhdangtien(cart['total']-cart['discount_total'])}} {% else %}
	    		0$ 
	    	{% endif %}
	    	</b>
	    </td>
	</tr>
	<tr class="">
	    <td colspan="4">
	    	Tax :
	    </td>	  
	    <td align="right">
	    	<b>{% if cart['tax'] is defined %} 
		    	{{ dinhdangtien(cart['tax']) }}
		    	{% set total_cart += cart['tax'] %}
		    	{% else %}
		    		0$
		    {% endif %}
		    </b>
	    </td>
	</tr>
	<!-- <tr class="">
	    <td colspan="4">
	    	Discount:
	    </td>	  
	    <td align="right">
	    	<b>{% if cart['discount_total'] is defined %} 
		    	-{{ dinhdangtien(cart['discount_total']) }}
		    	{% else %}
		    		0$
		    {% endif %}
		    </b>
	    </td>
	</tr> -->
	<tr class="info">
	    <td colspan="4">
	    	Sum total:
	    </td>
	    <td align="right" style="color:red;">
	    	<b>{% if cart['main_total'] is defined %} {{dinhdangtien(cart['main_total'])}} {% endif %}</b>
	    </td>	   	    
	    <input type="hidden" id="hidden_total_money" value="{{total_cart}}" />
	</tr>
</table>
<div class="payment_method_box">
	<table class="table table-bordered" cellpadding="0" cellspacing="0">
		<tr class="info">
			<td>Payment Method</td>
			<td align="right">Amount Tendered</td>
			<td align="right">Change Due</td>
		</tr>
		<tr>
			<td>
				{% if cart['payment_method'] is defined %}
					<table style="width:100%">
					{% for method, values in cart['payment_method'] %}
						<tr><td width="50%" style="font-size:1em;">{{method}} :</td><td width="50%" align="right" style="font-size:1em;">{{dinhdangtien(values)}}</td></tr>
					{% endfor %}
					</table>
				{% endif %}
			</td>
			<td align="right">{% if cart['amount_tendered'] is defined %}{{dinhdangtien(cart['amount_tendered'])}}{% endif %}</td>
			<td align="right">
				{% if cart['amount_tendered'] is defined %}
					<b>{{dinhdangtien(cart['amount_tendered']-cart['main_total'])}}</b>
				{% endif %}
			</td>
		</tr>
	</table>
</div>