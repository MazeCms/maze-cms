(function($){
	
	$.fn.menuTab = function(options){
		var settings = $.extend({
			activTab:1,
			stepScroll: 80,
			timeAnimate:100,
			callbackToggle1: function(){},
			callbackToggle2: function(){},
			resetevent: function(){}
			},options||{})
	
			
			function MenuTab(element)
			{
				var self = this;
				this.element = element;
				this.tab = this.element.find('.menu-tab-nav li');
				
				/*this.element.find('.menu-tab-content').hide();
				this.element.find('.menu-tab-activ').removeClass('menu-tab-activ');
				this.element.find('.menu-tab-nav a[data-id='+settings.activTab+']').parents('li').addClass('menu-tab-activ');
				this.element.find('#menu-tab-'+settings.activTab).show();*/
				
				
				
				this.elementTop = this.element.position().top;
				
				this.setPosition();
				
				
				
				settings.resetevent.call(this, this);
				
				this.element.find('.menu-tab-nav li').wrapAll($('<div>').addClass('scroll-nav'));
				
				this.sizeScroll = 0;
				
				this.scrollMenuTab();
				this.togglePanel();
				$(window)
				.bind('resize',function(){	
					self.setPosition(); 
					self.scrollMenuTab();
				})
				.bind('scroll',function(){
					self.setScrollPosition();
					self.scrollMenuTab();
				})
				
			
			}
				
			MenuTab.prototype = {
				
				constructor: MenuTab,
				
				positionSize: function()
				{
						var elem = this.element;					
						var position = elem.offset();
						var elemW = elem.outerWidth()
						var elemH = elem.outerHeight()
						
					return {position:position, hw:{w:elemW, h:elemH}}
				},
				
				documentSize: function()
				{
					return {h:$(window).height(), w:$(window).width()}
				},
				
				setPosition: function()
				{
						var StartPos = this.positionSize();
						var windowSize = this.documentSize();
						
						if($(document).scrollTop() > this.elementTop)
						{
							this.element.css({height:windowSize.h, top:0, left:0});
						}
						else{
							this.element.css({height:windowSize.h-StartPos.position.top});
						}
						
				},
				
				setScrollPosition: function()
				{
					var scrollDoc = $(document).scrollTop();
										
					var self = this;
					if(scrollDoc > this.elementTop)
					{
						this.element.css({height: this.documentSize().h, position: "fixed"})						
						
					}
					else if(scrollDoc < this.elementTop){
						this.element.css({top:0, position:""})
						this.setPosition();
					}
					
				},	
				scrollMenuTab: function()
				{
					var displayH = this.element.find('.menu-tab-nav').outerHeight();					
					var scrollH = this.element.find('.scroll-nav').outerHeight();
					
					var topArr = this.element.find('.menu-tab-arrow-top');
					var bottomArr = this.element.find('.menu-tab-arrow-bottom');
					if(displayH < scrollH){						
						bottomArr.removeClass('arr-disable');
						topArr.removeClass('arr-disable');						
						this.maxStep = scrollH - displayH;
						if(this.sizeScroll <= 0){topArr.addClass('arr-disable')}
						
						this.scrollEvent();
					}
					else
					{
						this.sizeScroll = 0;
						this.element.find('.scroll-nav').animate({top:0+'px'}, settings.timeAnimate);
						topArr.addClass('arr-disable');
						bottomArr.addClass('arr-disable');
					}
				},
				scrollEvent: function()
				{
					
					var self = this;
					var arrowTop = this.element.find('.menu-tab-arrow-top');
					var arrowBottom = this.element.find('.menu-tab-arrow-bottom');
					var slider = this.element.find('.scroll-nav');
					
					arrowBottom
					.unbind('click')
					.bind('click',function(){
					
						if(self.sizeScroll > self.maxStep){
						$(this).addClass('arr-disable');
						return false;
						}
						arrowTop.removeClass('arr-disable');	
						self.sizeScroll += settings.stepScroll;
												
						slider.animate({top:-self.sizeScroll+'px'},settings.timeAnimate);
						
					});
					
					arrowTop
					.unbind('click')
					.bind('click',function(){						
						
						self.sizeScroll -= settings.stepScroll;	
						
						if(self.sizeScroll < 0){
						self.sizeScroll = 0
						$(this).addClass('arr-disable');
						return false;
						}
						arrowBottom.removeClass('arr-disable');											
						slider.animate({top:-self.sizeScroll+'px'},settings.timeAnimate);
						
					});
					
				},
				togglePanel: function()
				{
					var panel = this.element.find('.menu-tab-body')
					var pos = this.element.position().left + this.element.find('.menu-tab-nav').outerWidth()
					
					var up = handlerUp;
					var down = handlerDown;
					if($.cookie('toggle_tab_left') == "up"){
						var down = handlerUp;
						var  up = handlerDown;
						panel.hide();
						settings.callbackToggle1.call(this)
						$('.toggel-panel-tab').css({left:pos, margin:0}).addClass('toggel-panel-tab-expand');
						settings.callbackToggle1.call(this)			
						}
					function handlerUp(){
					
						panel.hide()
						$(this).css({left:pos, margin:0}).addClass('toggel-panel-tab-expand');
						$.cookie('toggle_tab_left', "up", {path: '/'})
						settings.callbackToggle1.call(this)
					}
					function handlerDown(){
						panel.show()
						$.cookie('toggle_tab_left', "down", {path: '/'})
						$(this).removeAttr('style').removeClass('toggel-panel-tab-expand')
						settings.callbackToggle2.call(this)
					}
					$('.toggel-panel-tab').toggle(up,down)
				}
			
				
			}
			
			// создаем  объект класса Menu
			var self = $(this);
			var selfMenu = new MenuTab(self);
			return 	selfMenu;
		
	
	}
})(jQuery)