/*
 *	События
 *****************************
 * open.toolBarElem - Открытие панели
 * close.toolBarElem - закрыте панели
 *****************************
 *	Методы
 *****************************
 * close - закрыть меню
 *	options - настройки
 */
(function ($) {

    function ToolBarElem(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.toolBarElem.defaults, options || {});
        this.$mask = null;
        this.$toolbar = null;
        this.upadteTime = null;
        this.context = Array();
        this.init();
    }

    ToolBarElem.prototype = {
        constructor: ToolBarElem,
        init: function ()
        {

            var selfClass = this;
            this.createBar();

            this.$element.bind('mouseover', function (e) {
                e.stopPropagation();
                $('.content-elem-wrapp').toolBarElem('close');
                selfClass.showPanel();
            })
            $('.tool-bar-site').on('click mouseover' ,function(){
                selfClass.hidePanel();
            })

            $(window).resize(function () {
                selfClass.setPosition();
            });
            
           
            
            this.createToolTip();
            this.$toolbar.draggable({
                handle: $(this).find('.tool-settings-holder'),
                start: function () {
                    $.each(selfClass.context, function (i, val) {
                        val.mazeContext('close')
                    })
                }
            })

        },
        createBar: function ()
        {
            this.$element.addClass('content-elem-wrapp')
            
            var selfClass = this;
            var $btnParse = this.$element.children().eq(0);
            this.$toolbar = $('<div>').addClass('toolbar-settings-block').append($('<div>').addClass('tool-settings-holder'));
            $btnParse.addClass('toolbar-settings-button');
            $btnParse.children().each(function () {

                var $self = $(this);
                var $elemA = $(this).children('a');

                if ($elemA.attr('data-type') == 'siporator')
                {
                    $elemA.addClass('toolbar-settings-siporator');
                    $elemA.attr('href', 'javascript:void(0);');
                    return true;
                }
                var text = $elemA.html();

                $elemA.addClass('tool-settings-btn');

                $elemA.html('<span class="button-settings-icon"></span><span class="button-settings-text">' + text + '</span>');

                $self.append('<a class="tool-settings-arr" href="javascript:void(0);"></a>');

                var $menu = $(this).children('ul');

                if ($elemA.attr('data-icon'))
                {
                    $elemA.find('.button-settings-icon').css('background-image', 'url("' + $elemA.attr('data-icon') + '")');
                }
                var $arr = $(this).find('.tool-settings-arr');
                if ($menu.is('ul'))
                {
                    $arr.append('<span class="settings-icon-arr"></span>');
                }

                if ($elemA.attr('onclick') && $menu.is('ul'))
                {
                    selfClass.minBtntwo($arr, $elemA);
                    $arr.mazeContext({
                        data: $menu,
                        class_menu: selfClass.options.class_menu,
                        onAfterOpen: function () {
                            selfClass.clearClass();
                            this.parent().addClass('tool-settings-arr-active')
                        },
                        onAfterClose: function () {
                            this.parent().removeClass('tool-settings-arr-active')
                        }
                    });

                    selfClass.context.push($arr);
                }

                else if ($menu.is('ul'))
                {
                    selfClass.minBtnone($arr, $elemA);
                    $elemA.mazeContext({
                        data: $menu,
                        class_menu: selfClass.options.class_menu,
                        position: {
                            my: 'left top',
                            at: 'left bottom'
                        },
                        onAfterOpen: function () {
                            selfClass.clearClass();
                            this.parent().addClass('tool-settings-all-active')
                        },
                        onAfterClose: function () {
                            this.parent().removeClass('tool-settings-all-active')
                        }
                    });
                    selfClass.context.push($elemA);
                }
                else
                {
                    selfClass.minBtnone($arr, $elemA);
                }

                $menu.remove();
            });

            this.$toolbar.append($btnParse).appendTo('body');
            this.$mask = $('<div>').addClass('wrapper-settings-block').appendTo('body');
            this.$mask.hide();
          
            this.$toolbar.hide();
      },
        minBtntwo: function (arr, btn)
        {
            arr.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('li');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('tool-settings-arr-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('tool-settings-arr-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('tool-settings-arr-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('tool-settings-arr-active');
                        break;
                }

            })

            btn.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('li');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('tool-settings-btn-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('tool-settings-btn-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('tool-settings-btn-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('tool-settings-btn-active');
                        break;
                }

            })
        },
        minBtnone: function (icon, btn)
        {
            icon.add(btn).bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('li');

                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('tool-settings-all-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('tool-settings-all-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('tool-settings-all-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('tool-settings-all-active');
                        break;
                }

            })
        },
        clearClass: function ()
        {
            var classCss = ['tool-settings-all-active', 'tool-settings-all-hover', 'tool-settings-btn-active', 'tool-settings-btn-hover', 'tool-settings-arr-active', 'tool-settings-arr-hover'];
            var selfClass = this;
            $.each(classCss, function (i, name) {
                selfClass.$toolbar.find('.' + name).removeClass(name);
            })

        },
        setPosition: function ()
        {
            var selfClass = this;

            this.$mask.css({
                height: selfClass.$element.height() + 2,
                width: selfClass.$element.width() + 2
            })
            this.$mask.position({
                my: 'center',
                at: 'center',
                of: selfClass.$element
            })
            this.$toolbar.position({
                my: 'left bottom',
                at: 'left top',
                of: selfClass.$mask
            })
        },
        showPanel: function ()
        {
            if (this.$toolbar.is(':visible'))
                return false;

            this.$toolbar.show();
            this.$mask.show();
            this.setPosition();
            this.$element.trigger('open.toolBarElem')

        },
        hidePanel: function ()
        {
            if (!this.$toolbar.is(':visible'))
                return false;
            this.$toolbar.hide();
            this.$mask.hide();
            var selfClass = this;
            $.each(selfClass.context, function (i, val) {
                val.mazeContext('close')
            })
            this.$element.trigger('close.toolBarElem')
        },
        createToolTip: function ()
        {
            var options = this.options;
            this.$toolbar.find('.tool-settings-btn[title]').each(function ()
            {

                $(this).tooltip({
                    tooltipClass: 'dark-tooltip-bar',
                    show: {
                        effect: "slideDown",
                        delay: options.delayshow,
                        speed: 100
                    },
                    hide: {
                        effect: "fade",
                        delay: options.delayhide
                    },
                    position: {
                        my: 'left bottom-10',
                        at: 'left top'
                    }
                });
            })
        }

    }

    $.fn.toolBarElem = function (options) {
        var arg = arguments;

        return this.each(function () {
            var $this = $(this);

            var instants = $this.data('toolBarElem');

            if (instants)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instants.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "close")
                {
                    instants.hidePanel();
                    ;
                }



            }
            else
            {
                instants = new ToolBarElem(this, options);
                $this.data('toolBarElem', instants);
            }

        })
    }

    $.fn.toolBarElem.defaults = {
        class_menu: 'dark-maze-context',
        delayshow: 1200,
        delayhide: 500
    }

})(jQuery);
