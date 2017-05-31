<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="accountbox">
                {% if session_user == false %}
                <a class="btn btn-default pizbutton" href="/user/create-account" role="button">Create account</a>
                <a class="btn btn-default pizbutton" href="/user/signin" role="button">Sign in</a>
                {% else %}
                <span>Hello {{session_user['first_name']}} {{session_user['last_name']}}</span>
                &nbsp;&nbsp;&nbsp;
                <a class="btn btn-default pizbutton" href="/user/logout" role="button">Logout</a>
                {% endif %}
            </div>
            <span class="langbox">
            <a href="#" class="white lang">English</a>
            <a href="#" class="white lang">Français</a>
            </span>
        </div>
        <div class="col-md-4">
            <h2><a href="/" class="ir ng-binding" id="headerLogo">Pizza Hut</a></h2>
        </div>
        <div class="col-md-4 show-for-medium-up count-total-wrapper">
            <span class="total">$00.00</span>
            <span class="basket-icon">
                <span class="count">0</span>             
            </span>

            <div class="notification">
    
            </div>
            
        </div>
    </div>
</div>