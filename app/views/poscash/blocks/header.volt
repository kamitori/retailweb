<div id="main-nav">
    <ul>
        {% for item in mainmenu %}
            <li class="{% if item['link']==link %}active{% endif %}">
                <a href="{{item['link']}}">
                    <span class="up">{{item['up']}}</span>
                    <span class="down">{{item['down']}}</span>
                </a>
            </li>
    
            {% if mainmenu[loop.index] is defined  and mainmenu[loop.index]['link']==link %}
                <li class="last"></li>
            {% elseif loop.last %}
                <li class="{% if item['link']==link %} activelast {% else %} last {% endif %}"></li>
            {% else %}
                <li class="block_menu_divide {% if item['link']==link %} first {% endif %} "></li>
            {% endif %}
        {% endfor %}
    </ul>
</div>