function _keypress(e)
{
	var _key = null;
	if(window.event){
		_key=window.event.keyCode;
	}else{
		_key= e.which; //NON IE;
	}
	if( (_key >= 48 && _key <=57) || _key ==13 )
		if(_key==8 || _key==0)
			return 0;
		else
			return ;
	else
		return false;
}
$(function(){
	if($("._datetimepicker").length){
		//$('._datetimepicker').datetimepicker();
	}
	if($(".tab_nav").length){
		$(".tab_nav ul li").on("click",function(){
			$(".tab_nav ul li").removeClass('active');
			$(this).addClass('active');
		})
	}
	if($("#setting_menu").length){
		$("#setting_menu ul li").on("click",function(){
			window.location = '../../../../../../poscash/pos/setting/'+$(this).attr('data-id');
		})

	}
	init_height_pos();

	$(window).resize(function(){
		init_height_pos();
	})

	if($("#result_search_product").length && $("#result_search_product .item").length)
		var result_search_product = new IScroll('#result_search_product',{snap: '.item',mouseWheel:true});

	if($("#result_search_category").length && $("#result_search_category .item").length)
		var result_search_category = new IScroll('#result_search_category',{snap: '.item',mouseWheel:true});
	if($(".right_setting").length)
		var right_setting = new IScroll('.right_setting');

	$(".drop_upload .file").on("change",function(e){
		var files          = e.target.files;
		var reader         = new FileReader();
		var img_element    = $(this).parent().find('img');
		var note_element    = $(this).parent().find('.text_note');
		reader.onload      = function(frEvent) {
			var data_img       =frEvent.target.result;
			img_element[0].src = data_img;
			note_element.hide();
		}
		reader.readAsDataURL(files[0]);
	});

	$(".drop_upload *").not('.file').on("click",function(e){
		e.preventDefault();
		$(this).parent().find(".file").trigger("click");
	})

	$(".list_result_setting .item").on("click",function(){
		$(".list_result_setting .item").removeClass('active');
		$(this).addClass('active');
	})

	$("#current_order_list thead tr th").not(".no-sort").on("click",function(){
		var current_value = $(this).attr('data-sort-value');
		switch(current_value) {
		    case 'ASC':
		        $(this).attr('data-sort-value','DESC');
		        break;
		    case 'DESC':
		         $(this).attr('data-sort-value','');
		        break;
		    default:
		         $(this).attr('data-sort-value','ASC');
		}
		var arr_sort_by = [];
		var arr_sort_value = [];
		$("#current_order_list thead tr th").each(function(key,element){
				arr_sort_by.push($(element).attr('data-sort'));
				arr_sort_value.push($(element).attr('data-sort-value'));
		});
		var sort_by = arr_sort_by.join(',');
		var sort_value = arr_sort_value.join(',');
		path = '_sort_by='+sort_by+'&'+'_sort_value='+sort_value;
		window.location = window.location.href.split('?')[0]+'?'+path;
	})

	$("#retrieve_order_list thead tr th").not(".no-sort").on("click",function(){
		var current_value = $(this).attr('data-sort-value');
		switch(current_value) {
		    case 'ASC':
		        $(this).attr('data-sort-value','DESC');
		        break;
		    case 'DESC':
		         $(this).attr('data-sort-value','');
		        break;
		    default:
		         $(this).attr('data-sort-value','ASC');
		}
		var arr_sort_by = [];
		var arr_sort_value = [];
		$("#retrieve_order_list thead tr th").each(function(key,element){
				arr_sort_by.push($(element).attr('data-sort'));
				arr_sort_value.push($(element).attr('data-sort-value'));
		});
		var sort_by = arr_sort_by.join(',');
		var sort_value = arr_sort_value.join(',');
		path = '_sort_by='+sort_by+'&'+'_sort_value='+sort_value;
		window.location = window.location.href.split('?')[0]+'?'+path;
	})
	
});

function init_height_pos(){
	if($("#product_list").length){
		$("#product_list").height($(window).height()-120);
	}
	if($("#setting_menu").length){
		$("#setting_menu").height($(window).height()-120);
	}
	if($("#setting_content").length){
		$("#setting_content").height($(window).height()-120);
	}

	if($("#result_search_product").length){
		$("#result_search_product").height($(window).height()-290);
	}

	if($(".right_setting").length){
		$(".right_setting").height($(window).height()-150);
	}

	if($("#result_search_category").length){
		$("#result_search_category").height($(window).height()-260);
	}
	if($("#result_search_user").length){
		$("#result_search_user").height($(window).height()-260);
	}
}

function clear_drop_upload(){
	$(".drop_upload img").attr('src','');
	$(".drop_upload .text_note").show();
	$("#id").val();
}


function edit_category(obj){
	var id = $(obj).attr('data-id');
	var name = $(obj).attr('data-name');
	var parent = $(obj).attr('data-parent');
	var order = $(obj).attr('data-order');
	var description = $(obj).attr('data-description');
	var image = $(obj).attr('data-image');

	$("#id").val(id);
	$("#name").val(name);
	$("#order").val(order);
	$("#description").val(description);
	$("#image").parent().find("img").attr('src',document.location.origin +'/'+image);
	$("#image").parent().find(".text_note").hide();
	$("#parent_category option").each(function(key,elem){
		if($(elem).val() == parent){
			$(elem).attr("selected",true);
		}
	});
}

function save_category(){
	var data = new FormData(document.getElementById('form_category'));
	// var image = $("#image").val();
	// data.push({'name':'image','value':image});
	$.ajax({
		url : '../update-category',
		type : 'POST',
		processData: false,
		contentType: false,
		data : data
	}).done(function(response) {
	    response = JSON.parse(response);
		if(response.status=="success"){
			window.location.reload();
		}else{
			alert(response.message);
		}
	})
}

function delete_category(){
	var id = $("#form_category #id").val();
	$.ajax({
		url : '../delete-category',
		type : 'POST',
		data : {
			id : id
		},
		success : function(response){
			response = JSON.parse(response);
			if(response.status=="success"){
				window.location.reload();
			}else{
				alert(response.message);
			}
		}
	})
}

function search_category(){
	var key = $("#search_category").val();
	$.ajax({
		url : '../search-category',
		type  : 'POST',
		data : {
			key : key
		},
		success : function(response){
			response = JSON.parse(response);
			html='';
			if(response.length){
				$.each(response,function(key,category){
					html+='<div class="item">';
					html+='		<div class="col-xs-4 thumbnail">';
					html+='			<img src="'+window.location.origin+'/'+category.image+'" alt="">';
					html+='		</div>';
					html+='		<div class="col-xs-8 text-justify">';
					html+='			<div class="product_name">'+category.name+'</div>';
					html+='			<i class="fa fa-pencil edit" ';
					html+='			onclick = "edit_category(this)"';
					html+='			data-id = "'+category._id['$id']+'"';
					html+='			data-name = "'+category.name+'"';
					html+='			data-parent = "'+category.parent_id+'"';
					html+='			data-order = "'+category.order_no+'"';
					html+='			data-description = "'+category.description+'"';
					html+='			data-image = "'+category.image+'"></i>';
					html+='		</div>';
					html+='	</div>';
				});
			}
			$("#result_search_category .scroller").html(html);
		}
	})
}


function edit_product(obj){
	var id = $(obj).attr('data-id');
	var name = $(obj).attr('data-name');
	var sku = $(obj).attr('data-sku');
	var category = $(obj).attr('data-category');
	var price = $(obj).attr('data-price');
	var image = $(obj).attr('data-image');

	$("#id").val(id);
	$("#name").val(name);
	$("#sku").val(sku);
	$("#price").val(price);
	$("#image").parent().find("img").attr('src',document.location.origin +'/'+image);
	$("#image").parent().find(".text_note").hide();
	$("#category option").each(function(key,elem){
		if($(elem).val() == category){
			$(elem).attr("selected",true);
		}
	});
}

function save_product(){
	var data = new FormData(document.getElementById('form_product'));
	$.ajax({
		url : '../update-product',
		type : 'POST',
		processData: false,
		contentType: false,
		data : data
	}).done(function(response) {
	    response = JSON.parse(response);
		if(response.status=="success"){
			window.location.reload();
		}else{
			alert(response.message);
		}
	})
}

function delete_product(){
	var id = $("#form_product #id").val();
	$.ajax({
		url : '../delete-product',
		type : 'POST',
		data : {
			id : id
		},
		success : function(response){
			response = JSON.parse(response);
			if(response.status=="success"){
				window.location.reload();
			}else{
				alert(response.message);
			}
		}
	})
}

function search_product(){
	var key = $("#search_product").val();
	var category = $("#category_search").val();
	$.ajax({
		url : '../search-product',
		type  : 'POST',
		data : {
			key : key,
			category : category
		},
		success : function(response){
			response = JSON.parse(response);
			html='';
			if(response.length){
				$.each(response,function(key,product){
					html+='<div class="item">';
					html+='		<div class="col-xs-4 thumbnail">';
					html+='			<img src="'+window.location.origin+'/'+product.image+'" alt="">';
					html+='		</div>';
					html+='		<div class="col-xs-8 text-justify">';
					html+='			<div class="product_name">'+product.name+'</div>';
					html+='			<i class="fa fa-pencil edit" ';
					html+='			onclick = "edit_product(this)"';
					html+='			data-id = "'+product._id['$id']+'"';
					html+='			data-name = "'+product.name+'"';
					html+='			data-parent = "'+product.parent_id+'"';
					html+='			data-order = "'+product.order_no+'"';
					html+='			data-description = "'+product.description+'"';
					html+='			data-image = "'+product.image+'"></i>';
					html+='		</div>';
					html+='	</div>';
				});
			}
			$("#result_search_product .scroller").html(html);
		}
	})

};
function _retrieveSO(_id){
    $.ajax({
        url : '/poscash/orders/doRetrieve',
        type    :'POST',
        data    :   { 
            _id:_id,
        },
        cache: false,
        beforeSend: function(){
        },
        success: function(data, type){          
            if(data.error==0){
                window.location.href = '/poscash/orders/'+data._tokenKey;
            }
        }
    });

}