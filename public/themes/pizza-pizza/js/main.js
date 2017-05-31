var timeclock;
$(document).ready(function(){
	input_number();
	init_height();
	if($("#product_scroll").length && $("#product_scroll .product-items").length){
		var product_scroll = new IScroll('#product_scroll',{snap: '.product-items'});
	}
	if($("#menu_scroll").length)
		var menu_scroll = new IScroll('#menu_scroll',{snap: '.menu-item'});
	
	if($("#home_left").length)
		var home_left = new IScroll('#home_left',{snap: 'li'});
	
	if($(".scrollbox").length)
		var scrollbox = new IScroll('.scrollbox',{snap: '.scrollitem'});
	
	// if($("#custom_scroll").length){
	// 	var custom_scroll_scroll = new IScroll('#custom_scroll',{
	// 		 snap: 'li'
	// 	});
	// }
	if($("#list_product_cart").length  && $("#list_product_cart .item").length){
		var list_product_cart_scroll = new IScroll('#list_product_cart',{
			snap: '.item'
		});
	}
	if($("#select_day_cart").length){
		create_day("#select_day_cart");
	}
	if($("#cart_tab_nav").length){
		$("#cart_tab_nav ul li").on("click",function(){
			$("#cart_tab_nav ul li").removeClass('active');
			$(this).addClass('active');
			$("#cart_tab_content .content").removeClass('active');
			$("#cart_tab_content #"+$(this).attr('data-id')).addClass('active');
		});
	}

	if($("input[name=chk_type_cart]").length){
		$("input[name=chk_type_cart]").on("change",function(){
			var val = $("input[name=chk_type_cart]").val();
			if($("#chk_delivery").is(":checked")){
				$("#checkout_cart").slideDown(300);
				$("#list_product_cart").height($(window).height()-315);
			}else{
				$("#checkout_cart").slideUp(300);
				$("#list_product_cart").height($(window).height()-160);
			}
		})
	}

	// Product list

	$(".product_left_content").delegate(".product_item", "click",function(e){
		$(".product_item").removeClass("active");
		$(".drapbox").css('display','block');
		open_footer();
		var id = $(this).attr('id');
			id = id.replace("product_item_","");

		var html = $(this).html();
			html = '<div class="product-items" rel="'+id+'">'+
				 	'<div class="product_item box-shapdow-item active">'+
				 	html+
				 	'</div>'+
				 	'</div>';
		var offset = $( this ).parent().offset();
		$(".drapbox").css('top',offset.top);
		$(".drapbox").css('left',offset.left);	
		$(".drapbox").css('width',$( this ).parent().width()); 
		$(".drapbox").css('height',$( this ).parent().height());	
		$(".drapbox").html(html);
		
		$(".drapbox").draggable({
			revert: 'invalid',
			drag: function(event,ui){
				stopclock=1;
				open_footer();
			},
			stop: function(event,ui){
				$(".drapbox").css('display','none');
			}
		});
		$("#footer_cart").droppable({
	      drop: function( event, ui ){
	      	$(".drapbox").removeClass('smallbox');
	      	addCart($(".drapbox .product-items").attr('rel'));
	     //  	var html = $(".drapbox .product-items").html();
	     //  	var id = $(".drapbox .product-items").attr('id'); console.log(id);
	     //  		id = id.replace("product_item","footer_cart");

	     //  	html = '<div class="product-items col-md-2 col-sm-4" id="'+id+'">'+
				 	// 	html+
				 	// '</div>';
	     //  	$('#footer_cart').prepend(html);
	     //  	close_footer();
	      }
	    });
	});


	if($(".select_month").length){
		$(".select_month").on("change",function(){
			var month = $(this).val();
			var day=31
			if(month==4||month==6||month==9||month==11){
				day=30;
			}
			if(month==2){
				day=29;
			}
			var html='';
			for(i=1;i<=day;i++){
				html+='<option value="'+i+'">'+i+'</option>';
			}
			$(".select_day").html(html);
			$(".select_day").trigger("change");
		});
	}

loadingCalculator();

});



$(window).resize(function(){
	init_height();
})

function addCart(id){
	open_footer();
	var data = {};
	var item = "#product_item_"+id;
	data.id = id;
	data.name = $(item+" h2").html();
	var str = $(item+" .product_item_price").html();
	data.price = parseFloat(str.replace("$",''));
	data.image = $(item+" img").attr('src');
	data.quantity = 1;
	$.ajax({
	  method: "POST",
	  url: "/carts/addcart",
	  data: data
	})
	  .done(function(msg){
	  	$('.cart-total').html(msg.amount);
	  	$('.money').html('$'+msg.total_price);
	  	addSmallItem(msg);
	  	setTimeout(function(){ close_footer(); }, 3000);
	  });
}
function addSmallItem(objItem){
	var id = objItem.id+'-'+objItem.price_string;
	if(objItem.merge==1)
		$("#footer_cart_"+id).remove();
	var price = objItem.price*objItem.quantity;
	var html = '<div class="product-items col-md-2 col-sm-4" id="footer_cart_'+id+'">'+
				 	'<div class="product_item box-shapdow-item">'+
				 		'<h2>['+objItem.quantity+'] '+objItem.name+'</h2>'+
				 		'<img src="'+objItem.image+'" alt="'+objItem.name+'" />'+
				 		'<div class="product_item_desc">'+
                            '<p class="description_item">'+
                                objItem.name+
                            '</p>'+
                            '<span class="off_in_small">Starting from: </span>'+
                            '<span class="product_item_price">$'+price+'</span>'+
                        '</div>'+
                        '<div class="add_to_cart close_bg" onclick="addCart('+objItem.id+');"> <i class="fa fa-plus"></i> </div>'+
                        "<div class=\"remove_to_cart close_bg\" onclick=\"removeCart('"+id+"');\"><i class=\"fa fa-times\"></i></div>"+
				 	'</div>'+
			 	'</div>';
  	$('#footer_cart').prepend(html);
}
function removeCart(id){
	open_footer();
	$.ajax({
	  method: "POST",
	  url: "/carts/removeitem",
	  data: {skey:id}
	})
	  .done(function(msg){
	  	$('.cart-total').html(msg.amount);
	  	$('.money').html('$'+msg.total_price);
	  	$('#footer_cart_'+id).remove();
	  	setTimeout(function(){ close_footer(); }, 3000);
	 });
}
function dropCart(){
	open_footer();
	$.ajax({
	  method: "POST",
	  url: "/carts/dropcart",
	  data: {skey:id}
	})
	  .done(function(msg){
	  	$('.cart-total').html(msg.amount);
	  	$('.money').html('$'+msg.total_price);
	  	$('#footer_cart_'+id).remove();
	  	setTimeout(function(){ close_footer(); }, 3000);
	 });
}
function addProduct(id,cate_id,link){
	//get list product
	$(".menu-item").removeClass("active");
	$("#product_info_"+id).addClass("active");
	$.ajax({
	  method: "POST",
	  url: link,
	  data: {cate_id:cate_id}
	}).done(function(items){
		var html=''; var skey='';
	  	for(var k in items){
	  		skey = items[k].id+'-'+items[k].price;
	  		skey = skey.replace(".","_");
	  		html += '<div class="product-items col-md-4 col-sm-6">'+
				 	'<div class="product_item box-shapdow-item" id="product_item_'+items[k].id+'">'+
				 		'<h2>'+items[k].name+'</h2>'+
				 		'<img src="'+items[k].image+'" alt="'+items[k].name+'" />'+
				 		'<div class="product_item_desc">'+
                            '<p class="description_item">'+
                                items[k].description+
                            '</p>'+
                            '<span class="off_in_small">Starting from: </span>'+
                            '<span class="product_item_price">$'+items[k].price+'</span>'+
                        '</div>'+
                        '<div class="add_to_cart close_bg" onclick="addCart('+items[k].id+');"> <i class="fa fa-plus"></i> </div>'+
                        "<div class=\"remove_to_cart close_bg\" onclick=\"removeCart('"+items[k].id+"');\"><i class=\"fa fa-times\"></i></div>"+
				 	'</div>'+
			 	'</div>';
	  	}
	  	$(".product_left_content").html(html);
	 });
}
function loadingCalculator(){
	var w = $(window).width();
	var h = $(window).height();
	var top = (h/2)-27.5;
	var left = (w/2)-150;
	$('.logo-center').css('top',top);
	$('.logo-center').css('left',left);
	$('.logo-center').css('display','block');	
	setTimeout(function(){ $('.loading').css('display','none'); }, 1800);
}
function init_height(){
	var winh = parseInt($(window).height());
	$("#home_left").height(winh-149);
	$("#home_right").height(winh-149);
	// $("#list_category").height(winh-129-230);
	// $("#custom_product_left").height(winh-150);
	// $("#custom_scroll").height(winh-235);
	$("#list_product_cart").height(winh-315);
	$(".product_left").height(winh-120);
	$("#product_scroll").height(winh-190);
	$("#menu_scroll").height(winh-120);
	
}
function input_number(){
	$(".input_number .up").on("click",function(){
		var input = $(this).parent().find('input');
		var value = input.val()?input.val():0;
		value = parseInt(value);
		value++;
		if(value>999){
			value=999;
		}
		input.val(value);
		input.trigger("change");
	})

	$(".input_number .down").on("click",function(){
		var input = $(this).parent().find('input');
		var value = input.val()?input.val():0;
		value = parseInt(value);
		value--;
		if(value<1){
			value=1;
		}
		input.val(value);
		input.trigger("change");
	})
}
function create_day(obj){
	$(obj).datepicker({
		todayHighlight:true,
		enableOnReadonly:false

	});
	$(obj).datepicker('update',new Date());
}
function create_hour(obj_day,obj_hour){
	var now = new Date(srvTime());
	var day = new Date($(obj_day).val());
	day = day.getDate();
	if(day != now.getDate()){
		start = 0;
	}else{
		if(now.getHours()<11 || now.getHours()>23){
			start=0
		}else{
			var hour = now.getHours();
			var minute = now.getMinutes();
			start = (hour-11)*4;
			start+= Math.ceil(minute/15);
		}
	}
	var html='';
	var hour = 10;
	var minute= 0;
	for(i=start;i<49;i++){
		var hour_text = '';
		var minute_text = '';
		minute = (i%4)*15;
		if(minute==0){
			hour= hour+1;
			minute_text=minute+'0';
		}else{
			minute_text=minute;
		}
		if(hour==12){
			hour_text = hour;
			minute_text+=' PM';
		}else{
			if(hour>12){
				hour_text = hour-12;
				minute_text+=' PM';
				if((hour-12)<10){
					hour_text = '0'+hour_text;
				}
			}else{
				hour_text = hour;
				minute_text+=' AM';
			}
		}
		html+='<option value="'+i+'">'+hour_text+':'+minute_text+'</option>';
	}
	$(obj_hour).html(html);
}

function srvTime(){
	try {
	    //FF, Opera, Safari, Chrome
	    xmlHttp = new XMLHttpRequest();
	}
	catch (err1) {
	    //IE
	    try {
	        xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
	    }
	    catch (err2) {
	        try {
	            xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
	        }
	        catch (eerr3) {
	            //AJAX not supported, use CPU time.
	            alert("AJAX not supported");
	        }
	    }
	}
	xmlHttp.open('HEAD',window.location.href.toString(),false);
	xmlHttp.setRequestHeader("Content-Type", "text/html");
	xmlHttp.send('');
	return xmlHttp.getResponseHeader("Date");
}

function open_footer(){
	$("#footer").animate({bottom: "200px"});
	$("#footer_cart").animate({height: "200px"});
	// if(stopclock==0)
		// timeclock = setInterval(function(){ close_footer(); }, 3000);
}

function close_footer(){
	$("#footer").animate({bottom: "0px"});
	$("#footer_cart").animate({height: "0px"});
	// clearInterval(timeclock);
}
function linkTo(link){
	window.location.assign(link);
}
function sign_in_popup(){
	if($("#modal_signin").length){
		$("#modal_signin").modal({
			backdrop:true,
			show:true
		});
	}
}
function signin(){
	var email_input = $("#form_signin input[name=email]");
	var password_input = $("#form_signin input[name=password]");
	var error = $("#form_signin #error");

	var email = email_input.val();
	var password = password_input.val();
	
	if(password.length==0){
		error.text('Email or password is empty').fadeIn(1500);
		password_input.focus();
	}
	if(email.length==0){
		error.text('Email or password is empty').fadeIn(1500);
		email_input.focus();
	}

	$.ajax({
		url : '../../../../../../user/signin',
		type : 'POST',
		data : {
			email : email,
			password : password
		},
		success : function(data){
			data = JSON.parse(data);
			if(data.status=='success'){
				window.location.reload();
			}else{
				error.text(data.message).fadeIn(500,function(){
					setTimeout(function(){
						error.fadeOut(500)
					},4000);
				});
			}
		}
	})
}

function check_reinput(obj,input){
	var form_create_account = $(obj).parent().parent().parent().parent().parent().parent().parent().parent().find('#form_create_account');
	var value = form_create_account.find("#"+input).val();
 	var re_value = form_create_account.find("#re_"+input).val();
 	var error = form_create_account.find("#error");
 	if(value != re_value){
 		error.text(input.substring(0,1).toLocaleUpperCase() + input.substring(1)+' confirmation should be equal to '+input+'').fadeIn(1500);
 	}else{
 		error.text('');
 	}
	form_create_account.find("#re_"+input).focus();
 	return (value == re_value);
}

function create_account_popup(){
	if($("#modal_create_account").length){
		$("#modal_create_account").modal({
			backdrop:true,
			show:true
		});
	}
}

function check_email(email){
	var regex =/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	return regex.test(email);
}

function create_account(obj){
	var form_create_account = $(obj).parent().parent().parent().parent().parent().parent().find('#form_create_account');
	var dataArray = form_create_account.serializeArray();
	var data = form_create_account.serialize();
	var error = form_create_account.find("#error");

	if(dataArray[0]['value']==''){
		error.text('Firstname is empty').fadeIn(1500);
	}else{
		if(dataArray[1]['value']==''){
			error.text('Lastname is empty').fadeIn(1500);
		}else{
			if(dataArray[2]['value']==''){
				error.text('Email is empty').fadeIn(1500);
			}else{
				if(!check_email(dataArray[2]['value'])){
					error.text('Email format incorrect').fadeIn(1500);
				}else{
					if(dataArray[3]['value']==''){
						error.text('Re-email is empty').fadeIn(1500);
					}else{
						if(!check_email(dataArray[3]['value'])){
							error.text('Re-email format incorrect').fadeIn(1500);
						}else{
							if(dataArray[2]['value'] != dataArray[3]['value']){
								error.text('Email confirmation should be equal to email').fadeIn(1500);
							}else{
								if(dataArray[4]['value']==''){
									error.text('Please choose month').fadeIn(1500);
								}else{
									if(dataArray[5]['value']==''){
										error.text('Please choose day').fadeIn(1500);
									}else{
										if(dataArray[6]['value']==''){
											error.text('Phone number is empty').fadeIn(1500);
										}else{
											if(dataArray[7]['value']==''){
												error.text('Password is empty').fadeIn(1500);
											}else{
												if(dataArray[8]['value']==''){
													error.text('Re-password is empty').fadeIn(1500);
												}else{
													if(dataArray[7]['value'] != dataArray[8]['value']){
														error.text('Password confirmation should be equal to password').fadeIn(1500);
													}else{
														$.ajax({
															url: '../../../../../../user/create_account',
															type: 'POST',
															data : data,
															success: function(data){
																data = JSON.parse(data);
																if(data.status=='success'){
																	window.location.reload();
																}else{
																	alert(data.message);
																}
															}
														})
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}	
}

function delete_cart(obj){
	if(confirm('Do you want to delete this item')){
		var skey = $(obj).attr('data-skey');
		$.ajax({
			url:'../../../../../../carts/removeitem',
			type:'POST',
			data: {
				skey : skey
			},
			success: function(data){
				$(obj).parent().parent().parent().remove();
				$("span.amount").text(data.amount);
				$("span.total_price").text(data.total_price);
			}
		})
	}
}

function update_quantity(obj){
	var skey = $(obj).attr('data-skey');
	var price = $(obj).parent().parent().parent().find('.price span').text();
	price = parseFloat(price);
	var quantity = $(obj).val();
	$.ajax({
		url:'../../../../../../carts/updatequantity',
		type:'POST',
		data: {
			skey : skey,
			quantity: quantity
		},
		success: function(data){
			$("span.amount").text(data.amount);
			$("span.total_price").text(data.total_price);
			$(obj).parent().parent().parent().find('.amount span').text(price*quantity);
		}
	})
}
