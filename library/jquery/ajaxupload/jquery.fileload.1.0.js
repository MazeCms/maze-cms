(function( $ ){

	function FileLoad(element, options)
	{
		this.element = element;
		this.$element = $(element);
		this.count = 1;
		
		this.type = [
		'zip', 'rar', 'tar.gz', 'tar.bz', // архивы 
		'html', 'css', 'js','xml', // текст
		'swf', 'png', 'jpeg', 'jpg', 'gif', 'tif', 'svg', 'psd',  // изображения
		'pdf', 'txt', 'doc', 'docx', 'xlsx', 'lsx', 'ppt', 'pps',  // документы
		'mp3', 'wav', 'wma', // аудио
		'flv', 'mpg', 'avi', 'mpeg', // видео
		'pl', 'py','rb', 'sh','php', 'exe'];
			
		this.options = $.extend($.fn.fileLoad.defaults,options ||{});
		this._createLoader();
				
		this._init();
		
	}
	
	FileLoad.prototype = {
		
		constructor	:	FileLoad,
		
		_init		:	function()
		{
			this._getfile();
			this._deleteFile();
			this._setErrorServer();
			
			
		},
		
		_setErrorServer	:	function()
		{
			var self = this;
			this.$element.find('.file-load-input').bind('fileuploadfail', function (e, data) {				
				self._setFileBlockState(data.files[0].name, data.files[0].name, data.files[0].size, true, data.errorThrown);
				self.$element.triggerHandler('errorfileload');
			})
		},
		
		_createLoader	:	function()
		{
			var options = this.options;
			var temp = '<div class="file-load-maze">'
			+'<div class="file-load-footer">'
			+'<span>{TEXTDROP}</span>'
			+'<a href="javascript:void(0)" class="file-load-add"><input name="{NAME}" class="file-load-input" multiple type="file">'
			+'{TEXTADDFILE}</a></div></div>';
			
			temp = temp.replace(/{TEXTDROP}/, options.text_dropzone)
						.replace(/{NAME}/, options.name)
						.replace(/{TEXTADDFILE}/, options.text_addbtn);
			
			this.$element.append(temp);			
		},
		
		_deleteFile		:	function()
		{
			var self = this;
			this.$element.delegate('.delete-file', 'click', function(){
				var $row = $(this).closest('.file-load-row');
				$row.fadeOut(500, function(){$(this).remove(); self.count--});
				self.$element.triggerHandler('deletefileload');
			})
		},
		
		_getBlockFile	:	function(name)
		{
			var elem = null;
			this.$element.find('.file-load-row').each(function(){
				if($(this).data('filename') == name)
				{
					elem = $(this);
					return false;
				}
			})
			
			return elem;
		},
		
		_getfile		:	function()
		{
			var self = this, options = this.options;;
			
			this.$element.find('.file-load-input').fileupload({
			  dataType			: 'json',
			  url				: self.options.url,
			  singleFileUploads	: true,
			  multipart			: true,
			  submit			: function(e, data){
				if(self.count > options.limitFile)
				{
					self._getBlockFile(data.files[0].name).remove();
					return false;  
				}
				if((data.files[0].size/1024/1024) > options.limitFileSize)
				{
					self._setFileBlockState(data.files[0].name, data.files[0].name, data.files[0].size, true, options.messlimit);
					self.count++;
					return false;
				}
				if(self.getTypefile(data.files[0].name) == null || $.inArray(self.getTypefile(data.files[0].name), options.accessFile) == -1)
				{
					self._setFileBlockState(data.files[0].name, data.files[0].name, data.files[0].size, true, options.messType);
					self.count++;
					return false;
				}
				
				self.count++;
			  },
			  drop				: function(e, data) { 
			  	self.sendServer(e, data);
				self.$element.triggerHandler('changefileload'); 
			  },
			  change			: function(e, data) { 
			  	self.sendServer(e, data);
				self.$element.triggerHandler('changefileload'); 
			  },
			  dropZone			: self.$element.find('.file-load-footer'),	
			  done				: function (e, data){     
				
				var result = data.result.content;
				
				setTimeout(function(){
					self._setFileBlockState(result.original_name, result.new_name, result.size,result.error, result.message);
					self.$element.triggerHandler('donefileload', result.original_name);
				},500);
				  
			  },
		  
			  progress			: function (e, data) {
				
				var progress = parseInt(data.loaded / data.total * 100, 10);
				
				var target = self._getBlockFile(data.files[0].name);
				if(target !== null && target.find('.fl-progress-bar').is('.fl-progress-bar'))
				{
					target.find('.fl-progress-bar').width(progress+'%');
				}
						  
			  },
			  progressall			: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				
			  }
			})
		},
		
		_setFileBlockState	: function(name, newname, size, error, mess)
		{
			var target = this._getBlockFile(name), options = this.options;
			
			if(target == null) return false;
		   
			target.find('.fl-progress').remove();
			
			size = Number(size/1024).toFixed(2);
			
			target.find('.file-load-process').html('<div class="fl-name">'+name+' - '+size+' KB</div>');
			target.find('.mess-alert').toggleClass('mess-alert-info '+(error ? 'mess-alert-danger' : 'mess-alert-success'))
				.text(mess);
			target.find('.file-load-button').addClass('delete-file');
			if(!error)
			{
				var input = $('<input>',{type:'hidden',name:options.name+'[]'}).val(newname);
				target.append(input)
			}
			
			target.removeData('filename');
			
		},
		
		sendServer		:	function(e, data)
		{
			var self = this;
			$.each(data.files, function (index, file) {
				var type = self.getTypefile(file.name);
				var filebox = self._createFile(file.name);
				if(type)
				{
					filebox.find('.file-load-icon').addClass('fli-type-'+type.replace(/\.+/,'_'));
				}
				filebox.data('filename', file.name);						
				self.$element.find('.file-load-maze').prepend(filebox);
				
		  });	
		},
		
		_createFile		:	function(name)
		{
			var temp = '<div class="file-load-row">'
						+'<div class="file-load-icon"></div>'
						+'<div class="file-load-process"><div class="fl-progress fl-progress-striped active">'
						+'<div class="fl-progress-bar" style="width: 1%"></div></div></div>'
						+'<div class="file-load-message"><div class="mess-alert mess-alert-info">{FILENAME}</div></div>'
						+'<div class="file-load-button"></div>'
						+'</div>';
			return 	$(temp.replace(/{FILENAME}/, name));		
						
		},
		
		getTypefile		:	function(name)
		{
			var self = this;
			var type = false;
			
			$.each(self.type, function(i, val){
				if((new RegExp("\\."+val+"$", "i")).test(name))
				{
					type = val;
					return false;
				}
			});
			
			return type;
		}
		
		
	}
		
	
	$.fn.fileLoad = function(options) {
  		
		var arg = arguments;
		if(typeof arg[0] == "string" && arg[0] == "widget")
		{
			var instants = $(this).eq(0).data('fileLoad');			
			if (!instants) return false;
			return instants.$element;
		}
		return this.each(function(){
			$this = $(this);
			var instants = $this.data('fileLoad');
        	if (instants)
			{
				if(typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
				{
					instants.options[arg[1]] = arg[2];
				}
				if(typeof arg[0] == "string" && arg[0] == "optionsRefresh" && typeof arg[1] == "object")
				{
					instants._refreshSettings(arg[1]);
				}
				if(typeof arg[0] == "string" && arg[0] == "refresh")
				{
					instants.refresh();
				}
				if(typeof arg[0] == "string" && arg[0] == "clear")
				{
					instants.$element.find('.delete-file').trigger('click');
				}
				      		
			}
			else
			{
				instants = new FileLoad(this, options);
				$this.data('fileLoad',instants);
			}
		})
		 
		
	}
	
	$.fn.fileLoad.defaults = {
		text_dropzone	:	'Область для сброса фалов или',
		text_addbtn		:	'Добавить файл',
		name			:	'myfiles',
		url				:	'/upload.php',
		/*
		* Максимальное колличество файлов для закачки
		*/
		limitFile		:	10,
		/*
		* Максимально допустимый размер файла в MB
		*/
		limitFileSize	:	10,				
		messlimit		:	'Превышен максимально допустимый размер файла',
		/*
		* Разрешенные типы файлов
		*/
		accessFile		:	[
		'zip', 'rar', 'tar.gz', 'tar.bz', 
		'xml', 'swf', 'png', 'jpeg', 'jpg', 'gif', 'tif', 'svg', 'psd', 
		'pdf', 'txt', 'doc', 'docx', 'xlsx', 'lsx', 'ppt', 'pps'
		],
		messType		:	'Текущий тип  файла является не допустимыми для загрузки'	
	}
	
	
	
})( jQuery )
