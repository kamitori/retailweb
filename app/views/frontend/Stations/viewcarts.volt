<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="author" content="Anvy Developers">
	<title>BanhMiSub.com</title>
	<link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.css">
	<style type="text/css" media="screen">
		.screenbox{
			height: 48vh;
			padding: 2px;
		}
		.screenbox iframe{
			width: 100%;
			height: 100%
		}
		.screenbox .url_edit {
		    background: #fff none repeat scroll 0 0;
		    top: 0;
		    display: block;
		    font-size: 1.5em;
		    height: 50px;
		    opacity: 0;
		    position: absolute;
		    text-align: center;
		    width: 100%;
		    padding-top: 10px;
		}
		.screenbox:hover > .url_edit{
			opacity: 0.9;
		}
		.screenbox .url_edit:hover{
			opacity: 0.9;
		}
		.screenbox >  span{
			cursor: pointer;
		}

		.close_popover {
		    cursor: pointer;
		    padding: 3px;
		    position: absolute;
		    right: 5px;
		    top: 5px;
		}

		.back-to-index {
			margin: 0;
			position: fixed;
			top: 0;
			left: 0;
			width: 3vw;
			height: 7vh;
			z-index: 100;
			text-decoration: none;
			color: #ffffff;
			background-color: #ff9000;
			opacity: 0.5;
		}
		 
		.back-to-index i {
			font-size: 3vw;
			display: none;
		}

		.back-to-index:hover > i{
			display: inline-block;
		}

		@media only screen and (max-width: 940px){
		}		
	</style>
</head>
<body>
	<div class="container-fluid screenview">
		<!-- <div class="row" style="height:4vh">
			<a href="/">Main Menu</a>
		</div> -->
		<a href="/" class="back-to-index">
			<i class="glyphicon glyphicon-chevron-left"></i>
		</a>		
		<?php $i=0; ?>
		{% for index,screen in screenview %}
		<?php if($i % 3 == 0) { ?>
		<div class="row">
		<?php } ?>
			<div class="col-md-4 screenbox">
				<span class="url_edit">
					<span data-toggle="popover" data-screen-id="{{ screen['screen_id'] }}" data-screen-url="{{ screen['url'] }}" title="" class="glyphicon glyphicon-edit" data-original-title="Edit Url">{{ screen['url'] }}</span>
				</span>
				<iframe src="{{ screen['url'] }}"></iframe>				
			</div>
		<?php $i++; ?>
		<?php if($i % 3 == 0) { ?>
		</div>
		<?php } ?>
		
		{% endfor %}
		<!-- <div class="row">
			<div class="col-md-4 screenbox">
				<iframe src="/cartview/4"></iframe>
			</div>
			<div class="col-md-4 screenbox">
				<iframe src="/cartview/5"></iframe>
			</div>
			<div class="col-md-4 screenbox">
				<iframe src="/cartview/6"></iframe>
			</div>
		</div> -->
	</div>

<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>	
<script type="text/javascript">
$(function(){
	loadEditUrl();
});

function loadEditUrl(){
	$('.screenview [data-toggle=popover]').popover({
		content: function(){
			return '<div class="clear-fix"><input type="text" value="'+$(this).data('screen-url')+'"><button type="button" class="btn btn-default" onclick="changeUrl(this)">Save</button></div>';
		},
  		html:true,
  		container:'body',
  		width:150,
  		placement:'bottom',
  		template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="close_popover" onclick="closePopover()">X</div><div class="popover-content"></div></div>'
	});
	$('.screenview [data-toggle=popover]').on('shown.bs.popover',function(){
		html = '<input type="hidden" name="" id="screen_id" value="'+$(this).data('screen-id')+'">';
		$("#"+$(this).attr('aria-describedby')+' .popover-content').append(html);
	})

}
function closePopover(){
	$('.screenview [data-toggle=popover]').popover('hide');
}

function changeUrl(obj){
	var url = $(obj).prev().val();
	var screen_id = $(obj).parent().parent().find($("#screen_id")).val();
	$.ajax({
		url:'/screencarts/update_url',
		type:'POST',
		dataType : 'json',
		data: {
			screen_id: screen_id,
			url: url
		},		
		success:function(data){
			if(data.status == 'ok'){
				
				$(obj).parent().parent().html('<div>'+'Saved to screen '+screen_id+'</div>');

				location.href = '/view-all-carts';

			}else{
				$(obj).parent().parent().html('Save error');
			}
		}
	})
}
	
</script>
</body>
</html>