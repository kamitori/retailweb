$(document).bind('keydown', function(e) {
    if(e.which === 116) {
       // parkOrder();
       // return false;
    }
    if(e.which === 117) {
       showDiv('select-sale')
       return false;
    }
    if(e.which === 118) {
       showDiv('close-register')
       return false;
    }
    if(e.which === 82 && e.ctrlKey) {
       console.log('blocked');
    }
});
function VoidOrder(){
	errorAlert(
		'Are you sure ?'
		,'<div id="sl-void-sale-warning" class="lightbox lightbox-content warning-lightbox warning-lightbox-content lightbox--warning"><div class="lightbox-body"><h2 class="lightbox-body-subheading">Are you sure you want to void this sale?</h2><p class="lightbox-body-paragraph">All products and payments will be removed from the current sale. Voided sale information is saved in the sales history.</p></div><div class="lightbox-button-bar"><button type="button" id="sl-void-sale" value="Void Sale" class="btn btn--danger btn--well" onclick="reloadPage()">Void Sale</button><button type="button" onclick="closePopup()" value="Cancel" class="btn btn--well">Cancel</button></div></div>'
	);
}
function _updateQuantity(_currentstr,_check){
	var _div_quantity = $("#"+_check).find('div.quantity span.badge');
	_div_quantity.text(_currentstr);
	_check = _check.replace(/\-/g,' ');	
	CallAjax(_check,'createOrder',$("#currentOrderId").val(),_currentstr);
	closePopup();
}
function discountOrder(_currentstr){
	if(_currentstr.indexOf("%") >= 0){
		var _hiddensubtotal = parseFloat($("#hiddensubtotal").val());
		var _arr = _currentstr.split('%');
		_arr = parseFloat(_arr[0]);
		_price = (_hiddensubtotal*_arr)/100.0;
	}else{
		_price = parseFloat(_currentstr);
	}
	CallAjax(_price,'createDiscount',$("#currentOrderId").val());
	addItems(_price,'Discount',-1);
	closePopup();
}
function caculatorClick(_str,_check){
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
		}else if(_str=='enter'){
			if(_check){
				if(isNaN(_currentstr) || _currentstr=='' || _currentstr <=0 ) _currentstr = 1;
				_updateQuantity(_currentstr,_check);
			}else{
				discountOrder(_currentstr);
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
function _descrease_quantity(){
	$("#change-sale-quantity-input").val(parseInt($("#change-sale-quantity-input").val(),10) - 1);
}
function _increase_quantity(){
	$("#change-sale-quantity-input").val(parseInt($("#change-sale-quantity-input").val(),10) + 1);
}
function _editQuantity(_quantity,_id){
	alert('Quantity','<div id="change-sale-discount-popup" class="popup numpad-popup popup-align-top popup-visible"><div class="popup-content numpad-popup-content"><div class="popup-body"><form action="#" novalidate="" class="numpad-input-form numpad-input-open"><div  id="numpad_input" class="numpad"><div class="numpad-content"><div class="numpad-section numpad-section-main"><button type="button" value="1" class="numpad-key numpad-key-number" tabindex="0" onclick=caculatorClick("1",1)>1</button><button type="button" value="2" class="numpad-key numpad-key-number" tabindex="0" onclick=caculatorClick("2",2)>2</button><button type="button" value="3" onclick=caculatorClick("3",3) class="numpad-key numpad-key-number" tabindex="0">3</button><button type="button" value="4" onclick=caculatorClick("4",4) class="numpad-key numpad-key-number" tabindex="0">4</button><button type="button" value="5" onclick=caculatorClick("5",5) class="numpad-key numpad-key-number" tabindex="0">5</button><button type="button" value="6" onclick=caculatorClick("6",6) class="numpad-key numpad-key-number" tabindex="0">6</button><button type="button" value="7" onclick=caculatorClick("7",7) class="numpad-key numpad-key-number" tabindex="0">7</button><button type="button" value="8" onclick=caculatorClick("8",8) class="numpad-key numpad-key-number" tabindex="0">8</button><button type="button" value="9" onclick=caculatorClick("9",9) class="numpad-key numpad-key-number" tabindex="0">9</button><button type="button" value="0" onclick=caculatorClick("0",1) class="numpad-key numpad-key-number" tabindex="0">0</button><button type="button" onclick=caculatorClick("00",1) value="00" class="numpad-key numpad-key-double-zero" tabindex="0">00</button><button type="button" onclick=caculatorClick(".",1) value="." class="numpad-key numpad-key-decimal" tabindex="0">.</button></div><div class="numpad-section numpad-section-last"><button type="button" value="delete" onclick=caculatorClick("del",1) class="numpad-key numpad-key-delete" tabindex="0"><i class="numpad-icon-delete"></i></button><button type="button" value="+/-" onclick=caculatorClick("+/-",1) class="numpad-key numpad-key-percent" tabindex="0">+/-</button><button type="button" onclick=caculatorClick("enter","'+_id+'") value="return" class="numpad-key numpad-key-return" tabindex="0"><span>return</span></button></div></div></div><div class="numpad-input"><label class="numpad-input-label" for="change-sale-discount-input">Quantity</label><div class="numpad-input-content"><button type="button" value="toggle" class="numpad-input-button-toggle" title="Close Number Pad"><i onclick="hideCaculator()" class="numpad-icon-toggle"></i></button><input id="change-sale-quantity-input" type="text" class="numpad-input-input " placeholder="'+_quantity+'" title="'+_quantity+'" value="'+_quantity+'"><div class="numpad-input-spinner"><button onclick="_descrease_quantity();" type="button" value="decrease" class="numpad-input-button-decrease numpad-input-button-spinner"><i class="icon-minus-dark"></i></button><button onclick="_increase_quantity();" type="button" value="increase" class="numpad-input-button-increase numpad-input-button-spinner"><i class="icon-plus-dark"></i></button></div><div class="validation-bubble"></div></div></div></form></div></div><div class="popup-beak" style="position: relative; top: 0px; left: 141.984px;"></div></div>'
		,{
			'width':'344px',
			'height':'450px'
		});
}
function payorder(){
	CallAjax($("#currentOrderId").val(),'SaleOrders');
	window.print();
}
function clickToOpen(_id){
	confirm(
		'Action confirm'
		,'You are about to delete the current order and load another order. Are you sure?'
		,'red'
		,function(){
			$.ajax({
				url	: '/pos/orders/loadOrder',
				type	:'POST',
				data	:	{ txt_name:_id
				},
		    	cache: false,
				beforeSend: function(){
				},
				success: function(data, type){
					$("#item-checkout").html(data.datas);
					$("#hiddensubtotal").val(data.total);
					parent.$("#sub-total").html(data.sub_price);
					parent.$("#sub-tax").html(data.sub_tax);
					parent.$("#_total").html(data.to_pay);
					parent.$("#to-pay").html(data.to_pay);
					parent.$("#sale-order-description").html(data.description);
					parent.$("#currentOrderId").val(data._id);
					showDiv('contentCash');
				}
			});
		}
	);	
}
function _AddCustomer(){
	var _datas = [];
	var _check_required = false;
	$('.quickDataCustomer').each(function(){		
		var _id = $(this).attr('id');
		if($(this).attr('required') && $(this).val()==''){
			$(this).addClass('border-alert');
			$(this).attr('placeholder',$(this).attr('placeholder')+' is required');
			_check_required = true;
			return false;
		}else{
			$(this).removeClass('border-alert');
		}		
		_datas.push({
			_val : $(this).val(),
			_id : _id
		});
	});
	if(_check_required){
		return false;
	}
	_datas.push({
		'_val': $('input[name=gender]:checked').val(),
		'_id' : 'gender'
	});
	var _json = JSON.stringify(_datas);	
	CallAjax(_datas,'AddCustomer',$("#currentOrderId").val());
	closePopup();
}
function _AddNewCustomer(){	
	alert('Customer','<div id="ajax-popup-content" style="width:655px;">	<div class="box">            <div class="head box-gradient-modal text-center">                <h2 class="text">Quick Add a New Customer</h2>            </div>            <div class="content"><form method="post"> <div> <h3 id="_message_" style="color:red;text-align:center;"></h3></div>       <div class="size1of2 unit-boxed unit form-no-labels prm">    <h5 class="strong mbm font-large">Customer details</h5>    <div id="customer-contact-name" class="input-row line">        <div class="unit line">            <input required type="text" name="first_name" placeholder="First" class="quickDataCustomer split unit" id="first_name">            <input required type="text" name="last_name" placeholder="Last" class="split unit quickDataCustomer" id="last_name">        </div>    </div>       <div class="input-row line ">        <div class="unit field fullwidth">            <input required type="text" name="phone" placeholder="Phone" class="wide quickDataCustomer fullwidth" id="phone">        </div>    </div>    <div class="input-row line ">        <div class="unit field fullwidth">            <input required type="text" name="email" placeholder="Email" class="wide quickDataCustomer fullwidth" id="email">        </div>    </div>    <div id="customer-date-of-birth" class="input-row line">        <div class="unit" style="width:25%!important;line-height:30px">            <label class="man">Birthdate</label>        </div>        <div class="unit line" style="width:72%!important">            <input required type="text" name="dob_day" placeholder="DD" class="quickDataCustomer split day unit short-number" id="dob_day" style="width:20%!important">            <input required type="text" name="dob_month" placeholder="MM" class="quickDataCustomer split month unit" id="dob_month" style="width:20%!important">            <input required type="text" name="dob_year" placeholder="YYYY" class="quickDataCustomer split year unit" id="dob_year" style="width:58%!important;float:right">        </div>            </div>    <div class="input-row line fullwidth">        <div class="unit" style="width:25%;">            <label class="man">Gender</label>        </div>        <div class="unit line" style="width:72%;float:left">            <ul class="line radio_list" style="padding-left: 3px;"><li class="unit size1of2"><input name="gender" checked type="radio" value="1" id="gender_f">&nbsp;<label for="gender_f">Female</label></li><li class="unit size1of2"><input name="gender" type="radio" value="0" id="gender_m">&nbsp;<label for="gender_m">Male</label></li></ul>        </div>    </div>            <div class="input-row line">            <div class="unit field fullwidth">                <select name="customer_group_id" class="wide quickDataCustomer fullwidth" id="customer_group_id" style="height:30px"><option value="all-customer">All Customers</option></select>            </div>        </div>        </div><div class="size1of2 unit-boxed unit-right form-no-labels plm">    <h5 class="strong mbm font-large">Physical address</h5>    <div class="input-row line">        <div class="unit field fullwidth">            <input class="wide fullwidth quickDataCustomer" type="text" name="address1" required placeholder="Address" id="address1"></div>    </div>    <div class="input-row line">        <div class="unit field fullwidth">            <input class="wide fullwidth quickDataCustomer" type="text" name="address2" placeholder="Address" id="address2">        </div>    </div>    <div class="input-row line">        <div class="unit field fullwidth">            <input type="text" name="address3" required placeholder="Address" id="address3" class="fullwidth quickDataCustomer">        </div>    </div>    <div class="input-row line">        <div class="unit field" style="width:98% !important">            <input style="width:30% !important;float:left" class="split quickDataCustomer" type="text" name="poscode" required placeholder="Postcode" id="poscode">            <input style="width:65% !important;float:right" type="text" name="city" required placeholder="City" id="city" class="quickDataCustomer">            </div>    </div>       <div class="input-row line"><div class="unit field fullwidth"><input class="quickDataCustomer fullwidth" type="text" name="state" required placeholder="State" id="state">        </div></div><div class="input-row line" style="margin-top: 9px;"><div class="unit field fullwidth"><select name="country" id="country" class="quickDataCustomer fullwidth" style="height:30px"><option value="">Select a country</option><option value="CA" selected="selected">Canada</option></select>        </div></div></div><div class="clearer"></div><div class="modal-button-bar"><button style="color:#fff;background-image: -webkit-gradient(linear,50% 0,50% 100%,color-stop(0,#3da0f3),color-stop(100%,#006ad4)) !important;" type="button" onclick="_AddCustomer()" value="save" class="btn btn--primary btn--well btn-save modal-button">Save</button><a class="btn btn--well btn-cancel modal-button" onclick="closePopup()" href="javascrip:void(0)">Cancel</a></div></form></div></div></div>'
		,{
			'width':'700px',
			'height':'600px'
		});
}
function hideCaculator(){
	if($('.numpad').is(':visible')){		
		$('#numpad_input').toggle();
		$('.ja_default').css('height','240px');
	} 
	else{
		$('.ja_default').css('height','450px');
		$('#numpad_input').toggle();
	}
}
function showDiv(_name){
	$('.contentShow').hide();
	$('#'+_name).show(700);
}
function paginate(categoryName,page){
	if(page=='n'){
		page = parseInt($("#currentPage").val(),10) +1;
		maxPage = parseInt($("#maxPage").val(),10);
		if(page>maxPage) page = maxPage;
	}else if(page=='p'){
		page = parseInt($("#currentPage").val(),10) - 1;
		if(page<=0) page = 1;
	}
	page = parseInt(page,10);	
	var _link = 'index/redrawProductList';
	if(categoryName!='All') _link = 'pos/redrawProductList';
	$.ajax({
		url	: '/pos/'+_link,
		type	:'POST',
		data	:	{ 
			txt_name:categoryName,
			pageNumber : page
		},
    	cache: false,
		beforeSend: function(){			
		},
		success: function(data, type){
			$("#currentPage").val(page);
			$("#listproducts").html(data);
			$('.liCurrentActive').removeClass('active');
			$('.liCurrentActive').removeClass('liCurrentActive');
			$('#p'+(page+1)).addClass('liCurrentActive active')
		}
	});
}
function AddCaculator(){
	alert('Discount','<div id="change-sale-discount-popup" class="popup numpad-popup popup-align-top popup-visible"><div class="popup-content numpad-popup-content"><div class="popup-body"><form action="#" novalidate="" class="numpad-input-form numpad-input-open"><div id="numpad_input" class="numpad"><div class="numpad-content"><div class="numpad-section numpad-section-main"><button type="button" value="1" class="numpad-key numpad-key-number" tabindex="0" onclick=caculatorClick("1")>1</button><button type="button" value="2" class="numpad-key numpad-key-number" tabindex="0" onclick=caculatorClick("2")>2</button><button type="button" value="3" onclick=caculatorClick("3") class="numpad-key numpad-key-number" tabindex="0">3</button><button type="button" value="4" onclick=caculatorClick("4") class="numpad-key numpad-key-number" tabindex="0">4</button><button type="button" value="5" onclick=caculatorClick("5") class="numpad-key numpad-key-number" tabindex="0">5</button><button type="button" value="6" onclick=caculatorClick("6") class="numpad-key numpad-key-number" tabindex="0">6</button><button type="button" value="7" onclick=caculatorClick("7") class="numpad-key numpad-key-number" tabindex="0">7</button><button type="button" value="8" onclick=caculatorClick("8") class="numpad-key numpad-key-number" tabindex="0">8</button><button type="button" value="9" onclick=caculatorClick("9") class="numpad-key numpad-key-number" tabindex="0">9</button><button type="button" value="0" onclick=caculatorClick("0") class="numpad-key numpad-key-number" tabindex="0">0</button><button type="button" onclick="caculatorClick(00)" value="00" class="numpad-key numpad-key-double-zero" tabindex="0">00</button><button type="button" onclick=caculatorClick(".") value="." class="numpad-key numpad-key-decimal" tabindex="0">.</button></div><div class="numpad-section numpad-section-last"><button type="button" value="delete" onclick=caculatorClick("del") class="numpad-key numpad-key-delete" tabindex="0"><i class="numpad-icon-delete"></i></button><button type="button" value="%" onclick=caculatorClick("%") class="numpad-key numpad-key-percent" tabindex="0">%</button><button type="button" onclick=caculatorClick("enter") value="return" class="numpad-key numpad-key-return" tabindex="0"><span>return</span></button></div></div></div><div class="numpad-input"><label class="numpad-input-label" for="change-sale-discount-input">Discount (percentage or $ amount)</label><div class="numpad-input-content"><button type="button" value="toggle" class="numpad-input-button-toggle" title="Close Number Pad"><i onclick="hideCaculator()" class="numpad-icon-toggle"></i></button><input id="change-sale-discount-input" type="text" class="numpad-input-input " placeholder="E.g. 20% or 2.50" title="E.g. 20% or 2.50" pattern="[-]{0,1}([0-9]{0,}[.]{1}[0-9]{1,}|[0-9]{1,}[.]{0,1}[0-9]{0,})[%]{0,1}"><div class="validation-bubble"></div></div></div></form></div></div><div class="popup-beak" style="position: relative; top: 0px; left: 141.984px;"></div></div>'
		,{
			'width':'344px',
			'height':'450px'
		});
}
function NotesOrder(){
	infoAlert(
		'Add Notes!'
		,'<div id="sl-save-note-lightbox" class="lightbox lightbox-content form-lightbox form-lightbox-content lightbox--action"><div class="lightbox-header"></div><form class="lightbox-body"><div class="lightbox-form-row"><label for="sale-note-textareas">Note</label><textarea onchange="changeTextNotes(this.value)" id="sale-note-textareas" rows="4" cols="70" maxlength="2000" style="resize:vertical; width:442px;">'+$("#sale-order-description").val()+'</textarea></div><div class="lightbox-button-bar lightbox-button-bar--contained"><button type="button" value="Add Note" onclick="addNotes()" class="btn btn--primary btn--well sl-save-note">Add Note</button><button type="button" onclick="closePopup()" value="Cancel" class="btn btn--well">Cancel</button></div></form></div>'
	);
}
function changeTextNotes(_val){
	
}
function parkOrder(){
	CallAjax(0,'parkOrder',$("#currentOrderId").val());
	NotesOrder();
}
function addNotes(){
	var _note = $("#sale-note-textareas").val();
	CallAjax(_note,'saveNotes',$("#currentOrderId").val());
	closePopup();
}
function closePopup(){
	$('div.ja_close').click();
}
function reloadPage(){
	CallAjax(1,'parkOrder',$("#currentOrderId").val());
	parent.$('div.tier-2').find('div.row').remove();
	parent.$("#sub-total").html('$0.00');
	parent.$("#sub-tax").html('$0.00');
	parent.$("#_total").html('$0.00');
	parent.$("#to-pay").html('$0.00');
	$("#oL-"+$("#currentOrderId").val()).hide();
	closePopup();
}
function addPosProduct(obj){
	var _price = parseFloat($(obj).attr('data-price'));
	var _name = $(obj).find('span').html();
	CallAjax(_name,'createOrder',$("#currentOrderId").val());
	addItems(_price,_name);
}
function printCloseRegister(){
	window.print();
}
function CloseRegister(){
	CallAjax('close','CloseRegister');
}
function removeCustomer(obj){	
	$("#customer_name").hide();
	$("#_customer_display_name").html('&nbsp;');
	$("#deleted_customer").attr('data-id',0);
	CallAjax($("#currentOrderId").val(),'removeCustomer');
}
function CallAjax(_name,_link,_p,_v){
	if(!_p) _p = '';
	if(!_v) _v = '';
	$.ajax({
		url	: '/pos/orders/'+_link,
		type	:'POST',
		data	:	{ 
			txt_name:_name,
			txt_p:_p,
			txt_v:_v
		},
    	cache: false,
		beforeSend: function(){
		},
		success: function(data, type){			
			if(data.error_check_email){				
				if(data.error_check_email){
					$("#_message_").html(data.message);
				}
			}else{
				$("#_message_").html('');
				if(data.full_name){
					$("#customer_name").show();
					$("#_customer_display_name").html(data.full_name);
				}
			}
			if(data.order) {
				$("#currentOrderId").val(data.order);
				$("#hiddensubtotal").val(0);
			}else if(data._type) {
				$("#sub-total").html(data._totalPrice);
				$("#sub-tax").html(data._totalTax);
				$("#_total").html(data._topay);
				$("#to-pay").html(data._topay);
			}
		}
	});
}
function _addCustomer_(_name,_email){
	$("#customer_name").show();
	$("#_customer_display_name").html(_name);
	CallAjax(_email,'updateCustomer',$("#currentOrderId").val());
}
function addItems(_price,_name,_quantity){
	if(!_quantity) _quantity = 1;
	var _div_id = _name.replace(/\s/g,'-');
	var _check_ = $("#"+_div_id);
	if(_check_.length){
		var _div_quantity = _check_.find('div.quantity span.badge');
		var _quantity_ = parseInt(_div_quantity.text())+1;
		_div_quantity.text(_quantity_);
	}else{
		var _quantity_div = $("<div>", {class: "quantity col-md-1",'data-price':_price,'data-quantity':_quantity}).append($("<span>", {class: "badge","html":_quantity }));
		var _name_div = $("<div>", {class: "info col-md-5"}).append($("<div>", {class: "name","html":_name}));
		var _action_div = $("<div>", {class: "list col-md-1"}).append($("<i>", {class: "glyphicon glyphicon-th-list"}));
		var _uint_price_div = $("<div>", {class: "price col-md-2"}).append($("<span>", {class: "badge","html":_price.toMoney()}));
		var _amout_price_div = $("<div>", {class: "amount col-md-2"}).append($("<span>", {class: "badge","html":_price.toMoney()}));
		var _delete_div = $("<div>", {class: "delete col-md-1",onclick:"deleteItem(this)"}).append($("<i>", {class: "glyphicon glyphicon-remove-sign","html":"&nbsp;"}));	
		var _div = $("<div>", {class: "row",id:_div_id}).append(_quantity_div)
											.append(_name_div)
											.append(_action_div)
											.append(_uint_price_div)
											.append(_amout_price_div)
											.append(_delete_div);
		$("#item-checkout").prepend(_div);
	}	
	var _hiddensubtotal = parseFloat($("#hiddensubtotal").val()) + _price*_quantity;
	var _tax = _hiddensubtotal/10.0;
	ModifiedPrice(_hiddensubtotal,_hiddensubtotal,_tax)	
}
function escClick(){
	 $('autocomplete.ng').trigger({
        type: 'keydown',
        which: 27
    });
}
function ModifiedPrice(_hiddensubtotal,_newprice,_tax){
	if(_hiddensubtotal<=0 || isNaN(_hiddensubtotal)) _hiddensubtotal = 0;
	if(_newprice<=0 || isNaN(_newprice)) _newprice = 0;
	if(_tax<=0 || isNaN(_tax)) _tax = 0;
	var _topay = _tax + _newprice;
	$("#hiddensubtotal").val(_hiddensubtotal);
	$("#sub-total").html(_newprice.toMoney());
	$("#sub-tax").html(_tax.toMoney());
	$("#_total").html(_topay.toMoney());
	$("#to-pay").html(_topay.toMoney());
	$("#op-"+$("#currentOrderId").val()).html(_topay.toMoney());
}
function deleteItem(_obj){
	var _div = $(_obj).parent();
	var _hiddensubtotal = parseFloat($("#hiddensubtotal").val());		
	var _quantitydiv = $(_obj).closest('.row').find('div.quantity');
	var _newprice = _hiddensubtotal - (parseInt(_quantitydiv.attr('data-quantity'),10) * parseFloat(_quantitydiv.attr('data-price')));
	var _tax = _newprice/10.0;	
	ModifiedPrice(_newprice,_newprice,_tax);
	CallAjax($(_obj).attr('data-id'),'deleteOrder',$("#currentOrderId").val());
	_div.remove();
}
Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep)
{
    var n = this,
        c = isNaN(decimals) ? 2 : Math.abs(decimals),
        d = decimal_sep || '.',

        t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,

        sign = (n < 0) ? '-' : '$',

        i = parseInt(n = Math.abs(n).toFixed(c)) + '',

        j = ((j = i.length) > 3) ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
}
String.prototype.toNumber = function()
{
    var s = parseFloat(this.replace('$',''));
    return s;
}