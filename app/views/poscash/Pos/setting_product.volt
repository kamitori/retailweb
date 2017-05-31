<div class="col-md-6 col-lg-4 left_setting" id="product_list">
	<p class="lead text-center"><strong>Product List</strong></p>
	<div class="col-xs-12 block">
		<input type="text" id="search_product" name="search_product" class="form-control input-search" placeholder="Search by name, SKU"/>
		<i class="fa fa-search" onclick="search_product()"></i>
	</div>
	<div class="col-xs-12 block">
		<select class="form-control" name="category_search" id="category_search" onchange="search_product()">
				<option value="">Select category to search</option>
				{% for category in category_list %}
					<option value="{{category._id}}">{{category.name}}</option>
				{% endfor %}
	    </select>
	</div>

	<div class="col-xs-12 list_result_setting" id="result_search_product">
		<div class="scroller">
			{% for product in product_list %}
			<div class="item">
				<div class="col-xs-4 thumbnail">
					<img src="{{baseURL}}/{% if product.image is defined %}{{product.image}}{% endif %}" alt="">
				</div>
				<div class="col-xs-8 text-justify">
					<div class="product_name">{{product.name}}</div>
					<i class="fa fa-pencil edit" 
					onclick = "edit_product(this)"
					data-image="{% if product.image is defined %}{{product.image}}{% endif %}" 
					data-id="{% if product._id is defined %}{{product._id}}{% endif %}"
					data-name="{% if product.name is defined %}{{product.name}}{% endif %}"
					data-sku="{% if product.sku is defined %}{{product.sku}}{% endif %}"
					data-category="{% if product.category is defined %}{{product.category}}{% endif %}"
					data-price="{% if product.price is defined %}{{product.price}}{% endif %}"></i>
				</div>
			</div>
			{% endfor %}
		</div>
	</div>
</div>
<div class="col-md-6 col-lg-8 right_setting" id="product_detail">
	<form id="form_product" method="post" enctype="multipart/form-data">
		<div class=" form-group col-md-12">
			<button class="btn btn-success" type="reset" onclick="clear_drop_upload()">
				Create new product
			</button>
			<button class="btn btn-danger" type="button" onclick="delete_product()">
				Delete product
			</button>
		</div>
		<input type="hidden" id="id" name="id" value="">
		<div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Name:</label>
		        <div class="col-xs-8">
		            <input type="text" name="name" id="name" class="form-control" placeholder="Product name">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">SKU:</label>
		        <div class="col-xs-8">
		            <input type="text" name="sku" id="sku" class="form-control" placeholder="Product SKU">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Category:</label>
		        <div class="col-xs-8">
		            <select class="form-control" name="category" id="category">
							{% for category in category_list %}
								<option value="{{category._id}}">{{category.name}}</option>
							{% endfor %}
		            </select>
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Price:</label>
		        <div class="col-xs-8">
		            <input type="text" name="price" id="price" class="form-control" placeholder="Product price">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Image:</label>
		        <div class="col-xs-8">
	            	<div class="drop_upload">
	            		<input type="file" class="file" name="image" id="image" accept="image/*">
	            		<div class="text_note">
	            			Click to upload image.
	            		</div>
	            		<div class="image">
	            			<img src="" alt="">
	            		</div>
	            	</div>
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 text-center">
			<button class="btn btn-success" type="button" onclick="save_product()">Save product</button>
	    </div>
    </form>
</div>
