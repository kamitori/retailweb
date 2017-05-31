{% for index, item in cart['items'] %}
    <div class="product-items col-md-2 col-sm-4" id="item-{{ index }}">
        <div class="product_item box-shapdow-item">
            <h2>{{ item['name'] }}</h2>
            <div class="product_item_img"> 
                {% if item['image'] is empty %}
                    <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="{{ item['name'] }}" />
                {% else %}
                    <img src="{{ item['image'] }}" alt="{{ item['name'] }}" />
                {% endif %}
            </div>
            <div class="product_item_desc">
                <p class="description_item">
                    {{ item['name'] }}
                </p>
                <span class="product_item_price">${{ item['sell_price']|format }}</span>
                <span class="product_item_qty"><strong>{{ item['quantity'] }}</strong>pcs</span>
            </div>
            <div class="remove_to_cart close_bg" onclick="removeCart('{{ index }}');"><i class="fa fa-times"></i></div>
        </div>
    </div>
{% endfor %}