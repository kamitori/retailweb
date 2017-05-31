<div class="pull-left tab_nav" id="setting_menu">
	<ul>
		<li data-id="product" {% if module=='product'%} class="active" {% endif %}>Product</li>
		<li data-id="category" {% if module=='category'%} class="active" {% endif %}>Category</li>
		<li data-id="theme" {% if module=='theme'%} class="active" {% endif %}>Theme</li>
		<li data-id="user" {% if module=='user'%} class="active" {% endif %}>User</li>
	</ul>
</div>

<div class="pull-left tab_content" id="setting_content">
    <div id="{{module}}" class="tab">
    	<?php 
    		if($module!='')
    		echo $this->partial('Pos/setting_'.$module); 
    	?>
    </div>
</div>