<nav class="navbar navbar-default">
  <div class="container top-nav">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-status="off" id="changeMenu">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        {% if title!='Main menu' and off_main_menu==0 %}
            <div class="backbt"><a href="/">Main Menu</a></div>
        {% endif %}
        <button type="button" class="navbar-toggle pull-right" data-status="off" id="leftMenu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <button type="button" id="button_seach_station" onclick="$('#search_station').slideToggle(500,'swing');"><i class="fa fa-search"></i></button>
        <button type="button" id="button_pause_load" style="{%if autoload==1%}display:block;{%endif%}" onclick="$('#autoload').val(0);$(this).hide();$('#button_play_load').show();"><i class="fa fa-pause"></i></button>
        <button type="button" id="button_play_load" style="display:none;" onclick="$('#autoload').val(1);$(this).hide();$('#button_pause_load').show();"><i class="fa fa-play"></i></button>
        <input type="hidden" value="1" id="autoload" />
        <!-- <div class="signout_link"><a href="{{baseURL}}/user/logout">Sign out</a></div> -->
        <div class="navbar-brand">
            <div id="category_title" style="display:{% if use_combo==1 or use_group==1 %}none{% else %}block{% endif %};">{{title}}</div>
            <div id="combo_alert" style="display:{% if use_combo==0 %}none{% else %}block{% endif %};">
                Combo is processing ...<span class="comboitem" data-step="{{combo_step}}">{{step_description[combo_step]}} (step {{combo_step}}/3)</span> 
                <button type="button" class="btn btn-default close_combo_bt" style="display:inline-block;margin-top: -8px;">Cancel Combo</button>
                {% for key,description in step_description %}
                    <input type="hidden" id="step_description_{{key}}" value="{{description}}">
                {% endfor %}
            </div>
            <div id="group_alert" style="display:{% if use_group==0 %}none{% else %}block{% endif %};">
                Selecting Menu Item for <span class="user_name_group">{% if user_name is defined %}{{user_name}}{%endif%}</span>
                <button type="button" class="btn btn-default next_group_bt" style="display:inline-block;margin-top: -8px;">Next Guest</button>
                <button type="button" class="btn btn-warning end_group_bt" style="display:inline-block;margin-top: -8px;">Complete Order By Group</button>
            </div>
        </div>
    </div>
    
  </div>
</nav>