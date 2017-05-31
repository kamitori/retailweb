<div class="col-xs-6 col-lg-4 left_setting" id="category_list">
	<p class="lead text-center"><strong>Category List</strong></p>
	<div class="col-xs-12 block">
		<input type="text" id="search_category" name="search_category" class="form-control input-search" placeholder="Search category"/>
		<i class="fa fa-search" onclick="search_category()"></i>
	</div>
	<div class="col-xs-12 list_result_setting" id="result_search_category">
		<div class="scroller">
			{% for category in category_list %}
			<div class="item">
				<div class="col-xs-4 thumbnail">
					<img src="{{baseURL}}/{% if category.image is defined %}{{category.image}}{% endif %}" alt="">
				</div>
				<div class="col-xs-8 text-justify">
					<div class="product_name">{{category.name}}</div>
					<i class="fa fa-pencil edit" 
					onclick = "edit_category(this)"
					data-id = "{% if category._id is defined %}{{category._id}}{% endif %}"
					data-name = "{% if category.name is defined %}{{category.name}}{% endif %}"
					data-parent = "{% if category.parent_id is defined %}{{category.parent_id}}{% endif %}"
					data-order = "{% if category.order_no is defined %}{{category.order_no}}{% endif %}"
					data-description = "{% if category.description is defined %}{{category.description}}{% endif %}"
					data-image = "{% if category.image is defined %}{{category.image}}{% endif %}"></i>
				</div>
			</div>
			{% endfor %}
		</div>
	</div>
</div>
<div class="col-xs-6 col-lg-8 right_setting" id="category_detail">
	<form id="form_category" method="post" enctype="multipart/form-data">
		<div class=" form-group col-md-12">
			<button class="btn btn-success" type="reset" onclick="clear_drop_upload()">
				Create new category
			</button>
			<button class="btn btn-danger" type="button" onclick="delete_category()">
				Delete category
			</button>
		</div>
		<input type="hidden" id="id" name="id" value="">
		<div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Name:</label>
		        <div class="col-xs-8">
		            <input type="text" name="name" id="name" class="form-control" placeholder="Category name">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Parent category:</label>
		        <div class="col-xs-8">
		            <select class="form-control" name="parent_category" id="parent_category">
		            	<option value="">No parent</option>
						{% for category in category_list %}
							<option value="{{category._id}}">{{category.name}}</option>
						{% endfor %}
		            </select>
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Order No.:</label>
		        <div class="col-xs-8">
		            <input type="number" step="1" min="1" max="999" name="order" id="order" class="form-control" placeholder="Order No.">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Description:</label>
		        <div class="col-xs-8">
		            <input type="text" name="description" id="description" class="form-control" placeholder="Category description">
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
			<button class="btn btn-success" type="button" onclick="save_category()">Save category</button>
	    </div>
    </form>
</div>
