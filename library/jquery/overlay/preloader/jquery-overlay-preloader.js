(function ($) {

    $.fn.preloader = function (options) {
        var settings = $.extend({
            textLoad: 'Загрузка...'
        }, options || {});
        
        var $self = $(this);

        function setPosition() {
            var position = $self.position();
             
            $self.find('.maze-form-overlay').css({
                width: $self.outerWidth(),
                height: $self.outerHeight(),
                top: position.top,
                left: position.left});
            
            $self.find('.maze-form-preload').position({
                my: 'center',
                at: 'center',
                of: $self.get(),
                collision: 'flipfit',
            })            
        }
        
        function createPreload()
        {
             return $('<div>').addClass('maze-form-preload')
                    .html('<div class="maze-form-preload-icon"></div><div class="maze-form-preload-text">'+settings.textLoad+'</div>')
                    .appendTo($self)
        }
        
        function createOverlay()
        {
            return $('<div>').addClass('maze-form-overlay').appendTo($self)
        }
        if(typeof options == 'string')
        {
            if(options == 'start')
            {
                if($self.find('.maze-form-preload').is('.maze-form-preload')) return;
                createPreload();
                createOverlay();
                setPosition();
                $self.bind('scroll resize', setPosition);
                $(window).bind('scroll', setPosition)
            }
            else if(options == 'end')
            {
                $self.find('.maze-form-preload').remove();
                $self.find('.maze-form-overlay').remove();
                $self.unbind('scroll resize', setPosition)
                 $(window).unbind('scroll', setPosition)
            }
        }
        return this;
    }

    $.fn.preloaderBtn = function ()
    {
        var $self = $(this);

        if (arguments.length == 1 && typeof arguments[0] == "object")
            var options = arguments[0];
        if (arguments.length == 2 && typeof arguments[1] == "object")
            var options = arguments[1];

        var settings = $.extend({
            calss_preload: 'preload-circul-w'
        }, options || {});

        var pos = $self.css('position');
        $self.css('position', 'relative');
        var original = {
            text: $self.html(),
            width: $self.innerWidth(),
            height: $self.innerHeight()
        }


        function startPreload()
        {
            if ($self.find('.preload-btn').is('.preload-btn'))
                return false;
            $self.attr("disabled", "disabled")
            $('<div>')
                    .css({width: original.width, height: original.height})
                    .addClass("preload-btn " + settings.calss_preload)
                    .appendTo($self)
        }
        function closePreload()
        {
            if (!$self.find('.preload-btn').is('.preload-btn'))
                return false;
            $self.removeAttr("disabled")
            $self.find('.preload-btn').remove();
            $self.css('position', pos);
        }


        if ((arguments.length == 1 || arguments.length == 2) && typeof arguments[0] == "string" && arguments[0] == "start")
        {
            startPreload();
        }
        if ((arguments.length == 1 || arguments.length == 2) && typeof arguments[0] == "string" && arguments[0] == "close")
        {
            closePreload();
        }

        return this;

    }

    $.fn.overlayPreloader = function ()
    {
        var $self = this;

        if (arguments.length == 1 && typeof arguments[0] == "object")
            var options = arguments[0];
        if (arguments.length == 2 && typeof arguments[1] == "object")
            var options = arguments[1];

        var settings = $.extend({
            class_overlay: 'overlay',
            calss_preload: 'preloader-fix',
            overlay: true

        }, options || {});



        function overlay()
        {
            if (!settings.overlay)
                return false;

            if ($self.data("overlay"))
                return $self.data("overlay");
            var $overlay = $('<div class="' + settings.class_overlay + '"></div>');
            $self.data("overlay", $overlay);
            return $overlay;
        }
        function overlayPosition() {
            var position = $self.offset();
            overlay().css({width: $self.outerWidth(),
                height: $self.outerHeight(),
                top: position.top,
                left: position.left});
        }

        function startPreload()
        {
            if (settings.overlay)
                var $overlay = overlay();

            if ($("." + settings.calss_preload).hasClass(settings.calss_preload))
            {
                if (settings.overlay) {
                    $('body').before($overlay);
                    overlayPosition();
                }
                return false;
            }
            if (settings.overlay)
                overlayPosition();
            var $preloader = $('<div class="' + settings.calss_preload + '"><span></span></div>');
            $('body').after($preloader)
            if (settings.overlay)
            {
                $('body').before($overlay);
            }
            $self.data("preloader", $preloader);
            return 	$preloader;
        }

        function closePreload()
        {
            if (settings.overlay && $self.data("overlay"))
                $self.data("overlay").remove().removeData();
            if ($self.data("preloader"))
                $self.data("preloader").remove().removeData();

        }


        if ((arguments.length == 1 || arguments.length == 2) && typeof arguments[0] == "string" && arguments[0] == "start")
        {
            startPreload();
        }
        if ((arguments.length == 1 || arguments.length == 2) && typeof arguments[0] == "string" && arguments[0] == "close")
        {
            closePreload();
        }

        return this;

    }

    $.fn.mazePreload = function (options)
    {

        function MazePreload(elem, options)
        {
            this.$element = $(elem);
            this.options = $.extend({}, $.fn.mazePreload.defaults, options || {});
            this.init();
            this.overlay;
            this.preload;

        }
        MazePreload.prototype = {
            onstructor: MazePreload,
            init: function () {
                var self = this;
                this.preload = $('<div>').addClass('maze-preload-block').addClass(self.options.classPreload);
                this.preload.appendTo((self.options.appendTo == 'self' ? self.$element : self.options.appendTo)).hide();
                this.overlay = $('<div>').addClass('maze-preload-overlay').addClass(self.options.classOverlay);
                this.overlay.appendTo((self.options.appendTo == 'self' ? self.$element : self.options.appendTo)).hide();
            },
            open: function ()
            {
                var self = this;
                this.overlay
                        .show()
                        .css({
                            width: self.$element.outerWidth(true),
                            height: self.$element.outerHeight(true)
                        })
                        .position({
                            my: 'left top',
                            at: 'left top',
                            of: self.$element.get()
                        });

                this.preload.show().position({
                    my: 'center',
                    at: 'center',
                    of: self.$element.get(),
                    collision: 'flip'
                });
            },
            close: function ()
            {
                this.overlay.hide();
                this.preload.hide();
            }

        }


        var arg = arguments;
        return this.each(function () {

            var instance = $(this).eq(0).data('mazePreload');
            var $this = $(this).eq(0);

            if (instance)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instance.options[arg[1]] = arg[2];
                }
                else if (typeof arg[0] == "string" && arg[0] == "close")
                {
                    instance.close();
                }
                else if (typeof arg[0] == "string" && arg[0] == "open")
                {
                    instance.open();
                }
            }
            else
            {
                instance = new MazePreload(this, options);
                $this.data('mazePreload', instance);
            }
        })

    }

    $.fn.mazePreload.defaults = {
        classOverlay: '',
        classPreload: '',
        appendTo: 'self'
    };


})(jQuery);
