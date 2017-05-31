<div class="row product_left_header">
    <div class="col-md-12 tab">
        {% if tag_list|length > 1 %}
            <ul class="nav nav-tabs product_tab">
                <li class="nav_item active" data-tag=""><a href="#">All</a></li>
                {% for key,tag in tag_list %}
                    <li class="nav_item" data-tag="{{tag}}"><a href="#">{{tag}}</a></li>
                {% endfor %}
            </ul>
        {% endif %}
    </div>
    <!-- <div class="col-md-2">
        <button class="close_bt close_bg bt-reset" onclick="linkTo('{{ baseURL }}');">X</button>
    </div> -->
</div>
<div id="product_scroll">
    <div class="row product_left_content">
        {% for product in products %}
            <?php $str = str_replace(".", "_", $product['price']); ?>
            <div class="product-items col-md-4 col-sm-6" data-tag="{{ product['tag'] }}">
                <div class="product_item box-shapdow-item" id="product_item_{{ product['id'] }}">
                    <h2>{{ product['name'] }}</h2>
                    <div class="product_item_img"> 
                        {% if product['image'] is empty %}
                            <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="{{ product['name'] }}" />
                        {% else %}
                            <img src="{{ product['image'] }}" alt="{{ product['name'] }}" />
                        {% endif %}
                    </div>
                    <div class="product_item_desc">
                        <p class="description_item">
                            {{ product['description'] }}
                        </p>
                        <p class="product_desciption" style="display:none;">
                            {{ product['product_desciption'] }}
                        </p>
                        <span class="off_in_small">{% if product['custom']==1 %}Starting from{% else %}Price{% endif %}: </span>
                        <span class="product_item_price">$<?php echo number_format($product['price'],2);?></span>
                    </div>
                    <div class="add_to_cart close_bg" onclick="addCart('{{ product['id'] }}','{{ product['custom'] }}');" data-id="{{ product['id'] }}" data-custom="{{ product['custom'] }}"> <i class="fa fa-plus"></i> </div>
                    <div class="remove_to_cart close_bg" onclick="removeCart('{{ product['id'] }}-{{ str }}');"><i class="fa fa-times"></i></div>
                    <input type="hidden" value="{{ product['custom'] }}" id="custom_{{ product['id'] }}" />
                    <input type="hidden" value="{{ product['combo'] }}" id="iscombo_{{ product['id'] }}" />
                    <input type="hidden" value="{{ product['use_group_order'] }}" id="isordergroup_{{ product['id'] }}" />
                    <input type="hidden" value="{{ product['combo_sales'] }}" id="combosales_{{ product['id'] }}" />
                </div>
            </div>
        {% endfor %}
    </div>
</div>