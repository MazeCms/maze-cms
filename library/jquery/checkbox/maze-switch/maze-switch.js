/*
 * Радио - чекбокс переключатель
 ********************************
 * События
 ********************************
 * checked.mazeSwitch - срабатывает всегда при выделении 
 *	unChecked.mazeSwitch - срабатывает всегда при  снятии выделения 
 *	inchange.mazeSwitch - при клике по ползуну и выделении эелемнта
 * unchange.mazeSwitch -  при клике по ползуну и снятии выделения 
 * change - оригинальное событие change
 *********************************
 * Методы
 *********************************
 * options -  настройки
 *	unChecked - снять выделение
 *	checked -  выделить
 *	toggle - перелючить включено - выключено
 */
(function ($) {

    function MazeSwitch(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.mazeSwitch.defaults, options || {});
        this.$widget = null;
        this.$grip = null;
        this.$strip = null;
        this.$track = null;
        this.init();
    }

    MazeSwitch.prototype = {
        constructor: MazeSwitch,
        init: function ()
        {
            this.createSwitch();
            if (this.$element.attr('checked') == "checked")
            {
                this.checked();
            }
            else
            {
                this.unchecked();

            }
            var selfClass = this;

            this.$grip.click(function () {
                selfClass.change();
            })
            this.move();

        },
        createSwitch: function ()
        {
            var selfClass = this, options = this.options;

            this.$element.hide().wrap($('<div>').addClass('maze-switch-wrapper' + (options.class_switch ? ' ' + options.class_switch : '')));
            this.$widget = this.$element.parent('.maze-switch-wrapper')
            this.$widget.append('<div class="maze-switch-track"><div class="maze-switch-bg"></div></div><div class="maze-switch-grip"></div>');

            this.$grip = this.$widget.find('.maze-switch-grip');
            this.$strip = this.$widget.find('.maze-switch-bg');
            this.$track = this.$widget.find('.maze-switch-track');
            if (this.$element.attr('onchange'))
            {

                this.$element.bind('change', function (e) {
                    eval(selfClass.$element.attr('onchange'))
                })
            }
        },
        checked: function (callbackUser)
        {

            var options = this.options, selfClass = this;
            var stepMove = Math.floor(this.$track.width() - (this.$grip.width() / 2));
            function callback() {
                selfClass.options.onChecked.call(selfClass.$element);
                if ($.isFunction(callbackUser))
                    callbackUser();
                selfClass.$element.trigger('checked.mazeSwitch');
            }

            this.$element.attr('checked', true);
            this.$strip.stop().animate({'margin-left': 0}, options.speed);
            this.$grip.stop().animate({'left': stepMove}, options.speed, callback);

            if (this.$element.is(":radio"))
            {
                var $radio = $('input:radio[name=' + selfClass.$element.attr('name') + ']').not(selfClass.$element);
                $radio.each(function () {
                    if ($(this).data('mazeSwitch'))
                    {
                        $(this).mazeSwitch('unChecked');
                    }
                    else
                    {
                        $(this).removeAttr('checked');
                    }
                })

            }
        },
        unchecked: function (callbackUser)
        {
            if (!this.$element.attr('checked'))
                return false;
           
            var options = this.options, selfClass = this;
            var stepBg = this.$strip.width() / 2;
            function callback() {
                selfClass.options.onUnChecked.call(selfClass.$element);
                if ($.isFunction(callbackUser))
                    callbackUser();
                selfClass.$element.trigger('unChecked.mazeSwitch');
            }
            this.$element.removeAttr('checked');
            this.$strip.stop().animate({'margin-left': '-' + stepBg + 'px'}, options.speed);
            this.$grip.stop().animate({'left': 0}, options.speed, callback);
        },
        change: function ()
        {
            var selfClass = this;
            if (this.$element.attr('checked'))
            {
                this.unchecked(function () {
                    selfClass.$element.trigger('unchange.mazeSwitch');
                    selfClass.$element.trigger('change');
                });
            }
            else
            {
                this.checked(function () {
                    selfClass.$element.trigger('inchange.mazeSwitch');
                    selfClass.$element.trigger('change');
                });
            }
        },
        move: function ()
        {
            if (!('draggable' in jQuery.fn && this.options.draggable))
                return false;

            var selfClass = this;
            this.$grip.draggable({
                axis: 'x',
                containment: 'parent',
                drag: function (e, ui) {
                    var stepBg = (selfClass.$strip.width() / 2) - ui.position.left;
                    selfClass.$strip.stop().css({'margin-left': '-' + stepBg + 'px'});
                }
            })
        }


    }

    $.fn.mazeSwitch = function (options) {
        var arg = arguments;

        return this.each(function () {
            var $this = $(this);

            var instants = $this.data('mazeSwitch');

            if (instants)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instants.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "unChecked")
                {
                    instants.unchecked();
                    ;
                }
                if (typeof arg[0] == "string" && arg[0] == "checked")
                {
                    instants.checked();
                }
                if (typeof arg[0] == "string" && arg[0] == "toggle")
                {
                    instants.change();
                }

            }
            else
            {
                instants = new MazeSwitch(this, options);
                $this.data('mazeSwitch', instants);
            }

        })
    }

    $.fn.mazeSwitch.defaults = {
        class_switch: false,
        speed: 300,
        onChecked: $.noop, // срабатывает всегда при выделении 
        onUnChecked: $.noop, // срабатывает всегда при  снятии выделения 
        draggable: true
    }

})(jQuery);
