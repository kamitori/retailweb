<!-- <div id="main-nav">
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
</div> -->
 <nav class="navbar navbar-default">
  <div class="container top-nav">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <div class="navbar-brand" href="#">Main menu</div>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        {% for item in mainmenu %}
            <li class="{% if item['link']==link %}active{% endif %}">
                <a href="#">{{item['up']}} {{item['down']}}</a>
            </li>
        {% endfor %}
      </ul>
    </div>
  </div>
</nav>