(function ($) {

    $.mazeContext = {
        types: Array(),
        addType: function (name, object)
        {
            if (name in $.mazeContext.types)
            {
                if ('version' in methods)
                {
                    if ($.mazeContext.types[name].prototype.version > methods.version)
                    {
                        return false;
                    }
                }
            }
            $.mazeContext.types[name] = function () {
                AbstractType.apply(this, arguments)
            };
            $.mazeContext.types[name].prototype = this.createObject(AbstractType.prototype);
            $.mazeContext.types[name].prototype.constructor = $.mazeContext.types[name];

            $.extend($.mazeContext.types[name].prototype, object);
        },
        createObject: function (superClass)
        {
            if (superClass == null)
                throw TypeError("Текущее значение недопустимо");
            if (Object.create)
                return Object.create(superClass);
            var type = typeof superClass;
            if (type !== "object" && type !== "function")
                throw TypeError("Текущее значение недопустимо");

            function f() {
            }
            f.prototype = superClass;
            return new f();
        }
    }

    function AbstractType($elem, element, options, alloptions)
    {
        var selfClass = this;
        this.$elem = $elem;
        this.$element = $(element);
        this.alloptions = alloptions;
        this.options = $.extend({}, selfClass.defaults, options || {});
    }

    AbstractType.prototype = {
        constructor: AbstractType,
        defaults: {},
        _trigger: function (eventTypee, data) {

            this.$element.triggerHandler(eventTypee + '.mazecontext', data);

        },
        _create: $.noop
    }


    function MazeContext(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.mazeContext.defaults, options || {});
        this.$menu = null;
        this.parent = {};
        this.items = Array();
        this.init();
    }

    MazeContext.prototype = {
        constructor: MazeContext,
        init: function ()
        {
            this.create();
            this._eventMenu();
            this._eventSubmenu();
            var selfObj = this;
            setTimeout(function(){
                selfObj.options.onAfterInit.call(selfObj.$element)
            }, 600)
        },
        _triggerHandlers: function (method)
        {
            var arg = arguments;
            var selfClass = this;

            $.each(selfClass.type, function (type, object) {
                arg = Array.prototype.slice.call(arg, 1);
                if (method in object && typeof object[method] == 'function')
                {
                    object[method].apply(object, arg);
                }
            })
        },
        createType: function ($elem, name)
        {
            var options = this.options, selfClass = this;

            var instance, optionsType;

            if (name in $.mazeContext.types)
            {
                optionsType = name in options ? options[name] : {};
                instance = new $.mazeContext.types[name]($elem, selfClass.$element, optionsType, options);
                this.items.push(instance);
            }

            return instance;
        },
        create: function () {
            var options = this.options, selfClass = this;

            if (typeof options.data == 'function')
            {
                options.data = options.data.call(selfClass.$element);
            }

            if (typeof options.data == 'string')
            {
                options.data = this._parserHTML($(options.data));
            }

            if (typeof options.data == 'object' && options.data instanceof jQuery)
            {
                options.data = this._parserHTML(options.data);
            }

            if (!$.isArray(options.data) || !(0 in options.data))
                return false;


            $.each(options.data, function (i, val) {

                if (!('id' in val))
                    val['id'] = i + 1;

                if ('parent' in val)
                {
                    if (!(val.parent in selfClass.parent))
                    {
                        selfClass.parent[val.parent] = new Array();
                    }
                    selfClass.parent[val.parent].push(val);
                }
                else
                {
                    if (!(0 in selfClass.parent))
                    {
                        selfClass.parent[0] = new Array();
                    }
                    selfClass.parent[0].push(val);

                }
            })

            this.$menu = this._createMenu('root-context-menu', selfClass.parent[0], options.appendTo);

        },
        _parserHTML: function ($menu)
        {
            var result = Array();
            var $allLi = $menu.find('li');

            function recusive($elem)
            {

                $elem.children('li').each(function () {
                    var itemElem = {};
                    var $aTeg = $(this).children('a');
                    var $self = $(this);

                    if ($aTeg.attr('data-type'))
                    {
                        $.each($aTeg.get(0).attributes, function (i, attr) {
                            itemElem[attr.name.replace(/data-/, '')] = attr.value;
                        });

                        itemElem['id'] = $allLi.index($self) + 1;
                        var $parent = $(this).parent('ul').parent('li');
                        if ($parent.is('li') && $allLi.index($parent) !== -1)
                        {
                            itemElem['parent'] = $allLi.index($parent) + 1;
                        }

                        itemElem['title'] = $aTeg.html();
                        result.push(itemElem);
                        if ($(this).children('ul').is('ul'))
                        {
                            recusive($(this).children('ul'))
                        }
                    }
                    else
                    {
                        return true;
                    }
                })

            }

            recusive($menu);
            return result;
        },
        _createMenu: function (class_menu, data, append)
        {
            var options = this.options, selfClass = this;
            var $menu = $("<ul>").addClass('context-menu-maze ' + options.class_menu + (class_menu == null ? '' : ' ' + class_menu));

            $.each(data, function (i, val) {
                var html = '<a ';
                if ('href' in val)
                {
                    html += 'href="' + val.href + '"';
                }
                else
                {
                    html += 'href="javascript:void(0);"';
                }

                html += ' class="context-center-item"><span class="context-icon-item';

                if ('icon' in val && val.type !== 'siporator')
                {
                    html += '" style="background-image:url(\'' + val.icon + '\')"';
                }
                else if ('spriteClass' in val && val.type !== 'siporator')
                {
                    html += ' ' + val.spriteClass + '"';
                }
                else
                {
                    html += '"';
                }
                html += '></span></a>';

                html = $(html);

                var obj = selfClass.createType(html, val.type);

                if (obj !== null)
                {
                    var res = obj._create(val);
                    html.append(res);
                    html.append('<span class="context-arrow-item' + (val.id in selfClass.parent ? ' arrow' : '') + '"></span>');
                    var $item = $('<li>').append(html);
                    if (val.id in selfClass.parent)
                    {
                        selfClass._createMenu(null, selfClass.parent[val.id], $item)
                    }

                }

                $menu.append($item)
            })

            return $menu.appendTo(append);
        },
        _isOpen: function ()
        {
            if (this.$menu.is(":visible"))
            {
                return true;
            }
            return false;
        },
        _openMenu: function (e)
        {
            var options = this.options, selfClass = this;

            if (this.$menu == null)
                return false;

            if (this._isOpen())
                return false;

            $("body").find('.context-menu-maze').hide();

            var $self = this.$element;
            selfClass.$menu
                    .show()
                    .position({
                        my: options.position.my,
                        at: options.position.at,
                        of: options.position.of == null ? $self : (options.position.of == 'event' && e ? e : options.position.of),
                        using: function (position, feedback) {
                            $(this).css(position);
                            if ($.isFunction(options.position.using))
                            {
                                options.position.using.call(this, position, feedback)
                            }
                        }
                    });
            options.onAfterOpen.call($self);
            $self.triggerHandler('openmenu.mazecontext');
        },
        _closeMenu: function ()
        {
            var options = this.options;
            if (this.$menu == null)
                return false;
            if (!options.onBeforeClose.call(this.$element))
            {
                return false;
            }
            this.$menu.find('.context-menu-maze').hide();
            this.$menu.hide();
            options.onAfterClose.call(this.$element);
            this.$element.triggerHandler('closemenu.mazecontext');
        },
        _eventMenu: function ()
        {
            var options = this.options, selfClass = this, timerOver;


            if (options.eventOpen == 'click')
            {
                this.$element.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    selfClass._openMenu(e);
                })
                if (this.$menu !== null) {
                    this.$menu.bind('click', function (e) {
                        e.stopPropagation();
                    });
                }
                $(document).bind('click', function () {
                    selfClass._closeMenu();
                })
            }
            else if (options.eventOpen == 'context')
            {
                this.$element.bind('contextmenu', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    selfClass._openMenu(e);
                })
                if (this.$menu !== null) {
                    this.$menu.bind('click', function (e) {
                        e.stopPropagation();
                    });
                }
                $(document).bind('click', function () {
                    selfClass._closeMenu();
                })
            }
            else if (options.eventOpen == 'hover')
            {
                this.$element.bind('mouseover mouseout', function (e) {
                    e.stopPropagation();
                    if (e.type == 'mouseover')
                    {
                        var $self = this;
                        timerOver = setTimeout(function () {
                            selfClass._openMenu(e);
                        }, options.delayHover)
                    }
                    else
                    {
                        clearTimeout(timerOver);
                    }
                })
                if (this.$menu !== null) {
                    this.$menu.bind('mouseover mouseout', function (e) {
                        e.stopPropagation();
                    });
                }
                $(document).bind('mouseover mouseout', function () {
                    selfClass._closeMenu();
                })
            }

        },
        _eventSubmenu: function ()
        {
            var timeSubover, options = this.options;

            if (this.$menu == null)
                return false;

            this.$menu.find('a').bind('mouseover mouseout', function (e) {
                e.preventDefault();
                if (e.type == 'mouseover')
                {
                    var $parent = $(this).parent('li'), $this = $(this);

                    timeSubover = setTimeout(function () {


                        $parent.parent().find('.' + options.activeClass).removeClass(options.activeClass);

                        $parent.parent().find('.context-menu-maze').not($parent.find('.context-menu-maze')).hide();

                        if ($parent.children('.context-menu-maze').is('.context-menu-maze'))
                        {
                            $this.addClass(options.activeClass);
                            $menu = $parent.children('.context-menu-maze')

                            if ($menu.is(':visible'))
                                return true;

                            $menu.show().position({
                                my: 'left top',
                                at: 'right top',
                                of: $parent
                            });
                        }

                    }, options.delayHover)
                }
                else
                {
                    clearTimeout(timeSubover);
                }
            })
        },
        update: function ()
        {
            if (this.$menu !== null)
                this.$menu.remove();
            this.$menu = null;
            this.parent = {};
            this.create();
            var options = this.options;

            if (this.$menu == null)
                return false;

            if (options.eventOpen == 'click')
            {

                this.$menu.bind('click', function (e) {
                    e.stopPropagation();
                });
            }
            else if (options.eventOpen == 'hover')
            {
                this.$menu.bind('mouseover mouseout', function (e) {
                    e.stopPropagation();
                });
            }
            this._eventSubmenu();
        },
        
        destroy:function()
        {
            this._closeMenu();
            this.$menu.remove();
            this.$menu = null;
        }

    }

    $.fn.mazeContext = function (options) {
        var arg = arguments;

        return this.each(function () {


            var instants = $(this).data('mazeContext');

            if (instants)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instants.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "open")
                {
                    instants._openMenu();
                }
                if (typeof arg[0] == "string" && arg[0] == "close")
                {
                    instants._closeMenu();
                }
                if (typeof arg[0] == "string" && arg[0] == "update")
                {
                    instants.update();
                }
                 if (typeof arg[0] == "string" && arg[0] == "destroy")
                {
                    instants.destroy();
                }

                if (typeof arg[0] == "string")
                {
                    var arg_m = Array.prototype.slice.call(arg, 1);
                    var method = 'pub_' + arg[0];
                    $.each(instants.items, function (i, obj) {
                        if (method in obj)
                        {
                            obj[method].apply(obj, arg_m);
                        }
                    })

                }


            }
            else
            {
                instants = new MazeContext(this, options);
                $(this).data('mazeContext', instants);
            }
        })
    }

    $.fn.mazeContext.defaults = {
        // Событие при котором появляется контексное меню
        eventOpen: 'click',
        onAfterInit: function(){},
        onAfterOpen: function () {
        },
        onBeforeClose: function () {
            return true
        },
        onAfterClose: function () {
        },
        class_menu: 'default',
        activeClass: 'active',
        delayHover: 400,
        position: {
            my: 'right top',
            at: 'right bottom',
            of: null,
            using: $.noop
        },
        appendTo: 'body',
        data: []
    }

})(jQuery);

(function ($) {

    $.mazeContext.addType("link", {
        version: 1.0,
        _create: function (obj)
        {
            var selfClass = this;
            if ('actions' in obj)
            {
                this.$elem.click(function (e) {
                    obj.actions.call(this, selfClass.$element, e);
                    return false;
                })
            }
            if ('onclick' in obj)
            {
                this.$elem.attr('onclick', obj.onclick);
            }

            return obj.title;
        }
    });

    $.mazeContext.addType("siporator", {
        version: 1.0,
        _create: function (obj)
        {
            this.$elem.removeClass('context-center-item').addClass('context-siporator-item');
            return '<span class="siporator-menu"></span>';
        }
    });

    $.mazeContext.addType("checkbox", {
        version: 1.0,
        data: null,
        defaults: {
            real_checkbox: false,
        },
        _create: function (obj)
        {
            var $item = this.$elem, $menu = this.$element, selfClass = this, options = this.options;

            if ('checked' in obj && obj.checked)
            {
                setTimeout(checked, 500);
            }
            var html = obj.title;

            if (options.real_checkbox)
            {
                html += '<input type="checkbox" style="display:none" name="' + (obj.name ? obj.name : '') + '" value="' + (obj.value ? obj.value : '') + '" />'
            }
            this.data = obj;
            function checked(e)
            {
                if (e)
                    e.preventDefault();
                if ($item.find('.checked-menu').is('.checked-menu'))
                {
                    if (options.real_checkbox)
                    {
                        $item.find('input[type=checkbox]').removeAttr('checked');
                    }
                    $item.find('.context-icon-item').removeClass('checked-menu');
                    selfClass._trigger('unchecked', {item: $item, obj: obj});
                }
                else
                {
                    if (options.real_checkbox)
                    {
                        $item.find('input[type=checkbox]').attr('checked', true);
                    }

                    $item.find('.context-icon-item').addClass('checked-menu');
                    selfClass._trigger('checked', {item: $item, obj: obj});
                }
                if ('actions' in obj)
                {
                    obj.actions.call($menu, $item, obj);
                }

            }
            if ('onclick' in obj)
            {
                this.$elem.attr('onclick', obj.onclick);
            }

            this.$elem.click(function (e) {

                var $self = this;
                checked.call($self, e);
                if ('change' in obj && typeof obj.change == 'function')
                {
                    obj.change.call($menu, $item, obj);
                }

            });

            return html;
        }
    });

})(jQuery);