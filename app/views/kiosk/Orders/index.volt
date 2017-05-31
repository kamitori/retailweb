<div class="product_left shadow">
    {{ partial('blocks/product_left') }}
</div>
<div class="menu_right gray_bg" id="menu_scroll">
    <div class="right-menu-col">
        {% for item in all_products %}
            <div class="menu-item gray_bg apple_menu_shadow" onclick="addProduct({{item.id}},{{item.category_id}},'{{ baseURL }}/{{short_name_list[item.category_id]}}')" id="product_info_{{item.id}}">
                <div class="menu-item-img">
                    <img src="{{item.image}}" alt="" />
                </div>
                <div class="menu-item-lable">
                    <div class="textmid">
                        <p>{{item.name}}</p>
                    </div>
                </div>
                <div class="info-box" style="display:none">
                    <input class="id" type="hidden" value="{{item.id}}" />
                    <input class="name" type="hidden" value="{{item.name}}" />
                    <input class="price" type="hidden" value="{{item.price}}" />
                    <input class="image" type="hidden" value="{{item.image}}" />
                    <input class="description" type="hidden" value="{{item.description}}" />
                </div>
            </div>
        {% endfor %}
    </div>
</div>


<div class="popup_item apple_shadow1" style="display:none;">
    <div class="header_line barbot"></div>
    <div class="popup_content">
        <div class="productitem">
            <div class="popup_title">
                <h2>{{ product['name'] }}</h2>
                <p class="description_item">
                    Bánh mì ngon va re nhat tren the gioi. Bánh mì ngon va re nhat tren the gioi. Bánh mì ngon va re nhat tren the gioi. Bánh mì ngon va re nhat tren the gioi
                </p>
            </div>
            <div class="popup_image">
                <img src="http://retailweb.com/themes/banhmisub//images/default.png" alt="Banh mi" />
            </div>
        </div>
        <div class="popup_prices">
            <div class="popup_amount">
                <button class="btn down">-</button>
                <input type="text" name="" value="2" maxlength="3" data-skey="1-0" onchange="update_quantity(this)">
                <button class="btn up">+</button>
            </div>
            <div class="popup_price">
                $10.00
            </div>
            <div class="popup_add_bt">
                <button class="btn btn-success btn-block btn-lg">Add to Cart</button>
            </div>
        </div>
        <div class="popup_tab">
            <ul class="nav nav-tabs product_tab" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Small</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Medium</a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Large</a></li>
            </ul>
        </div>
        <div class="popup_option">
                
        </div>
    </div>
</div>