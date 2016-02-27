/*
//////////////////////////////////////////////////////////////////////////////////////////////////////
// 									ВСПЛЫВАЮЩИЕ ПОДСКАЗКИ
//
//////////////////////////////////////////////////////////////////////////////////////////////////////
*/

(function($){
	$.fn.tooltipLab = function(content, options){
		var settings = $.extend({
			content:content,
			addClass:"",
			position: "right", // место всплытия относительно элемента всплытия
			arrowSize:10, // размер стрелки, учивается при расчете растояния от элемента - события
			sizeShifts: 50, // резмер сдвига при появлени блока подсказки	
			timeAnimate: 200, // продолжительность анимации в mc
			height: 0, // высота блока подсказки если null то берется общая высота из таблицы стилей
			width: 	0 // ширина блока подсказки если null то берется общая ширина из таблицы стилей
			},options||{})
		
		var self = this; // текуший экземпляр класса объекта
		
		
		var obj = {
			/*
			/////////////////////////////////////////////////////////
			// инициализация объекта
			////////////////////////////////////////////////////////
			*/
			init: function(){
				var toolTip = this.createElement();
				this.element = toolTip ;
				var positionToolTip = this.getPosition(toolTip);
				var animateOptions = this.animateOptions(toolTip, positionToolTip);
				toolTip.animate(animateOptions,settings.timeAnimate);
				
			},
			
			/*
			/////////////////////////////////////////////////////////
			// настроки аницации подсказки
			// return (object)
			/////////////////////////////////////////////////////////
			*/
			animateOptions: function(toolTip, positionToolTip){
				
				switch(settings.position){
					case "right":						
						toolTip.css({display:'', opacity: "0", left:positionToolTip.left-settings.sizeShifts, top:  positionToolTip.top});
						var result = {
						left: positionToolTip.left,
						opacity: "1"
						}
					break;
					case "left":
						toolTip.css({display:'', opacity: "0", left:positionToolTip.left+settings.sizeShifts, top: positionToolTip.top});						
						var result = {
						left: positionToolTip.left,
						opacity: "1"
						}
					break;
					case "up":	
						toolTip.css({display:'', opacity: "0", left:positionToolTip.left, top: positionToolTip.top+settings.sizeShifts});					
						var result = {
						top: positionToolTip.top,
						opacity: "1"
						}
					break;
					case "down":
						toolTip.css({display:'', opacity: "0", left:positionToolTip.left, top: positionToolTip.top-settings.sizeShifts});					
						var result = {
						top: positionToolTip.top,
						opacity: "1"
						}
					break;
				}
				
				return result;								
			},
			/*
			////////////////////////////////////////////////
			// размеры блока подсказки 
			////////////////////////////////////////////////
			*/
			getSizeToolTip: function(block)
			{
				return {
					height: block.height(),
					width: block.width()
					}
			},
			/*
			////////////////////////////////////////////////
			// Позиция элемента относительно начала документа
			// return (object) {top, left}
			////////////////////////////////////////////////
			*/
			getPosition: function(toolTip){
				var actionElPos =  $(self).offset();
				var actionElSize = {
					height: $(self).outerHeight(),
					width: $(self).outerWidth()
					}
				var SizeToolTip =  this.getSizeToolTip(toolTip);	
				var	 arrow = toolTip.find(".arrow");		
				switch(settings.position){
					case "right":
						arrow.addClass('triangle-right')
						var result = {
						left: actionElPos.left - SizeToolTip.width - settings.arrowSize,
						top:  actionElPos.top - ((SizeToolTip.height/2) - (actionElSize.height/2)),
						}
					break;
					case "left":
						arrow.addClass('triangle-left')
						var result = {
						left: actionElPos.left + actionElSize.width + settings.arrowSize,
						top:  actionElPos.top - ((SizeToolTip.height/2) - (actionElSize.height/2))
						}
					break;
					case "up":
						arrow.addClass('triangle-up')
						var result = {
						left: actionElPos.left + ((actionElSize.width/2)- (SizeToolTip.width/2)),
						top:  actionElPos.top + (actionElSize.height + settings.arrowSize)
						}
					break;
					case "down":
						arrow.addClass('triangle-down')
						var control_border = actionElPos.left-(SizeToolTip.width/2);
						var result = {};
						if(control_border<0)
						{
							arrow.css({left:(actionElSize.width/2)+settings.arrowSize})
							result['left'] = actionElPos.left  - settings.arrowSize;
						}
						else if(actionElPos.left+(SizeToolTip.width/2) > $(window).width())
						{
							
							arrow.css({left:SizeToolTip.width -  (actionElSize.width/2+settings.arrowSize)})
							result['left'] = actionElPos.left  - (SizeToolTip.width - actionElSize.width - settings.arrowSize);
						}
						else
						{
							result['left'] = actionElPos.left + ((actionElSize.width/2)- (SizeToolTip.width/2));
						}
							result['top'] =  actionElPos.top - (SizeToolTip.height + settings.arrowSize);
					break;
				}
				
				return result;					
			},
			/*
			/////////////////////////////////////////////
			// создаем тело "подсказки"
			// return (object) element
			//////////////////////////////////////////////
			*/
			createElement: function(){
				var element = $('<div>')
				.addClass('tooltip'+' '+settings.addClass)
				.css({display:'none'})
				.appendTo('body')
				.append(
					$('<div>')
					.addClass('arrow'))
				.append(
					$('<div>')
					.addClass('tooltip-body')
					.html(settings.content)
				)
				if(settings.height !== 0)
				element.css({height:settings.height+'px'})
				if(settings.width !== 0)
				element.css({width:settings.width+'px'})
				return element;
			}
		}
		
		function handler()
		{
			obj.init();
		}		
						
		function correction(){
		
			if( obj.element !== undefined)
			{				
				var toolTip = obj.element;
				var positionToolTip = obj.getPosition(toolTip);				
				toolTip
				.css({left:positionToolTip.left,
					top:positionToolTip.top})	
			}
		}
		
		function deleteElement()
		{
			if( obj.element !== undefined)
			{							
				obj.element.remove();
			}
		}		
				
		//self.bind(settings.events, handler);
		
		
		$(window).bind('resize',correction);
		
		return {
			start: function(){handler()},
			closed: function(){deleteElement()}
			};
	}
/* ################################# end tooltip ###############################*/

$.fn.tooltipHover = function(options){
	return this.each(function(){
		var self = $(this)
		
		var content = self.attr('data-content')
		
		
		
		var toolTip = self.tooltipLab(content, options);
		
		function enterHandler()
		{			
			toolTip.start(); 
		}
		function leaveHandler()
		{	
			toolTip.closed(); 
		}
		self.hover(enterHandler,leaveHandler)
	})
}

$.fn.tooltipBlure = function(options){
	return this.each(function(){
	var self = $(this)
	var content = self.attr('data-content')
	var toolTip = self.tooltipLab(content, options)
	function enterHandler()
	{
		toolTip.start(); 
	}
	function leaveHandler()
	{
		toolTip.closed(); 
	}
	self.bind("focus",enterHandler);
	self.bind("blur", leaveHandler);
	})
}

$.fn.mouseHover = function(options){

	var content = this.attr('data-content')
	var toolTip = this.tooltipLab(content, options)
	function enterHandler()
	{
		toolTip.start(); 
	}
	function leaveHandler()
	{
		toolTip.closed(); 
	}
	enterHandler();
	this.bind("mouseout",leaveHandler);
	$(document).bind("click", leaveHandler);
	
}



$.fn.tooltipClick = function(content, options){

	var toolTip = this.tooltipLab(content, options)
	
	function enterHandler()
	{
		toolTip.start(); 
	}
	function leaveHandler()
	{
		toolTip.closed(); 
	}
	this.click("focus",enterHandler);
	
}

})(jQuery)