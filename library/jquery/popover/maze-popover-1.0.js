(function ($) {

    function MazePopover(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.mazePopover.defaults, options || {});
        this.$widget;
        this.init();
        this.query = null;
        this.loadCount = 0;
    }

    MazePopover.prototype = {
        constructor: MazePopover,
        init: function ()
        {
            this.create();
            this._setTrigger();
            var selfObj = this;
            $(window).resize(function(){
                if (selfObj._isOpen()) selfObj._setSize()
            })
        },
        create: function () {
            var options = this.options, selfClass = this;

            this.$widget = $('<div>').addClass('maze-popover').addClass(options.classBlock);

            var title = this.$element.attr('data-popover-title') ? this.$element.attr('data-popover-title') : options.title;
            if(title){
                 this.$widget.append($('<div>').addClass('maze-popover-title').html(title));
            }
           

            var content;

            if (typeof options.content == 'object' && options.content  instanceof jQuery)
            {
                content = options.content;
            }
            else if (typeof options.content == 'string')
            {
                content = $(options.content);
            }
            else if (typeof options.content == 'function')
            {
                content = options.content.call(this.$element, this.$widget);
            }

            this.$widget.append($('<div>').addClass('maze-popover-content').append(content));

            if ($.isArray(options.buttons) && options.buttons.hasOwnProperty('0'))
            {
                var $button = $('<div>').addClass('maze-popover-buttonset');

                $.each(options.buttons, function (i, prop) {
                    if (prop.hasOwnProperty('label'))
                    {
                        $button.append(
                                $('<button>', {type: "button"})
                                .addClass('maze-btn')
                                .addClass(prop.class_btn)
                                .html(prop.label)
                                .click(function (e) {
                                    prop.action.call(selfClass.$element, e)
                                })
                                )
                    }
                })

                this.$widget.append($button);

            }

            this.$widget.append($('<div>')
                    .addClass('maze-popover-arrow'))
                    .appendTo(options.appendTo)
                    .hide();

        },
        _setSize: function ()
        {

            var options = this.options, selfClass = this;

            var my, at;

            switch (options.position)
            {
                case "top":
                    my = 'center top+' + options.distance;
                    at = 'center bottom';
                    break;

                case "bottom":
                    my = 'center bottom-' + options.distance;
                    at = 'center top';
                    break;

                case "left":
                    my = 'right-' + options.distance + ' center';
                    at = 'left center';
                    break;

                case "right":
                    my = 'left+' + options.distance + ' center';
                    at = 'right center';
                    break;
            }
            this.$widget.show();
            this.$widget.find('.maze-popover-content').height(0)

            this.$widget.height(options.height).width(options.width);

            var $buttonset = this.$widget.find('.maze-popover-buttonset');
            var $title = this.$widget.find('.maze-popover-title');
            var heightContent = this.$widget.height();

            if ($title.is('.maze-popover-title'))
            {
                heightContent -= $title.outerHeight(true);
            }
            if ($buttonset.is('.maze-popover-buttonset'))
            {
                heightContent -= $buttonset.outerHeight(true);
            }


            this.$widget.find('.maze-popover-content').height(heightContent);

            this.$widget.position({
                my: my,
                at: at,
                of: selfClass.$element,
                collision: "flipfit",
                using: function (position, feedback) {

                    $(this).css(position);

                    var $arr = $(this).find('.maze-popover-arrow')
                            .addClass('maze-popover-arrow')
                            .show()
                            .removeClass("arrow-top arrow-bottom arrow-right arrow-left");

                    if (feedback.vertical == 'top' && feedback.horizontal == 'center' && (options.position == 'top' || options.position == 'bottom'))
                    {

                        var leftT = Math.ceil(feedback.target.left - feedback.element.left + (feedback.target.width / 2) - 8);
                        var leftE = Math.ceil((feedback.element.width / 2) - 8);
                        $arr.css({left: Math.min(leftT, leftE), top: '-16px'})
                        $arr.addClass("arrow-top")

                    }
                    else if (feedback.vertical == 'top' && feedback.horizontal == 'left' && (options.position == 'top' || options.position == 'bottom'))
                    {
                        var leftT = Math.ceil(feedback.target.left - feedback.element.left + (feedback.target.width / 2) - 8);
                        $arr.css({left: leftT, top: '-16px'})
                        $arr.addClass("arrow-top")
                    }

                    else if (feedback.vertical == 'bottom' && feedback.horizontal == 'center' && (options.position == 'top' || options.position == 'bottom'))
                    {

                        var leftT = Math.ceil(feedback.target.left - feedback.element.left + (feedback.target.width / 2) - 8);
                        var leftE = Math.ceil((feedback.element.width / 2) - 8);
                        $arr.css({left: Math.min(leftT, leftE), top: '100%'})
                        $arr.addClass("arrow-bottom")

                    }
                    else if (feedback.vertical == 'bottom' && feedback.horizontal == 'left' && (options.position == 'top' || options.position == 'bottom'))
                    {
                        var leftT = Math.ceil(feedback.target.left - feedback.element.left + (feedback.target.width / 2) - 8);
                        $arr.css({left: leftT, top: '100%'})
                        $arr.addClass("arrow-bottom")
                    }
                    else if ((feedback.vertical == 'middle' || feedback.vertical == 'top' || feedback.vertical == 'bottom') && feedback.horizontal == 'left' && (options.position == 'right' || options.position == 'left'))
                    {

                        var topT = Math.ceil(feedback.target.top - feedback.element.top + (feedback.target.height / 2) - 8);
                        $arr.css({top: topT, left: '-16px'});
                        $arr.addClass("arrow-right")
                    }
                    else if ((feedback.vertical == 'middle' || feedback.vertical == 'top' || feedback.vertical == 'bottom') && feedback.horizontal == 'right' && (options.position == 'left' || options.position == 'right'))
                    {

                        var topT = Math.ceil(feedback.target.top - feedback.element.top + (feedback.target.height / 2) - 8);
                        $arr.css({top: topT, left: '100%'});
                        $arr.addClass("arrow-left")
                    }
                    else
                    {
                        $arr.hide();
                    }

                }
            })
        },
        _isOpen: function ()
        {
            if (this.$widget.is(":visible"))
            {
                return true;
            }
            return false;
        },
        _open: function (e)
        {
            var options = this.options, selfClass = this;

            if (this.$widget == null)
                return false;

            if (this._isOpen())
                return false;

            this._setSize();
            this.$widget.hide();
            $.when(selfClass.$widget.show(options.show))
                    .done(function () {

                        var isLoad = options.loadone ? (selfClass.loadCount == 0 ? true : false) : true;
                        var url = null;
                        if(typeof options.url == 'function'){
                            url = options.url.call(this)
                        }else{
                            url = options.url
                        }
                       
                        if (url !== null && isLoad)
                        {
                            var content = selfClass.$widget.find('.maze-popover-content');
                            content.html('');
                            content.addClass('maze-popover-load');
                            selfClass.query = $.ajax({
                                url: url,
                                type: 'GET',
                                dataType: options.dataType,
                                cache: false,
                                success: function (data) {
                                    selfClass.query = null;
                                    content.removeClass('maze-popover-load');
                                    content.html(options.filter(data));

                                    selfClass.$element.triggerHandler('load.mazepopover');
                                    if (options.loadone)
                                        selfClass.loadCount = 1;
                                },
                                error: function (type) {
                                    if (options.loadone && type.statusText !== 'abort')
                                        selfClass.loadCount = 1;
                                    content.removeClass('maze-popover-load');
                                    selfClass.query = null;
                                }
                            });

                        }
                        selfClass.$element.attr('data-open-popover', 1);
                        options.onAfterOpen.call(selfClass.$element);
                        selfClass.$element.triggerHandler('open.mazepopover');

                    })

        },
        _close: function ()
        {
            var options = this.options, selfClass = this;

            if (this.$widget == null)
                return false;

            if (!this._isOpen())
                return false;

            if (!options.onBeforeClose.call(this.$element))
            {
                return false;
            }


            if (this.query !== null)
            {
                this.query.abort();
            }

            $.when(selfClass.$widget.hide(options.hide))
                    .done(function () {
                        selfClass.$element.removeAttr('data-open-popover');
                        options.onAfterClose.call(selfClass.$element);
                        selfClass.$element.triggerHandler('close.mazepopover');
                    })

        },
        _setTrigger: function ()
        {
            var options = this.options, selfClass = this, timerOver;


            if (options.trigger == 'toggle')
            {
                this.$element.bind('click', function (e) {
                    if ($(this).attr('data-open-popover'))
                    {
                        selfClass._close();
                    }
                    else
                    {
                        selfClass._open();
                    }
                })

            }
            else if (options.trigger == 'click')
            {
                this.$element.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    selfClass._open();
                })

                if (this.$widget !== null) {
                    this.$widget.bind('click', function (e) {
                        e.stopPropagation();
                    });
                }

                $(document).bind('click', function () {
                    selfClass._close();
                })
            }
            else if (options.trigger == 'hover')
            {
                this.$element.bind('mouseover mouseout', function (e) {
                    e.stopPropagation();
                    if (e.type == 'mouseover')
                    {
                        var $self = this;
                        timerOver = setTimeout(function () {
                            selfClass._open(e);
                        }, options.delayHover)
                    }
                    else
                    {
                        clearTimeout(timerOver);
                    }
                })
                if (this.$widget !== null) {
                    this.$widget.bind('mouseover mouseout', function (e) {
                        e.stopPropagation();
                    });
                }
                $(document).bind('mouseover mouseout', function () {
                    selfClass._close();
                })
            }

        },
        _destroy: function ()
        {
            this.$widget.remove();
            this.$widget = null;
        }

    }

    $.fn.mazePopover = function (options) {
        var arg = arguments;

        var instanceSingl = $(this).eq(0).data('mazePopover');
        if (instanceSingl)
        {
            if (typeof arg[0] == "string" && arg[0] == "widget")
            {
                return instanceSingl.$widget;
            }
        }

        return this.each(function () {

            $this = $(this);

            var instance = $this.data('mazePopover');

            if (instance)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instance.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "open")
                {
                    instance._open();
                }
                if (typeof arg[0] == "string" && arg[0] == "close")
                {
                    instance._close();
                }
                if (typeof arg[0] == "string" && arg[0] == "destroy")
                {
                    instance._destroy();
                }
                if (typeof arg[0] == "string" && arg[0] == "enable")
                {
                    instance._create();
                }
            }
            else
            {
                instance = new MazePopover(this, options);
                $this.data('mazePopover', instance);
            }
        })
    }

    $.fn.mazePopover.defaults = {
        trigger: 'hover',
        url: null,
        dataType: 'json',
        loadone: true,
        filter: function (data) {
            return data
        },
        title: null,
        content: $.noop,
        show: null,
        hide: null,
        distance: 0,
        onAfterOpen: function () {
        },
        onBeforeClose: function () {
            return true
        },
        onAfterClose: function () {
        },
        classBlock: 'default',
        delayHover: 400,
        height: 'auto',
        width: 'auto',
        buttons: [],
        position: "top",
        appendTo: 'body'
    }

})(jQuery);

