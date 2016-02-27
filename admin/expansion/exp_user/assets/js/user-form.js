jQuery(document).ready(function(){
	
	$( "#input-time-out" ).spinner({
		"min"	:	1,
		"max"	:	1000,
		"step"	:	1 
		});
	cms.progressBar("user-progressbar",0);
	
	 $("#user-phone").mask("+7(999) 999-99-99",{placeholder:" "});
		
	$meta_user = $("input[name*=meta]").not("#input-time-out");
	
	$meta_user.bind('change', prozessUserBar)
	
	function prozessUserBar()
	{
		var coout = 0;
		$meta_user.each(function(){
			if($(this).val() !== "") coout++;
		})
		var prozent = Math.round((coout/$meta_user.size())*100);
		$('#user-progressbar').progressbar('value', prozent).find('.ui-progressbar-value').text(prozent+"%")
	}
	setTimeout(prozessUserBar, 500);
	
	cms.progressBar("pass-progressbar",0);
	$("#valid-pass").hide();
	$("#send-pass").hide();
	$('input[name=new_password]')
	.bind('change keypress keydown keyup focusin focusout', function(e){
		
		
		var bar = 0;
		var text = "";
		var self_val = $(this).val();
		
		$("#valid-pass").show();
		$("#send-pass").show();	
		if(self_val !== '' && self_val.length <= 3)
		{
			bar = 10;
			text = messages.weakly			
		}
		if(self_val.length > 3)
		{
			bar = 30;
			text = messages.fair	
			if(getStringParent(self_val, 1, 2))  
			{
				bar = 40;
				text = messages.better		
			}
		}
		if(self_val.length > 5 && getStringParent(self_val, 2, 2))
		{
			bar = 50;
			text = messages.well	
			if(getStringParent(self_val, 4, 4))  
			{
				bar = 100;
				text = messages.super		
			}		
		}
		$('#pass-progressbar').progressbar('value', bar).find('.ui-progressbar-value').text(text)
		
	})
	
	
})



function addImages(elem)
{
	var $self = $(elem);
	
	function handler(files, fm)
	{
		if(files.url.search(/\.jpg|\.png|\.jpeg$/gi) !== -1)
		{
			$('.edit-avatar img').attr("src", files.url)
			$('input[name=avatar]').val(files.url)
		}
		
	}

	
	cms.loadFileManager(handler,{
		title		:	messages.file_manager,
		multi		:	false,
		onlyURL		:	false,
		startLoad	:	function(){$self.preloaderBtn("start")	},
		iframeLoad	:	function(){$self.preloaderBtn("close")	}
	}); 
	
	
	return false;		
}
function deleteImage()
{
	$('.edit-avatar img').attr("src",$('.edit-avatar').attr("data-noavatar"));
	$('input[name=avatar]').val("");
	return false
}

function getStringParent(text, str, int)
{
	var str_pattern =  text.match(/[a-z]+/g);
	var int_pattern =  text.match(/[0-9]+/g);
	
	if($.isArray(str_pattern))
	{
		if(str_pattern.length >= str || str_pattern.join("").length >= str) 
		{
			str_pattern = true;
		}
		else
		{
			str_pattern = false;
		}
	}
	else
	{
		str_pattern = false;
	}
	
	if($.isArray(int_pattern))
	{
		if(int_pattern.length >= int || int_pattern.join("").length >= int) 
		{
			int_pattern = true;
		}
		else
		{
			int_pattern = false;
		}
	}
	else
	{
		int_pattern = false;
	}
	return str_pattern && int_pattern ? true : false;
}

function validateUsersForm(task)
{
	var $username 		= $('input[name=username]');
	var $name 			= $('input[name=name]');
	var $email 			= $('input[name=email]');
	var $new_password 	= $('input[name=new_password]');
	var $repeat_password= $('input[name=repeat_password]');
	var $id_user		= $('input[name=id_user]');
	var ordering	= {};	
	
  var error = {
		flag:{},
		mess:{}
		}
  
	
	if($username.val() == '')
	{
		$username.parent().addClass('error')
		error.flag.username = false;
		error.mess.username = messages.username
	}
	else
	{
		$username.parent().removeClass('error')
		error.flag.username = true;
	}
	
	if($name.val() == '')
	{
		$name.parent().addClass('error')
		error.flag.name = false;
		error.mess.name = messages.name
	}
	else
	{
		$name.parent().removeClass('error')
		error.flag.name = true;
	}
	
	if($email.val() == '')
	{
		$email.parent().addClass('error')
		error.flag.email = false;
		error.mess.email = messages.email
	}
	else
	{
		$email.parent().removeClass('error')
		error.flag.email = true;
	}
	
	if($id_user.val() == '')
	{
		if($new_password.val() == '' || $repeat_password.val() == '')
		{
			$new_password.parent().addClass('error')
			$repeat_password.parent().addClass('error')
			error.flag.password = false;
			error.mess.password = messages.password
		}
		else
		{
			$new_password.parent().addClass('error')
			$repeat_password.parent().addClass('error')
			error.flag.password = true;
		}
	}
	
	if($new_password.val() !==  $repeat_password.val())
	{
		$new_password.parent().addClass('error')
		$repeat_password.parent().addClass('error')
		error.flag.password_ok = false;
		error.mess.password_ok = messages.password_ok
	}
	else
	{
		if(!($new_password.val() == '' || $repeat_password.val() == '') )
		{
			$new_password.parent().removeClass('error')
			$repeat_password.parent().removeClass('error')
			error.flag.password_ok = true;
		}
	}
	
	if(!($new_password.val() == '' && $repeat_password.val() == '') && $new_password.val() ==  $repeat_password.val())
	{
		var pattern = /[a-zA-Z0-9]+/gi;
			if( !pattern.test($new_password.val()))
			{
				$new_password.parent().addClass('error')
				$repeat_password.parent().addClass('error')
				error.flag.password_not = false;
				error.mess.password_not = messages.password_not
			}
			else
			{
				$new_password.parent().removeClass('error')
				$repeat_password.parent().removeClass('error')
				error.flag.password_not = true;
			}
	}
	if(!($username.val() == '' || $email.val() == ''))
	{
	cms.ajaxSend({
		task		:	'checkuser',
		serialize	:	false,
		param		:	{id_user:$id_user.val(), username:$username.val(), email: $email.val()},
		async		:	false,
		handler		:	function(data)
						{
							 $('.tollbar-panel').preloader(false)
							if(data.flag.username)
							{
								
								$username.parent().removeClass('error')
								error.flag.username = true;
								
							}
							else
							{
								$username.parent().addClass('error')
								error.flag.username = false;
								error.mess.username = data.mess.username
							}
							
							if(data.flag.email)
							{
								
								$email.parent().removeClass('error')
								error.flag.email = true;
								
							}
							else
							{
								$email.parent().addClass('error')
								error.flag.email = false;
								error.mess.email = data.mess.email
							}
						}
	})	
	
	}
	
	$.event.trigger('validate.user', error);
		
	for(proper in error.flag)
	{
		if(!error.flag[proper])
		{
			var text = '';
			for(proper in error.mess) text += '<div class="alert-menu-item"><i class="icon-warning-sign"></i>  '+error.mess[proper]+'</div>';
			text += '<p style="font-style:italic; font-size:12px">'+messages.foter+'</p>';
			cms.alertBtn(messages.title_alert, text, 'auto', 400); 
			return false;
		}
	}
  

	return true;
}