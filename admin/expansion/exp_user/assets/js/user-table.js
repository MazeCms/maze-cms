function sendMailUser(taskHandle, taskGetForm)
{
	cms.packHandler({
		title				:	messages.title,
		alert_title	:	messages.checked_title,
		alert_text	:	messages.checked_mess,
		ID					: "user-mail-form",
		task				:	taskHandle,
		width				:	800,
		getform			:	taskGetForm,
		form				:	".mailmess",
		sendbtn			:	messages.sendbtn,
		closebtn		:	messages.closebtn,
		checkform		: checkMailForm
	});	
}
function checkMailForm(e, task, dialog)
{
	var $form = $(".mailmess"); 
	var $theme = $form.find("input[name=theme]")
	var $tmp = $form.find("select[name=tmp_mail]");
	var $elem = $(e.target);
	
	if("tinyMCE" in window)
	{
		 var content = tinyMCE.get('mess_user').getContent()
	}
	else
	{	
		 var content =	$form.find("#mess_user").val(); 			  
	}
	
	  var error = {
			flag:{},
			mess:{}
			} 	
	if($theme.val() == '')
	{
		$theme.parent().addClass('error')
		error.flag.theme = false;
		error.mess.theme = messages.theme
	}
	else
	{
		$theme.parent().removeClass('error')
		error.flag.theme = true;
	}
	
	if($tmp.val() == '')
	{
		$tmp.parent().addClass('error-box')
		error.flag.tmp = false;
		error.mess.tmp = messages.tmp
	}
	else
	{
		$tmp.parent().removeClass('error-box')
		error.flag.tmp = true;
	}
	
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
	var user = Array();
		  $(".maze-grid .maze-grid-content input:checked").each(function(){
			  user.push($(this).val());
		  })
		
	function handler(data)
	{
	  if(data.flag) dialog.mazeDialog('close');	
	  $elem.preloaderBtn("close");				
	  cms.alertBtn(data.title,data.mess, 'auto');
	}
		
	cms.ajaxSend({
			task		: task,
			handler		: handler, 
			serialize	: false,
			preloader	: false,
			param		: {id_user:user, theme:$theme.val(),tmp:$tmp.val(), content:content},
			before		: function(){$elem.preloaderBtn("start")}
	})
	
	return false;		
}