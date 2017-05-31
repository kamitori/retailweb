$('.selectpicker').selectpicker();
$(".select2").select2();
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
})

$(".input_number .up").on("click",function(){
	var value = $(this).parent().prev().val();
	console.log(value)
	value = parseInt(value);
	value++;
	if(value>99) value=99;
	$(this).parent().prev().val(value);
})

$(".input_number .down").on("click",function(){
	var value = $(this).parent().prev().val();
	value = parseInt(value);
	value--;
	if(value<1) value=1;
	$(this).parent().prev().val(value);
})

$("#select_day_ship").each(function(key,value){
	create_day(value);
})

init_cart_nav();
function init_cart_nav(){
	var id_here = window.location.hash.substr(1);
	var array_id = ["order_summary","checkout","details","payment"];
	if(array_id.indexOf(id_here)<0){
		id_here = array_id[0];
	}
	$(".nav_cart .step").removeClass("here");
	$(".nav_cart .step").removeClass("complete");
	for(var i=0;i<=array_id.indexOf(id_here);i++){
		value = array_id[i];
		if(value==id_here){
			$("[data-id="+value+"]").addClass('here');
			$("[data-id="+array_id[i-1]+"]").addClass('prev');
		}else{
			$("[data-id="+value+"]").addClass('complete');
		}
	}

	$(".main_cart").addClass("hidden");
	$("#"+id_here).removeClass("hidden");
}

function check_reinput(input){
	var value = $("#form_create_account #"+input).val();
 	var re_value = $("#form_create_account #re_"+input).val();
 	var _input =  $("#form_create_account #re_"+input)[0];
 	if(value!=re_value){
 		_input.setCustomValidity(input.substring(0,1).toLocaleUpperCase() + input.substring(1)+' confirmation should be equal to '+input+'');
 	}else{
 		_input.setCustomValidity('');
 	}
}

function check_user(obj){
	var email = $("#form_create_account #email").val();
	$.ajax({
		url : '/user/check-user',
		type: 'POST',
		data:{
			email : email
		},
		success:function(data){
			if(data==1){
				obj.setCustomValidity('Your email has been used to created account');
			}else{
				obj.setCustomValidity('');
			}
		}
	})
}

function ship_time(){
	if($("#ship_time_now").is(":checked")){
		$(".ship_later").hide();
	}
	if($("#ship_time_later").is(":checked")){
		$(".ship_later").show();
	}
}

function create_day(obj){
	var now = new Date(srvTime());
	if(now.getHours()>23){
		now = now.setDate(now.getDate()+1);
		now = new Date(now);
	}
	var array_day = [];
	for(i=0;i<8;i++){
		array_day[i] = [];
		var date = now;
		var options = { weekday: 'long', month: 'numeric', day: 'numeric' };
		array_day[i]['text'] = date.toLocaleDateString('en-US', options);
		array_day[i]['value'] = 	date.getDate()+"-"+date.getMonth();
		now = now.setDate(now.getDate()+1);
		now = new Date(now);
	}
	var html='';
	$.each(array_day,function(key,elem){
		html+='<option value="'+elem.value+'">'+elem.text+'</option>';
	})
	$(obj).html(html);
	$(obj).select2();
}

function create_hour(obj_day,obj_hour){
	var now = new Date(srvTime());
	var day = $(obj_day).val().split('-')[0];
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
	alert(start);
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
	console.log(xmlHttp.getResponseHeader("Date"));
	return xmlHttp.getResponseHeader("Date");
}