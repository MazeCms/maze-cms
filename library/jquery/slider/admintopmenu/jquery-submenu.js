(function($){
	
	$.fn.submenu = function(options){
		var settings = $.extend({
			parent:true,
			parentSelector:'.cell-wrap li'		
			},options||{})
	
			
			function Menu(element)
			{
				var self = this;
				
				this.element = element;
				this.element.bind('mouseover', function(){self.overHandler()});
				$(window).bind('click ', function(){self.outHandler()})
				
				
				$(window).bind('resize',function(){	self.paramMenu(); });
						
			
			}
				
			Menu.prototype = {
				
				constructor: Menu,
				
				paramMenu: function()
				{
					var elem = this.element;
					if(parent){
						var elementParent = elem.parents(settings.parentSelector)
						var elemleftTop = elementParent.offset();
						var elemW = elementParent.outerWidth()
						var elemH = elementParent.outerHeight()
					}else{
						var elementParent = elem;
						var elemleftTop = elem.offset();	
						var elemW = elem.outerWidth()	
						var elemH = elem.outerHeight()
					}
					return {elem:elementParent, position:elemleftTop, hw:{h:elemH, w:elemW}}
				},
				
				getContent: function()
				{
					var id = "#submenu-"+this.element.attr('data-id-menu');
					if(  $(id).is(id) ){					
						var result = $('.submenu-content').find(id);
					}
					else{						
						var result = false;
					}
					return result;
				},
				
				overHandler: function()
				{
					var self = this;
					var elem = this.element;
					var param = this.paramMenu();
					var menu = this.getContent();
					$('.menu-block').hide();
					$('.activ-submenu').removeClass('activ-submenu');
					if(!menu) return false;
					var topH = param.position.top + param.hw.h;
					param.elem.addClass('activ-submenu')
					menu.css({top:topH, left:param.position.left}).show();
					
					this.subMenuLevel(menu)
					
					
					
				},
			
				outHandler: function()
				{
					var elem = this.element;
					var param = this.paramMenu();
					var menu = this.getContent();
					if(!menu) return false;
					param.elem.removeClass('activ-submenu');
					menu.css({top:0, left:0}).hide();
				},
			
				subMenuLevel: function(element)
				{
					function overHandler(){
						if(!$(this).children('.menu-block').is('.menu-block')) return false;
						
						var levelMenu = $(this).children('.menu-block')
						
					
						var widthL = levelMenu.outerWidth()
						var heightL = levelMenu.outerHeight()
						
						var topLeft =  $(this).position()
						var selfH = $(this).outerHeight()
						var selfW = $(this).outerWidth()
						levelMenu.css({top:topLeft.top-2, left:topLeft.left+selfW, width:widthL}).show();
						
					}
					function outHandler(){
						$(this).children('.menu-block').hide().removeAttr('style')
						
					}
					
					element.find('li').hover(overHandler, outHandler)
					
									
						
				}
			}
			
			// создаем  объект класса Menu
			return this.each(function(){
				var self = $(this);
				
				var selfMenu = new Menu(self);
				
			})
			
			
			
		
	
	}
})(jQuery)