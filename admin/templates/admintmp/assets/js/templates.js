jQuery(document).ready(function () {

    var $colonL = $('.left-colon'),
            menuH = $colonL.outerHeight(true),
            topStart = $colonL.offset().top;

    function scrollPosition() {
        if ($(document).scrollTop() > topStart)
        {
            $colonL.css({position: 'fixed', top: 0});
            $colonL.height($(window).height());
            $colonL.triggerHandler('resize');
        }
        else
        {
            $colonL.removeAttr('style').css({position: 'absolute'});
            $colonL.height($(window).innerHeight() - topStart)
            $colonL.triggerHandler('resize');
        }
    }
    function setPosition()
    {
        var minWidth = $('body').css('min-width').match(/\d+/);
        if (minWidth.length > 0)
        {
            minWidth = minWidth[0];
            if($(window).width() < minWidth)
            {
                $colonL.removeAttr('style').css({position: 'absolute'});
                $colonL.height($(document).innerHeight() - topStart);
            }
            else
            {
                scrollPosition();
            }

        }
        else
        {
            scrollPosition();
        }


    }

    setPosition();

    $colonL.mCustomScrollbar({
        axis: "y",
        mouseWheel:{
            enable:true,
            axis:'y'
        },
        scrollButtons: {
            enable: true
        },
        theme: "light-thick"
    });

    
    $(document).on('scroll', setPosition);
    $(window).on('resize', setPosition);
});