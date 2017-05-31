		/* Optional: Overwrites javascript's built-in alert function */
function alert_h(title, msg,size){
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = '';
	}
	if(size){
		$.jAlert({
			'title': title,
			'content': msg,
			'size':size
		});
	}else{
		$.jAlert({
			'title': title,
			'content': msg
		});
	}	
}

/* Optional: Overwrites javascript's built-in confirm function (DANGER: operates differently - returns true every time and doesn't stop execution!) - You must provide a callback */
function confirm_h(msg,content,theme,confirmCallback, denyCallback)
{
	$.jAlert({		
		'type': 'confirm',
		'title':msg,
		'content':content,
		'theme':theme,
		'confirmAutofocus':'.focusConfirm'
		, 'onConfirm': confirmCallback, 'onDeny': denyCallback });
}

/* Optional Alert shortcuts based on color */
function showAlert_h(title, msg, theme)
{
	$.jAlert({
		'title': title,
		'content': msg,
		'theme': theme
	});		
}

function successAlert_h(title, msg)
{
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = 'Success';
	}

	showAlert_h(title, msg, 'green');
}

function errorAlert_h(title, msg)
{
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = 'Error';
	}

	showAlert_h(title, msg, 'red');
}

function infoAlert_h(title, msg)
{
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = 'Info';
	}

	showAlert_h(title, msg, 'blue');
}

function warningAlert(title, msg)
{
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = 'Warning';
	}

	showAlert_h(title, msg, 'yellow');
}

function blackAlert_h(title, msg)
{
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = 'Warning';
	}

	showAlert_h(title, msg, 'black');
}

function imageAlert_h(img, imgWidth)
{
	if( typeof imgWidth == 'auto' )
	{
		iframeHeight = false;
	}

	$.jAlert({
		'image': img,
		'imageWidth': imgWidth
	});
}

function videoAlert_h(video)
{
	$.jAlert({
		'video': video
	});
}

function iframeAlert(iframe, iframeHeight)
{
	if( typeof iframeHeight == 'undefined' )
	{
		iframeHeight = false;
	}

	$.jAlert({
		'iframe': iframe,
		'iframeHeight': iframeHeight
	});
}

function ajaxAlert(url, onOpen)
{
	if( typeof onOpen == 'undefined' )
	{
		onOpen = function(alert){ //on open call back. Fires just after the alert has finished rendering
				return false;
			};
	}

	$.jAlert({
		'ajax': url,
		'onOpen': onOpen
	});
}
function alertB(title,msg){
	if( typeof msg == 'undefined' )
	{
		msg = title;
		title = '';
	}
	$.jAlert({
		'title': title,
		'content': msg,
		'btns': [
			/* Add a save button */
			{ 'text': 'Ok', 'theme': 'green', 'closeAlert': false, 'onClick': function(e){
					e.preventDefault();
					var btn = $('#'+this.id);
					var alert = btn.parents('.jAlert');
					alert.closeAlert();
					return false;
				}
			}]
	});
}

function confirmPass(){
	var btn = $(this),
		tr = btn.parents('tr'),
    name = tr.find('td').eq(0).text(),
    price = tr.find('td').eq(1).text();
	$.jAlert({
		'title': 'Edit product',
		'content': '<form>Name:<br><input type="text" name="product_name" value="'+name+'">Price:<br><input type="text" value="'+price+'" name="price"></form>',
		'onOpen': function(alert){
			      $('input[name="price"]').mask('$99.99');
						alert.find('form').on('submit', function(e){
							e.preventDefault();
						});
				},
		'autofocus': 'input[name="product_name"]',
		'btns': [
			/* Add a save button */
			{ 'text': 'Save', 'theme': 'green', 'closeAlert': false, 'onClick': function(e){
					e.preventDefault();
					var btn = $('#'+this.id),
						alert = btn.parents('.jAlert'),
						form = alert.find('form'),
						name = form.find('input[name="product_name"]').val(),
          			price = form.find('input[name="price"]').val();
					
					/* Verify required fields, validate data */
					if( typeof name == 'undefined' || name == '' ){
						errorAlert('Please enter a name!');
						return;
					}
        			if( typeof price == 'undefined' || price == '' ){
						errorAlert('Please enter a price!');
						return;
					}
					//make call to server with form.serialize() and store the product in your database, then return a json encoded object with success and msg.
					//var response = { success: false, msg: 'Error saving' };
					var response = { success: true, msg: 'Successfully saved!' };
					
					/* If the response wasn't a success, show an error alert */
					if( !response ){
						errorAlert(response.msg);
						return;
					}
					/* If it was successful, show a success alert */
					successAlert(response.msg);
					/* Add the product to the list with a remove button */
					tr.replaceWith('<tr><td>'+name+'</td><td>'+price+'</td><td><a href="#" class="editProduct">Edit </a> <a href="#" class="removeProduct">Remove</a></td></tr>');
					/* Close the alert */
					alert.closeAlert();
					return false;
				}
     		}
     	]    
   	}); //end jAlert


}