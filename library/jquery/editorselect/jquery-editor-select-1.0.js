/*
 автор: Николай Константинович Бугаев, нечего сюда смотреть, и заимствовать  мои коды
 by Nicholas Bugaev, nothing to see here, and borrow my codes
 */
(function ($) { // модуль

    function EditSelect(elem, options)
    {
        var self = this;
        this.$element = $(elem);
        this.$parent = this.$element.parent();
        this.$table = null;

        this.options = $.extend({}, $.fn.editSelect.defaults, options || {});
        this.$element.hide();
        this.getOption();
        this.create();

        this.init();

    }
    EditSelect.prototype = {
        constructor: EditSelect,
        init: function ()
        {
            this.setHanlers();
            this.setSort();
            this.setSelected();

        },
        create: function ()
        {
            var options = this.options, self = this;
            var table = '<table class="editor-select-table">';
            table += '<thead><tr><th>{TITLETEXT}</th><th colspan="2">{VALUETEXT}</th></tr></thead>';
            table += '<tbody>{CONTENT}</tbody><tfoot><tr class="editor-st-row-add"><td colspan="3">';
            table += '<a class="editor-st-add"></a></td></tr></tfoot></table>';
            var content = '';

            $.each(options.option, function (i, val) {
                content += self.createOption(val.title, val.value, val.select);
            });


            table = table.replace(/{TITLETEXT}/, options.title_text)
                    .replace(/{VALUETEXT}/, options.value_text)
                    .replace(/{CONTENT}/, content)

            this.$table = $(table);

            this.$parent.append(self.$table);

        },
        getOption: function ()
        {
            var options = this.options;

            if (this.$element.is('select'))
            {
                if (this.$element.find('select > option').is('option'))
                    options.option = Array();

                this.$element.find('option').each(function () {
                    options.option.push({title: $(this).text(), value: $(this).val(), select: $(this).is(':selected')});
                })

            }
        },
        createOption: function (title, value, checked)
        {
            var chekbox = '', celectClass = '';
            var content = '<tr{SELECT}>';
            content += '<td>{TITLE}</td><td>{VALUE}</td>'
                    + '<td class="editor-st-cell-tool">{CHECKBOX} <a class="editor-st-move"></a> <a class="editor-st-edit"> <a class="editor-st-delete"></a></td>'
            content += '</tr>';

            if (this.options.selected)
            {
                chekbox = '<a class="ed-checkbox' + (checked ? ' editor-st-checkbox-select' : ' editor-st-checkbox') + '"></a>';
                celectClass = checked ? ' class="ed-selected"' : '';
            }


            return 	content
                    .replace(/{TITLE}/, title)
                    .replace(/{VALUE}/, value)
                    .replace(/{CHECKBOX}/, chekbox)
                    .replace(/{SELECT}/, celectClass);
        },
        setHanlers: function ()
        {
            var self = this, options = this.options;
            this.$table.delegate('.editor-st-edit', 'click', function () {
                var $row = $(this).closest('tr');
                $(this).toggleClass('editor-st-edit editor-st-save');
                var $title = $row.find('td').eq(0);
                var $value = $row.find('td').eq(1);

                $title.html(self.getInput($.trim($title.text())));
                $value.html(self.getInput($.trim($value.text())));

            })
            this.$table.delegate('.editor-st-save', 'click', function () {
                var $row = $(this).closest('tr');
                $(this).toggleClass('editor-st-edit editor-st-save');
                var $title = $row.find('td').eq(0);
                var $value = $row.find('td').eq(1);

                $title.html($.trim($title.find('input[type=text]').val()));
                $value.html($.trim($value.find('input[type=text]').val()));

            })
            this.$table.delegate('.editor-st-delete', 'click', function () {
                $(this).closest('tr').remove();
            })

            this.$table.find('.editor-st-add').click(function () {
                self.$table.find('tbody').append(self.createOption(options.title_text, options.value_text));
                self.setSort();
            })
        },
        setSort: function ()
        {
            if (!this.options.sortable)
                return false;
            this.$table.find('tbody').sortable({
                axis: 'y',
                handle: $('.editor-st-move'),
                opacity: 0.5,
                cursor: 'move',
                helper: function (e, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });

                    return ui;
                },
                sort: function (e, ui) {
                    ui.placeholder.css({height: ui.helper.height() + 10})
                },
                beforeStop: function (e, ui) {
                    ui.placeholder.css('height', '')
                },
            })
        },
        setSelected: function ()
        {
            if (!this.options.selected)
                return;

            this.$table.delegate('.ed-checkbox', 'click', function () {
                var $row = $(this).closest('tr');
                $(this).toggleClass('editor-st-checkbox editor-st-checkbox-select');
                $row.toggleClass('ed-selected');
            })
        },
        getInput: function (val)
        {
            return '<input type="text" value="{VALUE}"/>'.replace(/{VALUE}/, val);
        },
        getValue: function ()
        {
            var value = Array();
            this.$table.find('tbody > tr').each(function () {
                var $title = $(this).find('td').eq(0);
                var $value = $(this).find('td').eq(1);
                value.push({title: $.trim($title.text()), value: $.trim($value.text()), select: $(this).hasClass('ed-selected')});
            })

            return value;
        },
        refreshSettings: function (options)
        {
            var self = this;
            this.options = $.extend({}, self.options, options || {});
        },
        refresh: function ()
        {
            var options = this.options, self = this;

            this.$table.find('tbody > tr').remove();
            var content = '';
            $.each(options.option, function (i, val) {
                content += self.createOption(val.title, val.value, val.select);
            });
            self.$table.find('tbody').append(content)
            this.setSort();
        }

    }

    $.fn.editSelect = function (options) {

        var arg = arguments;
        if (typeof arg[0] == "string" && arg[0] == "widget")
        {
            var instants = $(this).eq(0).data('editSelect');
            if (!instants)
                return false;
            return instants.$table;
        }
        if (typeof arg[0] == "string" && arg[0] == "value")
        {
            var instants = $(this).eq(0).data('editSelect');
            if (!instants)
                return false;
            return instants.getValue();
        }

        return this.each(function () {
            $this = $(this);
            var instants = $this.data('editSelect');
            if (instants)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instants.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "optionsRefresh" && typeof arg[1] == "object")
                {
                    instants.refreshSettings(arg[1]);
                }
                if (typeof arg[0] == "string" && arg[0] == "refresh")
                {
                    instants.refresh();
                }

            }
            else
            {
                instants = new EditSelect(this, options);
                $this.data('editSelect', instants);
            }
        })

    }

    $.fn.editSelect.defaults = {
        option: [],
        name: 'name',
        selected: true,
        sortable: true,
        title_text: 'Надпись',
        value_text: 'Значение'

    }


})(jQuery);
