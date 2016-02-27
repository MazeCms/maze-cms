(function( $ ){


$.fixedbox = function(options)
{
	var is_Create 	= $('body').find('.fixedbox-overlay').hasClass('fixedbox-overlay');
	
	function createElement()
	{
		$('<div>').addClass('fixedbox-overlay').prependTo('body');
		$('<div>').addClass('fixedbox-layer-wrap').insertAfter('.fixedbox-overlay')
		$('<div>').addClass('fixedbox-layer-body').appendTo('.fixedbox-layer-wrap');
		$('<div>').addClass('fixedbox-layer-content').appendTo('.fixedbox-layer-body');
		$('<div>').addClass('fixedbox-layer-content-center').appendTo('.fixedbox-layer-content');
		$('<div>').addClass('fixedbox-layer-close').appendTo('.fixedbox-layer-body')
	}
	
	function close()
	{
		$('.fixedbox-layer-wrap').trigger('close.fixedbox');
		$('.fixedbox-overlay').hide();
		$('.fixedbox-layer-wrap').hide();
		 
	}
	
	function destroy()
	{
		$('.fixedbox-overlay').remove();
		$('.fixedbox-layer-wrap').remove();
	}
	
	function styleBox()
	{
		var w = {}
		w.w = $(window).outerWidth(true);
		w.h = $(window).outerHeight();
		$overlay.css({height:w.h});
  		$wrap.css({height:w.h});
		$body.css({width:w.w});
		if(!$overlay.is(':hidden'))
		{
		  if(settings.scroll_over) $wrap_cont.css({marginTop:	-docScroll});
		 
		  $wrap_cont.css({
		  height			:	w.h,
		  overflow			:	'hidden',
		  position			:	'relative'
		  });
		}
		$content.css({width:settings.width_content});
		$btn_close.css({left:$content.offset().left+$content.outerWidth()})
	}	
	
	if(arguments.length == 1 && typeof arguments[0] == "object" ) var options = arguments[0];
	if(arguments.length == 1 && typeof arguments[0] == "string" && arguments[0] == "widget")
	{
		if(!is_Create) createElement();	
		return $('.fixedbox-layer-wrap')
	}
	if(arguments.length == 1 && typeof arguments[0] == "string" && arguments[0] == "close")
	{
		if(is_Create && !$('.fixedbox-overlay').is(':hidden')) close();
		return false;	
	}
	
	if(arguments.length == 1 && typeof arguments[0] == "string" && arguments[0] == "destroy")
	{
		if(is_Create) destroy();
		return false;	
	}
	
	if(arguments.length == 1 && typeof arguments[0] == "string" && arguments[0] == "closebox")
	{
		if(is_Create && !$('.fixedbox-overlay').is(':hidden')) $('.fixedbox-layer-close').trigger('click');
		return false;	
	}
	
	var settings = $.extend({
		  
		   wrap_selector			:	"#wrap-content",
		   class_body				:	"",
		   content					:	"",
		   width_content			:	750,
		   scroll_over				:	false,
		   clear_body				:	true,
		   open						:	function(){},
		   close					:	function(){}
		  
		  },options ||{});	
	
	var $wrap_cont 	= $(settings.wrap_selector)
	var docScroll 	= $(document).scrollTop();	
	
	
	
	
	if(!is_Create)
	{
		createElement();		
	}
	else
	{
		$('.fixedbox-overlay').show();
		$('.fixedbox-layer-wrap').show();
	}
	
	var $overlay		= $('.fixedbox-overlay');
	var $wrap			= $('.fixedbox-layer-wrap');
	var $body 			= $('.fixedbox-layer-body');
	var $content 		= $('.fixedbox-layer-content');
	var $center			= $('.fixedbox-layer-content-center').addClass(settings.class_body);
	var $btn_close 		= $('.fixedbox-layer-close');
	var original_style	= $wrap_cont.attr('style');
	
	$wrap_cont.data('scroll_doc', docScroll);	
	styleBox();
	settings.open();
	
	
	$center.append(settings.content);
	 
	$wrap.trigger('open.fixedbox');
	
	  $btn_close
	  .css({opacity:'0.3'})	 
	  .bind('mouseover mouseout', function(e){		
		  if(e.type =='mouseover') $(this).animate({opacity:'1'});
		  if(e.type =='mouseout') $(this).animate({opacity:'0.3'})
	  })
	  .one('click',function(){
		 $(this).unbind('mouseover mouseout')
		  $overlay.hide();
		  $wrap.hide();	
		  if(typeof original_style !== "undefined"){
		 	 $wrap_cont.attr('style',original_style);
		  }else{
			  $wrap_cont.removeAttr('style');
		  }
		
		  if(settings.clear_body) $center.html('');

		  $wrap.trigger('close.fixedbox');
		  	 
		 $(document).scrollTop($wrap_cont.data('scroll_doc'));
		  
	  })
	
	
	$(window).bind('resize scroll',styleBox);
	
	$wrap.bind('close.fixedbox', settings.close)
	
	return $wrap;
}

$.fn.fixedbox = function(options)
{
	var $self = $(this);

	var settings = $.extend({

		   content					:	$self,
		 
		  },options ||{});
		  
  	
  return  $.fixedbox(settings);
}	

})( jQuery );
