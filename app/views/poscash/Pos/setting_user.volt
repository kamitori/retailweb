<div class="col-md-6 col-lg-4 left_setting" id="user_list">
	<p class="lead text-center"><strong>user List</strong></p>
	<div class="col-xs-12 block">
		<input type="text" id="search_user" name="search_user" class="form-control input-search" placeholder="Search by name, SKU"/>
		<i class="fa fa-search" onclick="search_user()"></i>
	</div>

	<div class="col-xs-12 list_result_setting" id="result_search_user">
		<div class="scroller">
			
		</div>
	</div>
</div>
<div class="col-md-6 col-lg-8 right_setting" id="user_detail">
	<form id="form_user" method="post" enctype="multipart/form-data">
		<div class=" form-group col-md-12">
			<button class="btn btn-success" type="reset" onclick="clear_drop_upload()">
				Create new user
			</button>
			<button class="btn btn-danger" type="button" onclick="delete_user()">
				Delete user
			</button>
		</div>
		<input type="hidden" id="id" name="id" value="">
		<div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Name:</label>
		        <div class="col-xs-8">
		            <input type="text" name="name" id="name" class="form-control" placeholder="user name">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">SKU:</label>
		        <div class="col-xs-8">
		            <input type="text" name="sku" id="sku" class="form-control" placeholder="user SKU">
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Category:</label>
		        <div class="col-xs-8">
		            <select class="form-control" name="category" id="category">
						
		            </select>
		        </div>
			</div>
	    </div>
	    <div class="form-group col-md-12 col-lg-6">
			<div class="row">
		        <label class="control-label col-xs-4 text-right">Price:</label>
		        <div class="col-xs-8">
		            <input type="text" name="price" id="price" class="form-control" placeholder="user price">
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
			<button class="btn btn-success" type="button" onclick="save_user()">Save user</button>
	    </div>
    </form>
</div>
