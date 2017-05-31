{% for index, item in arr_item %}
    <div class="bms_item col-xs-12" id="bmsitem_{{index}}">
        <div class="cartbox_des">
            <div class="cartbox_img">
                {% if item['image'] is empty %}
                    <img src="{{ baseURL }}/{{ theme }}/images/default.png" alt="{{ item['products_name'] }}" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['products_name'] }}" />
                {% endif %}
            </div>
            <div class="cartbox_name">
                <h3>{{ item['products_name'] }}</h3>
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
                <div class="cart_note">
                    {% if item['note']!='' %}<b><i>Note:</i></b> <span>{{item['note']}}</span>{% endif %}
                </div>
            </div>
        </div>
        <div class="cartbox_quantity">
            {{ item['quantity'] }}
        </div>
        <div class="cartbox_order">
            {% if item['order_code'] is defined %}{{item['order_code']}}{% endif %}
        </div>
        <div class="cartbox_action">
            <button class="btn up mainbt btedit" data-order-id="{{item['order_id']}}" data-product-id="{{item['products_id']}}"  style="margin-top: -10px;">Complete</button>
        </div>
    </div>
{% endfor %}
    <div class="bms_item col-xs-12">
        <div class="cartbox_des"></div>
        <div class="cartbox_quantity"></div>
        <div class="cartbox_order"></div>
        <div class="cartbox_action"></div>
    </div>