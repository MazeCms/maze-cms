jQuery(document).ready(function () {
    $('#user-recover').hide();
    var w = $('.container').width();
    var h = $('.container').height();
    function getTop()
    {
        if ($(window).height() < $('.container').height()) {
            return 0;
        }
        else{
            return ($(window).height() / 2) - ($('.container').height() / 2);
        }
    }
    $('.container').css({top: -h}).animate({top: getTop()}, 800);
    $(window).resize(function () { $('.container').css({top: getTop()}) })
    
    $('.toggele-form').click(function(e){
        var $self = $(this), $wrap = $(this).closest('.container-from-login');
        toggleNotice();
        $wrap.stop().animate({top:-$wrap.height()}, 500, function(){
            $(this).find('form').each(function(){
                if($(this).is(':visible')){
                    $(this).hide();
                }
                else
                {
                    $(this).show();
                }
            });
            
            $(this).stop().animate({top: getTop()}, 800);
        })
        $(this).closest('.container').find('[data-text]').each(function(){
            var text = $(this).attr('data-text');
            $(this).attr('data-text', $(this).text()).text(text);
        })
        return false;
    })
    var $notice = $('.notice');
    function toggleNotice(error, cssclass)
    {
        var cssclass = cssclass || 'notice-danger';
     
        function slideUp(callback)
        {
            return $notice.animate({top: -$notice.height()}, 500, function () {
                $(this).hide().removeClass('notice-danger notice-success')
                
                if (typeof callback == 'function')
                    callback.call(this);
            })
        }
        if (error)
        {
            $notice.find('.warn').html(error);
            if ($notice.is(':visible'))
            {
                slideUp(function () {
                    $notice.addClass(cssclass).show().animate({top: 0}, 500)
                })
            }
            else {
                $notice.show().removeClass('notice-danger notice-success').addClass(cssclass).css({top: -$notice.height()}).animate({top: 0}, 500)
            }

        }
        else
        {
            if ($notice.is(':visible')) {
                slideUp()
            }
        }

        return false;
    }
    if ($notice.find('.warn').text() == '')
    {
        $notice.hide();
    }
    else
    {
        toggleNotice($notice.find('.warn').text())
    }
    $notice.find('.close').click(function () {
        toggleNotice();
        return false;
    })
    $('.form').bind('beforeAjax.mazeForm error.mazeForm success.mazeForm', function (e) {
        if (e.type == 'beforeAjax') {
            $(this).find('[type=submit]').preloaderBtn('start');
        }
        else {
            $(this).find('[type=submit]').preloaderBtn('close');
        }
    })
    
    $('.form').bind('error.mazeForm success.mazeForm', function (e, obj) {
        if (e.type == 'error')
        {
            var text = $.map(obj.error, function (val) {
                return val.mess.join(', ');
            })
            toggleNotice(text.join(', '));
        }
        else
        {
            toggleNotice();
             var $btn = $(this).find('[type=submit]').preloaderBtn('start');
            $.ajax({
                url: $(this).attr('action')+($(this).attr('action').indexOf('?') == -1 ? '?' : '&')+'clear=ajax',
                type: 'post',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (data) {
                    if($(e.target).is('#user-login'))
                    {
                       document.location = document.location.href; 
                    }
                    else
                    {
                        if(data.message)
                        {
                            toggleNotice(data.message.text, (data.message.type == 'success' ? 'notice-success' : false));
                        }
                    }
                    
                    $btn.preloaderBtn('close');
                },
                error: function (xhr, err) {
                    toggleNotice('Ошибка выполнения запроса');
                    $btn.preloaderBtn('close');
                }
            });
        }

    }).bind('beforeSubmit.mazeForm', function(){return true;})
  
    
})

//logo.setlangAjax = function(elem){
//	//console.log($(elem).val())
//	$.ajax({
//		url:"/user/?run=lang&clear=ajax",
//		data:{lang:$(elem).val()},
//		type: "POST",
//		dataType:"json",
//		cache:false,
//		beforeSend: function(){$(".tooltip").remove();$(".wrapper").preloader(true);},
//		success: function(data){
//							
//			$(".tooltip").remove();	
//			
//			if(data.redirect){
//				document.location = document.location
//				return false
//			}
//			$(".wrapper").preloader(false); 
//		}	
//		})	
//}
