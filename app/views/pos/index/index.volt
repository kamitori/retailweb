<div id="home_left">
	<div class="scroller">
		<ul>
			{% for banner in banners %}
			<li class="new_shadow ipad_link" data-link="{{baseURLPos}}/{{banner.link}}">
				<a href=""><img src="/{{banner.image}}" alt=""></a>
			</li>
			{% endfor %}
		</ul>
	</div>
</div> 
<div id="home_right" class="scrollbox">
	<div class="scroller">
		<div class="scrollitem">
			{% for item in banners_right %}
				<img src="/{{item.image}}" alt="banner" class="box-shapdow-item  banner_right" />
			{% endfor %}
		</div>
		<div class="scrollitem" id="list_category">
			{% for category in categories %}
				{% if(category['image'] != NULL) %}
				<div class="block apple_shadow apple_bg ipad_link" data-link="{{baseURLPos}}/{{category['short_name']}}">
					<a href="{{baseURLPos}}/{{category['short_name']}}">
						<div class="imgbox">
							<img src="{{category['image']}}" alt="" />
						</div>
						<span class="category_name">{{category['name']}}</span>
					</a>
				</div>
				{% endif %}
			{% endfor %}
		</div>
		<!-- <div class="scrollitem" >
			{% for item in banners_right %}
				<img src="/{{item.image}}" alt="banner" class="box-shapdow-item banner-right" />
			{% endfor %}
		</div> -->
	</div>
</div>

<div class="ticker_special">Specials</div>

