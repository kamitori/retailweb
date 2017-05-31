
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
                <h3><a href="">Order {{ item['code'] }}</a></h3>
                <div class="sell_price">
                    {% if item['cart']['items'] is defined %}
                        {% for itemcart in item['cart']['items'] %}
                            <?php
                                $banh_mi_size = 0;
                                if(isset($itemcart['options'])){
                                    foreach ($itemcart['options'] as $key => $value) {
                                        if($value['option_group']=="Size"){
                                            if($value['quantity']&&$value['_id']=='56491b54124dca9460f4d4b0'){
                                                $banh_mi_size=11;
                                            }
                                            if($value['quantity']&&$value['_id']=='56491b05124dca2165f4d46b'){
                                                $banh_mi_size=8;
                                            }
                                        }
                                    }
                                }
                                
                            ?>
                            <p class="{% if itemcart['completed'] is defined %}done{% endif %}">
                                <b>{{ itemcart['quantity'] }}</b>.
                                {{ itemcart['name'] }}
                                {% if banh_mi_size>0 %} 
                                    <b>- size {{banh_mi_size}}"<b> 
                                {% endif %} 
                                (${{ itemcart['sell_price'] }}) 
                                = <b>{{ itemcart['total'] }}</b>
                                {% if itemcart['completed'] is defined %}
                                    [DONE]
                                {% endif %}
                            </p>
                        {% endfor %}
                    {% endif %}
                </div>
                <div class="cart_note">
                    {% if item['cart']['note'] is defined %}{{ item['cart']['note'] }}{% endif %}
                </div>
                <div class="description_item" style="display:none">
                </div>
                {% if item['had_paid'] == 1%}
                    <div style="color:red; font-size:1.5em;">* Have paid
                    {% if item['status'] == 'Returned' %}
                        <span style="color: #D66418">(Returned)</span>
                    {% endif %}
                    </div>
                {% elseif item['had_paid_amount'] >0 %}
                    <div style="color:blue; font-size:1.5em;">* Amount Tendered: {{dinhdangtien(item['had_paid_amount'])}}</div>
                {% endif %}
                <div class="delivery_address">
                    {% if item['delivery_address'] is defined %}
                        Delivery Address: {{ item['delivery_address']}}
                    {% endif %}
                </div>
            </div>
        </div>
        <div class="order_cartbox_qty">
            
            <!-- <button class="btn up mainbt btedit {% if item['completed'] is defined %} paynow{% else%} notcompleted{% endif %}" data-id="{{item['order_id']}}">Pay now</button> -->            
            <!--<button class="btn up mainbt btedit delorder" data-orderid="{{item['order_id']}}" onclick="delete_order('{{item['order_id']}}',0)" style="color:#111;margin-left:5%;">Canceled</button>-->
            <button class="btn up mainbt btedit print_account_order" data-id="{{item['order_id']}}">Print</button>
            {% if item['had_paid'] == 0%}
                <button class="btn up mainbt btedit paynow" data-status="{% if item['completed'] is defined %}completed{% else%} notcompleted{% endif %}" data-id="{{item['order_id']}}">Pay now</button>
            {% else%}
                <button class="btn up mainbt btedit notcompleted sendproduct" data-id="{{item['order_id']}}">Send</button>
            {% endif %}
            <button class="btn up mainbt btedit edit_account_order" data-id="{{item['order_id']}}">Edit</button>
            <button class="btn up mainbt btedit delorder" data-orderid="{{item['order_id']}}" style="color:#111;margin-left:5%;">Canceled</button>

        </div>
        <div class="order_cartbox_price" rel="{{item['cart']['main_total']-item['had_paid_amount']}}">
            {% if item['cart']['main_total'] is defined %}{{ item['cart']['main_total'] }}{% endif %}
        </div>
        <div class="time_elapsed_string"><?php echo $item['pick_time'];?></div>
        <!-- <button class="delete_order" title="delete" onclick="delete_order('{{item['order_id']}}')">X</button> -->
    </div>
{% endfor %}
<div class="orderbox_item col-xs-12"><br /><br /></div>
</div>
</div>