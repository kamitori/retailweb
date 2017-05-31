<div class="row product_left_header">
    <div class="col-md-10 tab">
        <!-- <ul class="nav nav-tabs product_tab" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Small</a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Medium</a></li>
            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Large</a></li>
        </ul> -->
    </div>
    <div class="col-md-2">
        <button class="close_bt close_bg bt-reset" onclick="linkTo('{{ baseURLPos }}');">X</button>
    </div>
</div>
<div id="product_scroll">
    <div class="row product_left_content">
        {% for product in products %}
            <?php $str = str_replace(".", "_", $product['price']); ?>
            <div class="product-items col-md-4 col-sm-6" id="remove_item_{{product['product_id']}}">
                <div class="product_item box-shapdow-item" id="product_item_{{product['product_id']}}">
                    <h2>{{ product['name'] }}</h2>
                    <img src="{{ product['image'] }}" alt="{{ product['name'] }}" />
                    <div class="product_item_desc">
                        <p class="description_item">
                            {{ product['description'] }}
                        </p>                        
                        <span class="product_item_price">${{ product['price'] }}</span>
                    </div>
                    <div class="add_to_cart close_bg" onclick="addCart('{{ product['product_id'] }}','0');" >
                        <i class="fa fa-plus"></i> 
                    </div>
                    <div class="add_to_cart close_bg" onclick="removeFavorite('{{ product['product_id'] }}','{{ product['user_id'] }}');" style="right:65px">
                        <i class="fa fa-remove"></i> 
                    </div>                    
                </div>
            </div>
        {% endfor %}
    </div>
</div>