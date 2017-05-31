function setCombo(product_id){
    $("#category_title").css("display","none");
    $("#combo_alert").css("display","block");
    $(".comboitem").attr("data-step",'1');
    $(".comboitem").html($("#step_description_1").val()+" (step 1/3)");
    var  combo_sales = parseFloat($("#combosales_"+product_id).val());
    $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/carts/begin-combo/',
            data: {product_id:product_id,combo_sales:combo_sales},
            success:function(html){
                openCate('Banh Mi SUBS',appHelper.baseURL+'/banh_mi_subs');
                $("#menuleft_banh_mi_subs").addClass("active");
                $("#category_title").html('Banh Mi SUBS');
            }
        });
}
function cancelCombo(){
    $("#category_title").css("display","block");
    $("#combo_alert").css("display","none");
    $(".comboitem").attr("data-step",'0');
    $(".comboitem").html($("#step_description_0").val()+" (step 0/3)");
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + '/carts/cancel-combo/',
        success:function(html){
        }
    });
}
function endCombo(){
    $("#category_title").css("display","block");
    $("#combo_alert").css("display","none");
    $(".comboitem").attr("data-step",'0');
    $(".comboitem").html($("#step_description_0").val()+"(step 0/3)");
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + '/carts/stop-combo/',
        success:function(html){

        }
    });
}
function qtyAddCombo(n,ids,cartKey){
    var comboqty = parseInt($("#popup_qtycombo_"+ids).val());
    if(n==0)
        comboqty--;
    else
        comboqty++;
    // console.log(comboqty);
    if(comboqty>0){
        $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/carts/addqty-combo/',
            data:{cartKey:cartKey, comboqty:comboqty},
            success:function(html){
                reload_cart(function(){
                });
            }
        });
    }
}
function setGroup(){
    var product_id = $("#begin_choice_product").attr("data-proid");
    var username = $("#username_1").val();
    $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/carts/begin-group/',
            data: {product_id:product_id,username:username},
            success:function(result){
                if(result.next_uid!=undefined){
                    $(".username").attr("value","Name of Guest "+result.next_uid);
                    $(".username").val("Name of Guest "+result.next_uid);
                    $(".username").attr("placeholder","Name of Guest "+result.next_uid);
                }
                $("#category_title").css("display","none");
                $("#group_alert").css("display","block");
                $(".user_name_group").html('Selecting Menu Item for '+username);
                $('#use-group-modal').modal('toggle');
            }
        });
}
function nextGroup(){
    $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/carts/next-group/',
            data: [],
            success:function(result){
                if(result.next_uid!=undefined){
                    $(".username").attr("value","Name of Guest "+result.next_uid);
                    $(".username").val("Name of Guest "+result.next_uid);
                    $(".username").attr("placeholder","Name of Guest "+result.next_uid);
                }
                $('#use-group-modal').modal('toggle');
            }
        });
}
function endGroup(){
    $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/carts/end-group/',
            data: [],
            success:function(html){
                $("#category_title").css("display","block");
                $("#group_alert").css("display","none");
            }
        });
}
function addCart(id, custom) {
    
    var data = {};
    var item = "#product_item_" + id;

    data.id = id;
    data.name = $(item + " h2").html();
    var str = $(item + " .product_item_price").html();
    data.price = parseFloat(str.replace("$", ''));
    data.image = $(item + " img").attr('src');
    data.quantity = 1;
    data.description = $(item + " .description_item").html();
    data.product_desciption = $(item + " .product_desciption").html();
    data.combo = $("#iscombo_"+id).val();
    data.isgroup = $("#isordergroup_"+id).val();

    if(data.isgroup==1){
        
        $('#use-group-modal').modal('toggle');
        $("#username_1").focus();
        $("#begin_choice_product").attr("data-proid",id);       

    }else if(data.combo==1){
        setCombo(id);
    }else{
        open_footer();
        $('.popup_qty').val(1);
        $(".popup_content .item_id").val(data.id);
        $(".popup_content .popup_title h2").html(data.name);
        $(".popup_content .popup_title .description_item").html(data.product_desciption);
        $(".popup_price").html("$" + data.price);
        $("#sell_price_popup_qty_main").val(data.price);
        $(".popup_content .popup_image img").attr("src", data.image).load();
        $(".popup_item").css("display", "block");
        $('.op_description').html("");
        $('.note_product').val('');
        $(".popup_add_cart").css("display", "block");
        $(".bgpopup").css("display", "block");
        $(".popup_update_cart").css("display", "none");
        clearNoteProduct();

        $.ajax({
            method: "POST",
            url: appHelper.baseURL + '/products/option/' + data.id,
            success:function(html){
                $(".tabs").html(html);
                calPrice();
                $(".popup_option").height(328);
                if($(".popup_scroller_1 .option_item").length){
                    var popup_scroller_1 = new IScroll('.popup_option', {
                        snap: '.option_item'
                    });
                    setTimeout(function() {
                        popup_scroller_1.refresh();
                        popup_scroller_1.scrollTo(0,0);
                    }, 0);
                }
                if($(".popup_scroller_2 .option_item").length){
                    var popup_scroller_2 = new IScroll('.popup_option', {
                        snap: '.option_item'
                    });
                    setTimeout(function() {
                        popup_scroller_2.refresh();
                        popup_scroller_2.scrollTo(0,0);
                    }, 0);
                }
                
                
                setTimeout(function() {
                    LockAllDefault();
                    //add description default
                    // addDescriptionDefault();
                    var description_default = $('.description_default').html();
                    $('.op_description').html(description_default);
                }, 100);
            }
        });
    }
}
function addDescriptionDefault(){
    $('.isdefault').each(function(){
        var idd = $(this).attr('id');
        idd = idd.replace("isdefault_","");
        if($('#default_qty_'+idd).val()==1){    
            updateDescription(idd,1);
        }
    });
}
function LockAllDefault(){
    $('.isdefault').each(function(){
        var idd = $(this).attr('id');
        idd = idd.replace("isdefault_","");
        $('#'+idd).attr("disabled", true);
        $('#islocked_'+idd).attr("value", 1);
        $('#islocked_'+idd).val(1);
    });
}
function unLockAllDefault(){
    $('.isdefault').each(function(){
        var idd = $(this).attr('id');
        idd = idd.replace("isdefault_","");
        $('#'+idd).attr("disabled", false);
        $('#islocked_'+idd).attr("value", 0);
        $('#islocked_'+idd).val(0);
    });
}
function removeValuesDefault(){
    var max = parseInt($('#max_choice').val());
    $('.isdefault').each(function(){
        if($(this).val()==1){
            var idd = $(this).attr('id');
            idd = idd.replace("isdefault_","");
            $('#'+idd).val(0);
            $("#ximg_"+idd).css("display","block");
        }
    });
}
function resetValuesDefault(){
    $('.isdefault').each(function(){
        var idd = $(this).attr('id');
        idd = idd.replace("isdefault_","");
        var dfv = $('#default_qty_'+idd).val()
        $('#'+idd).val(dfv);
        if(dfv==0){
            $("#ximg_"+idd).css("display","block");
        }else{
            $("#ximg_"+idd).css("display","none");
        }
    });
    var description_default = $('.description_default').html();
    $('.op_description').html(description_default);
}
function getProductData(){
	var data = {
            'main': {
                '_id': $('.popup_prices .item_id').val(),
                'quantity': $('.popup_prices .popup_qty').val(),
                'note':$(".note_product").val()
            },
            'options': []
        };
	$('.popup_option .popup_qty').each(function() {
        var qty = $(this).val();
        var _id = $(this).attr('id');
        var isdefault = 0;
        if($("#isdefault_"+_id).length){
            isdefault = 1;
        }
        data.options.push({
            '_id': _id,
            'quantity': qty,
            'name': $("#name_"+_id).html(),
            'image':$("#img_"+_id).attr("src"),
            'group_id':$("#group_id_"+_id).val(),
            'group_name':$("#group_name_"+_id).val(),
            'group_type':$(this).data('group-type'),
            'group_order':$(this).data('group-order'),
            'option_group':$(this).data('group-qty'),
            'option_type':$(this).data('option-type'),
            'level':$(this).data('group-level'),
            'isfinish':$(this).data('group-finish'),
            'default_qty':$("#default_qty_"+_id).val(),
            'default':isdefault
        });
    });
    // console.log(data);
    return data;
}

var calProcess;

function calPrice() {
    if (typeof calProcess != 'undefined'){
        clearTimeout(calProcess);
    }
    calProcess = setTimeout(function(){
        $.ajax({
            url: appHelper.baseURL + '/products/calculate',
            data: getProductData(),
            type: 'POST',
            success: function(result) {
                if (result.error === 0) {
                    $('.popup_price').html("$" + result.total);
                    $('#sell_price_popup_qty_main').val(result.total);
                    // console.log(result);
                } else if (result.error === 1){
                    alert(result.message);
                }
                calProcess = undefined;
            }
        });
    }, 500);
}
function addCustomCart() {
    $('.popup_item').hide();
    var data = getProductData();
	data.main.image = $('.popup_image img').attr('src');
    $.ajax({
        url: appHelper.baseURL + "/carts/add-cart",
        method: "POST",
        data: data,
    	success: function(result) {
            open_footer();
    		if (result.error == 0) {
                $('.cart-total').text(result.cart.quantity);
                $("#main_order_qty").val(result.cart.quantity);
                // $('.money').text("$"+result.cart.total);
                // $('.taxval').text("$"+result.cart.tax);
                $('.main_total').text("$"+result.cart.main_total);
    			addSmallItem(result.product);
    			$(".drapbox").hide();
                if(result.is_use_group==1){
                    $("#category_title").css("display","none");
                    $("#group_alert").css("display","block");
                }else if(result.combo_step!=undefined && parseInt(result.combo_step)<4){
                    //after add product
                    var des = $("#step_description_"+result.combo_step).val();
                    $(".comboitem").attr("data-step",result.combo_step);
                    $(".comboitem").html(des+" (step "+result.combo_step+"/3)");
                    //open cate
                    $('.menu-item').removeClass("active");

                    if(result.combo_step=='0'){
                        $("#category_title").css("display","block");
                        $("#combo_alert").css("display","none");
                        $(".comboitem").attr("data-step",'0');
                        $(".comboitem").html($("#step_description_0").val()+"(step 0/3)");
                        // $(".bgpopup").css("display", "none");
                        // return true;
                    }
                    if(result.combo_step=='2'){
                        openCate('Appetizers',appHelper.baseURL+'/appetizers');
                        $("#menuleft_appetizers").addClass("active");
                        $("#category_title").html('Appetizers');
                        // console.log(appHelper.baseURL+'/appetizers');
                    }
                    if(result.combo_step=='3'){
                       openCate('Drinks',appHelper.baseURL+'/drinks');
                       $("#menuleft_drinks").addClass("active");
                       $("#category_title").html('Drinks');
                    }

                }else if(parseInt(result.combo_step)>3){//endcombo
                    endCombo();
                    reload_cart(function(html){
                        swith_cart(html,1);
                    });
                }
    		} else if (result.error == 1) {
    			alert(result.message);
    		}
            $(".bgpopup").css("display", "none");
        }
    });
}
function updateCart() {
    $('.popup_item').hide();
    var data = getProductData();
    data.main.image = $('.popup_image img').attr('src');
    data.cart_id = $('#popup_cart_id').val();
    $.ajax({
        url: appHelper.baseURL + "/carts/update-cart",
        method: "POST",
        data: data,
        success: function(result) {
            if (result.error == 0) {
                $('.cart-total').html(result.cart.quantity);
                $("#main_order_qty").val(result.cart.quantity);
                $('.main_total').html('$' + result.cart.main_total);
                reload_cart(function(html){});
            } else if (result.error == 1) {
                alert(result.message);
            }
        }
    });
}
function removeCart(cartKey) {
    open_footer();
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + "/carts/remove-item",
        data: {
            cartKey: cartKey
        },
        success: function(result) {
        	if (result.error == 0) {
        		$('.cart-total').html(result.cart.quantity);
                $("#main_order_qty").val(result.cart.quantity);
        		$('.money').html('$' + result.cart.total);
        		$('#item-' + cartKey).remove();
        		setTimeout(function() {
        		    close_footer();
        		}, 3000);
        	} else if (result.error == 1) {
        		alert(result.message);
        	}
	    }
    });
}
function dropCart() {
    open_footer();
    $.ajax({
            method: "POST",
            url: "/carts/dropcart",
            data: {
                skey: id
            }
        })
        .done(function(msg) {
            $('.cart-total').html(msg.amount);
            $("#main_order_qty").val(msg.amount);
            $('.money').html('$' + msg.total_price);
            $('#footer_cart_' + id).remove();
            setTimeout(function() {
                close_footer();
            }, 3000);
        });
}

// function edit_cart(obj) {
//     var cartKey = $(obj).attr('data-cart-key');
//     var product_id = $(obj).attr('data-product-id');
//     var quantity = $(obj).attr('data-quantity');
//     var price = $(obj).attr('data-price');
//     $.ajax({
//             url: appHelper.baseURL +'/carts/get-option-item',
//             type: 'POST',
//             data: {
//                 cartKey: cartKey
//             },
//             success:function(res){
//                 $(".popup_item .tabs").html(res);
//                 $('.popup_prices .item_id').val(product_id);
//                 $('.popup_prices .popup_qty').val(quantity);
//                 $("#save_edit_cart").attr('data-cart-key',cartKey);
//                 $(".popup_option").height(500);
//                 if($(".popup_option .option_item").length){
//                     var popup_option = new IScroll('.popup_option', {
//                         snap: '.option_item'
//                     });
//                     setTimeout(function() {
//                         popup_option.refresh();
//                         popup_option.scrollTo(0,0);
//                     }, 0);
//                 }
//             }
//         });
//     $(".popup_item").show();
// }

function saveEditCart(obj){
    var data = getProductData();
    var cartKey = $(obj).attr('data-cart-key');
    data['cartKey'] = cartKey;
    $.ajax({
        url : appHelper.baseURL + '/carts/update-item',
        type: 'POST',
        data: data,
        success:function(result){
            if (result.error == 0) {
                $('.cart-total').html(result.cart.quantity);
                $("#main_order_qty").val(result.cart.quantity);
                $('.main_total').html('$' + result.cart.main_total);
                $('#item-'+ cartKey + ' .item-total').text(result.product.total);
                $(".popup_item").hide();
                window.location.reload();
            } else if (result.error == 1) {
                alert(result.message);
            }
            quantityChangingProcess[ cartKey ] = undefined;
        }
    })

}

function delete_cart(cartKey) {
    confirm_h('Alert','Do you want to delete this item','default',function(){
        $.ajax({
                url: appHelper.baseURL +'/carts/remove-item',
                type: 'POST',
                data: {
                    cartKey: cartKey
                },
                success: function(result) {
                    reload_cart(function(html){});
                    if (result.error == 0) {
                        $('#item-'+cartKey).remove();
                        $('.cart-total').html(result.cart.quantity);
                        $("#main_order_qty").val(result.cart.quantity);
                        $(".total_price").text(result.cart.total);
                        $('.main_total').text("$"+result.cart.main_total);
                    } else if (result.error == 1) {
                        alert(result.message);
                    }
                }
            });
      }, function(){
        console.log('denied');
    });
}
function deleteCombo(combo_id) {
    confirm_h('Alert','Do you want to delete this item','default',function(){
        $.ajax({
                url: appHelper.baseURL +'/carts/remove-combo',
                type: 'POST',
                data: {
                    combo_id: combo_id
                },
                success: function(result) {
                    reload_cart(function(html){});
                    if (result.error == 0) {
                        $('#item-'+cartKey).remove();
                        $('.cart-total').html(result.cart.quantity);
                        $("#main_order_qty").val(result.cart.quantity);
                        $(".total_price").text(result.cart.total);
                        $('.main_total').text("$"+result.cart.main_total);
                    } else if (result.error == 1) {
                        alert(result.message);
                    }
                }
            });
      }, function(){
        console.log('denied');
    });
}

function changeCombo(cartKey,combo_step){
    confirm_h('Alert','Do you want to CHANGE this item','default',function(){
        $.ajax({
                url: appHelper.baseURL +'/carts/change-combo',
                type: 'POST',
                data: {
                    cartKey:cartKey
                },
                success: function(result) {
                    reload_cart(function(html){
                        swith_cart(html,1);
                    });
                    $("#category_title").css("display","none");
                    $("#combo_alert").css("display","block");
                    var des = $("#step_description_"+combo_step).val();
                    $(".comboitem").attr("data-step",combo_step);
                    $(".comboitem").html(des+" (step "+combo_step+"/3)");
                    //open cate
                    $('.menu-item').removeClass("active");
                    if(combo_step=='1'){
                        openCate('Banh Mi SUBS',appHelper.baseURL+'/banh_mi_subs');
                        $("#menuleft_banh_mi_subs").addClass("active");
                        $("#category_title").html('Banh Mi SUBS');
                    }
                    if(combo_step=='2'){
                        openCate('Appetizers',appHelper.baseURL+'/appetizers');
                        $("#menuleft_appetizers").addClass("active");
                        $("#category_title").html('Appetizers');
                        console.log(appHelper.baseURL+'/appetizers');
                    }
                    if(combo_step=='3'){
                       openCate('Drinks',appHelper.baseURL+'/drinks');
                       $("#menuleft_drinks").addClass("active");
                       $("#category_title").html('Drinks');
                    }
                    $("#footer_cart").css("height","0px");
                }
            });
      }, function(){
        console.log('denied');
    });
}

function deleteGroupByUser(user_id) {
    confirm_h('Alert','Do you want to delete this item','default',function(){
        $.ajax({
                url: appHelper.baseURL +'/carts/remove-user-group',
                type: 'POST',
                data: {
                    user_id: user_id
                },
                success: function(result) {
                    reload_cart(function(html){});
                    if (result.error == 0) {
                        $('#item-'+cartKey).remove();
                        $('.cart-total').html(result.cart.quantity);
                        $("#main_order_qty").val(result.cart.quantity);
                        $(".total_price").text(result.cart.total);
                        $('.main_total').text("$"+result.cart.main_total);
                    } else if (result.error == 1) {
                        alert(result.message);
                    }
                }
            });
      }, function(){
        console.log('denied');
    });
}

function changeGroupByUser(cartKey,user_id){
    confirm_h('Alert','Do you want to CHANGE this item','default',function(){
        $.ajax({
                url: appHelper.baseURL +'/carts/change-user-group',
                type: 'POST',
                data: {
                    cartKey:cartKey,
                    user_id:user_id
                },
                success: function(result) {
                    reload_cart(function(html){
                        swith_cart(html,1);
                    });
                    $("#category_title").css("display","none");

                    if(result.next_uid!=undefined){
                        $(".username").attr("value",result.user_name);
                        $(".username").val(result.user_name);
                        $(".username").attr("placeholder",result.user_name);
                    }
                    $("#category_title").css("display","none");
                    $("#group_alert").css("display","block");
                    $(".user_name_group").html(result.user_name);
                    // $('#use-group-modal').modal('toggle');

                    $("#footer_cart").css("height","0px");
                }
            });
      }, function(){
        console.log('denied');
    });
}

var quantityChangingProcess = {};

function update_quantity(obj) {
    var cartKey = $(obj).data('cart-key');
    $(obj).parent().next().next().find('button').attr('data-quantity',$(obj).val());
    if (typeof quantityChangingProcess[ cartKey ] != 'undefined') {
    	clearTimeout(quantityChangingProcess[ cartKey ]);
    }
    alert();
    quantityChangingProcess[ cartKey ] = setTimeout(function() {
    	var quantity = $(obj).val();
    	$.ajax({
    	    url: appHelper.baseURL + '/carts/update-quantity',
    	    type: 'POST',
    	    data: {
    	        cartKey: cartKey,
    	        quantity: quantity
    	    },
    	    success: function(result) {
                reload_cart(function(html){});
    	    	if (result.error == 0) {
    	    		// $("span.amount").text(result.cart.quantity);
    	    		// $(".total_price").text(result.cart.total);
                    $('.cart-total').html(result.cart.quantity);
                    $("#main_order_qty").val(result.cart.quantity);
                    $('.main_total').html('$' + result.cart.main_total);
    	    		$('#item-'+ cartKey + ' .item-total').text(result.product.total);
    	    	} else if (result.error == 1) {
    	    		alert(result.message);
    	    	}
    	    	quantityChangingProcess[ cartKey ] = undefined;

    	    }
    	});
    }, 500);
}
function upload_option_qty(id,qty){
    $.ajax({
        url: appHelper.baseURL + '/carts/update-quantity',
        type: 'POST',
        data: {
            cartKey: id,
            quantity: qty
        },
        success: function(result) {
            if (result.error == 0) {
                $("#price_fc_"+id).text(result.product.total);
                $('.cart-total').text(result.cart.quantity);
                $("#main_order_qty").val(result.cart.quantity);
                $('.money').text("$"+result.cart.total);
                $('.taxval').text("$"+result.cart.tax);
                $('.main_total').text("$"+result.cart.main_total);

                //update user group total
                if(result.product.group_total != undefined)
                {
                    var group_total_tag = $('.group_total[data-user-id="'+result.product.user_id+'"]');
                    if(group_total_tag.length > 0)
                    {
                        group_total_tag.text(result.product.group_total);     
                    }               
                }
                reload_cart(function(html){});           

            } else if (result.error == 1) {
                console.log(result.message);
            }
        }
    });
}
function reload_cart(callBack){
	$.ajax({
	    method: "POST",
	    url: appHelper.baseURL + "/carts/viewcart",
	    data: {type:""},
	    success:function(html){
            $(".cartbox").html(html);
            setTimeout(function(){
                $(".rerotate .main_total").text($(".cartbox_price .main_total").text());
                $(".cart-total").text($("#main_order_qty").val());
                $("#cart-note").text($(".order_notes").text());
            },500);
            var h = $(window).height();
            var bot = h-42;
            $("#cartbox").css("height",bot+"px");
            if($('.cartbox_item').length){
                var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
                setTimeout(function(){
                    cartbox.refresh();
                    cartbox.scrollTo(0,0);
                }, 0);
            }
            $("#cash_tend").val('');
            $(".change_due").html('0.00');
	    	callBack(html);
	    }
	});
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + "/carts/small-cart",
        success:function(html){
            $("#footer_cart").html(html);
        }
    });
}
var inprogress_save_order = 0;
function swith_cart(html,type){
    clearTimeout(timeclock);
    var h = $(window).height();
    var bot = h-42;
    //Save order
    if(inprogress_save_order == 0 && type==3){
        save_order();
        $(".cart_tool").css("display","none");
    //open cart
    }else if($('#view_cart').attr('rel')=='off' || type==2){
        $("#footer").stop().css("z-index",1001).animate({bottom: bot+"px"},"fast");
        $("#footer_cart").css("height",0);
        $("#footer_cart_detail").css("z-index",1000).animate({height: bot+"px"},"fast","linear",function(){
            $("#view_cart").attr('rel','on');
            $("#view_cart").html("Hide cart");
            if(type==2){
                bot = bot-135;
                // $(".customer_info").css("display","block").css("height","135px");
                $(".cash_info").css("display","block");
                $(".resetrote").html("Save order");
                if($("#cash_tend").val()=='0.00')
                    $("#cash_tend").val('');
                $("#cash_tend").focus();
                $(".cart_tool").css("display","none");
            }else{
                $(".cart_tool").css("height","0px").css("display","block").animate({ height: '45px'});
            }
            $("#cartbox").css("height",bot+"px");
            if($('.cartbox_item').length){
                var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
                setTimeout(function(){
                    cartbox.refresh();
                    cartbox.scrollTo(0,0);
                }, 0);
            }

        });

    }else{
        $("#footer").stop().css("z-index",2).animate({bottom:"0px"},"fast");
        $("#footer_cart_detail").css("z-index",1).animate({height: "0px"},"fast","linear",function(){
            $("#view_cart").attr('rel','off');
            $("#view_cart").html("View cart");
        });
        $(".cash_info").css("display","none");
        $(".resetrote").html("Place order");
        var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
        cartbox.destroy();
    }
}
    
function save_order_old(completed){
    var soStatus = 'New';
    var orderType = $("#order_type").val();
    var Paidby = $("#Paidby").val();
    if(completed==undefined)
        soStatus = 'In progress';
    else if(completed=='In production'){
        soStatus = 'In production';
    }
    if(orderType==1){
        soStatus = 'In production';
        Paidby = 'On Account';
    }

    inprogress_save_order = 1;
    $.ajax({
        method: "POST",
        url: appHelper.baseURL +"/orders/create-order",
        data: {
            Paidby:Paidby,
            cash_tend:$("#cash_tend").val(),
            emailInfo : $("#emailInfo").val(),
            nameInfo : $("#nameInfo").val(),
            orderType : orderType,
            soStatus : soStatus
        },
        success:function(dd){
            // reload_cart(function(html){});
            $("#footer").stop().css("z-index",2).animate({bottom:"0px"},"fast");
            $("#footer_cart_detail").css("z-index",1).animate({height: "0px"},"fast","linear",function(){
                $("#view_cart").attr('rel','off');
                $("#view_cart").html("View cart");
            });
            $(".customer_info").css("display","none").css("height","0px");
            $(".resetrote").html("Place order");
            $(".cart-total").html("0");
            $("#main_order_qty").val(0);            
            $(".money").html("$0.00");
            $(".main_total").html("$0.00");
            $("#cart-note").val('');
            $("#footer_cart").html("");
            $("#cash_tend").val('');
            $(".change_due").html('0.00');
            inprogress_save_order = 0;
            if(orderType==1){
                setTimeout(function(){
                    window.location.assign(appHelper.baseURL + "/orders/adjustment_order");
                },500);
            }
            // var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
            // cartbox.destroy();
        }
    });
}
function save_order(completed,callBack){
    var soStatus = 'New';
    var orderType = $("#order_type").val();

    var sumtotal =  UnFortmatPrice($(".rerotate .main_total").text());
    var time_delivery = $("#time_delivery").val();
    var Paidby = new Array();    
    $('.input_amount_tender').each(function(){
        if($(this).val() > 0)
        {
            Paidby.push($(this).attr('data-payment-method'));
        }
    });
    var account_and_pay =0;
    if(completed==undefined)
        soStatus = 'In progress';
    else if(completed=='In production'){
        soStatus = 'In production';
    }else if(completed=='save_on_account_and_pay'){
        soStatus = 'In production';
        account_and_pay = 1;
        Paidby.push("On Account");
    }
    if(orderType==1){
        soStatus = 'In production';
    }
    inprogress_save_order = 1;
    // var customer_info = {};

    // customer_info['cs_fullname']    = $("#cs_fullname").val();
    // customer_info['cs_phone']       = $("#cs_phone").val();
    // customer_info['cs_email']       = $("#cs_email").val();
    // customer_info['cs_company']     = $("#cs_company").val();
    // customer_info['cs_address_1']   = $("#cs_address_1").val();
    // customer_info['cs_address_2']   = $("#cs_address_2").val();
    // customer_info['cs_address_3']   = $("#cs_address_3").val();
    // customer_info['cs_town_city']   = $("#cs_town_city").val();
    // customer_info['cs_province_state'] = $("#cs_province_state").val();
    // customer_info['cs_province_state_id'] = $("#cs_province_state_id").val();
    // customer_info['cs_zip_postcode'] = $("#cs_zip_postcode").val();
    // customer_info['cs_country']     = $("#cs_country").val();
    // customer_info['cs_country_id']  = $("#cs_country_id").val();
    // console.log(customer_info);

    var province = $("#cs_province_state_id").val()+' '+$("#cs_province_state").val();
    var address = $("#cs_address_1").val();
    var address_2 = $("#cs_address_2").val();
    var address_3 = $("#cs_address_3").val();
    var town_city = $("#cs_town_city").val();
    var postal_code = $("#cs_zip_postcode").val();

    $.ajax({
        method: "POST",
        url: appHelper.baseURL +"/orders/create-order",
        data: {
            Paidby:Paidby,
            cash_tend:sumtotal,
            emailInfo : $("#cs_email").val(),
            nameInfo : $("#cs_fullname").val(),
            phone : $("#cs_phone").val(),
            orderType : orderType,
            soStatus : soStatus,
            time_delivery:time_delivery,
            province:province,
            address:address,
            address_2:address_2,
            address_3:address_3,
            town_city:town_city,
            postal_code:postal_code            
        },
        success:function(dd){
            // reload_cart(function(html){});
            $("#footer").stop().css("z-index",2).animate({bottom:"0px"},"fast");
            $("#footer_cart_detail").css("z-index",1).animate({height: "0px"},"fast","linear",function(){
                $("#view_cart").attr('rel','off');
                $("#view_cart").html("View cart");
            });
            $(".customer_info").css("display","none").css("height","0px");
            $(".resetrote").html("Place order");
            $(".cart-total").html("0");
            $("#main_order_qty").val(0);            
            $(".money").html("$0.00");
            $(".main_total").html("$0.00");
            $("#cart-note").val('');
            $("#footer_cart").html("");
            //$("#cash_tend").val('');
            //$(".change_due").html('0.00');

            //Hide payment popup
            $('#payment-calculation-modal').modal('hide');    

            inprogress_save_order = 0;
            if(orderType==1){
                setTimeout(function(){
                    window.location.assign(appHelper.baseURL + "/orders/adjustment_order");
                },500);
            }
            if(orderType==2 && account_and_pay==0){
                setTimeout(function(){
                    window.location.assign(appHelper.baseURL + "/orders/account_order");
                },500);
            }

            if(callBack!=undefined){
                setTimeout(function(){
                    callBack(dd.order_id);
                },500);
            }
            // var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
            // cartbox.destroy();
        }
    });
}

function edit_order(order_id){
    var soStatus = 'In progress';
    inprogress_save_order = 1;
    $.ajax({
        method: "POST",
        url: appHelper.baseURL +"/orders/edit-order",
        data: {
            Paidby:[$("#Paidby").val()],
            cash_tend:$("#cash_tend").val(),
            emailInfo : $("#emailInfo").val(),
            nameInfo : $("#nameInfo").val(),
            soStatus : soStatus,
            orderId : order_id
        },
        success:function(html){
            $(".cartbox").html(html);
            var h = $(window).height();
            var bot = h-42;
            $("#cartbox").css("height",bot+"px");
            if($('.cartbox_item').length){
                var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
                setTimeout(function(){
                    cartbox.refresh();
                    cartbox.scrollTo(0,0);
                }, 0);
            }
            //checkout
            // $(".cash_info").css("display","block");
            // $(".resetrote").html("Save order");
            // if($("#cash_tend").val()=='0.00')
            //     $("#cash_tend").val('');
            // $("#cash_tend").focus();
            // $(".cart_tool").css("display","none");
            $(".back_to_cart").css("display","none");
            $(".order_list_bt").css("display","inline-block");
            //footer info
            setTimeout(function(){
                $(".rerotate .main_total").text($(".cartbox_price .main_total").text());
                $(".cart-total").text($("#main_order_qty").val());
                $("#cart-note").text($(".order_notes").text());
            },500);
            
            inprogress_save_order = 0;
        }
    });
}
function load_saved_orders(){
    $.ajax({
        url: appHelper.baseURL + '/orders/saved-orders',
        type: 'POST',
        data: {
            note: 'test'
        },
        success: function (html){
            $(".cartbox").html(html);
            var h = $(window).height();
            var bot = h-42;
            $("#cartbox").css("height",bot+"px");
            $(".back_to_cart").css("display","inline-block");
            $(".order_list_bt").css("display","none");
            if($('.cartbox_item').length){
                var cartbox = new IScroll('#cartbox',{snap: '.cartbox_item'});
                setTimeout(function(){
                    cartbox.refresh();
                    cartbox.scrollTo(0,0);
                }, 0);
            }
        }
    })
}
function delete_order(order_id,qt){
    if(qt==undefined)
        qt = 1;
    check_permission(function(){
        $.ajax({
            method: "POST",
            url: appHelper.baseURL + "/orders/delete-order",
            data: {
                orderId : order_id,
                qt : qt
            },
            success:function(html){
                $("#cartbox_"+order_id).animate({height:"0px"}).remove();
                if(qt==0){
                    location.reload();
                }
            }
        });
    });
}
function check_permission(yesBack){
    var hashkey = $(".hashkey").val();
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + "/user/check-permission",
        data: {
            hashkey : hashkey
        },
        success:function(result){
            if(result=='ok'){
                confirm_h("Confirm deleting",'Do you want delete this item ?',"default",yesBack, function(){});
            }else{
                confirm_h("Input password",'<input type="password" value="" class="pass_input focusConfirm" id="del_pass_input" />',"default",function(){check_pass(yesBack);}, function(){});
            }
        }
    });
}
function check_pass(yesBack){
    var hashkey = $("#del_pass_input").val();
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + "/user/check-pass",
        data: {
            hashkey : hashkey
        },
        success:function(result){
            if(result=='ok'){
                yesBack();
            }else{
                confirm_h("Input password",'<p class="red_color">Wrong password. Please check again</p><input type="password" value="" class="pass_input focusConfirm" id="del_pass_input" />',"default",function(){check_pass(yesBack);}, function(){});
            }
        }
    });
}

function editCart(id,product_id){
    var data = {};
    var item = "#cartbox_" + id;
    data.id = product_id;
    data.name = $(item + " .cartbox_name h3").html();
    var str = $(item + " .sell_price").html();
    data.price = parseFloat(str.replace("$", ''));
    data.image = $(item + " .cartbox_img img").attr('src');
    data.quantity = $("#fc_"+id).val();
    data.description = $(item + " .description_item").html();

    $('.popup_qty').val(data.quantity);
    $(".popup_content .item_id").val(data.id);
    $(".popup_content .popup_title h2").html(data.name);
    $(".popup_content .popup_title .description_item").html(data.description);
    $(".popup_price").html("$" + data.price);
    $("#sell_price_popup_qty_main").val(data.price);
    $(".popup_content .popup_image img").attr("src", data.image).load();
    $(".popup_item").css("display", "block");
    $(".popup_add_cart").css("display", "none");
    $(".popup_update_cart").css("display", "block");
    $('.op_description').html('');
    addNoteProduct();
    $('.note_product').val($('#note_product_'+id).text()); console.log($('#note_product_'+id).text());
    $(item + " .cartbox_name p").each(function(){
        $('.op_description').append('<p id="od_'+$(this).data('idp')+'">'+$(this).html()+'</p>');
    });

    $.ajax({
        method: "POST",
        url: appHelper.baseURL + '/products/opcart/' + id,
        success:function(html){
            $(".tabs").html(html);
            calPrice();
            $(".popup_option").height(328);
            if($(".popup_option .option_item").length){
                var popup_option = new IScroll('.popup_option', {
                    snap: '.option_item'
                });
                setTimeout(function(){
                    popup_option.refresh();
                    popup_option.scrollTo(0,0);
                }, 0);
            }
        }
    });
}

function optionLevel(n){
    $('.level_box').css("display","none");
    $('.level_'+n).css("display","block");
}
function updateSessionPayment(){
    var total = 0.00;
    var Paidby = {};
    $('.input_amount_tender').each(function(){
        if($.isNumeric($(this).val()))
        {
            total += parseFloat($(this).val());
            if($(this).val() > 0)
                Paidby[$(this).attr('data-payment-method')] = $(this).val();
        }
    });

    if($("#payment_for_order").val()==''){
        $.ajax({
            method: "POST",
            data:{total:total,Paidby:Paidby},
            url: appHelper.baseURL + '/carts/update-sepay/',
            success:function(html){
                
            }
        });
    }
    
    $.ajax({
        method: "GET",
        url: appHelper.baseURL + '/carts/get-current-time',
        success:function(result){
            // console.log(result); 
            //Set time to 15 min after  
            var date_after_format = getDatetimeFormated(result.datetime, 15);
            // console.log('time_after:'+time_after);          
            $('#time_delivery').val(date_after_format);
        }
    });
}
function getDatetimeFormated(datetime, extra_minutes)
{
    var todayDate=new Date(datetime);
    todayDate.setMinutes(todayDate.getMinutes() + extra_minutes);
    var format ="AM";
    var hour=todayDate.getHours();
    var min=todayDate.getMinutes();
    if(hour>11){format="PM";}
    if (hour   > 12) { hour = hour - 12; }
    if (hour   == 0) { hour = 00; }  
    if (min < 10){min = "0" + min;}
    var date_format = todayDate.getMonth()+1 + "/" + todayDate.getDate() + "/" +  todayDate.getFullYear()+" "+hour+":"+min+" "+format;
    return date_format;

}
function get_total_amount_tender(){
    var total = 0.00;
    $('.input_amount_tender').each(function(){
        if($.isNumeric($(this).val()))
        {
            total += parseFloat($(this).val());
        }
    });
    return total;
}
function setAmountTenderToAll(obj)
{
    var total = parseFloat($('.rerotate .main_total').text().replace('$',''));
    var input_amount_tender = $(obj).parent().parent().find('.input_amount_tender');

    //set all to zero
    $('.input_amount_tender').each(function(){
        if($(this).attr('data-payment-method') != 'Coupon code'){
            $(this).val(FortmatPrice(0.00));
            $(this).trigger('keyup');
        }
    });

    if(input_amount_tender.length > 0){
        input_amount_tender.val(FortmatPrice(total));
        input_amount_tender.trigger('keyup');
    }
    updateSessionPayment();
}
function updatePaymentMethodCredit(str){
    $(".credit_cart_value").attr("data-payment-method",str);
    updateSessionPayment();
}

function finalize_old(){
    var payment =  $("#cash_tend").val();
    var sumtotal =  UnFortmatPrice($(".rerotate .main_total").text());
    var pm = $("#Paidby").val();
    if(pm!="Cash"){
        $('#print-modal').modal('toggle');
        confirm_h('Message','<div class="noteja">Customers will pickup and pay later. Do you want to save this line?</div>','default',function(){
            save_order('In production');
        },function(){});

    }else if(payment>=sumtotal){
        $('#print-modal').modal('toggle');
        var money_rt = FortmatPrice(payment - sumtotal);
        confirm_h('Cash in return','<div class="noteja">Pay money balance to customer: $<b>'+money_rt+'</b></div>','default',function(){
            save_order('In production');
        },null);
    }else{
        alertB('<div class="noteja">The payment is not enough.</div>');
    }    
}
function finalizeCheck(){
    // alert();
    var on_account_amount = parseFloat($("#tender_onaccount").val());
    if(on_account_amount>0){ 
        $('#choice-customer').modal('toggle');
    }else{
        finalize();
    }
}
function finalize(type){
    var change_due =  UnFortmatPrice($("#change_due").text());
    var Paidby = new Array();    
    $('.input_amount_tender').each(function(){
        if($(this).val() > 0){
            Paidby.push($(this).attr('data-payment-method'));
        }
    });
   
    var change_due = parseFloat($('#change_due').text().replace('$',''));
    var note =  '<div class="noteja">Pay money balance to customer: $<b>'+change_due+'</b></div>';
    var title_note = 'Cash in return';
    if(type != undefined && type == 1){ 

        fullname = $("#cs_fullname").val();
        phone = $("#cs_phone").val();
        if(fullname.trim() == '' || phone.trim() == '') {
            alertB('<div class="noteja">Please input at least name and phone.</div>');
            return;
        }

        var note =  '<div class="noteja">Save this order with the customer infomation?</div>';
        title_note = 'Save and print order';
    }

    if(change_due >= 0 && $("#payment_for_order").val()!=''){
        var order_id = $("#payment_for_order").val();
        var completed = $("#payment_completed").val();
        var time_delivery = $("#time_delivery").val();
        
        confirm_h(title_note,note,'default',function(){
            setTimeout(function(){
                $.ajax({
                    url: appHelper.baseURL + '/orders/pay_account_order',
                    type: 'POST',
                    data:{
                        change_due:change_due, order_id:order_id, Paidby:Paidby,completed:completed
                    },
                    success: function(result) {
                        $("#priting_iframe").attr("src",appHelper.baseURL+'/print-order/'+order_id);
                        $('#choice-customer').modal('hide');
                        setTimeout(function(){
                            $("priting_iframe").load();
                            $('#print-modal').modal('toggle');

                        },500);
                        $('#payment-calculation-modal').modal('toggle');
                        $("#payment_for_order").val('');
                        $("#payment_completed").val('');
                        $.ajax({
                            url: appHelper.baseURL + '/orders/account-order',
                            type: 'GET',
                            success: function(html){
                                $("#account_order").remove();
                                $(".main-content").append(html);
                            }
                        });

                    }
                });
             }, 1000);                
        },null);
    
    }else if(change_due >= 0){   
        confirm_h(title_note,note,'default',function(){
            setTimeout(function(){
                var action = 'In production';
                if($.inArray('On Account', Paidby) != -1)
                {
                    action = 'save_on_account_and_pay';
                }
                save_order(action,function(order_id){
                    $("#priting_iframe").attr("src",appHelper.baseURL+'/print-order/'+order_id);
                    $('#choice-customer').modal('hide');
                    setTimeout(function(){
                        $("priting_iframe").load();
                        $('#print-modal').modal('toggle');
                    },500);
                    $.ajax({
                        url: appHelper.baseURL + '/orders/account-order',
                        type: 'GET',
                        success: function(html){
                            $("#account_order").remove();
                            $(".main-content").append(html);
                        }
                    });                    
                });
            }, 500);                
        },null);
               
    }else{
        alertB('<div class="noteja">The payment is not enough.</div>');
    }    
}

function changeTax(){
    $.ajax({
        method: "POST",
        url: appHelper.baseURL + '/carts/change-tax/',
        data:{'taxper':$(".tax_select").val()},
        success:function(result){
            if (result.error == 0) {
                $('.main_total').text("$"+result.cart.main_total);
                reload_cart(function(html){});
            } else if (result.error == 1) {
                // console.log(result.message);
            }
        }
    });
}

function clear_cart(){

    confirm_h('Confirm clear cart','<div class="noteja">Proceed with clearing cart? </div>','default',function(){
        confirm_h('Confirm save order','<div class="noteja">Do you want save this order ? </div>','default',function(){
            $.ajax({
                url: appHelper.baseURL + '/carts/clear-cart',
                type: 'POST',
                data:{
                    Paidby:$("#Paidby").val(),
                    cash_tend:$("#cash_tend").val(),
                    emailInfo : $("#emailInfo").val(),
                    nameInfo : $("#nameInfo").val(),
                    soStatus : 'In progress',
                    save_order:"yes",
                    orderType:$("#order_type").val()
                },
                success: function(result) {
                    reload_cart(function(){});
                }
            });
        },function(){
            $.ajax({
                url: appHelper.baseURL + '/carts/clear-cart',
                type: 'POST',
                data:{'save_order':"no"},
                success: function(result) {
                    reload_cart(function(){});
                }
            });
        });        
    },function(){
        
    });    
}
function sendproduct_account_order(order_id){
    var total = -1*parseFloat($('#orderbox_'+order_id+' .order_cartbox_price').text().replace('$',''));
    confirm_h('Confirm send products','<div class="noteja">Sent all products to clients and completed all station?</div>','default',function(){
        $.ajax({
            url: appHelper.baseURL + '/orders/sendproduct_account_order',
            type: 'POST',
            data:{
                order_id : order_id
            },
            success: function(result) {
                $("#orderbox_"+order_id).remove();
            }
        });
    },function(){
         
    });
}
function pay_account_order(order_id){
    var total = -1*parseFloat($('#orderbox_'+order_id+' .order_cartbox_price').text().replace('$',''));
    confirm_h('Confirm pay order','<div class="noteja">Payment: <input type="text" value="" class="payment_input focusConfirm" id="payment_input" data-orderid="'+order_id+'" /><p>Repay:<span class="repay_ao">'+total+'</span></p></div>','default',function(){
        var repay_ao = parseFloat($(".repay_ao").text());
        if(repay_ao>=0){
            $.ajax({
                url: appHelper.baseURL + '/orders/pay_account_order',
                type: 'POST',
                data:{
                    cash_tend:$("#payment_input").val(),
                    order_id : order_id
                },
                success: function(result) {
                    $("#orderbox_"+order_id).remove();
                }
            })
        }else{
             alertB('<div class="noteja">The payment is not enough.</div>');
        }
    },function(){
         
    });
}
function pay_account_order_with_method(order_id,completed){
    $("#payment_for_order").val(order_id);
    $("#payment_completed").val(completed);
    var total = $("#orderbox_"+order_id+" .order_cartbox_price").attr("rel");
    $("#total_owning").text(total);
    $('.rerotate .main_total').text(total);
    $('#payment-calculation-modal').modal('toggle');
    $("#on_account_line").css("display","none");    
}

function adjustment_order(order_id){
    $.ajax({
        url: appHelper.baseURL + '/orders/edit_adjustment_order',
        type: 'POST',
        data:{
            cash_tend:$("#payment_input").val(),
            orderId : order_id
        },
        success: function(html) {
            $("#footer").css("display","block");
            reload_cart(function(html){
                swith_cart(html,1);
            });
        }
    });        
}

function reprocess_order_now(order_id){
    confirm_h("Reprocess order",'All products of order will not be completed. Do you want to reprocess this order?',"default",function(){
        $.ajax({
            url: appHelper.baseURL + '/orders/reprocess_order_now',
            type: 'POST',
            data:{
                cash_tend:$("#payment_input").val(),
                orderId : order_id
            },
            success: function(html) {
                window.location.reload();            
            }
        });
    },function(){});
          
}

function view_order_history_detail(order_id){
    $.ajax({
        url: appHelper.baseURL + '/orders/view_order_history_detail',
        type: 'POST',
        data:{
            cash_tend:$("#payment_input").val(),
            orderId : order_id
        },
        success: function(html) {
            $(".order_detail_box").html(html);
            $('#order_detail').modal('toggle');
        }
    });
}

function applyCoupon(reload){
    var coupon = $("#coupon_code").val();
    $.ajax({
        url: appHelper.baseURL + '/carts/apply-coupon',
        type: 'POST',
        data:{
            coupon:coupon,
        },
        success: function(data) {
            var t = data.message;
            $("#discount_value").html(data.voucher_value);
            if(data.new_main_total!=undefined)
                $(".rerotate .main_total").text('$'+data.new_main_total);
            $("#total_owning").text(data.new_main_total);
            $("#discount_value_input").val(data.voucher_value);
            if(t.indexOf("Discount")==0){
                $("#coupon_value").text(data.message);
                $("#error_coupon").text('');
                if(reload==1)
                    reload_payment_calculation_box();
                else{
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
                    $('.bt_apply_voucher').html('<button type="button" class="btn btn-default btn-removecoupon" onclick="removeCoupon()">Clear</button>');
                    $('#coupon_code').attr("disabled","disabled");
                }
            }
            else{
                $("#error_coupon").text(data.message);
                $("#coupon_value").text('');
            }
            $("#error_coupon").show();
        }
    });
}
function  removeCoupon(){
    $.ajax({
        url: appHelper.baseURL + '/carts/remove-coupon',
        type: 'POST',
        success: function(data){
            if(data.new_main_total!=undefined)
                $(".rerotate .main_total").text('$'+data.new_main_total);
            reload_payment_calculation_box();
        }
    });
}

function applyPromo(reload){
    var promo = $("#promo_code").val();
    if(promo=='101'){
        var free_qty = $("#free_item_qty").val();
        $("#free_total_span").html(""+free_qty);
        $('#cart-list-free').modal('toggle');
        $('.free_qty').val(0);

    }
    else
    $.ajax({
        url: appHelper.baseURL + '/carts/apply-promo',
        type: 'POST',
        data:{
            promo:promo,
        },
        success: function(data) {
            var t = data.message;
            $("#discount_value").html(data.voucher_value);
            if(data.new_main_total!=undefined)
                $(".rerotate .main_total").text('$'+data.new_main_total);
            $("#total_owning").text(data.new_main_total);
            $("#discount_value_input").val(data.voucher_value);
            if(t.indexOf("Total")==0){
                $("#promo_value").text(data.message);
                $("#error_promo").text('');
                if(reload==1)
                    reload_payment_calculation_box();
                else{
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
                    $('.bt_apply_promo').html('<button type="button" class="btn btn-default btn-removepromo" onclick="removePromo()">Clear</button>');
                    $('#promo_code').attr("disabled","disabled");
                }
            }
            else{
                $("#error_promo").text(data.message);
                $("#promo_value").text('');
            }
            $("#error_promo").show();
        }
    });
}
function  removePromo(){
    $.ajax({
        url: appHelper.baseURL + '/carts/remove-promo',
        type: 'POST',
        success: function(data){
            if(data.new_main_total!=undefined)
                $(".rerotate .main_total").text('$'+data.new_main_total);
            reload_payment_calculation_box();
        }
    });
}
function  popup_list(typemodel){
    $.ajax({
        url: appHelper.baseURL + '/orders/popup-list',
        type: 'POST',
        data:{
            typemodel:typemodel,
        },
        success: function(data){
            $('#select-contact').modal('toggle');
            $('.contact_list_box').html(data.table_html);
            var h = $(window).height();
            var bot = h-100;
            $(".contact_list_box").css("height",bot+"px");
            $(".contact_list_box").css("overflow-y","auto");
            $(".contact_list_box").css("overflow-x","hidden");
            // $(".contact_list_box").height(bot);
            // if($(".contact_list_box").length){
            //     var popup_scroll = new IScroll('.contact_list_box', {
            //         snap: '.contact_list_items'
            //     });
            //     setTimeout(function(){
            //         popup_scroll.refresh();
            //         popup_scroll.scrollTo(0,0);
            //     }, 0);
            // }
            reformat_datatable();
        }
    });
}
function reformat_datatable() {
    // Setup - add a text input to each footer cell
    $('#table_contact thead tr[id="search_field"] th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#table_contact').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
}
function choice_contacts(ids){
    var data_json = JSON.parse($("#after_choose_contact_"+ids).val());
    $("#cs_fullname").val(data_json.first_name+' '+data_json.last_name);
    $("#cs_contact_id").val(ids);
    $("#cs_phone").val(data_json.direct_dial);
    $("#cs_email").val(data_json.email);
    $("#cs_company").val(data_json.code);
    //address
    var address = data_json.addresses[0];
    $("#cs_address_1").val(address.address_1);
    $("#cs_address_2").val(address.address_2);
    $("#cs_address_3").val(address.address_3);
    $("#cs_town_city").val(address.town_city);
    $("#cs_province_state_id").val(address.province_state_id);
    $("#cs_province_state").val(address.province_state);
    $("#cs_country_id").val(address.country_id);
    $("#cs_country").val(address.country);
    $("#cs_zip_postcode").val(address.zip_postcode);

    $('#select-contact').modal('hide');    
}


function calFreeQty(type,ids){
    var total = parseInt($("#free_total_span").text());
    var other_id = '5681c266124dcae13ab42c3e';
    var idqty = $("#free_item_"+ids).val();
    if(total==0)
        console.log("nothing");
    else if(type=='down' && idqty!=0)
        idqty--;
    else if(type=='up' && idqty<total)
        idqty++;
    else
        console.log("Nothing");

    //gan gia tri moi cho id dang xet
    $("#free_item_"+ids).val(idqty);
    //xac dinh id khac neu la pop
    if(ids=='5681c266124dcae13ab42c3e')
        other_id = '5716ff49124dcada0530dc80';
    //gan cac gia tri cho cac id khac
    $("#free_item_"+other_id).val(total-idqty);

    var free_item_list = {};
    if(idqty!=0)
        free_item_list[""+ids] = idqty;
    if(total-idqty!=0)
        free_item_list[""+other_id] = total-idqty;
    console.log(free_item_list);
    var promo_code = $("#promo_code").val();

    //save code va tao item moi
    $.ajax({
        url: appHelper.baseURL + '/carts/add_free_items',
        type: 'POST',
        data:{
            promo_code:promo_code,
            free_item_list:free_item_list
        },
        success: function(data){
            // $('#cart-list-free').modal('hide');
            reload_payment_calculation_box();
        }
    });

}

