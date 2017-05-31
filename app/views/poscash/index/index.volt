<div id="home_right" class="scrollbox" style="width:100%;">
    <div class="scroller">
        <div class="scrollitem" id="list_category">
            {% for category in categories %}
                {% if(category['image'] != NULL) %}
                <div class="block apple_shadow apple_bg ipad_link" data-link="{{baseURL}}/poscash/orders/lists/{{category['short_name']}}">
                    <a href="{{baseURL}}/poscash/orders/lists/{{category['short_name']}}">
                        <div class="imgbox">
                            <img src="{{category['image']}}" alt="" />
                        </div>
                        <span class="category_name">{{category['name']}}</span>
                    </a>
                </div>
                {% endif %}
            {% endfor %}
        </div>
    </div>
</div>

