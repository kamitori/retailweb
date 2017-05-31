<div class="row">
    <div class="col-md-12">
        <hr>
    </div>
</div>
<div class="row menufooter">
    <div class="col-md-2 fitem">
        <div>
            <h4>Order</h4>
        </div>
        <div class="ng-scope">
            <ul class="subnav" id="navigation">
                {% for item in menu %}
                    <li title="Deals" class="ng-scope">
                        <a href="{{ baseURL }}/{{ item.short_name }}" class="ng-binding">{{item.name}}</a>
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>

    <div class="col-md-2 fitem">
        {% set order=1 %}
        {% for item in menufooter %}
            {% if loop.first %}
                {% set order=item.order_no %}
            {% endif %}
            {% if order != item.order_no %}
                </div><div class="col-md-2 fitem">
            {% endif %}
            {% set order=item.order_no %}
                <div><h4>{{item.name}}</h4></div>
                <div class="ng-scope">
                    {% if submenufooter[item.id] is not empty %}
                        <ul class="subnav" id="navigation">
                            {% for subitem in submenufooter[item.id] %}
                                <li title="Deals" class="ng-scope">
                                    <a href="{{ baseURL }}/pages/{{ subitem['short_name'] }}" class="ng-binding">{{subitem['name']}}</a>
                                </li>
                            {% endfor %}
                        </ul>
                    {% endif %}
                </div>
        {% endfor %}
    </div>
    <div class="col-md-4 fitem">
        <h4>Connect with BanhmiSub</h4>
        <div class="row social_footer">
            <a href="#">
                <div class="col-xs-3">
                    <span id="fb_icon"></span>
                </div>
                <div class="col-xs-9">
                    Like us on Facebook
                </div>
            </a>
        </div>
        <div class="row social_footer">
            <a href="#">
                <div class="col-xs-3">
                    <span id="tw_icon"></span>
                </div>
                <div class="col-xs-9">
                    Like us on Twitter
                </div>
            </a>
        </div>
        <div class="row social_footer">
            <a href="#">
                <div class="col-xs-3">
                    <span id="yt_icon"></span>
                </div>
                <div class="col-xs-9">
                    Like us on Youtube
                </div>
            </a>
        </div>
    </div>


</div>
<div class="row">
    <div class="col-md-12 textfooter">
       {{ configs['about_footer'] }}
    </div>
</div>
