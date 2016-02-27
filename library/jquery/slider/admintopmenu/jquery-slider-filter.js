(function($){
	$.fn.sliderFilter = function(options){
		var settings = $.extend({
			tabActive:"#system",
			timeAnim:1000
			
			},options||{})
			
			
var Slider = (function(element){
				function Slider(element)
				{
					this.element = element;
					this.sliderWrap();
					this.gropSlider = this.element.find('.grop-slider');
					this.wrapSlider = this.element.find('.slider-wrap');
					this.displaySlider =  this.element.find('.dispaly-view');
					this.navigSlider = this.element.find('.navigation');
					this.displayWidth = this.displaySlider.outerWidth();
					this.setDispaly();
					this.tabFilter();
					this.clearScroll();
					this.scrollBar();
					this.resize();
					this.count = 0;
					this.scrollRight();
					this.scrollLeft();
					this.scrollBarDrag();
					this.toggleSlider();
					
				}
					
				Slider.prototype = {
					// имя конструктора
					constructor: Slider,
					
					sliderWrap: function(){
						var silder = this.element.find('.cell-wrap');			
						silder.each(function(){
							$(this)				
							.wrap($('<li>', {id:$(this).attr("data-id")})
									.addClass('grop-slider')
									)											
							})
						this.element.find('.grop-slider').wrapAll($('<ul>').addClass('slider-wrap'))		
						
					},
					activTab: function()
					{
						return this.wrapSlider.find('.grop-slider:not(:hidden)');
					},
					sizeCorect: function(elem, selector)
					{
						var ref = elem.clone()
						var refC = elem.parents(selector).clone();
						var wrap = refC.html('').append(ref).appendTo('body').hide();		
						var w = wrap.outerWidth();
						wrap.remove();
						return w ;
					},
					
					slideSize:function()
					{
						var obj =this;
						var silder = this.gropSlider
							var width = 0;
							silder.each(function(){
								var widthLi = 0;
								$(this).find('li').each(function(){widthLi += obj.sizeCorect($(this), ".cell-wrap") })
								$(this).find('.cell-wrap').css({width:widthLi+'px'})	
								var gropSlider = obj.sizeCorect( $(this).find('.cell-wrap'), '.grop-slider' )
								width += gropSlider
								})
						return {height: silder.outerHeight(),
								width: width}
						
					},
					
					
					dispalyView: function()
					{
						var dispaly = this.displaySlider;
						return {height: dispaly.outerHeight(),
								width: this.displayWidth}
					},
					
					setDispaly: function()
					{
						var silder = this.slideSize();
						var dispaly = this.dispalyView();
						var silderEl = this.wrapSlider
						if(dispaly.width < silder.width)
						{							
						
							silderEl.css({width:silder.width+'px'});
							
						}
						silderEl.find('.grop-slider').hide();	
						var active = $.cookie('id_tab_slider') || settings.tabActive;
						this.element.find('.navigation').find('a[href='+active+']').addClass('selected');
						silderEl.find(active).show();	
					},
					
					disableClass:function(elem) 
					{
						if(!elem.hasClass("disable"))
						{
							elem.addClass("disable");
						}
					},
					
					clearScroll: function()
					{			
						this.wrapSlider.css({left:0+'px'},200)
						
						var scrollEl =  this.activTab().outerWidth();
						
						var display = this.displayWidth ;
						
						if(scrollEl < display){
							
							$('.arrow-right a').addClass("disable")
							$('.arrow-left a').addClass("disable")
							}
						else{
							$('.arrow-right a').removeClass("disable")
							$('.arrow-left a').addClass("disable")
						}
					},
					scrollLeft: function()
					{
						var silderEl = this.wrapSlider
						var display = this.displayWidth ;
						var cadrElW = this.element.find('.cell-wrap li').outerWidth();
						var self = this;
						self.clearScroll()
						$('.arrow-left a').unbind('click');
						$('.arrow-left a').click(function(event){
							
							var scrollEl = self.activTab().outerWidth();
							event.preventDefault();	
							
							if(scrollEl > display){
								var stepMax = scrollEl-display;	
															
								self.count = (Math.round(self.count/cadrElW))*cadrElW	
								
								if(self.count>0){
									$('.arrow-right  a').removeClass("disable")	
									self.count = self.count-cadrElW	
									silderEl.animate({left:-(self.count)+'px'},settings.timeAnim)						
									self.scrollBarLeft(self.count);
										
														
																
									if(self.count == 0 )self.disableClass($(this))					
								}
								else{self.disableClass($(this))}
							}
							else{
								self.disableClass($(this));
							}
							})
					},
					scrollRight: function()
					{
						var silderEl = this.wrapSlider
						var display = this.displayWidth ;
						var cadrElW = this.element.find('.cell-wrap li').outerWidth();
						var self = this;
						$('.arrow-right a').unbind('click');
								
						$('.arrow-right a').click(function(event){
							var scrollEl = self.activTab().outerWidth();	
									
							event.preventDefault();				
							if(scrollEl > display){
								var stepMax = scrollEl-display;
								
								self.count = (Math.round(self.count/cadrElW))*cadrElW					
								if(stepMax>self.count){					
									$('.arrow-left  a').removeClass("disable")
									silderEl.animate({left:-(self.count+cadrElW)+'px'},settings.timeAnim)						
									self.scrollBarRight(self.count+cadrElW);
									 
									self.count += cadrElW;						
									
									if(self.count > stepMax )self.disableClass($(this))	
								}
								else{self.disableClass($(this))}
							}
							else{
								self.disableClass($(this));
							}
							})
					},
					
					scrollBar: function()
					{
						var wSlid = this.activTab().outerWidth();
						var wDis = this.displayWidth ;
						$('.scroll-bogie').css({left:0+'px'}).hide();
						if(wSlid>wDis){
							
							var wBar = Math.round(wDis/(wSlid/wDis));
							
							$('.scroll-bogie').show().css({width: wBar})
						}
					},
					
					scrollBarRight: function(step)
					{
						var scalingFactor = (this.activTab().outerWidth()/this.displayWidth).toFixed(2);
						var wSlid = this.activTab().outerWidth();
						var wDis = this.displayWidth;
						var wStepBar = Math.round((wSlid - wDis)/scalingFactor);	
						var anim = {};
						if(Math.round(step/scalingFactor) > wStepBar){anim['left'] = wStepBar +'px'}
						else{anim['left'] = Math.round(step/scalingFactor) +'px'}
						$('.scroll-bogie').animate(anim,settings.timeAnim)
					},
					
					
					scrollBarDrag: function()
					{
						var self = this;
						
						function drag(event)
						{
							var bogie = self.bogie = $(this);
							
							var scope = bogie.parents('.scroll-bar').width() - bogie.width();
							var positionEl = bogie.position();
							var startX = event.pageX;
							var startY = event.pageY;
							var scalingFactor = (self.activTab().outerWidth()/self.displayWidth).toFixed(2);
							var deltaX = startX - positionEl.left;
							var deltaY = startY - positionEl.top;
							
							$(document).bind('mousemove',moveHandler);
							$(document).bind('mouseup',upHandler);
							
							function upHandler(event)
							{
								$(this).unbind('mousemove',moveHandler)
								$(this).unbind('mouseup',upHandler)
								
								event.stopPropagation();
							}
								
							function moveHandler(event)
							{
								var scrollStep = event.pageX-deltaX;
								var count = 0;
								if(scrollStep > 0 && scrollStep < scope){
									bogie.css({left:scrollStep+'px'})
									
									self.count = Math.round( scrollStep*scalingFactor );
									self.wrapSlider.css({left:-self.count+'px'})
									self.arroawDisable();
									
								}			
							}			
						}
						$('.scroll-bogie').bind('mousedown',drag)
						
					},
					arroawDisable: function()
					{
						var maxScrol =  this.activTab().outerWidth() - this.displayWidth;
						if(this.count>0){
							$('.arrow-left a').removeClass("disable")
							$('.arrow-right a').removeClass("disable")
							}
						if(Math.round(this.count) == maxScrol-1)
						{
							$('.arrow-right a').addClass("disable")
						}
						if(Math.round(this.count) == 1)
						{
							$('.arrow-left a').addClass("disable")
							$('.arrow-right a').removeClass("disable")
						}
						
					},
					scrollBarLeft: function(step)
					{
						var scalingFactor = (this.activTab().outerWidth()/this.displayWidth).toFixed(2);		
						$('.scroll-bogie').animate({left:Math.round(step/scalingFactor) +'px'},500)
					},
					
					resize:function()
					{	
						var self = this;		
						$(window).bind('resize',function(){
							self.count = 0;	
							self.displayWidth = self.displaySlider.outerWidth();					
							self.clearScroll();
							self.scrollBar();
							self.scrollLeft();
							self.scrollRight();
							});
					},
					
					update: function()
					{
							this.count = 0;	
							this.displayWidth = this.displaySlider.outerWidth();					
							this.clearScroll();
							this.scrollBar();
							this.scrollLeft();
							this.scrollRight();
					},
					
					tabFilter: function()
					{
						var tab = this.navigSlider.find('a');
						
						var self = this;
						function handler(event){
							event.preventDefault();					
							tab.removeClass('selected')
							var id = $(this).attr('href');
							$.cookie('id_tab_slider', id);
							$(this).addClass('selected');			
							$('.grop-slider').hide();
							$(id).show();
							self.clearScroll();
							self.scrollBar();
							self.count = 0;
						}
						tab.click(handler)
					},
					toggleSlider: function()
					{
						var self = this;
						var slider = this.element.find('.toggle-slider');
						var toggleElem = this.element.find('.toggle-panel')
						var up = handlerUp;
						var down = handlerDown;
						if($.cookie('toggle_slider') == "up"){
							var down = handlerUp;
							var  up = handlerDown;
							slider.hide()
							toggleElem.removeClass("toggle-panel-up").addClass("toggle-panel-down")						
							}
						function handlerUp(){
							
							$('.arrow-left a').unbind('click');
							slider.slideUp(100);
							$.cookie('toggle_slider',"up")
							$(this).removeClass("toggle-panel-up").addClass("toggle-panel-down")
							
						}
						function handlerDown(){
							
							slider.slideDown(100);
							$.cookie('toggle_slider',"down")
							$(this).removeClass("toggle-panel-down").addClass("toggle-panel-up")
							self.update();
						}
						toggleElem.toggle(up, down)
					}
					
					
				}
				return new Slider(element);
	})(this)
	
	
	}
})(jQuery)