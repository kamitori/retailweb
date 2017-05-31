// $(function(){
// 	//automatically signing out after a few time in the idle, exclude drink, bms and online station
// 	var pathname = $(location).attr('pathname');
// 	if(pathname != '/drink-station' && pathname != '/bms-station' && pathname != '/online-station' && pathname != '/cart')
// 	{
// 		$.ajax({
// 			url: appHelper.baseURL + '/user/check_is_logged_in',
// 			type: 'GET',
// 			success: function (result) {
// 				if(result.status)
// 				{
// 					var idle_time = 300000; //5 minutes
// 					$( document ).idleTimer( idle_time );
// 					$( document ).on( "idle.idleTimer", function(event, elem, obj){
// 						// function you want to fire when the user goes idle
// 						$( this ).idleTimer("destroy");
// 						location.href = appHelper.baseURL + '/user/logout';
// 					});
// 					$( document ).on( "active.idleTimer", function(event, elem, obj, triggerevent){
// 						// function you want to fire when the user becomes active again
// 						$( this ).idleTimer( idle_time );
// 					});					
// 				}
// 				else
// 				{
// 					$( document ).idleTimer("destroy");
// 				}
// 			}
// 		});		

// 	}
// });

function reorder(_id){
	$.ajax({
	  method: "POST",
	  url: "/lastorders/reorder",
	  data: {id:_id}
	})
	.done(function(msg){
	  	window.location.href = '/carts';
	});
}


function openCate(cate_name, link) {
    $.ajax({
        method: "POST",
        url: link,
        data: {
            cate_name: cate_name
        },
        success:function(html){
        	$(".product_left").html(html);
	        $(".drapbox").hide();
	        init_height();
	        // $(".popup_option").height(328);
	        if($("#product_scroll .product-items").length){
			var product_scroll = new IScroll('#product_scroll', {
				snap: '.product-items',
				mouseWheel: true,
			});
			$(".navbar-brand").html();
				setTimeout(function() {
				product_scroll.refresh();
				product_scroll.scrollTo(0,0);
			}, 0);
	        }
		        
        }
    });
}

function updateDescription(id,qty){
	if(id=='popup_qty_main')
		return false;
	
	var is_default = 0;
	var havex = 1;
	if($('#isdefault_'+id).length){
		is_default = 1;
	}

	var default_qty = parseInt($("#default_qty_"+id).val());
	var lock_all_default = parseInt($("#lock_default_when_no_choice").val());
	var change_amout = parseInt($("#change_amout").val());
	
	//default Yes
	var default_yes = 0;
	if(is_default==1 && qty==0)
		default_yes = 1;

	//neu qty=default va co hien trong description thi remove
	if(qty==default_qty && $('#od_'+id).length && is_default==0){
		$('#od_'+id).remove();
		havex = -1;

	//neu qty=default va khong hien trong description thi ko lam gi
	}else if(qty==default_qty && is_default==0){
		// console.log("nothing");
		havex = 0;

	}else if(is_default==1 && qty==0 && $('#od_'+id).length){
		$('#od_'+id).remove();

	//hien qty khac default va va thuoc nhom finish & hien trong description
	}else if($('#od_'+id).length && $("#"+id).data('group-finish')==1 && default_yes == 0){
		var lable = $("#"+id+" option:selected").text();
		$('#od_'+id).html($("#name_"+id).html()+' (<b>'+lable+'</b>)');
	
	//hien qty khac default & hien trong description
	}else if($('#od_'+id).length && default_yes == 0){
		$('#od_'+id).html('<b>'+qty+"</b> "+$("#name_"+id).html());
	
	//hien qty khac default va va thuoc nhom finish
	}else if($("#"+id).data('group-finish')==1 && default_yes == 0){
		var lable = $("#"+id+" option:selected").text();
		$('.op_description').prepend('<p id="od_'+id+'">'+$("#name_"+id).html()+' (<b>'+lable+'</b>) </p>');
	
	//hien qty khac default
	}else if(default_yes == 0){
		$('.op_description').prepend('<p id="od_'+id+'"><b>'+qty+"</b> "+$("#name_"+id).html()+'</p>');
	}
	//gach cheo hinh
	if(qty==0){
		$("#ximg_"+id).css("display","block");
	}else{
		$("#ximg_"+id).css("display","none");
	}
	if(is_default==1)
		havex = 0;
	change_amout += havex;
	//co thay doi
	if(change_amout>0){
		// nhung hien tai khong thay doi defaul
		if(is_default==0 && lock_all_default==1){  
			$("#lock_default_when_no_choice").val(0);//mo khoa cac default
			//removeValuesDefault();
			unLockAllDefault();
		}
	}else{
		if(is_default==0){
			resetValuesDefault();
			LockAllDefault();
			$("#lock_default_when_no_choice").val(1);//khoa cac default
		}		
	}
	$("#change_amout").val(change_amout);
}
function upQty(id,type,_notbutton){
	if($('#islocked_'+id).val()==1){
		return false;
	}
	var qty = parseInt($('#'+id).val());
	//count Yes
	var max = parseInt($('#max_choice').val());
	var oldsum = 0;
	if($("select[data-group-type='Inc']").length>0)
	$("select[data-group-type='Inc']").each(function(){
        if(parseInt($(this).val())>0){
           oldsum++;
        }
    });
	if(qty==0)
		oldsum++;
	if(max>0 && oldsum>max)
		return alertMax();
	//end count Yes

	if(isNaN(qty)){
		$('#'+id).val(1);
		updateDescription(id,1);
	}else if($('#'+id).data('group-finish')==1 && qty==document.getElementById(id).options.length-1){
		$('#'+id).val(0);
		updateDescription(id,0);
	}else{
		if(_notbutton) $('#'+id).val(qty);
		else $('#'+id).val(qty+1);
		updateDescription(id,qty+1);
	}
	if(type=='update_cart'){
		upload_option_qty(id.replace("fc_",""),qty+1);
	}
	else
		calPrice();
}
function downQty(id,type){
	if($('#islocked_'+id).val()==1){
		return false;
	}
	var qty = parseInt($('#'+id).val());	
	if(isNaN(qty)){
		$('#'+id).val('1');
		updateDescription(id,1);
	}else{
		qty = qty -1;
		if(qty<1){
			qty=1;
		}
		$('#'+id).val(qty);
		updateDescription(id,qty);
	}
	if(type=='update_cart'){
		upload_option_qty(id.replace("fc_",""),qty);
	}
	else
		calPrice();
}
function changeQty(id,type){
	if($('#islocked_'+id).val()==1){
		return false;
	}
	var qty = parseInt($('#'+id).val());
	//count Yes
	var max = parseInt($('#max_choice').val());
	var oldsum = 0;
	if($("select[data-group-type='Inc']").length>0)
	$("select[data-group-type='Inc']").each(function(){
        if(parseInt($(this).val())>0){
           oldsum++;
        }
    });
	if(qty==0)
		oldsum++;
	if(max>0 && oldsum>max)
		return alertMax();
	//end count Yes
	
	if(isNaN(qty)){
		$('#'+id).val('1');
		updateDescription(id,1);
	}else if(qty>-1){
		updateDescription(id,qty);
	}
	if(type=='update_cart'){
		upload_option_qty(id.replace("fc_",""),qty);
	}
	else
		calPrice();
}
function alertMax(){
	$(".message_box").html("ADD MAXIMUM "+$('#max_choice').val()+" OPTION(S)");
	
	setTimeout(function() {
		$(".message_box").animate({color: "white"});
		$(".message_box").html("");
        $(".message_box").animate({color: "red"});
    }, 5000);
}
function closePopup(){
	$(".popup_item").css("display","none");
	$(".bgpopup").css("display", "none");
}
function addSmallItem(item) {

	if ($('#item-'+ item.cartKey).length) {
		$('#item-'+ item.cartKey + ' .item-quantity').text(item.quantity);
		$('#item-'+ item.cartKey + ' .item-sell-price').text('$'+ item.sell_price);
	} else {
		var html = '<div class="product-items col-md-2 col-sm-4" id="item-'+ item.cartKey +'">' +
				    '<div class="product_item box-shapdow-item">' +
				        '<h2>'+ item.name +'</h2><img src="'+ item.image +'" alt="'+ item.name +'">' +
				        '<div class="product_item_desc">' +
				            '<p class="description_item">'+ item.description +'</p><span class="off_in_small"></span><span class="product_item_price item-sell-price">$'+ item.sell_price +'</span><span class="product_item_qty"><strong class="item-quantity">'+ item.quantity +'</strong>pcs</span></div>' +
				        '<div class="remove_to_cart close_bg" onclick="removeCart(\''+ item.cartKey +'\');"><i class="fa fa-times"></i></div>' +
				    '</div>' +
				'</div>';
    	$('#footer_cart').prepend(html);
	}
}
function loadingCalculator(){
	var w = $(window).width();
	var h = $(window).height();
	var top = (h/2)-27.5;
	var left = (w/2)-150;
	$('.logo-center').css('top',top);
	$('.logo-center').css('left',left);

	$('.logo-center').css('display','block');
	setTimeout(function(){
		$('.loading').css('display','none');
        localStorage.loaded = 1;
	}, 1800);
}
function init_height(){
	var winh = parseInt($(window).height());
	$("#home_left").height(winh-106);
	$("#home_right").height(winh-116);
	$("#list_product_cart").height(winh-315);
	$(".product_left").height(winh-88);
	$("#product_scroll").height(winh-170);
	$("#menu_scroll").height(winh-88);
	$(".popup_option").height(328);
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
function open_footer(hole){
	$("#footer").animate({bottom: "200px"});
	$("#footer_cart").animate({height: "200px"});
	if(hole==undefined){
		clearTimeout(timeclock);
		timeclock = setTimeout(function(){ close_footer(); }, 1500);
	}
}
function close_footer(){
	$("#footer").animate({bottom: "0px"});
	$("#footer_cart").animate({height: "0px"});
	clearTimeout(timeclock);
}
function linkTo(link){
	window.location.assign(link);
	location.href = link;
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
	if(email.length==0){
		error.text('Email or password is empty').fadeIn(1500);
		email_input.focus();
		return;
	}
	if(password.length==0){
		error.text('Email or password is empty').fadeIn(1500);
		password_input.focus();
		return;
	}
	$.ajax({
		url : appHelper.baseURL + '/user/signin',
		type : 'POST',
		data : {
			email : email,
			password : password
		},
		success : function(data){
			data = JSON.parse(data);
			if(data.status=='success'){
				if($("#_link").val()){
					window.location.href = $("#_link").val();
				}else{
					window.location.reload();
				}
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
	var form_create_account = $(obj).parent().parent().parent().parent().parent().parent().parent().find('#form_create_account');
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
															url: appHelper.baseURL + '/user/create_account',
															type: 'POST',
															async: false,
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
function modal_location(){
	if($("#modal_location").length){
		$("#modal_location").modal({
			backdrop:false,
			show: true
		})
	}
}
function find_location(){
	var postal_code = $("#postal_code_location").val().replace(" ","");
	var street_number = $("#street_number_location").val();
	var street_name = $("#street_name_location").val();
	var city = $("#city_location").val();
	var province = $("#province_location").val();
	var address = '';
	if(postal_code.length==0){
		address = street_number+'-'+street_name+','+city+','+province;
	}
	$.ajax({
		url : '../../../../../../../../carts/findlocation',
		type : 'POST',
		data : {
			postal_code : postal_code,
			address : address
		},
		success : function(data){
			data = JSON.parse(data);
			if(data.status=='success'){
				window.location.reload();
			}else{
				alert(data.message);
			}
		}
	})
}

function datatype_number(){
	$(".number_input").each(function(key,element){
		$(element).prop('autocomplete','off');
		$(element).prop('autocorrect','off');
		$(element).prop('autocapitalize','off');
		$(element).prop('spellcheck','false');
		$(element).keydown(function(e){
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) ||
				 // Allow: home, end, left, right
				(e.keyCode >= 35 && e.keyCode <= 39)) {
					if(e.keyCode==190){
						if($(this).val().indexOf(".")>0){
							e.preventDefault();
						}
						else{
							if($(this).val().length){
								return;
							}else{
								e.preventDefault();
							}
						}
					}else{
						// let it happen, don't do anything
					 	return;
					}

			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)  ) {
				e.preventDefault();
			}
		});
		$(element).on("change",function(e){
			$(this).val(FortmatPrice($(this).val()));
		});
		var old_value = $(this).val();
		$(element).on("blur",function(){
			if(old_value != $(this).val()){
				$(this).trigger('change');
				old_value=$(this).val();
			}
		})
	});

}

function findEmail(obj){
	$.ajax({
		url : appHelper.baseURL + '/orders/get-contact',
		type : 'POST',
		data :{
			email : $(obj).val()
		},
		success:function(data){
			html='';
			if(data.length){
				$.each(data,function(key,contact){
					html+='<p><a onclick="choose_contact(this)" data-name="'+contact.fullname+'" data-email="'+contact.email+'">'+contact.email+'</a></p>'
				})
				$("#findResult").html(html);
				if(html!=''){
					$("#findResult").show();
				}
			}else{
				$("#btnAddContact").show();
				$("#findResult").html(html);
				if(html!=''){
					$("#findResult").hide();
				}
			}

		}
	})

}

function choose_contact(obj){
	$("#nameInfo").val($(obj).attr('data-name'));
	$("#emailInfo").val($(obj).attr('data-email'));
	$("#findResult").hide();
	$("#btnAddContact").hide();
}

function createContact(){
	var name = $("#nameInfo").val();
	var email = $("#emailInfo").val();
	$.ajax({
		url : appHelper.baseURL + '/orders/create-contact',
		type : 'POST',
		data :{
			email : email,
			name : name
		},
		success : function(data){
			if(data.has_created){
				alert('This customer has been created');
			}
			$("#nameInfo").val(data.fullname);
	 		$("#emailInfo").val(data.email);
	 		$("#findResult").hide();
			$("#btnAddContact").hide();
		}
	});
}

function addNoteProduct(){
	$(".note_product").css("display","block");
	$(".add_note_product").css("display","none");
	// $(".hidden_note_product").css("display","block");
	$(".clear_note_product").css("display","block");
	$(".note_product").focus();
}
function clearNoteProduct(){
	$(".note_product").val('');
	$(".note_product").css("display","none");
	$(".add_note_product").css("display","block");
	$(".clear_note_product").css("display","none");
}

function updateCartNote(textarea){
	var note = $(textarea).val();
	$.ajax({
		url: appHelper.baseURL + '/carts/update-note',
		type: 'POST',
		data: {
			note: note
		},
		success: function (result) {
			$(".order_notes").text(note);
			if (result.error === 0) {
				$('#cart-note-modal').modal('hide');
			}
		}
	})
}
function confirm_station(obj){
	var status = $(obj).attr('data-change-status-to');
	var message = 'Do you want to <b>CONFIRM</b> this order?';
	if(status == 'Cancelled')
	{
		message = 'Do you want to <b>CANCEL</b> this order?';
	}
	confirm_h("",message,"default",function(){
		var order_id = $(obj).attr('data-order-id');
		var product_id = $(obj).attr('data-product-id');
		$.ajax({
			url : appHelper.baseURL + "/stations/change-status",
			type: "POST",
			data: {
				order_id : order_id,
				product_id : product_id,
				status : status
			},
			success: function(result){
				load_station();
			}
		});
	},function(){

	});
}
function complete_station(obj){
	confirm_h("","Do you want to complete?","default",function(){
		var order_id = $(obj).attr('data-order-id');
		var product_id = $(obj).attr('data-product-id');
		var cart_id = $(obj).attr('data-cart-id');
		var bms_type = $(".bms_type").val();
		// console.log(bms_type);
		if(order_id==product_id){
			$.ajax({
				url : appHelper.baseURL + "/stations/complete-task",
				type: "POST",
				data: {
					task_id : order_id					
				},
				success: function(result){
					if(result==1){
						load_station();
					}else{
						//alert(result);
					}
				}
			});
		}else{
			$.ajax({
				url : appHelper.baseURL + "/stations/complete",
				type: "POST",
				data: {
					order_id : order_id,
					product_id : product_id,
					cart_id : cart_id,
					bms_type:bms_type
				},
				success: function(result){
					if(result==1){
						load_station();
					}else{
						//alert(result);
					}
				}
			});
		}
	},function(){

	});
}
function load_station(){
	var loadtype = $(".bms_type").val();
	var autoload = $("#autoload").val();
	if(autoload==1)
	$.ajax({
		url : appHelper.baseURL + "/stations/"+loadtype,
		type: "GET",
		success: function(result){
			var cl = 'bms_box_scroller';
			if(loadtype == 'online')
			{
				cl = 'online_orders_box_scroller';	
			}
			$("."+cl).html(result);
			
			//$('.online_orders_box_scroller .btedit').html('Confirm');
			
			var h = $(window).height();
            var bot = h-42;
            $("#bms_box").css("height",bot+"px");
            if($('.bms_item').length){
                var bms_box = new IScroll('#bms_box',{snap: '.bms_item'});
                setTimeout(function(){
                    bms_box.refresh();
                    bms_box.scrollTo(0,0);
                }, 0);
            }
          //   setTimeout(function(){
          //   	var check_ring = $("#ring").val();
       			// console.log(check_ring);
          //   	if(check_ring==1)
          //   		playmusic();
          //   },500);
		}
	});
}

function complete_all_station(obj){
	confirm_h("","Do you want to complete this order?","default",function(){
		var order_id = $(obj).attr('data-order-id');
		var is_manager = $(obj).attr('data-is-manager');
		var bms_type = $(".bms_type").val();
		// console.log(bms_type);
		$.ajax({
			url : appHelper.baseURL + "/stations/complete-all",
			type: "POST",
			data: {
				order_id : order_id,
				is_manager:is_manager,
				bms_type:bms_type
			},
			success: function(result){
				if(result==1){
					load_station();
				}
			}
		});
	},function(){

	});
}


function launchIntoFullscreen(element) {
  if(element.requestFullscreen) {
    element.requestFullscreen();
  } else if(element.mozRequestFullScreen) {
    element.mozRequestFullScreen();
  } else if(element.webkitRequestFullscreen) {
    element.webkitRequestFullscreen();
  } else if(element.msRequestFullscreen) {
    element.msRequestFullscreen();
  }
}

function exitFullscreen() {
  if(document.exitFullscreen) {
    document.exitFullscreen();
  } else if(document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if(document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }
}
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

function onFocusQuantity(obj,ids){
	// if(ids==undefined)
	// 	ids="popup_qty_main";	
	// alert_h('Number pad','<div id="change-sale-discount-popup" class="popup numpad-popup popup-align-top popup-visible"><div class="popup-content numpad-popup-content"><div class="popup-body"><form action="#" novalidate="" class="numpad-input-form numpad-input-open"><div id="numpad_input" class="numpad"><div class="numpad-content"><div class="numpad-section numpad-section-main"><button type="button" value="1" class="numpad-key numpad-key-number" tabindex="0" onclick=_caculatorClick("1")>1</button><button type="button" value="2" class="numpad-key numpad-key-number" tabindex="0" onclick=_caculatorClick("2")>2</button><button type="button" value="3" onclick=_caculatorClick("3") class="numpad-key numpad-key-number" tabindex="0">3</button><button type="button" value="4" onclick=_caculatorClick("4") class="numpad-key numpad-key-number" tabindex="0">4</button><button type="button" value="5" onclick=_caculatorClick("5") class="numpad-key numpad-key-number" tabindex="0">5</button><button type="button" value="6" onclick=_caculatorClick("6") class="numpad-key numpad-key-number" tabindex="0">6</button><button type="button" value="7" onclick=_caculatorClick("7") class="numpad-key numpad-key-number" tabindex="0">7</button><button type="button" value="8" onclick=_caculatorClick("8") class="numpad-key numpad-key-number" tabindex="0">8</button><button type="button" value="9" onclick=_caculatorClick("9") class="numpad-key numpad-key-number" tabindex="0">9</button><button type="button" value="0" onclick=_caculatorClick("0") class="numpad-key numpad-key-number" tabindex="0">0</button><button type="button" onclick="_caculatorClick(00)" value="00" class="numpad-key numpad-key-double-zero" tabindex="0">00</button><button type="button" onclick=_caculatorClick(".") value="." class="numpad-key numpad-key-decimal" tabindex="0">.</button></div><div class="numpad-section numpad-section-last"><button type="button" value="delete" onclick=_caculatorClick("del") class="numpad-key numpad-key-delete" tabindex="0"><i class="fa fa-arrow-circle-left"></i></button><button type="button" value="delete" onclick=_caculatorClick("clear") class="numpad-key numpad-key-delete" tabindex="0"><i class="fa fa-remove"></i></button><button type="button" onclick=_caculatorClick("enter","'+ids+'") value="return" class="numpad-key numpad-key-return" tabindex="0"><span>return</span></button></div></div></div><div class="numpad-input"><label class="numpad-input-label" for="change-sale-discount-input">Quantity</label><div class="numpad-input-content"><button type="button" value="toggle" class="numpad-input-button-toggle" title="Close Number Pad"><i onclick="hideCaculator()" class="numpad-icon-toggle"></i></button><input onkeypress="return _keypress();" style="font-size:30px;" id="change-sale-discount-input" value="'+$(obj).val()+'" type="text" class="numpad-input-input " placeholder="E.g. 20% or 2.50" title="E.g. 20% or 2.50" pattern="[-]{0,1}([0-9]{0,}[.]{1}[0-9]{1,}|[0-9]{1,}[.]{0,1}[0-9]{0,})[%]{0,1}"><div class="validation-bubble"></div></div></div></form></div></div><div class="popup-beak" style="position: relative; top: 0px; left: 141.984px;"></div></div>'
	// ,{
	// 	'width':'344px',
	// 	'height':'450px'
	// });
}
function _caculatorClick(_str,ids,_check){
	if(_check){
		var _currentstr = $("#change-sale-quantity-input").val();
	}else{
		var _currentstr = $("#change-sale-discount-input").val();
	}	
	var _temp = parseInt(_str,10);
	if(_temp>=0 && _temp<=9){
		var check = false;
		if(_currentstr.indexOf("%") >= 0){
			check = true;
		}
		if(_str!='00') _currentstr = parseFloat(_currentstr + _str);
		else if(_str!='000') _currentstr = parseFloat(_currentstr + _str);
		else _currentstr = parseFloat(_currentstr)*100;
		if(check) _currentstr +='%';
	}else{
		if(_str=='.'){
			if(_currentstr.indexOf(".") >= 0){
			}else{
				_currentstr = _currentstr+'.';	
			}
		}else if(_str=='del'){
			_currentstr = _currentstr.slice(0,-1);
			if(_currentstr=='') _currentstr = 0;
		}else if(_str=='clear'){
			_currentstr = 0;
		}else if(_str=='enter'){
			// $("#popup_qty_main").val(_currentstr);
			$("#"+ids).val(_currentstr);
			upQty('popup_qty_main','',true);
			$('div.ja_close').click();
			if(ids=='cash_tend'){
				cal_repay(_currentstr);
			}
			return;
		}else if(_str=='%'){
			if(_currentstr.indexOf("%") >= 0){
				var _arr = _currentstr.split('%');				
				_currentstr = _arr[0]+'%';
			}else{
				_currentstr = parseFloat(_currentstr);
				if(_currentstr==0 || isNaN(_currentstr)) _currentstr = '0%';
				else _currentstr += '%';
			}			
		}else if(_str=='00'){
			if(_currentstr.indexOf("00") >= 0){
			}else{
				_currentstr = _currentstr +'00';
				_currentstr = parseFloat(_currentstr);
			}			
		}else if(_str=='+/-'){
			_currentstr = parseFloat(_currentstr)*-1;
		}		
	}
	_currentstr = _currentstr.toString();
	if(_currentstr!='' && _currentstr.indexOf("%") >= 0){
		var _arr = _currentstr.split('%');
		if(parseFloat(_arr[0])>100)
			_currentstr = '100%';
	}	
	if(_check){
		_currentstr = parseInt(_currentstr,10);
		if(isNaN(_currentstr) || _currentstr=='') _currentstr = 1;
		$("#change-sale-quantity-input").val(_currentstr);
	}else{
		$("#change-sale-discount-input").val(_currentstr);
	}	
}

function cal_repay(cash_tend){
	var total = parseFloat($('.rerotate .main_total').text().replace('$',''));
	var change_due = FortmatPrice(cash_tend - total);
	if(change_due<0){
		$(".change_due").text('-'+Math.abs(change_due));
	}else{
		$(".change_due").text(''+change_due);
	}
	$(".change_due").parent().show();
}
function changePaymentType(){
	var pm = $("#Paidby").val();
	if(pm=="On Account" || pm=="Credit card"){
		$(".pm_cash_type").css("display","none");
	}else{
		$(".pm_cash_type").css("display","block");
	}
}
function changeMenu(){
	var status = $("#changeMenu").attr("data-status");
	if(status=="off"){
		showMainMenu(true);
		showMenuCenter(false);  
	}else{
		showMainMenu(false);
	}
}

function leftMenu(){
	
	var status = $("#leftMenu").attr("data-status");
	if(status=="off"){
		showMenuCenter(true); 
		showMainMenu(false);	
	}else{
		showMenuCenter(false);  
	}
}
function showMainMenu(status)
{
	if(status)
	{
		$("#changeMenu").attr("data-status","on");
		//$(".main_menu_popup").css("display","block");
		$('#main_menu_popup').modal('show'); 		
	}
	else
	{
		$("#changeMenu").attr("data-status","off");
		//$(".main_menu_popup").css("display","none");
		$('#main_menu_popup').modal('hide'); 		
	}
}
function showMenuCenter(status)
{
	if(status)
	{
		$("#leftMenu").attr("data-status","on");
		$('#menu_popup_center').modal('show'); 		
	}
	else
	{
		$("#leftMenu").attr("data-status","off");
		$('#menu_popup_center').modal('hide'); 		
	}
}

function checkActionShift(action)
{
	$.ajax({
		url: appHelper.baseURL + '/staffs/check_performed_shift/'+action,
		type: 'POST',
		data: {},
		success: function (result) {
			if(result.status == false)
			{
				actionShift(action);
			}
			else
			{
				//alert('The '+action+'ing in your shift for today was already performed.');	
				alertB('The '+action+'ing in your shift for today was already performed.');
			}
		}
	});			
}
function actionShift(action)
{
	confirm_h('Confirm','Do you want to '+action+' your shift?','default',function(){
		$.ajax({
			url: appHelper.baseURL + '/staffs/'+action+'_shift',
			type: 'POST',
			data: {},
			success: function (result) {
				alertB(result.message);
			}
		});						
	});
}

function performShift(action, contact_id, contact_pwd, our_rep_id, our_rep)
{
	confirm_h('Confirm','Do you want to '+action.toUpperCase()+' your shift?','default',function(){
		$.ajax
		({
			type: "GET",
			dataType : 'jsonp',
			crossDomain:true,
			url: appHelper.JT_URL + '/services/perform_shift/'+action,
			data: {
				contact_id: contact_id,				
				contact_pwd: contact_pwd,
				our_rep_id: our_rep_id,
				our_rep: our_rep
			},
			jsonpCallback: 'serverCallback',
			success: function (data) {
			  //console.log(data);
			  alertB(data.message);
			},
			failure: function(xhr, ajaxOptions, thrownError) {alert("Error!");}
		}); 
						
	});
}

function AddLineUsename(){
	var count = $(".use_group_form .form-group").length +1;
	var new_html = '<div class="form-group">';
		new_html += ' <label for="username_'+count+'"></label>';
		new_html += ' <input type="text" class="form-control username" id="username_'+count+'" placeholder="Customer Name '+count+'" value="Customer Name '+count+'">';
		new_html += '</div>';
	$(".use_group_form").append(new_html);
}

function openInternalTasks(){
	$('#task_list').modal('toggle');
	$('#main_menu_popup').modal('toggle');
	
}
function doubleTask(task_id){
	$.ajax({
		url: appHelper.baseURL + '/stations/double-task/',
		type: 'POST',
		data: {task_id:task_id},
		success: function(result){
			$('#task_list').modal('toggle');
		}
	});
}
function collapse_expand(order_id,type){
	$.ajax({
		url: appHelper.baseURL + '/stations/collapse-expand/',
		type: 'POST',
		data: {order_id:order_id,type:type},
		success: function(result){
			load_station();
		}
	});
}