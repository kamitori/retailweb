var timeclock;
var myStorage = localStorage;
$(document).ready(function(e){

	//sync data client-server
	// syncDataClientServer();

	//init
	input_number();
	init_height();

	// Scrolls
	if($("#product_scroll").length && $("#product_scroll .product-items").length){
		var product_scroll = new IScroll('#product_scroll',{snap: '.product-items',mouseWheel:true,disablePointer: true,disableTouch: false,disableMouse: false});
	}
	if($("#menu_scroll").length && $("#menu_scroll .menu-item").length)
		var menu_scroll = new IScroll('#menu_scroll',{snap: '.menu-item',disablePointer: true,disableTouch: false,disableMouse: false});

	if($("#home_left").length && $("#home_left li").length)
		var home_left = new IScroll('#home_left',{snap: 'li',disablePointer: true,disableTouch: false,disableMouse: false});

	// if($(".popup_option").length && $(".popup_option .option_item").length)
	// 	var popup_option = new IScroll('.popup_option',{snap: '.option_item'});

	if($(".scrollbox").length && $(".scrollbox .scrollitem").length)
		var scrollbox = new IScroll('.scrollbox',{snap: '.scrollitem',disablePointer: true,disableTouch: false,disableMouse: false});
	
	if($("#list_product_cart").length  && $("#list_product_cart .item").length){
		var list_product_cart_scroll = new IScroll('#list_product_cart',{
			snap: '.item',disablePointer: true,disableTouch: false,disableMouse: false
		});
	}
	if($('#cartbox').length && $(".cartbox_item").length)
		var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item',disablePointer: true,disableTouch: false,disableMouse: false});

	$('.ipad_link').bind('touchstart touchend click', function(e) {
        e.preventDefault();
        location.href = $(this).data('link');
    });
    
    $('.finalizes').bind('touchstart click',function(e){
    	document.getElementById("priting_iframe").contentDocument.location.reload(true);    	
    });
    
  	$('.menu-item').bind("click touchstart", function(e){
  		e.preventDefault();
  		var check = true;
  		var step = $(".comboitem").attr("data-step");
  		if(step=='1' && $(this).data('linkkey')!='banh_mi_subs')
  			check = false;
  		if(step=='2' && $(this).data('linkkey')!='appetizers')
  			check = false;
  		if(step=='3' && $(this).data('linkkey')!='drinks')
  			check = false;
  		if(check){
	  		openCate($(this).data('key'),$(this).data('link'));
	  		$('.menu-item').removeClass("active");
	  		$(this).addClass("active");
	  		var title = $(this).find(".textmid p").html();
	  		$("#category_title").html(title);	
  		}
  		
	});	
  	
  	$('.order_list_bt').bind("touchend click", function(e){
  		e.preventDefault();
  		load_saved_orders();
  	});
  	$(".main-content").delegate(".delorder", "click touchend",function(e){
  		e.preventDefault();
  		var orderid = $(this).attr('data-orderid');
  		delete_order(orderid,0);
  	});
  	$('.viewnow').bind("touchend click", function(e){
  		e.preventDefault();
  		var orderid = $(this).attr('data-orderid');
  		view_order_history_detail(orderid);
  	});
  	$('.back_to_cart').bind("touchend click", function(e){
  		e.preventDefault();
  		reload_cart(function(html){
			$(".back_to_cart").css("display","none");
            $(".order_list_bt").css("display","inline-block");
  		});
  	});
  	// View Cart
  	$('#view_cart').bind("touchend click", function(e){
  		e.preventDefault();
  	// $("#footer").delegate("#view_cart", "touchend",function(e){
  		reload_cart(function(html){
			swith_cart(html,1);
  		});
    });
	
	$('#saveorder').bind("touchend click", function(e){
		e.preventDefault();
		reload_cart(function(html){
			swith_cart(html,3);
  		});
	});

	$('#exit_from_cart').bind("touchend click", function(e){
		e.preventDefault();
		window.location.assign(window.location.href);
	});

	$('#checkout').bind("touchend click", function(e){
		e.preventDefault();
		reload_cart(function(html){
			reload_payment_calculation_box();
			$('#payment-calculation-modal').modal('toggle');			
  		});
	});
	

	$('.clear_cart_bt').bind("touchend click", function(e){
		e.preventDefault();
		clear_cart();
	});
	$('.close_combo_bt').bind("touchend click", function(e){
		e.preventDefault();
		cancelCombo();
	});
	$('.next_group_bt').bind("touchend click", function(e){
		e.preventDefault();
		nextGroup();
	});
	$('.end_group_bt').bind("touchend click", function(e){
		e.preventDefault();
		endGroup();
	});
	
	$(".tabs").delegate(".excludes", "click touchend",function(e){
		e.preventDefault();
		var group = $(this).data("group");
		// console.log(group);
		var pid = $(this).data("pid");
		// console.log(pid);
		var default_id = $("[name='"+group+"']").val();
		$("[data-group='"+group+"']").removeClass('option_item_active');
		$(this).addClass('option_item_active');
		$("[data-group-qty='"+group+"']").val(0).attr("value","0");
		$("#"+pid).val(1).attr("value","1");
		if(pid==default_id && $('#od_'+default_id).length){
			$('#od_'+default_id).remove();
		}else if($('#od_'+default_id).length){
			$('#od_'+default_id).html($("#name_"+pid).html());
		}else{
			$('.op_description').append('<p id="od_'+default_id+'">'+$("#name_"+pid).html()+'</p>');
		}
		calPrice();
	});

	$(".tabs").delegate(".change_qty_img", "click touchend",function(e){
		e.preventDefault();
		upQty($(this).attr("alt"));
	});
	$(".bms_box_scroller").delegate(".btedit", "click touchend",function(e){
		e.preventDefault();
		complete_station($(this));
	});
	$(".online_orders_box_scroller").delegate(".btedit", "click touchend",function(e){
		e.preventDefault();
		confirm_station($(this));
	});
	//$('.online_orders_box_scroller .btedit').html('Confirm');
	

	$(".product_left").delegate(".nav_item", "click touchstart touchend",function(e){
    	var tag = $(this).data("tag");
    	if(tag!=''){
    		$(".product_left_content .product-items").css("display","none");
    		$("[data-tag^='"+tag+"']").css("display","block");
    		var h = $(window).height()-42;
    		$("#product_scroll").height(h);
    		product_scroll.scrollTo(0,0);
           
    	}else{
    		$(".product_left_content .product-items").css("display","block");
    	}
    	$(".nav_item").removeClass("active");
    	$(this).addClass("active");
    	$(".drapbox").css('display','none');
    });

	// Product list
	$(".product_left").delegate(".product_item", "dblclick",function(e){
		$(".product_item").removeClass("active");
		var id = $(this).attr('id');
		id = id.replace("product_item_","");
		var custom = 0;
		custom = $("#custom_"+id).val();
		addCart(id,custom);
		
	});
	// var dragable=true;
	// var first_touch = {};
	// var end_touch = {};
	// $(".product_left").delegate(".product_item", "touchend",function(e){
	// 	var check=false;
	// 	if(!end_touch.x){
	// 		check=true;
	// 	}
	// 	if( Math.abs(first_touch.x - end_touch.x)<4){
	// 		check=true;
	// 	}
	// 	if(check){
	// 		$(".product_item").removeClass("active");
	// 		var id = $(this).attr('id');
	// 		id = id.replace("product_item_","");
	// 		var custom = 0;
	// 		custom = $("#custom_"+id).val();
	// 		addCart(id,custom);
	// 	}
	// 	first_touch = {};
	// 	end_touch = {};
		
	// });
	$(".product_left").delegate(".product_item","touchmove",function(e){
		end_touch = {x:e.originalEvent.touches[0].pageX,y:e.originalEvent.touches[0].pageY}
	});
	$(".product_left").delegate(".product_item","touchstart",function(e){
		first_touch = {x:e.originalEvent.touches[0].pageX,y:e.originalEvent.touches[0].pageY}
		e.preventDefault();
	});
	var tapedTwice = false;
	var first_touch = {};
	var end_touch = {};
	$(".product_left").delegate(".product_item","touchend",function(e){
		var check=false;
		if(!end_touch.x){
			check=true;
		}
		if( Math.abs(first_touch.x - end_touch.x)<5){
			check=true;
		}
		if(check){
			if(!tapedTwice) {
				tapedTwice = true;
				setTimeout( function() { tapedTwice = false; }, 400 );
				return false;
			}
			$(".product_item").removeClass("active");
			var id = $(this).attr('id');
			id = id.replace("product_item_","");
			var custom = 0;
			custom = $("#custom_"+id).val();
			addCart(id,custom);
		}
		first_touch = {};
		end_touch = {};
	});

	$("body").delegate(".payment_input", "keyup",function(e){
		var cash_tend = $(this).val();
		var ids = $(this).data('orderid');
		var total = parseFloat($('#orderbox_'+ids+' .order_cartbox_price').text().replace('$',''));
		var change_due = FortmatPrice(cash_tend - total);
		if(change_due<0){
			$(".repay_ao").text('-'+Math.abs(change_due));
		}else{
			$(".repay_ao").text(''+change_due);
		}
	});
	$(".product_left_backup").delegate(".product_item", "click touchend",function(e){
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
			start: function() {
		        open_footer();
		    },
			drag: function(event,ui){
			},
			stop: function(event,ui){
				$(".drapbox").css('display','none');
				close_footer();
			}
		});
		$("#footer_cart").droppable({
	      drop: function( event, ui ){
	      	$(".drapbox").removeClass('smallbox');
	      	var custom = 0;
	      	custom = $("#custom_"+id).val();
	      	addCart($(".drapbox .product-items").attr('rel'),custom);
	      	close_footer();
	      }
	    });
	});


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

	if (typeof(Storage) !== "undefined" && localStorage.loaded==1){
		$('.logo-center').css('display','block');
		$('.loading').css('display','none');
	}
	else
		loadingCalculator();

	$("#last_order thead tr th").not(".no-sort").on("click",function(){
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
		$("#last_order thead tr th").each(function(key,element){
				arr_sort_by.push($(element).attr('data-sort'));
				arr_sort_value.push($(element).attr('data-sort-value'));
		});
		var sort_by = arr_sort_by.join(',');
		var sort_value = arr_sort_value.join(',');
		path = '_sort_by='+sort_by+'&'+'_sort_value='+sort_value;
		window.location = window.location.href.split('?')[0]+'?'+path;
	});

	if($("#cash_tend").length){
		$("#cash_tend").keyup(function(){
			var cash_tend = parseFloat($(this).val()); 
			cal_repay(cash_tend);
		});
		$("#cash_tend").change(function(){
			$("#cash_tend").val(FortmatPrice($(this).val()));
		});
	}

	if($(".number_input").length){
		datatype_number();
	} 
	$("body").delegate(".bgpopup", "click touchend",function(e){
		e.preventDefault();
		closePopup();
	});

	$('#changeMenu').bind("touchend click", function(e){
		e.preventDefault();
		changeMenu();
	});
	$('.main-content').bind("click", function(e){
		e.preventDefault();
		$("#changeMenu").attr("data-status","off");
		$("#leftMenu").attr("data-status","off");
		$(".main_menu_popup").css("display","none");
	});

	$('.main-content').bind("touchend", function(e){
		e.cancelable=true;
		// e.preventDefault();
		$("#changeMenu").attr("data-status","off");
		$("#leftMenu").attr("data-status","off");
		$(".main_menu_popup").css("display","none");
	});

	$('#leftMenu').bind("touchend click", function(e){
		e.preventDefault();
		leftMenu();
	});
	
	if($("#bms_box").length){
		$("#footer").css("display","none");
		var h = $(window).height()-42;
        $("#bms_box").css("height",h+"px");
        if($("#bms_box .bms_item").length){
            var bms_box = new IScroll('#bms_box',{snap: '.bms_item',disablePointer: true,disableTouch: false,disableMouse: false});
        }
		setInterval(load_station,5000);
	}
	if($("#account_order").length){
		$("#footer").css("display","none");
		var h = $(window).height()-42;
        $("#account_order").css("height",h+"px");
        if($("#account_order .orderbox_item").length){
            var bms_box = new IScroll('#account_order',{snap: '.orderbox_item',disablePointer: true,disableTouch: false,disableMouse: false});
        }
	}

  	$(".main-content").delegate(".paynow", "click touchend",function(e){
		pay_account_order_with_method($(this).data('id'),$(this).data('status'));
	});
	$(".main-content").delegate(".sendproduct", "click touchend",function(e){
		sendproduct_account_order($(this).data('id'));
	});
	$(".main-content").delegate(".edit_account_order", "click touchend",function(e){
		adjustment_order($(this).data('id'));
	});
	$(".main-content").delegate(".reprocess_account_order", "click touchend",function(e){
		reprocess_order_now($(this).data('id'));
	});
	$(".main-content").delegate(".print_account_order", "click touchend",function(e){
		$("#priting_iframe").attr("src",appHelper.baseURL+'/print-order/'+$(this).data('id'));
        setTimeout(function(){
            $("priting_iframe").load();
            $('#print-modal').modal('toggle');
        },500);
	});
	$(".editnow").bind("touchend click", function(e){
		adjustment_order($(this).data('id'));
	});


	$("body").delegate(".input_amount_tender", "keyup",function(e){
		e.preventDefault();
		var amount_tender = FortmatPrice($(this).val());
		amount_tender_text = $(this).parent().parent().find('.amount_tender_text');
		amount_tender_text.text(amount_tender);

		var total = parseFloat($('.rerotate .main_total').text().replace('$',''));
		var total_amount_tender = get_total_amount_tender();
		var change_due = total_amount_tender - total;
		var change_due_txt = FortmatPrice(total_amount_tender - total);
		if(change_due<-0.005){
			$("#change_due").text('-'+FortmatPrice(Math.abs(change_due)));
			$('#finalize_payment_btn').hide();
		}else{
			$("#change_due").text(''+change_due_txt);
			if(total > 0){
				$('#finalize_payment_btn').show();	
			}
			else{
				$('#finalize_payment_btn').hide();
			}
		}
	});
	$("body").delegate(".input_amount_tender", "change",function(e){
		$(this).val(FortmatPrice($(this).val()));
		// $('#tender_onaccount').val(0.00);
		// var on_account_tender_text = $('#tender_onaccount').parent().parent().find('.amount_tender_text');
		// on_account_tender_text.text(FortmatPrice(0.00));
		$(this).trigger('keyup');
		updateSessionPayment();
	});
	$("body").delegate(".coll_exp", "click touchend",function(e){
		// console.log($(this).text());
		collapse_expand($(this).data('order-id'),$(this).text());
	});
	$("body").delegate(".completeall", "click touchend",function(e){
		complete_all_station($(this));
	});

});

$(document).on( 'pageinit',function(event){
});

$(window).resize(function(){
	init_height();
});

$(function(){
	reload_payment_calculation_box();
});
function reload_payment_calculation_box(){
	$.ajax({
        method: "POST",
        url: appHelper.baseURL + '/carts/payment-calculation',
        success:function(html){
            $('.payment_calculation_box').html(html);
         	setTimeout(function(){
                var total = parseFloat($('.rerotate .main_total').text().replace('$',''));
				var total_amount_tender = get_total_amount_tender();
				var change_due = total_amount_tender - total;
				var change_due_txt = FortmatPrice(total_amount_tender - total);
				if(change_due<0){
					$("#change_due").text('-'+FortmatPrice(Math.abs(change_due)));
					$('#finalize_payment_btn').hide();
				}else{
					$("#change_due").text(''+change_due_txt);
					if(total > 0){
						$('#finalize_payment_btn').show();	
					}
					else{
						$('#finalize_payment_btn').hide();
					}
				}
            }, 300);
        }
    });
}


if( /Android|webOS|iPhone|iPod|Blackberry|Windows Phone/i.test(navigator.userAgent)){
    $('.popup_qty').each(function(){
        $(this).attr("onchange", "changeQty($(this).attr('id'));");
    });
    $('.tax_select').each(function(){
        $(this).attr("onchange", "changeTax();");
    });
    $('.change_qty_img').each(function(){
        $(this).attr("onclick", "upQty('"+$(this).attr("alt")+"'');");
    });
    $('.payment_type').each(function(){
        $(this).attr("onchange", "changePaymentType();");
    });
}

function syncDataClientServer(){
	$msg = '';
	$.ajax({
        method: "POST",
        dataType: "json",
        url: appHelper.baseURL + '/service/sync-data-from-server',
        success:function(res){
            $msg += res.message + ' ';
            $.ajax({
		        method: "POST",
		        dataType: "json",
		        url: appHelper.baseURL + '/service/sync-data-to-server',
		        success:function(res){
		            $msg += res.message;
		            console.log('msg:'+$msg);
		        }
		    });
        }
    });
}