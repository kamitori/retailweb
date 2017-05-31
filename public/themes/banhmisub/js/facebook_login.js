window.fbAsyncInit = function() {
	FB.init({
		appId      : '1553326401585117',
		xfbml      : true,
		version    : 'v2.0'
	});
};

(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = '/themes/pizzahut/js/facebook-sdk.js';
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk')
);


function statusChangeCallback(response) {
	// The response object is returned with a status field that lets the
	// app know the current login status of the person.
	// Full docs on the response object can be found in the documentation
	// for FB.getLoginStatus().
	if (response.status === 'connected') {
	  // Logged into your app and Facebook.
	  testAPI();
	} else {
		FB.login(function(response) {
			// checkLoginState();
		}, {scope: 'public_profile,email'});
	}
}

function testAPI() {
	FB.api('/me', function(response) {
		var email = response.email;
		var fb_id = response.id;
		var first_name = response.first_name;
		var last_name = response.last_name;
		$.ajax({
			url : '/user/check-user',
			type: 'POST',
			data:{
				email : email,
				fb_id : fb_id 
			},
			success:function(data){
				if(data==1){
					window.location.reload();
				}else{
					show_create_account(email,first_name,last_name,fb_id);
				}
			}
		})
	});
}

function checkLoginState() {
	FB.getLoginStatus(function(response) {
	  statusChangeCallback(response);
	});
}
function show_create_account(email,first_name,last_name,fb_id){
	$("#modal_fb").modal("show");
	$("#form_create_account #email").val(email);
	$("#form_create_account #re_email").val(email);
	$("#form_create_account #first_name").val(first_name);
	$("#form_create_account #last_name").val(last_name);
	$("#form_create_account #facebook_id").val(fb_id);
}

$("#modal_fb").modal({
	backdrop : false,
	show : false
})