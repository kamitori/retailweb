<style type="text/css" media="screen">
    #button_seach_station{
        display: block !important;
    }
</style>

<div class="search_box customer_info" id="search_station" style="display:none;">
    <form action="/{{type}}-station" id="myForm" method="POST">
        <div class="form-inline" style="float:left;">
          <div class="form-group">
            <label class="sr-only" for="codeOrder">Code</label>
            <input type="text" class="form-control" name="codeOrder" id="codeOrder" placeholder="Order number" value="{{codeOrder}}" />
          </div>
          <input type="submit" class="btn btn-default botbg" style="color:white;" value="Find order" onclick="document.getElementById('myForm').submit();" />
        </div>
    </form>
</div>
{% for index, item in arr_item %}
    <div class="bms_item col-xs-12 pos_{{ item['color_bar']}}" id="bmsitem_{{index}}" style="padding:10px;height:auto;">
        <div class="cartbox_des">
            <div class="cartbox_img">
                {% if item['collapse']== 1 and item['arr_image'] is not empty  %}
                    {% for images in item['arr_image'] %}
                        <img src="{{images}}" alt="{{images}}" style="width:20%;margin:0px;" />
                    {% endfor %}
                {% elseif item['image'] is empty %}
                    <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="{{ item['products_name'] }}" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['products_name'] }}" style="max-height:55px;width:auto;" />
                {% endif %}
            </div>
            <div class="cartbox_name">
                <?php
                    $banh_mi_size = 0;
                    if(isset($item['options'])){
                        foreach ($item['options'] as $key => $value) {
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
                {% if item['collapse']== 1 and item['line'] is not empty  %}
                    {% for kk,vv in item['line'] %}
                        <h4 style="font-size:14px;{% if vv['completed'] is defined and vv['completed'] ==1 %}color:green{%endif%}"><b>{{ vv['quantity'] }}</b>. {{ vv['products_name'] }} {% if banh_mi_size>0 %} 
                           - size {{banh_mi_size}}" 
                        {% endif %}</h4>
                        {% if vv['options'] is defined and vv['options']|length >0 %}
                            {% for opp in vv['options'] %}
                                {% if opp['is_change'] is defined and opp['is_change']==1 %}
                                    {% if opp['group_type'] is defined and opp['group_type']!='Exc' and opp['isfinish']==0 %}
                                        <b>{{opp['quantity']}}</b>:
                                    {% endif %}
                                    {{opp['name']}}
                                    {% if opp['isfinish']==1 %}
                                        <b>({{finish_option[opp['_id']][opp['finish']]}})</b>
                                    {% endif %},
                                {% endif %}
                            {% endfor %}
                        {% endif %}
                    {% endfor %}
                {% else%}  
                    <h3 style="font-size:14px;{% if item['completed'] is defined and item['completed'] ==1 %}color:green{%endif%}">{{ item['products_name'] }} {% if banh_mi_size>0 %} 
                           - size {{banh_mi_size}}" 
                        {% endif %}</h3>
                    {% if item['options'] is defined and item['options']|length >0 %}
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
                    <div class="cart_note">
                        {% if item['note'] is defined and item['note']!='' %}<b><i>Note:</i></b> <span>{{item['note']}}</span>{% endif %}
                    </div>
                {% endif %}
            </div>
        </div>
        <div class="cartbox_quantity" style="padding-top:25px;">
            {% if item['quantity'] is defined %}
                {{ item['quantity'] }}
            {% else %}
                0
            {% endif %}
        </div>
        <div class="cartbox_order">
            {% if item['order_code'] is defined %}{{item['order_code']}}{% endif %}
            <br />
            <button style="margin-top:20px;" data-order-id="{{item['order_id']}}" class="coll_exp newbt {% if item['collapse']== 1 %} bt_expand bartop {%endif%}">{% if item['collapse']== 1 %}Expand{%else%}Collapse{%endif%}</button>
        </div>
        <div class="cartbox_action">
            {% if item['collapse']== 1%}
                {% if item['had_paid']==1 %}
                    <button class="btn up mainbt main_btns completeall" data-order-id="{{item['order_id']}}" data-is-manager="1" style="margin-top: -10px;">Complete All</button>
                {%endif%}
            {%else%}
                {% if item['completed'] is defined and item['completed'] ==1 %}

                {%else%}
                    <button class="btn up mainbt btedit" data-order-id="{{item['order_id']}}" data-product-id="{{item['products_id']}}" data-cart-id="{{item['cart_id']}}"  style="margin-top: -10px;">Complete</button>
                
                {%endif%}
            {%endif%}
            <div class="time_elapsed_string">
                <?php echo time_elapsed_string($item['time']);?>
                {% if item['late'] is defined %}
                    <span style="color:red; text-transform:uppercase; font-weight:bold;font-size: 1.2em;">(late)</span>
                {% endif %}
                <p>
                    POS #{{item['pos_no']}}
                    {% if item['had_paid']==1 %}
                        <span style="color:blue;"> PAID</span>
                    {%else%}
                        <span style="color:red;"> Not Paid 
                            {% if item['had_paid_amount']>0 %}
                            ( {{dinhdangtien(item['had_paid_amount'])}} )
                            {% endif %}
                        </span>
                    {% endif %}
                </p>
            </div>
        </div>
    </div>
{% endfor %}
    <div class="bms_item col-xs-12">
        <div class="cartbox_des"></div>
        <div class="cartbox_quantity"></div>
        <div class="cartbox_order"></div>
        <div class="cartbox_action"></div>
    </div>
    <?php if(!empty($arr_order_late)) {?>
    <div class="box_order_late" style="display:none;">
        <p style="text-transform:uppercase;color:#444;">Order late</p>
        {% for order_late in arr_order_late %}
        <p>{{ order_late['no'] }}</p>
        {% endfor %}
    </div>
    <?php } ?>
    <input type="hidden" id="ring" value="{{ring}}" />
    <input type="hidden" id="timelog" value="{{timelog}}" />
    <script>
        if(document.getElementById('ring').value==1) {
            playmusic();
            var oldTitle = document.title;
            var runtime ;
            function blink(){       
                var msg = "New!";
                document.title = document.title == msg ? ' ' : msg;
            }
            function clearTitle(){
                clearInterval(runtime);
                document.title = oldTitle;
                window.onmousemove = null;
            }
            newExcitingAlerts = (function () {
                runtime = setInterval(blink, 1000);
                window.onmousemove = clearTitle;
            }());   
        }
    </script>
