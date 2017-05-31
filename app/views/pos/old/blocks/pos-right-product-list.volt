<div class="tab-content" style="clear:both">
	<div role="tabpanel" class="tab-pane active" id="drink">
		<div class="group_block" id="listproducts">
			{{ListProducts}}			
		</div>
	</div>	
</div>
{% if(TotalPage) %}
	{{TotalPage}}
{% endif %}