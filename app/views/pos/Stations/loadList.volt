{% for index,order in arr_order %}
	{% for index2,product in order['products'] %}
		{% for index3,drink in arr_drink %}
			{% if (drink == product['products_id']) %}
				{% if product['completed'] is not defined %}
				<tr {% if index is odd %} class="stroke" {%endif%}>
					<td>{{ product['products_name'] }}</td>
					<td>{{ product['quantity'] }}</td>
					<td></td>
					<td class="text-center"><button class="btn btn-success" type="btn" data-order-id="{{order['_id']}}" data-product-id="{{product['products_id']}}" onclick="complete_station(this)">Complete</button></td>
				</tr>
				{% endif %}
			{% endif %}
		{% endfor %}
	{% endfor %}
{% endfor %}