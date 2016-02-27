/*
 *	События
 ******************
 * minimalBar - свернуть панель инструментов
 * unminimalBar - развернуть панель инструментов
 * fixedlBar - зафиксировать панель инструментов
 * unfixedlBar - снять фиксацию пенель инструментов
 * inmessage	-	появление нового сообщения
 *******************
 * Методы
 *******************
 * minibar - свернуть -развернуть панель
 * fixedbar - закрепить - открепить панель
 * setMessage - Вывод сообщения $( ".selector" ).toolBarSite('setMessage', 'текст сообщения', 'error')
 *		string  'текст сообщения';
 *		string	'error' - тип сообщения 'success' | 'error'
 *	
 */
(function ($) {

    function ToolBarSite(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.toolBarSite.defaults, options || {});
        this.$btnpanel = this.$element.find('.tbs-panel-buttons');
        this.$btnright = this.$element.find('.tbs-bar-right');
        this.$redmore = null;
        this.upadteTime = null;
        this.init();
    }

    ToolBarSite.prototype = {
        constructor: ToolBarSite,
        init: function ()
        {
            this.$element.prependTo('body');

            var selfClass = this;
            this.paresButtonSet();

            this.resizePanel();

            $(window).resize(function () {
                selfClass.resizePanel();
            })
            this.createToolTip();

            $('#tbs-top-appmenu').mazeContext({
                data: $('#tbs-top-appmenu').next('ul'),
                class_menu: selfClass.options.class_menu,
                position: {
                    my: 'left top',
                    at: 'left bottom'
                },
                onAfterOpen: function () {
                    this.addClass('active')
                },
                onAfterClose: function () {
                    this.removeClass('active')
                }
            });

            if (this.getValue('minibar'))
            {
                setTimeout(function () {
                    selfClass.minimalBar()
                }, 300);
            }
            $('.tbs-bottom-switch').click(function () {
                selfClass.minimalBar();
                return false;
            });

            if (this.getValue('fixbar'))
            {
                setTimeout(function () {
                    selfClass.fixedBar()
                }, 300);
            }
            $('#fixed-top-bar').click(function () {
                selfClass.fixedBar();
                return false;
            });

            this.createMessages();
            var heightBar = this.$element.outerHeight(true);
            $('body').css({'background-position': '0 ' + heightBar + 'px'})
        },
        minimalBar: function ()
        {

            var $bottom = this.$element.find('.tbs-bottom-tools');


            if ($bottom.is(':visible'))
            {
                this.$element.addClass('minimized-bar-tools');
                $bottom.hide();
                $('#tabs-site-topbar').hide();
                $('#tabs-admin-topbar').removeClass('tabs-bar').addClass('active');
                this.$element.trigger('minimalBar');
                this.setValue('minibar', 1);
                $('body').addClass('mini-admin-toolbar');
            }
            else
            {
                this.$element.removeClass('minimized-bar-tools');
                $bottom.show();
                $('#tabs-site-topbar').show();
                $('#tabs-admin-topbar').removeClass('active').addClass('tabs-bar');
                this.$element.trigger('unminimalBar');
                this.setValue('minibar', null);
                $('body').removeClass('mini-admin-toolbar')
            }

            var heightBar = this.$element.outerHeight(true);
            $('body').css({'background-position': '0 ' + heightBar + 'px'});

            if ($('.empty-fixed-bar').is('.empty-fixed-bar'))
            {
                $('.empty-fixed-bar').css({height: heightBar})
            }
            this.resizePanel();
        },
        fixedBar: function ()
        {
            if (this.$element.is('.fixed-bar-mode'))
            {
                $('.empty-fixed-bar').remove();
                this.$element.removeClass('fixed-bar-mode');
                $('#fixed-top-bar').find('.icon-top-bar').toggleClass('bar-icon-clip fixed-active');
                this.$element.trigger('unfixedlBar');
                this.setValue('fixbar', null);
            }
            else
            {
                var heightBar = this.$element.outerHeight(true);
                this.$element.addClass('fixed-bar-mode');
                $('<div>')
                        .addClass('empty-fixed-bar')
                        .css({height: heightBar})
                        .prependTo('body');
                $('#fixed-top-bar').find('.icon-top-bar').toggleClass('bar-icon-clip fixed-active');
                this.$element.trigger('fixedlBar');
                this.setValue('fixbar', 1);
            }

        },
        paresButtonSet: function ()
        {
            var selfClass = this;
            this.$btnpanel.find('.tbs-bottom-group').each(function () {
                $(this).find('ul').each(function () {
                    if ($(this).is('.big-btn-tools'))
                    {
                        var $icon = $(this).find('.icon-big-tool');
                        var $btn = $(this).find('.btn-big-tool');

                        var $menu = $btn.parent().find('ul');
                        if ($menu.is('ul'))
                        {
                            $btn.append('<span class="big-btn-arrow"></span>')
                        }

                        if ($icon.attr('onclick') && $menu.is('ul'))
                        {
                            selfClass.bigBtntwo($icon, $btn);
                            $btn.mazeContext({
                                data: $menu,
                                class_menu: selfClass.options.class_menu,
                                position: {
                                    my: 'left top',
                                    at: 'left bottom'
                                },
                                onAfterOpen: function () {
                                    selfClass.clearClass();
                                    this.parents('.big-btn-tools').addClass('big-btn-active')
                                },
                                onAfterClose: function () {
                                    this.parents('.big-btn-tools').removeClass('big-btn-active')
                                }
                            });

                        }

                        else if ($icon.attr('onclick'))
                        {
                            $btn.attr('onclick', $icon.attr('onclick'));
                            $btn.attr('href', $icon.attr('href'));
                            selfClass.bigBtnone($icon, $btn);

                        }
                        else if ($menu.is('ul'))
                        {
                            selfClass.bigBtnone($icon, $btn);
                            $(this).mazeContext({
                                data: $menu,
                                class_menu: selfClass.options.class_menu,
                                position: {
                                    my: 'left top',
                                    at: 'left bottom'
                                },
                                onAfterOpen: function () {
                                    selfClass.clearClass();
                                    this.addClass('big-all-active')
                                },
                                onAfterClose: function () {
                                    this.removeClass('big-all-active')
                                }
                            });
                        }

                    }
                    else if ($(this).is('.min-btn-tools'))
                    {
                        $(this).find('li').each(function () {
                            var $btn = $(this).find('.min-btn-tool');
                            var $arr = $(this).find('.min-arr-tool');
                            var $menu = $arr.parent().find('ul');

                            if ($menu.is('ul'))
                            {
                                $arr.append('<span class="big-btn-arrow"></span>')
                            }

                            if ($btn.attr('onclick') && $menu.is('ul'))
                            {
                                selfClass.minBtntwo($arr, $btn);
                                $arr.mazeContext({
                                    data: $menu,
                                    class_menu: selfClass.options.class_menu,
                                    onAfterOpen: function () {
                                        selfClass.clearClass();
                                        this.parent().addClass('min-arr-active')
                                    },
                                    onAfterClose: function () {
                                        this.parent().removeClass('min-arr-active')
                                    }
                                });
                            }

                            else if ($btn.attr('onclick'))
                            {
                                selfClass.minBtnone($arr, $btn);

                            }
                            else if ($menu.is('ul'))
                            {
                                selfClass.minBtnone($arr, $btn);
                                $btn.mazeContext({
                                    data: $menu,
                                    class_menu: selfClass.options.class_menu,
                                    position: {
                                        my: 'left top',
                                        at: 'left bottom'
                                    },
                                    onAfterOpen: function () {
                                        selfClass.clearClass();
                                        this.parent().addClass('min-all-active')
                                    },
                                    onAfterClose: function () {
                                        this.parent().removeClass('min-all-active')
                                    }
                                });
                            }

                        })
                    }
                })
            })
        },
        bigBtntwo: function (icon, btn)
        {
            icon.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('.big-btn-tools');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('big-icon-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('big-icon-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('big-icon-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('big-icon-active');
                        break;
                }

            })

            btn.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('.big-btn-tools');

                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('big-btn-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('big-btn-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('big-btn-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('big-btn-active');
                        break;
                }

            })
        },
        bigBtnone: function (icon, btn)
        {
            icon.add(btn).bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('.big-btn-tools');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('big-all-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('big-all-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('big-all-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('big-all-active');
                        break;
                }

            })
        },
        minBtntwo: function (arr, btn)
        {
            arr.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('li');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('min-arr-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('min-arr-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('min-arr-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('min-arr-active');
                        break;
                }

            })

            btn.bind('mouseover mouseout mousedown mouseup', function (e) {
                var $parent = $(this).parents('li');
                switch (e.type)
                {
                    case 'mouseover':
                        $parent.addClass('min-btn-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('min-btn-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('min-btn-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('min-btn-active');
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
                        $parent.addClass('min-all-hover');
                        break;

                    case 'mouseout':
                        $parent.removeClass('min-all-hover');
                        break;

                    case 'mousedown':
                        $parent.addClass('min-all-active');
                        break;

                    case 'mouseup':
                        $parent.removeClass('min-all-active');
                        break;
                }

            })
        },
        clearClass: function ()
        {
            var classCss = ['big-all-hover', 'big-all-active', 'min-all-hover', 'min-all-active', 'min-btn-hover', 'min-btn-active',
                'min-arr-hover', 'min-arr-active', 'big-btn-hover', 'big-btn-active', 'big-icon-hover', 'big-icon-active'];
            var selfClass = this;
            $.each(classCss, function (i, name) {
                selfClass.$btnpanel.find('.' + name).removeClass(name);
            })

        },
        resizePanel: function ()
        {
            if (this.$element.find('.tbs-bottom-tools').is(':hidden'))
                return false;

            var panelLeftW = this.$btnpanel.outerWidth(true);
            var panelRightW = this.$btnright.outerWidth(true);
            var winW = this.$element.width() - 20;
            var totalBtn = winW - panelRightW;
            var selfClass = this;
            var panel = this.$btnpanel.children().not('.tbs-bottom-redmore').toArray().reverse();
            var lastHiddenPan = this.$btnpanel.children(".tbs-bottom-group:hidden").first();
            var lastHiddenSip = this.$btnpanel.children(".tbs-siporator-single:hidden").first();

            if (panelLeftW > totalBtn)
            {
                this.createRedmore();

                $.each(panel, function (i, value) {
                    if ($(value).is(':visible') && $(value).is('.tbs-bottom-group'))
                    {
                        $(value).hide();
                    }
                    if ($(value).is(':visible') && $(value).is('.tbs-siporator-single'))
                    {
                        $(value).hide();
                        selfClass.resizePanel();
                        selfClass.createContextRedmore();
                        return false;
                    }
                });
            }

            else if (lastHiddenPan.is('.tbs-bottom-group') && (panelLeftW + lastHiddenPan.outerWidth(true) + lastHiddenSip.outerWidth(true)) < totalBtn)
            {
                this.$btnpanel.children().each(function () {
                    if ($(this).is(':hidden') && $(this).is('.tbs-bottom-group'))
                    {
                        $(this).show();
                        selfClass.resizePanel();
                        selfClass.createContextRedmore();
                        return false;
                    }
                    if ($(this).is(':hidden') && $(this).is('.tbs-siporator-single'))
                    {
                        $(this).show();
                    }
                });

                if (this.$btnpanel.children(':hidden').size() == 0 && this.$redmore !== null)
                {
                    this.$redmore.hide();
                }

            }

        },
        createRedmore: function ()
        {
            if (this.$btnpanel.find('.tbs-bottom-redmore').is('.tbs-bottom-redmore'))
            {
                if (this.$btnpanel.find('.tbs-bottom-redmore').is(":hidden"))
                {
                    this.$btnpanel.find('.tbs-bottom-redmore').show();
                }
                return this.$redmore;
            }
            var selfClass = this;
            var redmore = $("<div>").addClass('tbs-bottom-group tbs-bottom-redmore');
            redmore.append('<ul class="big-btn-tools"><li><a class="icon-big-tool" href="#"></a></li><li><a class="btn-big-tool" href="#">Eщё<span class="big-btn-arrow"></span></a></li></ul>');
            this.$btnpanel.append(redmore);
            this.bigBtnone(redmore.find('.icon-big-tool'), redmore.find('.btn-big-tool'))

            this.$redmore = redmore;

            this.$redmore.find('.big-btn-tools').mazeContext({
                class_menu: selfClass.options.class_menu,
                onAfterOpen: function () {
                    this.addClass('big-all-active')
                },
                onAfterClose: function () {
                    this.removeClass('big-all-active')
                }
            });
            return this.$redmore
        },
        createContextRedmore: function ()
        {
            if (this.$redmore == null && this.$redmore.is(':hidden'))
                return false;

            var $parentLi = this.$redmore.find('.btn-big-tool').parent();
            var selfClass = this;

            if ($parentLi.find('ul').is('ul'))
                $parentLi.find('ul').remove();

            var $menuRedmore = $("<ul>");

            this.$btnpanel.children(':hidden').each(function () {

                var $self = $(this)
                if ($self.is('.tbs-siporator-single'))
                {
                    $menuRedmore.append('<li><a data-type="siporator"></a></li>');
                }
                else if ($self.is('.tbs-bottom-group'))
                {
                    $self.children().each(function () {

                        if ($(this).is('.big-btn-tools'))
                        {
                            var $item = $(this).children().eq(0).clone();
                            var $submenu = $(this).children().eq(1).clone();

                            $item.find('.icon-big-tool')
                                    .attr('data-type', 'link')
                                    .html($submenu.children('.btn-big-tool').text())
                            if ($submenu.children('ul').is('ul'))
                            {
                                $item.append($submenu.children('ul'))
                            }
                            $menuRedmore.append($item);
                        }
                        else if ($(this).is('.min-btn-tools'))
                        {
                            $(this).children().each(function () {
                                var $item = $(this).clone();
                                var $itemA = $item.find('.min-btn-tool');
                                if ($itemA.find('.min-icon-tools').css('background-image'))
                                {
                                    $itemA.removeClass('min-btn-tool').attr('data-icon', $itemA.find('.min-icon-tools').css('background-image').replace(/url\("([^"]+)"\)/, '$1'));
                                }
                                $itemA.attr('data-type', 'link');
                                $itemA.find('.min-icon-tools').remove();
                                $item.find('.min-arr-tool').remove();
                                $menuRedmore.append($item);
                            })
                        }

                    })
                }
            });
            var fSip = $menuRedmore.children('li').first();
            if (fSip.find('a[data-type=siporator]').is('a'))
            {
                fSip.remove();
            }

            $parentLi.append($menuRedmore.hide());
            clearTimeout(selfClass.upadteTime);
            this.upadteTime = setTimeout(function () {
                selfClass.$redmore.find('.big-btn-tools').mazeContext('options', 'data', $parentLi.children('ul')).mazeContext('update');
            }, 500)

        },
        createToolTip: function ()
        {
            var options = this.options;
            this.$btnpanel.find('.big-btn-tools[title], .min-btn-tools > li[title]').each(function ()
            {

                $(this).tooltip({
                    tooltipClass: 'dark-tooltip-bar',
                    show: {
                        effect: "slideDown",
                        delay: options.delayToolTip,
                        speed: 100
                    },
                    hide: {
                        effect: "fade",
                        delay: options.delayToolTip
                    },
                    position: {
                        my: 'left top',
                        at: 'left bottom+5',
                        of: $(this).is('.big-btn-tools') ? this : $(this).parent().get()
                    }
                });
            })
        },
        createMessages: function ()
        {
            var $mess = $('#tool-bar-messages');
            var selfClass = this;

            function positionsMes()
            {
                $mess.position({
                    my: 'left top',
                    at: 'left bottom',
                    of: selfClass.$element.get()
                })

                return $mess;
            }

            if (this.$element.find('#tool-bar-messages').is('#tool-bar-messages'))
            {
                this.$element.after($mess);
                this.$element.bind('minimalBar unminimalBar fixedlBar unfixedlBar', function (e) {
                    if ($mess.is(':visible'))
                    {
                        if (e.type == 'fixedlBar')
                        {
                            $mess.css('position', 'fixed');
                        }
                        else if (e.type == 'unfixedlBar')
                        {
                            $mess.css('position', 'absolute');
                            $('html, body').stop().animate({scrollTop: 0}, selfClass.options.speedScroll, false, positionsMes);
                            return false;
                        }
                        positionsMes();
                    }
                })
            }
            if ($mess.text() !== '')
            {
                var text = $mess.text();

                $mess.stop().removeAttr('style');

                $mess.html('<div class="tool-bar-messages-text">' + text + '</div><div class="tool-bar-messages-close"></div>')

                $('.tool-bar-messages-close').click(function () {
                    selfClass.closeMessage();
                    return false;
                })


                positionsMes().slideDown(selfClass.options.speedOpenMess);

                if (this.$element.is('.fixed-bar-mode'))
                {
                    $mess.css('position', 'fixed');
                    positionsMes();
                }
                else
                {
                    $('html, body').stop().animate({scrollTop: 0}, selfClass.options.speedScroll, false, positionsMes);
                }

                this.$element.trigger('inmessage');
            }
        },
        closeMessage: function ()
        {
            var $mess = $('#tool-bar-messages'), options = this.options;
            var def = $.Deferred();
            if ($mess.is(':visible'))
            {
                $mess.slideUp(options.speedCloseMess, def.resolve);
            }
            else
            {
                def.resolve();
            }

            return def;
        },
        setMessage: function (text, type)
        {
            var $mess = $('#tool-bar-messages');
            var res = this.closeMessage();
            var selfClass = this;
            if (text == '')
                return false;


            res.done(function ()
            {
                if (type == 'success')
                {
                    $mess.removeClass('error-messages').addClass('success-messages')
                }
                else if (type == 'error')
                {
                    $mess.removeClass('success-messages').addClass('error-messages')
                }
                else
                {
                    return false;
                }

                $mess.html(text);
                selfClass.createMessages();
            });

        },
        setValue: function (name, valAs)
        {
            if (!('cookie' in jQuery))
                return false;
            var value = $.cookie('toolBarSite');
            var selfClass = this;
            if (value)
            {
                value = value.split(',');
                if ($.isArray(value))
                {
                    var res = {};
                    $.each(value, function (i, val) {
                        var pos = val.indexOf('=');
                        if (pos !== -1)
                        {
                            res[val.substring(0, pos)] = val.substring(pos + 1);
                        }
                    })
                    var resStr = Array();
                    if (valAs == null)
                    {
                        delete  res[name];
                    }
                    else
                    {
                        res[name] = valAs;
                    }
                    $.each(res, function (name, val) {
                        resStr.push(name + '=' + val)
                    })

                    $.cookie('toolBarSite', resStr.join(','));
                }
            }
            else
            {
                $.cookie('toolBarSite', name + '=' + valAs, {expires: selfClass.options.expires});
            }
        },
        getValue: function (name)
        {
            if (!('cookie' in jQuery))
                return false;

            var value = $.cookie('toolBarSite');
            if (value)
            {
                value = value.split(',');
                if ($.isArray(value))
                {
                    var res = {};
                    $.each(value, function (i, val) {
                        var pos = val.indexOf('=');
                        if (pos !== -1)
                        {
                            res[val.substring(0, pos)] = val.substring(pos + 1);
                        }
                    })

                    return name in res ? res[name] : false;

                }
            }
            return false;
        }



    }

    $.fn.toolBarSite = function (options) {
        var arg = arguments;

        var $this = $(this);

        var instants = $this.data('toolBarSite');

        if (instants)
        {
            if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
            {
                instants.options[arg[1]] = arg[2];
            }
            if (typeof arg[0] == "string" && arg[0] == "minibar")
            {
                instants.minimalBar();
                ;
            }
            if (typeof arg[0] == "string" && arg[0] == "fixedbar")
            {
                instants.fixedBar();
            }
            if (typeof arg[0] == "string" && arg[0] == "setMessage")
            {
                instants.setMessage(arg[1], arg[2]);
            }


        }
        else
        {
            instants = new ToolBarSite(this, options);
            $this.data('toolBarSite', instants);
        }

        return this;
    }

    $.fn.toolBarSite.defaults = {
        class_menu: 'dark-maze-context',
        delayToolTip: 1200,
        speedOpenMess: 300,
        speedCloseMess: 500,
        speedScroll: 500,
        expires: 100
    }

})(jQuery);
