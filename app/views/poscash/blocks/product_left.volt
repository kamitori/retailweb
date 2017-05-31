<div class="row product_left_header">
    <div class="col-md-10 tab">
        <!-- <ul class="nav nav-tabs product_tab" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Small</a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Medium</a></li>
            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Large</a></li>
        </ul> -->
    </div>
    <div class="col-md-2">
        <button class="close_bt close_bg bt-reset" onclick="linkTo('{{ baseURL }}');">X</button>
    </div>
</div>
<div id="product_scroll">
    <div class="row product_left_content">
        {% for product in products %}
            <?php $str = str_replace(".", "_", $product['price']); ?>
            <div class="product-items col-md-4 col-sm-6">
                <div class="product_item box-shapdow-item" id="product_item_{{ product['id'] }}">
                    <h2>{{ product['name'] }}</h2>
                    {% if product['image'] is empty %}
                        <img src="{{ baseURL }}/{{ theme }}/images/default.png" alt="{{ product['name'] }}" />
                    {% else %}
                        <img src="{{ product['image'] }}" alt="{{ product['name'] }}" />
                    {% endif %}
                    <div class="product_item_desc">
                        <p class="description_item">
                            {{ product['description'] }}
                        </p>
                        <span class="off_in_small">Starting from: </span>
                        <span class="product_item_price">${{ product['price'] }}</span>
                    </div>
                    <div class="add_to_cart close_bg" onclick="addCart({{ product['id'] }});"> <i class="fa fa-plus"></i> </div>
                    <div class="remove_to_cart close_bg" onclick="removeCart('{{ product['id'] }}-{{ str }}');"><i class="fa fa-times"></i></div>
                </div>
            </div>
        {% endfor %}
    </div>
</div>