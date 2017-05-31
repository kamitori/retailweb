<ul class="nav nav-tabs" role="tablist">
	{% for me in menu %}
		<li role="presentation" class="
			{% if currentCategory == me['short_name'] %}
				{{'active'}}
			{% endif %}
		">
			<a href="{{baseURL}}/pos/pos/menus/{{me['short_name']}}" aria-controls="drink" role="tab" data-toggle="tab">
				{{me['name']}}
			</a>
		</li>
	{% endfor %}
	<li role="presentation" class="
		{% if currentCategory == 'All' %}
			{{'active'}}
		{% endif %}
	">
		<a href="{{baseURL}}/pos" aria-controls="all" role="tab" data-toggle="tab">
			All
		</a>
	</li>
</ul>