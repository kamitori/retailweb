<div class="product_lefts shadow" style="background: #eeeae7;width: 78%;height: 670px;float: left;">
    {{ partial('Favorites/left') }}
</div>
<div class="menu_right gray_bg" id="menu_scroll">
    <div class="right-menu-col">
        {% for item in category %}
            <a href="{{baseURLPos}}/{{item['short_name']}}">
            	<div class="menu-items gray_bg apple_menu_shadow" style="width: 100%;clear: both;height: 77px;cursor: pointer;">
	                <div class="menu-item-img">
	                    <img src="{{item['image']}}" alt="" />
	                </div>
	                <div class="menu-item-lable">
	                    <div class="textmid">
	                        <p>{{item['name']}}</p>
	                    </div>
	                </div>
	            </div>
            </a>
        {% endfor %}
    </div>
</div>
<div class="bgpopup"></div>
<div class="popup_item box-shapdow-item" style="display:none;">
    <div class="header_line barpopup">
        <div class="close_popup" onclick="closePopup();">x</div>
    </div>
    <div class="popup_content">
        <div class="productitem">
            <div class="popup_title">
                <h2>Bánh mì ngon va re nhat tren the gioi</h2>
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
                <button class="btn down mainbt" onclick="downQty('popup_qty_main')">-</button>
                <input class="popup_qty" type="text" value="1" id="popup_qty_main" />
                <button class="btn up mainbt" onclick="upQty('popup_qty_main')">+</button>
                <input id="sell_price_popup_qty_main" value="0" type="hidden" />
            </div>
            <div class="popup_price">
                $10.00
            </div>
            <div class="popup_add_bt">
                <button class="" onclick="addCustomCart()">Add to Cart</button>
                <input class="item_id" type="hidden" value="" />
            </div>
        </div>
        <div class="tabs">
            {{ partial('Categories/option_product') }}
        </div>
    </div>
</div>