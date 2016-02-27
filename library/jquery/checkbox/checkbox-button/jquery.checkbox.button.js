(function($){
	$.fn.checkbox = function(options){
		var settings = $.extend({
			enableClass:"checkbox-green", // класс - стиль состояния включения
			disableClass:"checkbox-red", // класс - стиль состояния выключения
			slidePosition:"left", // режим анимации переключения (up, left)
			customClass:"",
			enableText:"|", // текст кнопоки включения по умолчанию
			disableText:"0",	 // текст кнопоки выключения по умолчанию		
			timeAnimate:200, // время анимации
			calback: function(elem){}
			},options||{})
		/*
		///////////////////////////////////////////////////////
		// Класс преобразования стандатного переключателя в крутой))
		// @param element (object) - обект jquery - сам переключатель
		// @param enable (sting) - текст кнопки - состояние включено
		// @param disable (sting) - текст кнопки - состояние выключено
		///////////////////////////////////////////////////////
		*/	
		function Checkbox(element, enable, disable)
		{
			this.element = element;
			this.enable = enable;
			this.disable = disable;
			this.wrappInput();
			this.wrapp = this.wrappText();
			this.btnSize = this.slideAnim(this.wrapp);
		}
		
		Checkbox.prototype = {
		// имя конструктора
		constructor: Checkbox,
		
		wrappInput: function(){
			this.element.wrap("<div class=\"btn-checkbox\"></div>");
			this.element.css({display:'none'});
			this.wrapElement = this.element.parents(".btn-checkbox")
		},
		/*
		/////////////////////////////////////////////////////////////////////////
		// обертка блока кнопок
		//
		/////////////////////////////////////////////////////////////////////////
		*/
		wrappText: function(){
			var wrapp = $('<div>')
			.addClass("wrapper-checkbox")			
			.append($("<div>")
				.addClass("checkbox "+ settings.disableClass + " " + settings.customClass)
				.html(this.disable) )
			.append ($("<div>")
				.addClass("checkbox "+settings.enableClass + " " + settings.customClass)
				.html(this.enable) );
			
			this.element.before(wrapp);
			return 	wrapp;
		},
		/*
		///////////////////////////////////////////////////////////////////////
		// Установка блока slide в зависимости от типа анимации
		// return (object) - размеры блока обертки текста
		///////////////////////////////////////////////////////////////////////
		*/
		slideAnim:function(wrapp){
				var btnDisable = wrapp.find('.'+settings.disableClass);
				var btnEnable = wrapp.find('.'+settings.enableClass);			
				var sizeBtn = this.size(btnDisable, btnEnable);
				switch(settings.slidePosition){
					case "left":						
						btnDisable.css({height:sizeBtn.height, width:sizeBtn.width });
						btnEnable.css({height:sizeBtn.height, width:sizeBtn.width });
					break;
					case "up":						
						btnDisable.css({height:sizeBtn.height, width:sizeBtn.width, float:"none"});
						btnEnable.css({height:sizeBtn.height, width:sizeBtn.width, float:"none" });
						
					break;
				}				
				sizeBtn = this.size(btnDisable, btnEnable);
																
				this.wrapElement.css({width:sizeBtn.width, height:sizeBtn.height})
				
				wrapp.css({height:sizeBtn.height, width:sizeBtn.width + sizeBtn.width});
										
				return sizeBtn		
			},
			/*
			/////////////////////////////////////////////////////////////////////////////
			// определяем размеры кнопки относительно содержимого
			//  return (object) - размеры наибольшего блока обертки текста
			/////////////////////////////////////////////////////////////////////////////
			*/
			size:function(btnDisable, btnEnable){
		
			var height = Math.max(btnDisable.hideSize('outerHeight'), btnEnable.hideSize('outerHeight'));
			var width = Math.max(btnDisable.hideSize('outerWidth'), btnEnable.hideSize('outerWidth'));
			
			return {height: height,
					width: width}
			}
		
		}
		
		// текст кнопки
		
		function btnText(self){
			var enable = self.attr('data-enable') || settings.enableText;
			var disable = self.attr('data-disable') || settings.disableText;
			self.removeAttr('data-enable').removeAttr('data-disable')
			return {enable:enable, disable:disable	}
		};
		
		/*start each*/
		
		var $obj = $(this).not(function(){
			if($(this).attr("display") == "none")
			{
				return true;
			}
			else
			{
				return false;
			}
			
		})
	
		return $obj.each(function(){
			var self = $(this);			
			
			var text = btnText(self); // получаем текст кнопок
			var obj = new Checkbox(self, text.enable, text.disable); // создаем объект переключателя
			var size = obj.btnSize; // размеры блока текста
			
			// формируем параметры объекта анимации
			switch(settings.slidePosition){
					case "left":						
						var animOptions = {press:{marginLeft:-size.width+'px'},
										release:{marginLeft:0+'px'}}
					break;
					case "up":						
						var animOptions = {press:{marginTop:-size.height+'px'},
										release:{marginTop:0+'px'}}						
					break;
				}
			// если выключатель включен
			if(self.is(':checked'))
			{
				obj.wrapp.css(animOptions.press);
			}
			// назначаем обработчик переключателя
			function handlerToggle(){
				if(typeof self.attr('checked') !== 'undefined')
				{
					obj.wrapp.animate(animOptions.release, settings.timeAnimate);
					self.removeAttr('checked')
				}
				else
				{				
					obj.wrapp.animate(animOptions.press, settings.timeAnimate)
					self.attr('checked','')
				}
				settings.calback(obj)				
			};
			// html отбработчик onchange - перехватываем и выполняем
			obj.wrapElement.bind('click', function(){
				self.trigger('change')
				})
			
			// регистрируем обработчик								
			obj.wrapElement.click(handlerToggle)
			
			self.data('o', {obj:obj, handler: handlerToggle});
		})
		/*end each*/
	}
	
 $.fn.hideSize = function () {
    if (arguments.length && typeof arguments[0] == 'string') {
      var dim = arguments[0];
	  $(this).addClass('liActualSize')
      if (this.is(':visible')) return this[dim]();
      var clone = $('body').clone().css({
        position: 'absolute',
        top: '-99999px',
		left: '-99999px',
        visibility: 'hidden'
      }).appendTo('body');
	  clone.find('*').show();
	  var s = clone.find('.liActualSize')[dim]();
      clone.remove();
	  $(this).removeClass('liActualSize')
	 
      return s;
    };
    return undefined;
  };	
})(jQuery)	