<!-- <div id="main-nav">
    <ul>
        <li class="active">
            <a href="{{ baseURLPos }}/">
                <span class="up">Order</span>
                <span class="down">now</span>
            </a>
        </li>
        <li class="block_menu_divide first"></li>
        <li class="">
            <a href="#" onclick="modal_location()">
                <span class="up">Select</span>
                <span class="down">Location</span>
            </a>
        </li>
        <li class="block_menu_divide"></li>
        <li>
            <a href="{{ baseURLPos }}/orders/repeat-last-order">
                <span class="up">Repeat</span>
                <span class="down">Last order</span>
            </a>
        </li>
        <li class="block_menu_divide"></li>
        <li>
            <a href="/favorites">
                <span class="up">Saved</span>
                <span class="down">Favourites</span>
            </a>
        </li>
        <li class="block_menu_divide"></li>
        {% if session_user == false %}
        <li>
            <a href="#" onclick="sign_in_popup();">
                <span class="up">Sign</span>
                <span class="down">&nbsp;&nbsp;in</span>
            </a>
        </li>
        <li class="block_menu_divide"></li>
        <li>
            <a href="#" onclick="create_account_popup();">
                <span class="up">Create</span>
                <span class="down">Account</span>
            </a>
        </li>
        {% else %}
        <li>
            <a href="#">
                <span class="up">Account</span>
                <span class="down">settings</span>
            </a>
        </li>
        <li class="block_menu_divide"></li>
        <li>
            <a href="{{baseURLPos}}/user/logout">
                <span class="up">Log</span>
                <span class="down">Out</span>
            </a>
        </li>
        {% endif %}
        <li class="last"></li>
    </ul>
</div> -->

<nav class="navbar navbar-default">
  <div class="container top-nav">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-status="off" id="changeMenu">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        {% if title!='Main menu' %}
            <div class="backbt"><a href="{{ baseURLPos }}">Main Menu</a></div>
        {% endif %}
        <button type="button" class="navbar-toggle pull-right" data-status="off" id="leftMenu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <!-- <div class="signout_link"><a href="{{baseURLPos}}/user/logout">Sign out</a></div> -->
        <div class="navbar-brand">{{title}}</div>
    </div>
    
  </div>
</nav>