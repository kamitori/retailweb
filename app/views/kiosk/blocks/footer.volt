<div id="footer" class="botbg">
    <div class="block_footer">
        Your Cart:
    </div>
    <div class="block_footer" id="number_in_cart">
        <span class="cart-total">{{amount}}</span> Items in Cart
    </div>
    <div class="block_footer">
        <i class="fa fa-shopping-cart"></i> <a href="/carts">View cart</a> <i class="fa fa-caret-up"></i>
    </div>
    <div class="block_footer " id="total">
        Total: <span class="money">${{total_price}}</span>
    </div>
    <div class="block_footer close_bg" id="checkout" onclick="window.location.assign('{{ baseURL }}/carts');">
        <div class="resetrote">Checkout <!-- <i class="fa fa-caret-right"> </i>--></div>
    </div>
</div>
<div id="footer_cart" class="smallbox" style="height:0px;">
    {% for product in cart_items %}
        <div class="product-items col-md-2 col-sm-4" id="footer_cart_{{ product['id'] }}-{{ product['price_string'] }}">
            <div class="product_item box-shapdow-item">
                <h2>[{{ product['quantity'] }}] {{ product['name'] }}</h2>
                {% if product['image'] is empty %}
                    <img src="{{ baseURL }}/{{ theme }}/images/default.png" alt="{{ product['name'] }}" />
                {% else %}
                    <img src="{{ product['image'] }}" alt="{{ product['name'] }}" />
                {% endif %}
                <div class="product_item_desc">
                    <p class="description_item">
                        {{ product['name'] }}
                    </p>
                    <span class="product_item_price">${{ product['price']*product['quantity'] }}</span>
                </div>
                <div class="remove_to_cart close_bg" onclick="removeCart('{{ product['id'] }}-{{ product['price_string'] }}');"><i class="fa fa-times"></i></div>
            </div>
        </div>
    {% endfor %}
</div>
<div class="drapbox"></div>