{% set comid = -1 %}
{% set uid = -1 %}
{% for index, item in cart['items'] %}
    {% if item['combo_id'] is defined and comid != item['combo_id'] %}
    {% set comid = item['combo_id'] %}
    <div class="cartbox_item col-xs-12 combo_option">
        <div class="cartbox_des">
            <div class="cartbox_img"><h3>COMBO{{item['combo_id']+1}}</h3></div>
            <div class="cartbox_name"><h3></h3></div>
        </div>
        <div class="cartbox_qty">
            <div class="qtybox">
                <button class="btn down mainbt" onclick="qtyAddCombo(0,'{{index}}',{{item['combo_id']}})">-</button>
                <input class="popup_qtycombo" id="popup_qtycombo_{{index}}" type="text" value="{{ combo_list[item['combo_id']]['quantity'] }}" style="width: 55px;">
                <button class="btn up mainbt" onclick="qtyAddCombo(1,'{{index}}',{{item['combo_id']}})">+</button>
            </div>            
            <button class="btn up mainbt btedit" onclick="deleteCombo('{{item['combo_id']}}')">Delete</button>
            <!-- <button class="btn up mainbt btedit" style="color:#111;" onclick="editCombo('{{item['combo_id']}}')">More</button> -->
        </div>
        <div class="cartbox_price">
            $<span  id="price_fc_{{index}}"><?php echo number_format((double)$combo_list[$item['combo_id']]['total'],2) ?></span>
        </div>
    </div>
    {% endif %}

    {% if item['user_id'] is defined and uid != item['user_id'] %}
    {% set uid = item['user_id'] %}
    <div class="cartbox_item col-xs-12 user_group_box">
        <div class="cartbox_des">
            <h3>
                {% if item['user_id'] is defined and user_list[item['user_id']] is defined %}
                    {{user_list[item['user_id']]}}
                {% endif %}
            </h3>
        </div>
        <div class="cartbox_qty">
            <div class="qtybox">
                <button class="btn down mainbt" onclick=""></button>
                <!-- <input class="price_user_group" id="price_user_group_{{index}}" type="text" value="{{ item['user_id'] }}" style="width: 55px;"> -->
                <button class="btn up mainbt" onclick=""></button>
            </div>
            <button class="btn up mainbt btedit" onclick="deleteGroupByUser('{{item['user_id']}}')">Delete</button>
            <button class="btn up mainbt btedit" style="color:#111;" onclick="changeGroupByUser('{{index}}','{{item['user_id']}}')">More</button>
        </div>
        <div class="cartbox_price">
            $<span class="group_total"  id="price_fc_{{index}}" {% if item['user_id'] is defined %}data-user-id="{{item['user_id']}}"{% endif %}>
                {% if item['user_id'] is defined and user_data[item['user_id']]['total'] is defined %}
                    {{user_data[item['user_id']]['total'] }}
                {% endif %}
            </span>
        </div>
    </div>
    {% endif %}

{% if item['combo_id'] is defined %}
    <div class="col-xs-4 combo_option" id="cartbox_{{index}}" style="padding:1%;">
        <div class="cartbox_des combo_des">
            <div class="cartbox_img combo_img">
                <div class="combo_imgbox">
                {% if item['image'] is empty %}
                    <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="{{ item['name'] }}" style="cursor:pointer;" onclick="changeCombo('{{index}}','{{item['combo_step']}}')" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['name'] }}" style="cursor:pointer;" onclick="changeCombo('{{index}}','{{item['combo_step']}}')" />
                {% endif %}
                </div>
                <p><button class="btn up btedit" style="color:#111;" onclick="changeCombo('{{index}}','{{item['combo_step']}}')">Change</button></p>
            </div>
            <div class="cartbox_name combo_name">
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
        <div class="cartbox_qty" style="display:none;">
            <div class="qtybox">
                <button class="btn down mainbt" onclick="downQty('fc_{{index}}','update_cart')">-</button>
                <input class="popup_qty" type="text" value="{{ item['quantity'] }}" id="fc_{{index}}" style="width: 55px;">
                <button class="btn up mainbt" onclick="upQty('fc_{{index}}','update_cart')">+</button>
            </div>
           
        </div>
        <div class="cartbox_price" style="display:none;">
        </div>
    </div>
{% else %}
    <div class="cartbox_item col-xs-12 {% if item['user_id'] is defined%}ofgroup{%endif%}" id="cartbox_{{index}}">
        <div class="cartbox_des">
            <div class="cartbox_img">
                {% if item['image'] is empty %}
                    <img src="{{ baseURL }}/{{ theme }}/images/default.png" alt="{{ item['name'] }}" onclick="editCart('{{index}}','{{ item['_id']}}')" style="cursor:pointer;" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['name'] }}" onclick="editCart('{{index}}','{{ item['_id']}}')" style="cursor:pointer;" />
                {% endif %}
            </div>
            <div class="cartbox_name">
                <h3>{{ item['name'] }}</h3>
                {% if item['options']|length >0 %}
                    {% for opp in item['options'] %}
                        {% if opp['is_change'] is defined and opp['is_change']==1 %}
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
                    {% if item['user_id'] is defined and user_list[item['user_id']] is defined%}
                        <br />{{user_list[item['user_id']]}}
                    {% endif %}
                </div>
                <div class="description_item" style="display:none">{{ item['description']}}</div>
            </div>
        </div>
        <div class="cartbox_qty">
            <div class="qtybox">
                <button class="btn down mainbt" onclick="downQty('fc_{{index}}','update_cart')">-</button>
                <input class="popup_qty" type="text" value="{{ item['quantity'] }}" id="fc_{{index}}" style="width: 55px;">
                <button class="btn up mainbt" onclick="upQty('fc_{{index}}','update_cart')">+</button>
            </div>
                <button class="btn up mainbt btedit" onclick="delete_cart('{{index}}')">Delete</button>
                <button class="btn up mainbt btedit" style="color:#111;" onclick="editCart('{{index}}','{{ item['_id']}}')">Edit</button>
        </div>
        <div class="cartbox_price">
                $<span  id="price_fc_{{index}}"><?php echo number_format((double)($item['sell_price']*$item['quantity']),2) ?></span><br/>
                {% if item['total_promo'] is defined and item['quantity_promo'] is defined and item['total_promo']>0 %}
                    <span style="font-size:12px;">Promo({{item['quantity_promo']}})</span>
                    <span style="font-size:18px;color:red;">
                        - $<?php echo number_format((double)($item['total_promo']),2);?>
                    </span>
                {% endif %}
        </div>
    </div>
{% endif %}
{% endfor %}


    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des">&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;padding-right: 15px;">
            <h3>Sub total</h3>
        </div>
        <div class="cartbox_price">
            <span class="money">$<?php echo number_format((double)($cart['total']),2) ?></span>
        </div>
    </div>
    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des">&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;padding-right: 15px;">
           <h3>GST
           <div class="taxbox" style="display:none;">
                <select class="tax_select" onchange="changeTax();">
                    {% for key,value in taxlist %}
                        <option value="{{key}}" {% if key==cart['taxper'] %} selected="selected" {% endif %}>{{value}}</option>
                    {% endfor %}
                </select>
            </div>
            </h3>
        </div>
        <div class="cartbox_price">
            
            <span class="taxval">$<?php echo number_format((double)($cart['tax']),2) ?></span>
        </div>
    </div>
    <div class="cartbox_item col-xs-12">
        <div class="cartbox_des order_notes">{{ cart['note'] }}&nbsp</div>
        <div class="cartbox_qty" style="text-align: right!important;padding-right: 15px;">
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