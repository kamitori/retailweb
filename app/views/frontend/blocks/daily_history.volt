<div id="account_order" lass="row col-xs-12">
<div class="orderbox row">
{% for index, item in orders %}
    <div class="orderbox_item col-xs-12" id="orderbox_{{item['order_id']}}">
        <div class="cartbox_des">
            <div class="cartbox_img">
                Time: {{ item['time'] }}<br />
                {% if item['cart']['items'] is defined %}
                    {% for itemcart in item['cart']['items'] %}
                        {% if itemcart['image'] is defined %}
                            <img src="{{ itemcart['image'] }}" alt="{{ itemcart['name'] }}" class="ahalf_img" />
                        {% endif %}
                    {% endfor %}
                {% endif %}
            </div>
            <div class="cartbox_name">
                <h3>Order {{ item['code'] }}</h3>
                <div class="sell_price">
                    {% if item['cart']['items'] is defined %}
                        {% for itemcart in item['cart']['items'] %}
                            <p class="">
                                <b>{{ itemcart['quantity'] }}</b>.
                                {{ itemcart['name'] }} (${{ itemcart['sell_price'] }}) 
                                = <b>{{ itemcart['total'] }}</b>
                            </p>
                        {% endfor %}
                    {% endif %}
                </div>
                <div class="cart_note">
                    {% if item['cart']['note'] is defined %}{{ item['cart']['note'] }}{% endif %}
                </div>
                <div class="description_item" style="display:none">
                </div>
            </div>
        </div>
        <div class="order_cartbox_qty">
            <button class="btn up mainbt btedit viewnow" data-orderid="{{item['order_id']}}">View order</button>
        </div>
        <div class="order_cartbox_price">
            {% if item['cart']['main_total'] is defined %}{{ item['cart']['main_total'] }}{% endif %}
        </div>
        <!-- <button class="delete_order" title="delete" onclick="delete_order('{{item['order_id']}}')">X</button> -->
    </div>
{% endfor %}
<div class="orderbox_item col-xs-12"><br /><br /></div>
</div>
</div>