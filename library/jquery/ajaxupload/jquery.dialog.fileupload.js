(function ($) {
    'use strict';

$.dialogFileLoad = function(settings)
{
	var options = $.extend({
		name					:	'myfiles',
		id						:	"add-avatar",
		multiple			:	false,
		url						:	'user/?run=loadfile',
		modal					:	false,
		draggable			:	false,
		/*
		* Максимальное колличество файлов для закачки
		*/
		limitFile			:	10,
		/*
		* Максимально допустимый размер файла в MB
		*/
		limitFileSize	:	10,	
		onDone				:	function(){},		
		text_dropzone	:	'Область для сброса фалов или',
		text_addbtn		:	'Добавить файл',
		messlimit			:	'Превышен максимально допустимый размер файла',
		loadtext			:	"Подаждите идет загрузка...",
		messType			:	'Текущий тип  файла является не допустимыми для загрузки',
		/*
		* Разрешенные типы файлов
		*/
		accessFile		:	[	'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 
		'image/svg+xml', 'image/tiff', 'image/vnd.microsoft.icon', 'image/vnd.wap.wbmp'
		]
	}, settings || {});
	
	
	if($("#"+options.id).is("#"+options.id))
	{
		$("#"+options.id).find(".alert").remove();
		$("#"+options.id).mazeDialog("open");
		return false;
	}
	
	function getTplDrop(textdrop, name, textbtn, multiple)
	{
		var temp = '<div class="file-load-maze">'
			+'<div class="file-load-dropzone">'
			+'<span>{TEXTDROP}</span>'
			+'<a href="javascript:void(0)" class="btn-load-add"><input name="{NAME}" class="file-load-input" {MULTIPLE} type="file">'
			+'{TEXTADDFILE}</a></div></div>';
			
			return temp.replace(/{TEXTDROP}/, textdrop)
						.replace(/{NAME}/, multiple ? name+"[]" : name)
						.replace(/{TEXTADDFILE}/, textbtn)
						.replace(/{MULTIPLE}/, multiple ? "multiple" : "");
	}
	
		var $dialog  = $("<div>", {id:options.id}).append(getTplDrop(options.text_dropzone, options.name, options.text_addbtn, options.multiple))				
	
	 $dialog.mazeDialog({
		autoOpen		: false,
		title				: "Добавить аватар",
		modal				:	options.modal,
		mode				:	'static',
		draggable		:	options.draggable,
		width				:	500,
		resize			: false,
		toolbarhead	:	{minidialog:{}, fulldialog:{}}
	})
	.mazeDialog("open");
	
	function getTplProcess(textload)
	{
		var proces = '<span class="file-load-text-process">{TEXTLOAD}</span>'
		+'<div class="load-progress load-progress-striped active"><div class="load-progress-bar" style="width: 1%;"></div></div>';
		
		return proces.replace(/{TEXTLOAD}/, textload)
	}
	
	function setMethodLoad()
	{
		$dialog.find('.file-load-input').fileupload({
			  dataType					: 'json',
			  url								: options.url+"&clear=ajax",
			  singleFileUploads	: false,
			  multipart					:	true,
			  submit						: function(e, data){
				
					var error = "";
					
					$.each(data.files, function(i, val){
						if((val.size/1024/1024) > options.limitFileSize)
						{
							error += options.messlimit+" - "+val.name+"<br>";
						}
						
						var ext = val.name.substr(val.name.lastIndexOf(".")+1)
						var type = null;
						$.each(options.accessFile, function(i, mime){
							if(mime.indexOf(ext) !== -1)
							{
								type = ext;
							}
						})
						
						if(type == null)
						{
							error += options.messType+"<br>";
						}					
					})
					
					if(error !== "")
					{
						$dialog.prepend($("<div>").addClass("alert alert-danger").html(error))
						return false;
					}			
			  },
			  drop				: function(e, data) { 
			  },
			  change			: function(e, data) { 
					$dialog.find(".alert").remove();			  
			  },
			  dropZone			: $dialog.find('.file-load-dropzone'),	
			  done					: function (e, data){   
						$dialog.html(getTplDrop(options.text_dropzone, options.name, options.text_addbtn, options.multiple));
						setMethodLoad();	
						options.onDone.call($dialog, data.result);  
			  },		  
			  progressall			: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
					if(!$dialog.find(".file-load-text-process").is(".file-load-text-process"))
					{
						$dialog.html(getTplProcess(options.loadtext));
					}
					$dialog.find('.load-progress-bar').width(progress+'%');
				
			  }
			})
	}
	
	setMethodLoad();
	
	return false;		
}

})( jQuery );
