/*
 автор: Николай Константинович Бугаев, нечего сюда смотреть, и заимствовать  мои коды
 by Nicholas Bugaev, nothing to see here, and borrow my codes
 */
(function ($) { // модуль

    function PreloaderContent(elem, options)
    {
        var self = this;
        this.$element = $(elem);
        this.$dialog = null;
        this.count = 0;
        this.$overlay = null;

        this.options = $.extend({}, $.fn.preloaderContent.defaults, options || {});

        this.createDialog();

        this.init();

    }
    PreloaderContent.prototype = {
        constructor: PreloaderContent,
        init: function ()
        {

            var self = this;

            $(window).bind('resize scroll', function () {
                if (self.isOpen())
                {
                    self.setPosition();
                    self.showOverlay();
                }
            })
        },
        createDialog: function ()
        {
            var options = this.options, self = this;
            var dialog = '<div class="preloader-content-dialog"></div>';

            this.$dialog = $(dialog).hide().appendTo(options.appedTo)
        },
        createRow: function ()
        {
            var self = this, options = this.options;

            this.count++;

            var row = '<div class="preloader-content-row-{N}">'
                    + '<div class="preloader-content-title">{TITLE}</div>'
                    + '<div class="preloader-content-progress">'
                    + '<div class="preloader-content-progress-bar"></div>'
                    + '</div></div>';

            row = row.replace(/{N}/, self.count)
                    .replace(/{TITLE}/, options.title);

            this.$dialog.prepend(row);

            return  self.count;
        },
        isOpen: function ()
        {
            if (this.$dialog.is(":visible"))
                return true;
            return false;
        },
        setPosition: function ()
        {
            var options = this.options, position = options.position,
                    self = this
            myAt = [],
                    offset = [0, 0];

            if (typeof position == "function")
            {
                position = options.position.call(self.$element);
            }

            if (position)
            {
                if (typeof position === "string" || (typeof position === "object" && "0" in position))
                {
                    myAt = position.split ? position.split(" ") : [position[0], position[1]];
                    if (myAt.length === 1)
                    {
                        myAt[1] = myAt[0];
                    }

                    $.each(["left", "top"], function (i, offsetPosition) {
                        if (+myAt[ i ] === myAt[ i ])
                        {
                            offset[ i ] = myAt[ i ];
                            myAt[ i ] = offsetPosition;
                        }
                    });

                }
            }

            this.$dialog.position({
                my: myAt[0] + (offset[0] < 0 ? offset[0] : "+" + offset[0]) + " " +
                        myAt[1] + (offset[1] < 0 ? offset[1] : "+" + offset[1]),
                at: myAt.join(" "),
                of: self.$element,
                collision: "fit",
                using: function (pos) {
                    var topOffset = $(this).css(pos).offset().top;
                    if (topOffset < 0) {
                        $(this).css("top", pos.top - topOffset);
                    }
                }
            })

        },
        open: function ()
        {
            var self = this, options = this.options, def = $.Deferred();
            
            if (this.isOpen())
                return def.resolve();
            
            this.$dialog.show();
            this.setPosition();
            this.$dialog.hide();

            if (typeof options.show == "string" && options.show !== "")
            {
                this.$dialog.stop().effect(options.show, {mode: "show"}, this.options.speed, def.resolve)
            }
            else if (typeof this.options.show == "function")
            {
                this.options.stop().show.call(this.$dialog, this, def.resolve);
            }
            else
            {
                this.$dialog.stop().show(1, def.resolve);
            }
            this.showOverlay();
            def.done(function () {

                options.onOpen.call(self.$element);
            })
        },
        deletRow: function (id)
        {
            var self = this;

            function remove(id)
            {
                if (self.$dialog.find('.preloader-content-row-' + id).is('.preloader-content-row-' + id))
                {
                    self.$dialog.find('.preloader-content-row-' + id).remove();
                    self.count--;
                }
            }
            if (this.count == 1)
            {
                this.close().done(function () {
                    remove(id);
                })
                return false
            }

            remove(id)

        },
        close: function ()
        {
            

            var self = this, options = this.options, def = $.Deferred();

            if (!this.isOpen())
                return def.resolve();
            
            if (typeof options.hide == "string" && options.hide !== "")
            {
                this.$dialog.stop().effect(options.hide, {mode: "hide"}, this.options.speed, def.resolve)
            }
            else if (typeof this.options.hide == "function")
            {
                this.options.stop().hide.call(this.$dialog, this, def.resolve);
            }
            else
            {
                this.$dialog.stop().hide(1, def.resolve);
            }
            def.done(function () {
                if (self.$overlay !== null)
                {
                    self.$overlay.fadeOut(options.speedOverlay);
                }
                options.onClose.call(self.$element);
            })
            return def;
        },
        showOverlay: function ()
        {
            var options = this.options, self = this;

            if (!options.showOverlay)
                return false;

            if (this.$overlay == null)
            {
                this.$overlay = $('<div>').addClass('preloader-content-overlay');
                this.$overlay.hide().appendTo(options.appedTo);
            }

            if (options.showOverlay)
            {
                this.$overlay
                        .css({
                            top: self.$element.offset().top,
                            left: self.$element.offset().left,
                            width: self.$element.width(),
                            height: self.$element.height()
                        })

                if (this.$overlay.is(":visible"))
                    return true;

                this.$overlay.fadeIn(options.speedOverlay);
            }
        },
        refreshSettings: function (options)
        {
            var self = this;
            this.options = $.extend({}, self.options, options || {});
        },
        update: function ()
        {
            if (this.isOpen())
            {
                this.setPosition();
            }
        }

    }

    $.fn.preloaderContent = function (options) {

        var arg = arguments;
        var instants = $(this).eq(0).data('preloaderContent');
        var $this = $(this).eq(0);

        if (instants)
        {
            if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
            {
                instants.options[arg[1]] = arg[2];
            }
            else if (typeof arg[0] == "string" && arg[0] == "optionsRefresh" && typeof arg[1] == "object")
            {
                instants.refreshSettings(arg[1]);
            }
            else if (typeof arg[0] == "string" && arg[0] == "close" && 1 in arg)
            {

                instants.deletRow(arg[1]);
                instants.update();
            }
            else if (typeof arg[0] == "string" && arg[0] == "update")
            {
                instants.update();
            }
            else if (typeof arg[0] == "string" && arg[0] == "open")
            {
                instants.open();
                var id = instants.createRow();
                instants.update();
                return id;
            }


        }
        else
        {
            instants = new PreloaderContent(this, options);
            $this.data('preloaderContent', instants);
        }

    }

    $.fn.preloaderContent.defaults = {
        title: "Подождите идет загрузка...",
        appedTo: 'body',
        classPreload: '',
        showOverlay: true,
        position: 'center',
        show: "fade",
        hide: "fade",
        speed: 200,
        speedOverlay: 100,
        onOpen: $.noop,
        onClose: $.noop

    }


})(jQuery);
