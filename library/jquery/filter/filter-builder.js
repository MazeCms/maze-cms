/*
 * События
 * visibleField - скрытие показ параметра фильтра
 *
 * Методы
 * save - сохранить текущий фильтр
 * add - добавить фильтр (вкладку)
 * reset -  сброс параметров формы фильтра	
 * filter -  применить фильтр
 * rename -  переименовать фильтр
 * delete - удалить текущий фильтр
 * toggle -  скрыть показать фильтр
 */
(function ($) {

    function FilterBuilder(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.filterBuilder.defaults, options || {});
        this.$visible = this.$element.find('.filter-form-visible-list');
        this.$form = this.$element.find('form');
        this.init();
    }

    FilterBuilder.prototype = {
        constructor: FilterBuilder,
        init: function ()
        {
            var selfClass = this;
            this.cretateVisible()

            this.$element.find('.filter-form-trigger-add').click(function () {
                selfClass.add();
            })
            this.$form.submit(function () {
                return  selfClass.options.onFilter.call(selfClass.$element, this)
            });

            this.$form.bind('reset', function () {
                selfClass.options.onReset.call(selfClass.$element, this)
            })

            if (('cookie' in jQuery))
            {
                if ($.cookie('filterVisible') == 0) {
                   this.$form.hide();
                }
            }
            
            this.$element.bind('.filter-form-toggle').click(function(e){
               if($(e.target).is('.filter-form-toggle')) selfClass.toggle();
                
            })

        },
        toggle: function () {
            if (!('cookie' in jQuery))
                return false;
            if (this.$form.is(':visible'))
            {
                this.$form.stop().fadeOut(300);
                $.cookie('filterVisible', 0, {expires: 100});
            }
            else
            {
                this.$form.stop().fadeIn(300);
                $.cookie('filterVisible', 1, {expires: 100});
            }

        },
        filter: function () {
            this.$form.submit();
        },
        reset: function () {
            this.$form.trigger('reset');
        },
        add: function () {
            var url = this.$form.attr('action');
            var selfClass = this;
            selfClass.options.beforeLoad.call(selfClass.$element);
            $.post(selfClass.getUrl('add'), selfClass.$form.serialize(), function (data) {
                selfClass.options.afterLoad.call(selfClass.$element);
                var $li = selfClass.$element.find('.filter-form-trigger-add').parent();
                var content = $('<li>').append($('<a>', {href: data.attributes.urlfilter}).text(data.attributes.title));
                $li.before(content);
            }, 'json');
        },
        rename: function () {
            var selfClass = this;
            if (!this.isActive() || this.isNewFilter())
                return;
            var $active = this.$element.find('.filter-form-tabs-link .active')
            if ($active.find('input').is('input'))
                return;
            $active.find('a').hide();
            var textOld = $active.find('a').text();
            var $input = $('<input>', {type: 'text'}).val($active.find('a').text());
            $active.append($input);
            $input.on('focusout keydown', function (e) {
                if (e.type !== 'focusout')
                {
                    if (e.which !== 13)
                        return;
                }
                if ($(this).val() == '')
                    return;
                var textNew = e.type == 'focusout' ? textOld : $(this).val();
                $active.find('a').text(textNew);
                $(this).remove();
                $active.find('a').show();
                if (textOld !== textNew)
                {
                   selfClass.options.beforeLoad.call(selfClass.$element);
                    $.post(selfClass.getUrl('rename'), selfClass.$form.serialize() + '&newname=' + textNew, function (data) {
                         selfClass.options.afterLoad.call(selfClass.$element);
                    }, 'json');
                }
            })
        },
        isActive: function ()
        {
            return  this.$element.find('.filter-form-tabs-link .active').is('.active')
        },
        isNewFilter: function ()
        {
            return this.$form.find('[id*=-filter_id]').val() == '';
        },
        delete: function () {
            var selfClass = this;
            if (!this.isActive())
                return;
            selfClass.options.beforeLoad.call(selfClass.$element);
            $.post(selfClass.getUrl('delete'), selfClass.$form.serialize(), function (data) {
                selfClass.options.afterLoad.call(selfClass.$element);
                var filter_id = selfClass.$form.find('[id*=-filter_id]');
                filter_id.val(null);
                selfClass.$element.find('.filter-form-tabs-link .active').remove();
            }, 'json');
        },
        save: function () {
            var selfClass = this;
            selfClass.options.beforeLoad.call(selfClass.$element);
            $.post(selfClass.getUrl('save'), selfClass.$form.serialize(), function (data) {
                selfClass.options.afterLoad.call(selfClass.$element);
                var filter_id = selfClass.$form.find('[id*=-filter_id]');
                if (selfClass.isNewFilter())
                {
                    filter_id.val(data.attributes.filter_id);
                    var $li = selfClass.$element.find('.filter-form-trigger-add').parent();
                    var content = $('<li>').append($('<a>', {href: data.attributes.urlfilter}).text(data.attributes.title));
                    $li.before(content);
                }
            }, 'json');
        },
        getUrl: function (cmd)
        {
            var url = this.$form.attr('action');
            var selfClass = this;
            url += url.indexOf('?') !== -1 ? '&' : '?';
            url += 'filtercmd=' + cmd;
            if (typeof selfClass.options.ajaxParam == 'object')
            {
                url += '&' + $.param(selfClass.options.ajaxParam);
            }
            return url;
        },
        cretateVisible: function ()
        {
            var selfClass = this;

            $.each(selfClass.options.elemFilter, function (i, val) {
                if (!val.checked)
                    $('.' + val.value).hide();
            })
            this.$visible.mazeContext({
                data: selfClass.options.elemFilter,
                appendTo: selfClass.$form,
                checkbox: {
                    real_checkbox: true
                },
                position: {
                    my: 'left top',
                    at: 'left bottom'
                },
                onAfterOpen: function () {
                    this.addClass('active')
                },
                onAfterClose: function () {
                    this.removeClass('active')
                },
                onAfterInit: function () {
                    this.bind('checked.mazecontext unchecked.mazecontext', function (e, obj) {

                        var method = e.type == 'checked' ? 'show' : 'hide';
                        $('.' + obj.obj.value)[method]();

                        $.post(selfClass.getUrl('visible'), selfClass.$form.serialize(), function (data) {

                        }, 'json');

                        if (typeof selfClass.options.onVisible == 'function')
                            selfClass.options.onVisible.call(this, e, obj)
                        selfClass.$element.trigger('visibleField');
                    })
                }

            })
        }

    }

    $.fn.filterBuilder = function (options) {
        var arg = arguments;

        var $this = $(this);

        var instance = $this.data('filterBuilder');

        if (instance)
        {
            if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
            {
                instance.options[arg[1]] = arg[2];
            }
            if (typeof arg[0] == "string" && arg[0] == "save")
            {
                instance.save();
            }
            if (typeof arg[0] == "string" && arg[0] == "add")
            {
                instance.add();
            }
            if (typeof arg[0] == "string" && arg[0] == "reset")
            {
                instance.reset();
            }
            if (typeof arg[0] == "string" && arg[0] == "filter")
            {
                instance.filter();
            }
            if (typeof arg[0] == "string" && arg[0] == "delete")
            {
                instance.delete();
            }
            if (typeof arg[0] == "string" && arg[0] == "rename")
            {
                instance.rename();
            }
            if (typeof arg[0] == "string" && arg[0] == "toggle")
            {
                instance.toggle();
            }

        }
        else
        {
            instance = new FilterBuilder(this, options);
            $this.data('filterBuilder', instance);
        }

        return this;
    }

    $.fn.filterBuilder.defaults = {
        // список для контесного меню параметры фильтра
        elemFilter: {},
        // событие фильтрации
        onFilter: function () {
            return false;
        },
        beforeLoad:function(){
            this.find('form').addClass('filter-load')
        },
        afterLoad:function(){
             this.find('form').removeClass('filter-load')
        },
        // событие сброса фильтра 
        onReset: $.noop,
        // дополнительные параметры выполняемые при ajax запросе
        ajaxParam: {clear: 'ajax'}
    }

})(jQuery);
