<div class="product_left shadow">
    {{ partial('Categories/product_left') }}
</div>
<div class="menu_right" id="menu_scroll">
    <div class="right-menu-col">
        {% for item in category %}
            <div class="menu-item" data-link="{{ baseURL }}/{{ item['short_name'] }}" data-key="{{item['value']}}" data-linkkey="{{ item['short_name'] }}" id="menuleft_{{ item['short_name'] }}">
                <div class="menu-item-img">
                    <img src="{{item['image']}}" alt="" />
                </div>
                <div class="menu-item-lable">
                    <div class="textmid">
                        <p>{{item['name']}}</p>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
</div>
