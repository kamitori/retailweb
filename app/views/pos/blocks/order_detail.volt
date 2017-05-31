{% for index, item in cart['items'] %}
    <div class="cartbox_item col-xs-12" id="cartbox_{{index}}">
        <div class="cartbox_des">
            <div class="cartbox_img">
                {% if item['image'] is empty %}
                    <img src="{{ baseURL }}/{{ theme }}/images/default.png" alt="{{ item['name'] }}" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['name'] }}" />
                {% endif %}
            </div>
            <div class="cartbox_name">
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
                <div class="sell_price">
                    Unit price: <b>$<?php echo number_format((double)$item['sell_price'],2) ?></b>
                </div>
                <div class="cart_note">
                    {% if item['note']!='' %}<b><i>Note:</i></b> <span id="note_product_{{index}}">{{item['note']}}</span>{% endif %}
                </div>
                <div class="description_item" style="display:none">{{ item['description']}}</div>
            </div>
        </div>
        <div class="cartbox_qty">
            <div class="qtybox">
                <span class="cartbox_price">{{ item['quantity'] }}</span>
            </div>
        </div>
        <div class="cartbox_price">
            $<span  id="price_fc_{{index}}"><?php echo number_format((double)($item['sell_price']*$item['quantity']),2) ?></span>
        </div>
    </div>
{% endfor %}


    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des">&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;">
            <h3>Sub total</h3>
        </div>
        <div class="cartbox_price">
            <span class="money">$<?php echo number_format((double)($cart['total']),2) ?></span>
        </div>
    </div>
    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des">&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;">
           <h3>Tax</h3>
        </div>
        <div class="cartbox_price">
            <div class="taxbox">
                {% for key,value in taxlist %}
                    {% if key==cart['taxper'] %}
                    {{value}}
                    {% endif %}
                {% endfor %}
            </div>
            <span class="taxval">$<?php echo number_format((double)($cart['tax']),2) ?></span>
        </div>
    </div>
    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des order_notes">{{ cart['note'] }}&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;">
            <h3>Total</h3>
        </div>
        <div class="cartbox_price">
            <span class="main_total">$<?php echo number_format((double)($cart['main_total']),2) ?></span>
        </div>
    </div>
    <div class="cartbox_item col-xs-12"><br /><br /></div>

<input type="hidden" id="main_order_qty" value="{% if cart['quantity'] is defined %}{{cart['quantity']}}{% endif %}" />
<input type="hidden" id="main_order_id" value="{% if cart['order_id'] is defined %}{{cart['order_id']}}{% endif %}" />
<input type="hidden" id="order_type" value="{% if cart['order_type'] is defined %}{{cart['order_type']}}{%else%}0{% endif %}" />