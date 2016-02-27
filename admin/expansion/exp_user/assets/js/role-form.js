function validateRoleForm(task)
{
	
	var $name 			= $('input[name=name]');

	
  var error = {
		flag:{},
		mess:{}
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
	
	
	$.event.trigger('validate.role', error);
		
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