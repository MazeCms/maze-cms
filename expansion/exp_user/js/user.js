$.user = {};

$.user.sendForm = function userLogo(elem)
{
	var $form = $(elem).parents("form");
	$form.find('.alert').remove();
	$form.find('.has-error').removeClass("has-error");
	var $logo  = $form.find('input[name=login]');
	var $pass  = $form.find('input[name=password]');
	var $captcha  = $form.find('input[name=captcha]');
	
	var url = MAZE.URI($form.attr('action'));	
	
	url.setVar("clear", "ajax");
	
	$(elem).preloaderBtn("start");
	
	function renderErr(text){return '<div class="alert alert-danger">'+text+'</div>'}
	
	$.post(url.toString(), $form.serialize(), function(data){
		
		if("errorcaptcha" in data)
		{
			
			$captcha.parent()
				.append(renderErr(data.errorcaptcha))
				.addClass('has-error');
		}
		
		if("errorlogo" in data)
		{
			$logo.parent().append(renderErr(data.errorlogo))
			.addClass('has-error');;
		}
		
		if("errorpass" in data)
		{
			$pass.parent().append(renderErr(data.errorpass))
			.addClass('has-error');;
		}
		if("redirect" in data && data.redirect)
		{
			document.location = document.location;
			return false;
		}		
		$(elem).preloaderBtn("close");
		
	}, "json")
	
	return false;
}
$.user.updateCaptcha = function(elem)
{
	var $form = $(elem).parents("form");
	$(elem).preloaderBtn("start");
	var $img = $form.find('img[src*=getCaptcha]')
	var url = MAZE.URI($img.attr('src'));
	if(url.hasVar("nocache"))
	{
		var count =	Number(url.getVar("nocache"))+1;
		
		url.setVar("nocache", count);
	}
	else
	{
		url.setVar("nocache", 1);
	}
	$img.one("load", function(){
		$(elem).preloaderBtn("close");
	})
	$img.attr("src", url.toString());
	return false;
	
}

$.user.redirect = function (href)
{
	document.location = href;
	return false;
}

$.user.addAvatar = function(idinsert)
{
	$.dialogFileLoad({
		id					:	"user-avatar",
		name				:	"avatar",
		modal				:	true,
		url					:	"/user/?run=loadavatar",
		accessFile	:	optiomsLoad.accessFile,
		limitFile		:	optiomsLoad.limitFile,
		onDone			:	function(data){
			
			if("src" in data.avatar)
			{
				var src = $("#"+idinsert).attr("src");
				var url = MAZE.URI(src);	
			
				if(url.hasVar("nocache"))
				{
						var count = url.getVar("nocache");
						url = MAZE.URI(data.avatar.src);						
						url.setVar("nocache", Number(count)+1);
				}	
				else
				{
					url = MAZE.URI(data.avatar.src);
						url.setVar("nocache", 1);
				}
				$("#"+idinsert).attr("src", url.toString());
				$("input[name=avatar]").val(data.avatar.src)
			}
		}	
	});
	return false;
}
$.user.delAvatar = function(idinsert, altimg)
{
	$("#"+idinsert).attr("src", altimg);
	$("input[name=avatar]").val("")
	return false;
}
