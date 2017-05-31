{% set menu_width = 100/menu|length %}

<nav id="main_menu" class="navbar navbar-default">
    <div class="menu">
        <div class="navbar-header hidden">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation 1</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                {% for item in menu %}
                    <li class="{% if active_id==item.id %} active {% endif %}" style="width:{{menu_width}}%">
                        <a href="{{ baseURL }}/{{ item.short_name }}">{{item.name}}</a>
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>
</nav>