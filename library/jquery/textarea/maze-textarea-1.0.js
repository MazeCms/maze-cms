(function($){

	function MazeTextarea(elem, options)
	{
		this.$element 	= $(elem);	
		this.options = $.extend({}, $.fn.mazeTextarea.defaults, options||{});	
		this.init();		
	}
	
	MazeTextarea.prototype = {
		constructor		:	MazeTextarea,
		init					:	function()
		{
				if(!this.$element.is('textarea')) return false;
				var options = this.options, selfClass = this;
				
				var $text = this.$element;
				$text.addClass('maze-textarea-mask')
				var h = this.$element.height();
				 h = options.minHeight ? options.minHeight : h ;
				var step = Number($text.css('line-height').replace(/px/, ''));
				var siporatot = '<div style="height:'+step+'px"></div>';
				$text.css({resize:'none',overflow:'hidden'})
				$text.bind('keyup focus click blur', function(e){			
					var $self = $(this);
					var val = $self.val()
					var length = $('<div>')
												.css({position:'absolute', top:-99999, left:-99999,width:$text.outerWidth(true)})
												.html(val.split(/\r|\r\n|\n/).join('<br>'))
												.appendTo('body');		
					var hr = length.outerHeight(true)+step;			
					$self.height(hr > h ? hr : h);
					length.remove();			
				})
				
				if(!options.nl2br) return false;
				var $input;
				$(options.context).bind(options.eventReplace, function(){
					var val = selfClass.$element.val();
					val = val.split(/\r|\r\n|\n/).join(options.str);		
					if($input == null)
					{
						var name =	selfClass.$element.attr('name');
						$input = $('<input>', {type:'hidden', name:name}).val(val).insertAfter(selfClass.$element);
						selfClass.$element.removeAttr('name');
					}
					else
					{
						$input.val(val);
					}					
					
				})
		}		
		
	}
	
	$.fn.mazeTextarea = function(options){
		var arg = arguments;
		
		return this.each(function(){
		
			
			var instants = $(this).data('mazeTextarea');
			
      if (!instants)
			{
				instants = new MazeTextarea(this, options);
				$(this).data('mazeTextarea',instants);				      		
			}
			
		})
	}
	
	$.fn.mazeTextarea.defaults = {
		nl2br:true,
		str:'<br>',
		eventReplace:'submit',
		context:'body',
		minHeight:null
	}
	
})(jQuery);
