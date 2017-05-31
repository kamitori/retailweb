<!DOCTYPE html>
<html>
<head>
<style type="text/css">
@font-face {
    font-family: 'Avenir Next LT Pro Bold';
    src: url('{{baseURL}}/fonts/Avenir/AvenirNextLTPro-Bold.woff') format('woff');
    font-weight: normal;
    font-style: normal;
}
table{
	width: 100%;
	padding: 0;
	margin: 0;
}
td{
	padding:1%;
}
thead>tr>td{
	background: #DAD8D8;
	font-weight: bold;
	padding:2%;
}
tr>td:nth-last-child(2), tr>td:nth-last-child(3){
	text-align: right;
}
tr>td:last-child{
	font-weight: bold;
	text-align: right;
	padding-right: 5px;
}
.line td{
	border-top: 1px dashed #000;
}
.bgbox{
	position: absolute;
	position: fixed;
	top: 0;
	height: 60px;
	background: white;
	width: 100%;
}
.bgbox button{
	font-size: 18px;
    color: white;
	margin:2% 2% 2% 35%;
	float: left;
	
	font-family: 'Avenir Next LT Pro Bold';
	text-transform: uppercase;
	border-color: #ccc;
	display: inline-block;
    padding: 6px 12px;
   	outline: none!important;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    border-radius: 4px;
	background: rgb(66,62,63);
    background: -moz-linear-gradient(top, rgba(66,62,63,1) 0%, rgba(51,47,48,1) 53%, rgba(31,28,29,1) 100%);
    background: -webkit-linear-gradient(top, rgba(66,62,63,1) 0%,rgba(51,47,48,1) 53%,rgba(31,28,29,1) 100%);
    background: linear-gradient(to bottom, rgba(66,62,63,1) 0%,rgba(51,47,48,1) 53%,rgba(31,28,29,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#423e3f', endColorstr='#1f1c1d',GradientType=0 );
}
@media print{    
    .no-print, .no-print *{
        display: none !important;
    }
    footer {page-break-after: always;}
}
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
	    <td>Product</td>
	    <td>Price</td>
	    <td>Qty</td>
	    <td>Total</td>
	    </tr>
	</thead>
	{% for index, item in cart['items'] %}
	<tr>
	    <td>
	        <h3>{{ item['name'] }}</h3>
	         {% if item['options']|length >0 %}
	            {% for opp in item['options'] %}
	                {% if opp['is_change']==1 %}
	                    <p data-idp="{{opp['_id']}}">
	                    {% if opp['group_type'] is defined and opp['group_type']!='Exc' and opp['isfinish']==0 %}
	                        <b>{{opp['quantity']}}</b>:
	                    {% endif %}
	                    {{opp['name']}}
	                    {% if opp['isfinish']==1 %}
	                        <b>({{finish_option[opp['_id']][opp['finish']]}})</b>
	                    {% endif %}
	                    </p>
	                {% endif %}
	            {% endfor %}
	        {% endif %}
	    </td>
	    <td>
	        <?php echo number_format((double)$item['sell_price'],2) ?>
	    </td>
	    <td>
	        {{ item['quantity'] }}
	    </td>
	    <td>
	        <?php echo number_format((double)($item['sell_price']*$item['quantity']),2) ?>
	    </td>
	</tr>
	{% endfor %}
	
	<tr class="line">
	    <td></td>
	    <td></td>
	    <td>Subtotal: <b>{% if cart['quantity'] is defined %}{{cart['quantity']}}{% endif %}</b></td>
	    <td>{% if cart['total'] is defined %}<?php echo number_format((double)$cart['total'],2) ?>{% endif %}</td>
	</tr>
	<tr>
	    <td></td>
	    <td></td>
	    <td>Tax ({% if cart['taxper'] is defined %}{{cart['taxper']}}{% endif %}%)</td>
	    <td>{% if cart['tax'] is defined %}<?php echo number_format((double)$cart['tax'],2) ?>{% endif %}</td>
	</tr>
	<tr>
	    <td></td>
	    <td></td>
	    <td>Sum total</td>
	    <td>{% if cart['main_total'] is defined %}<?php echo number_format((double)$cart['main_total'],2) ?>{% endif %}</td>
	</tr>
</table>
<div class="bgbox no-print">
	<button class="print_bt no-print" onclick="myFunction()">Print order</button>
</div>
<script type="text/javascript" src="{{baseURL}}/bower_components/jquery/dist/jquery.min.js"></script>
<script>
function myFunction() {
    window.print();
}
</script>
</body>
<!-- <footer></footer> -->
</html>