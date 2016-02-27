/*
 * События 
 **********************************************
 * Ядро
 ----------------------------------------------
 * beforeGetContent.mazegrid - перед отресовкой контентной части таблицы (до проверки режима options.mode = "default"....)
 *		@params - event, data(object) - объект визитер
 *	afterGetContent.mazegrid - после добавления таблицы в контент
 * afterSortFild.mazegrid - после сортировки поля таблицы
 *		@params obj(object)  - elem(jquery) - целевой элемент, fild(array) -  отсортированный поля
 * hideFild.mazegrid, showFild.mazegrid - скрытие показ столбца 
 *		@params elem(jquery) - целевой элемент
 */
(function ($) {

    $.mazeGrid = {
        plugins: Array(),
        addPlugin: function (name, object)
        {
            if (name in $.mazeGrid.plugins)
            {
                if ('version' in object)
                {
                    if ($.mazeGrid.plugins[name].prototype.version > object.version)
                    {
                        return false;
                    }
                }
            }

            $.mazeGrid.plugins[name] = function () {
                AbstractGrid.apply(this, arguments)
            };
            $.mazeGrid.plugins[name].prototype = this.createObject(AbstractGrid.prototype);
            $.mazeGrid.plugins[name].prototype.constructor = $.mazeGrid.plugins[name];

            $.extend($.mazeGrid.plugins[name].prototype, object);
        },
        getPlugins: function (name, method)
        {
            var arg = arguments;

            if (name in $.mazeGrid.plugins)
            {
                if (method in $.mazeGrid.plugins[name].prototype)
                {
                    $.mazeGrid.plugins[name].prototype[method].apply($.mazeGrid.plugins[name].prototype, Array.prototype.slice.call(arg, 2))
                }
            }
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
        },
        getInstance: function (elem, options)
        {
            options = $.extend({}, $.fn.mazeGrid.defaults, options || {});

            var plugin = Array();

            if (typeof options.plugins == "string" && options.plugins !== '')
            {
                var parsPlug = options.plugins.split(',');
                plugin = $.map(parsPlug, function (val) {
                    return $.trim(val);
                })
                if ($.inArray('core', plugin) == -1)
                {
                    plugin.unshift('core');
                }
                else
                {
                    var index = $.inArray('core', plugin);
                    plugin.splice(index, 1);
                    plugin.unshift('core');
                }
            }
            else
            {
                plugin.push('core');
            }

            var plugObject = $.map(plugin, function (val) {
                if (val in $.mazeGrid.plugins)
                {
                    return {name: val, obj: new $.mazeGrid.plugins[val](elem, val in options ? options[val] : null, options)};
                }
            })

            return plugObject;
        }
    }

    function AbstractGrid(element, options, alloptions)
    {
        var selfClass = this;
        this.$element = $(element);
        this.alloptions = alloptions;
        this.options = $.extend({}, selfClass.defaults, options || {});
    }

    AbstractGrid.prototype = {
        constructor: AbstractGrid,
        defaults: {},
        _trigger: function (eventTypee, data) {

            this.$element.triggerHandler(eventTypee + '.mazegrid', data);

        },
        getTotal: function () {
            if (this.alloptions.datatype == 'local')
            {
                this.alloptions.total = this.alloptions.data.length;
            }
            return this.alloptions.total;
        },
        getData: function () {
            if (this.alloptions.datatype == "local")
            {

                var total = this.getTotal();
                var first = 1, last = this.alloptions.page * this.alloptions.rowNum;
                if (this.alloptions.page !== 1)
                {
                    first = (this.alloptions.page - 1) * this.alloptions.rowNum
                }

                if (last > total)
                    last = total;
                var data = this.getSort();
                var res = data.slice(first == 1 ? first - 1 : first, last);

                return this.getGroup(res);
            }
            var selfClass = this;
            if (!$.isArray(selfClass.alloptions.data))
            {
                selfClass.alloptions.data = [];
            }
            return this.getGroup(this.alloptions.data);
        },
        dataMerge: function (data)
        {
            var selfClass = this;
            $.each(data, function (i, obj) {
                selfClass.alloptions.data.push(obj);
            })

        },
        getLength: function ()
        {
            var selfClass = this;
            if (!(0 in selfClass.alloptions.data))
                return null;
            var count = 0;
            $.each(selfClass.alloptions.data[0], function (i) {
                count++;
            })
            return count - selfClass.alloptions.colHide.length;
        },
        getSort: function ()
        {
            var options = this.alloptions;
            if (!options.sortfild)
                return options.data;


            var params = this.gerModelName(options.sortfild);
            var result = null;
            switch (params.sorttype)
            {
                case 'int':
                    result = options.data.sort(function (a, b) {
                        if (options.sortorder == 'asc')
                        {
                            return a[options.sortfild] - b[options.sortfild];
                        }
                        else
                        {
                            return b[options.sortfild] - a[options.sortfild];
                        }
                    })
                    break;

                default:

                    result = options.data.sort(function (a, b) {
                        if (options.sortorder == 'asc')
                        {
                            if (a[options.sortfild] < b[options.sortfild])
                                return -1;
                            if (a[options.sortfild] > b[options.sortfild])
                                return 1;
                            return 0;
                        }
                        else
                        {
                            if (a[options.sortfild] > b[options.sortfild])
                                return -1;
                            if (a[options.sortfild] < b[options.sortfild])
                                return 1;
                            return 0;
                        }
                    })
                    break;
            }

            return result;
        },
        getGroup: function (data)
        {
            var options = this.alloptions;

            if (!('groupField' in options.groupingView) || !options.grouping || !('0' in options.groupingView.groupField))
                return data;
            var result = {};
            var selfClass = this;
            var titleFild = $.map(options.groupingView.groupField, function (val) {
                var title = null;
                $.each(options.colModel, function (i, value) {
                    if (value.name == val)
                    {
                        title = value.title;
                        return false;
                    }
                })
                return title ? title : val;
            })

            $.each(data, function (i, obj) {

                var key = $.map(options.groupingView.groupField, function (val) {
                    if (val in obj)
                        return obj[val];
                })

                var keyStr = key.join('-');

                var name = keyStr;
                if (typeof options.groupingView.groupName == 'function')
                {
                    name = options.groupingView.groupName.call(selfClass.$element, key, options.groupingView.groupField, titleFild);
                }


                if (!(keyStr in result))
                {
                    result[keyStr] = {
                        namegroup: name,
                        data: Array()
                    }
                }
                result[keyStr].data.push(obj);
            })

            return 		result;

        },
        gerModelName: function (name)
        {
            var selfClass = this;

            for (var i = 0; i < selfClass.alloptions.colModel.length; i++)
            {
                if (selfClass.alloptions.colModel[i].name == name)
                {
                    return selfClass.alloptions.colModel[i];
                    break;
                }
            }
            return false;
        },
        gerModelIndex: function (index)
        {
            var selfClass = this;

            for (var i = 0; i < selfClass.alloptions.colModel.length; i++)
            {
                if (!('index' in selfClass.alloptions.colModel[i]))
                    continue;

                if (selfClass.alloptions.colModel[i].index == index)
                {
                    return selfClass.alloptions.colModel[i];
                    break;
                }
            }
            return false;
        },
        getSizePage: function ()
        {
            var total = this.getTotal(), selfClass = this;
            return 	Math.ceil(total / selfClass.alloptions.rowNum)
        },
        _sortCol: function ()
        {
            var selfClass = this;
            if (this.alloptions.colModel.length !== this.alloptions.sortCol.length)
                return false;
            var copy = Array();
            $.each(selfClass.alloptions.sortCol, function (i, val) {
                var obj = selfClass.gerModelName(val);
                if (obj)
                {
                    copy.push(obj);
                }
            })

            this.alloptions.colModel = copy;

        },
        preloader: function (mode)
        {
            var selfClass = this;

            if (this.alloptions.overlay && !this.$element.find('.maze-grid-overlay').is('.maze-grid-overlay'))
            {
                this.$element.append($('<div>').addClass('maze-grid-overlay'))
            }
            if (!this.alloptions.overlay && this.$element.find('.maze-grid-overlay').is('.maze-grid-overlay'))
            {
                this.$element.find('.maze-grid-overlay').remove();
            }

            if (!this.$element.find('.maze-grid-preload').is('.maze-grid-preload'))
            {
                this.$element.append(
                        '<div class="maze-grid-preload"><span class="maze-grid-preload-icon"></span><span class="maze-grid-preload-text">{TEXT}</span></div>'
                        .replace(/{TEXT}/, selfClass.alloptions.textpreloader)
                        )

                $(window).resize(function () {
                    if (selfClass.alloptions.updateReize && selfClass.$element.find('.maze-grid-preload').is(':visible'))
                    {
                        selfClass.preloader('open');
                    }
                })
            }

            switch (mode)
            {
                case	"open":
                    if (this.$element.find('.maze-grid-overlay').is('.maze-grid-overlay'))
                    {
                        this.$element.find('.maze-grid-overlay').css({
                            width: selfClass.$element.width(),
                            height: selfClass.$element.height(),
                        })
                                .show();
                        this.$element.find('.maze-grid-preload').show().position({
                            my: 'center',
                            at: 'center',
                            of: selfClass.$element,
                            collision: 'flipfit',
                        })
                    }
                    break;

                case "close":
                    if (this.$element.find('.maze-grid-overlay').is('.maze-grid-overlay'))
                    {
                        this.$element.find('.maze-grid-overlay').hide();
                    }
                    this.$element.find('.maze-grid-preload').hide();
                    break;
            }

        },
        loadData: function (param)
        {
            if (this.alloptions.url == '')
                return false;
            var selfClass = this;
            var data = {};

            data[selfClass.alloptions.pagename] = selfClass.alloptions.page;
            data[selfClass.alloptions.fildname] = selfClass.alloptions.sortfild;
            data[selfClass.alloptions.ordername] = selfClass.alloptions.sortorder;
            data[selfClass.alloptions.rowname] = selfClass.alloptions.rowNum;

            this.send($.extend(data, selfClass.alloptions.params, param || {}), function (req) {
                selfClass._trigger('beforeLoadData', req)
                var data = selfClass.alloptions.filterData(req);
                selfClass.alloptions = $.extend(selfClass.alloptions, data || {});

                selfClass._createInfo();
                selfClass._getPage();
                selfClass._getContent();
                selfClass.updatePaginator();
            })

        },
        send: function ()
        {
            var selfClass = this, url, param, callback;

            $.each(arguments, function (i, prop) {
                switch (typeof prop)
                {
                    case 'string':
                        url = prop;
                        break;

                    case 'object':
                        param = prop;
                        break;

                    case 'function':
                        callback = prop;
                        break;
                }
            })
            url = url ? url : this.alloptions.url;

            $.ajax({
                url: url,
                type: 'POST',
                data: param,
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    selfClass.preloader('open')
                },
                success: function (data) {
                    selfClass.preloader('close');
                    if (typeof callback == 'function')
                    {
                        callback.call(selfClass.$element, data);
                    }
                },
                error: function ()
                {
                    selfClass.preloader('close');
                }
            })
        },
        _create: $.noop,
        _init: $.noop,
        _update: $.noop
    }

    $.fn.mazeGrid = function (options) {
        var arg = arguments;


        var instance = $(this).eq(0).data('mazeGrid'); // массив объектов
        if (typeof options == 'string' && options == 'getoptionsall')
        {
            var result;
            $.each(instance, function (i, val)
            {
                if (val.name == 'core')
                {
                    result = val.obj.alloptions;
                    return false;
                }
            })

            return result;
        }
        if (instance)
        {
            if (!(0 in arg))
                return true;
            var method = arg[0];
            var plug;
            var pars = arg[0].split("::");

            if (0 in pars)
                plug = pars[0];

            if (1 in pars)
                method = pars[1];



            $.each(instance, function (i, val) {

                if (pars.length == 2)
                {

                    if (val.name == plug && method in val.obj)
                    {
                        val.obj[method].apply(val.obj, Array.prototype.slice.call(arg, 1));
                    }
                    return true;
                }
                if (method in val.obj)
                {
                    val.obj[method].apply(val.obj, Array.prototype.slice.call(arg, 1));
                }

            })

        }
        else
        {

            instance = $.mazeGrid.getInstance($(this).get(0), options);
            $.each(instance, function (i, val) {
                val.obj._create();
                val.obj._init();
            })

            $(this).eq(0).data('mazeGrid', instance);

        }

        return this;
    }

    $.fn.mazeGrid.defaults = {
        data: [],
        url: "",
        overlay: true,
        datatype: "local", // local | server - тип данных
        height: 'auto',
        mode: 'default',
        rowNum: 10, // число записей на страце
        rowList: [5, 10, 20, 30, 400],
        params: {},
        total: 0,
        page: 1,
        sortfild: null,
        sortorder: 'asc',
        colModel: [],
        filterData: function (data) {
            return data
        },
        showBottomPanel: true,
        labelinfo: "Показано {FIRST} - {LAST} из {SIZE}",
        labelrow: "Записей",
        labelpage: "из {SIZE}",
        labelmenu: {
            textasc: 'По возрастанию',
            textdesc: 'По убыванию',
            textgroup: 'Группировать'
        },
        textpreloader: "Загрузка...",
        pagename: "page",
        rowname: "pagesize",
        ordername: "order",
        fildname: "list",
        minWidth: 600,
        groupingView: {},
        grouping: true,
        opengroup: true,
        updateReize: true,
        colHide: [],
        sortCol: [],
        sorttableCol: true,
        buttons: [
            {
                btnClass: 'maze-grid-btn-update',
                icon: '',
                actions: function () {
                    this.mazeGrid('update')
                }
            }
        ],
        plugins: 'checkbox, core, tree, movesort, contextmenu, tooltip'

    }

})(jQuery);

(function ($) {

    $.mazeGrid.addPlugin("core", {
        version: 1.0,
        _create: function ()
        {
            this._trigger('beforeCreateCore');

            var html = '<div class="maze-grid-wrapp"><div class="maze-grid-header"><table cellspacing="0" cellpadding="0" border="0"><thead><tr></tr></thead></table></div>'
                    + '<div class="maze-grid-content"></div>'
                    + '</div>';
            if (this.alloptions.showBottomPanel) {
                html += '<div class="maze-grid-footer"><table cellspacing="0" cellpadding="0" border="0">'
                        + '<tbody><tr><td class="maze-grid-footer-left"><div class="maze-grid-footer-innert"></div></td>'
                        + '<td class="maze-grid-footer-center"><div class="maze-grid-footer-innert"><span class="maze-grid-btn maze-grid-page-first"></span><span class="maze-grid-btn maze-grid-page-previous"></span><input class="maze-grid-page-number" value="1" type="text" /> <span class="maze-grid-page-size"> </span><span class="maze-grid-btn maze-grid-page-next"></span><span class="maze-grid-btn maze-grid-page-last"></span></div></td>'
                        + '<td class="maze-grid-footer-right"><div class="maze-grid-footer-innert"><span class="maze-grid-footer-list-page"></span><span class="maze-grid-footer-info-page"></span></div></td></tr></tbody></table>'
            }
            html += '</div>';
            this.$element
                    .addClass('maze-grid')
                    .append(html);

            var selfClass = this;
            this.$element.find('.maze-grid-wrapp').mCustomScrollbar({
                axis: "x",
                theme: "3d-dark",
                callbacks: {
                    onScroll: function () {
                        selfClass.resizeCol()
                    }
                }
            });
        },
        _init: function () {
            this._sortCol();
            this._createHeader();
            this._hoverHeader();
            this._createlist();
            this._createInfo();
            this._getPage();
            if (this.alloptions.datatype == "local")
            {
                this._getContent();
            }
            this.getPaginator();

            var selfClass = this;
            this.selectSortFild();


            selfClass.$element.find('.maze-grid-header .maze-grid-sorttable').click(function () {
                var name = $(this).attr('data-grid-fild');
                selfClass.checkSortFild(name);
            })
            $(window).resize(function () {
                if (selfClass.alloptions.updateReize)
                {

                    selfClass.setSize('resize');
                }

            })


            this.setMenuFild();

            this.resizeCol();
            this.sortTableCol();
            if (selfClass.alloptions.datatype == "server")
            {
                this.loadData(selfClass.alloptions.params);
            }
            this._setButton();

            if ($.isArray(selfClass.alloptions.colHide))
            {
                this.hideShowCol(this.alloptions.colHide, 'hide');
            }

            this.setSize();
        },
        _setButton: function ()
        {
            var selfClass = this;
            var posFot = this.$element.find('.maze-grid-footer-left .maze-grid-footer-innert')
            var defaultBtn = {
                btnClass: '',
                icon: '',
                actions: $.noop
            };
            $.each(selfClass.alloptions.buttons, function (i, btn) {
                var options = $.extend(defaultBtn, btn)
                var btnS = $('<span>').addClass('maze-grid-btn ' + options.btnClass);
                if (options.icon !== '')
                {
                    btnS.css('background-image', 'url("' + btnS.icon + '")')
                }
                btnS.click(function () {
                    options.actions.call(selfClass.$element, btnS);
                })
                posFot.append(btnS)
            })
        },
        _createHeader: function () {
            var trHeader = this.$element.find('.maze-grid-header tr');
            var selfClass = this;
            $.each(selfClass.alloptions.colModel, function (i, val) {
                var $td = $('<td><div class="maze-grid-header-innert">' + ('title' in val ? val.title : '') + '<span class="maze-grid-header-arr"></span></div></td>');
                if (i == 0)
                {
                    $td.addClass('maze-grid-header-cell-first');
                }
                if (i == selfClass.alloptions.colModel.length - 1)
                {
                    $td.addClass('maze-grid-header-cell-last');
                }

                if ('sorttable' in val && val.sorttable)
                {
                    $td.addClass('maze-grid-sorttable');
                }

                $td.attr('data-grid-fild', val.name)
                trHeader.append($td);
            })

        },
        _hoverHeader: function () {
            this.$element.find('.maze-grid-header .maze-grid-sorttable .maze-grid-header-innert').bind('mouseover mouseout', function (e) {
                e.preventDefault();
                if (e.type == 'mouseover')
                {
                    $(this).addClass('maze-grid-header-hover');
                }
                else
                {
                    $(this).removeClass('maze-grid-header-hover');
                }
            });

            this.$element.find('.maze-grid-header .maze-grid-sorttable .maze-grid-header-arr').bind('mouseover mouseout', function (e) {
                e.preventDefault();
                if (e.type == 'mouseover')
                {
                    $(this).parents('.maze-grid-sorttable').addClass('maze-grid-header-hover-arr');
                }
                else
                {
                    $(this).parents('.maze-grid-sorttable').removeClass('maze-grid-header-hover-arr');
                }
            });
        },
        _createlist: function () {
            var $rightFooter = this.$element.find('.maze-grid-footer .maze-grid-footer-right .maze-grid-footer-list-page');
            var selfClass = this;
            var html = this.alloptions.labelrow + '  <select>';

            $.each(selfClass.alloptions.rowList, function (i, val) {
                html += '<option ' + (selfClass.alloptions.rowNum == val ? 'selected' : '') + ' value="' + val + '">' + val + '</option>'
            })
            html += '</select>';
            $rightFooter.html(html);
        },
        _createInfo: function () {
            var selfClass = this;

            var total = this.getTotal();
            var first = 1, last = selfClass.alloptions.page * selfClass.alloptions.rowNum;
            if (selfClass.alloptions.page !== 1)
            {
                first = (selfClass.alloptions.page - 1) * selfClass.alloptions.rowNum
            }
            if (last > total)
                last = total;

            var $rightFooter = this.$element.find('.maze-grid-footer .maze-grid-footer-right .maze-grid-footer-info-page');
            $rightFooter.html((selfClass.alloptions.labelinfo.replace(/{FIRST}/, first).replace(/{LAST}/, last).replace(/{SIZE}/, total)));

            this.$element.find('.maze-grid-footer .maze-grid-page-number').val(selfClass.alloptions.page);
        },
        _getPage: function () {
            var selfClass = this;

            var $centerFooter = this.$element.find('.maze-grid-footer .maze-grid-footer-center .maze-grid-page-size');
            $centerFooter.html(selfClass.alloptions.labelpage.replace(/{SIZE}/, selfClass.getSizePage()));
        },
        _getContent: function () {

            var selfClass = this;

            this._trigger('beforeGetContent', selfClass.alloptions);

            if (selfClass.alloptions.mode !== "default")
                return false;

            var $content = this.$element.find('.maze-grid-content');
            var $header = this.$element.find('.maze-grid-header .maze-grid-footer-center .maze-grid-page-size')

            var $table = $('<table cellspacing="0" cellpadding="0" border="0">').append('<tbody>');



            function createRow(row, group)
            {
                var group = group ? ' data-group-row="' + group + '"' : '';
                var $htmlRow = $('<tr' + group + '>').data('gridRow', row);
                ;
                $.each(selfClass.alloptions.colModel, function (i, params) {

                    $.each(row, function (name, value) {
                        if (params.name == name)
                        {
                            var align = 'align' in params ? ' style="text-align:' + params.align + '"' : '';
                            $htmlRow.append('<td ' + align + ' data-grid-fild="' + name + '"><div class="maze-grid-content-innert">' + value + '</div></td>');
                            return false;
                        }
                    })
                })
                $table.find('tbody').append($htmlRow)
            }

            if ('groupField' in selfClass.alloptions.groupingView && 0 in selfClass.alloptions.groupingView.groupField)
            {
                $content.html("");
            }
            $.each(selfClass.getData(), function (i, val) {
                if ('data' in val && selfClass.alloptions.grouping)
                {

                    var $htmlRow = $('<div class="maze-grid-group-row" data-group-row="' + i + '">');
                    $htmlRow.append('<div class="maze-grid-group-innert"><span class="maze-grid-group-toggle"></span>' + val.namegroup + '</div>');
                    $content.append($htmlRow);
                    $table = $('<table cellspacing="0" cellpadding="0" border="0">').append('<tbody>');
                    $.each(val.data, function (y, vald) {
                        createRow(vald, i)
                    })
                    $content.append($table);
                }
                else
                {
                    createRow(val)
                }
            });

            if (!('groupField' in selfClass.alloptions.groupingView && 0 in selfClass.alloptions.groupingView.groupField))
            {
                $content.html($table);
            }

            if ($.isArray(selfClass.alloptions.colHide))
            {
                selfClass.hideShowCol(selfClass.alloptions.colHide, 'hide');
            }

            selfClass.openAllGroup(selfClass.alloptions.opengroup ? 'open' : 'close');

            this.$element.find('.maze-grid-group-toggle').click(function () {
                var $self = $(this).parents('.maze-grid-group-row');
                var name = $self.attr('data-group-row');
                var rowGroup = selfClass.$element.find('tr[data-group-row]').not('.maze-grid-group-row').filter(function () {
                    if ($(this).attr('data-group-row') == name)
                        return true;
                    return false;
                })

                if (rowGroup.eq(0).is(":visible"))
                {
                    selfClass.groupOpenClose(name, 'close');
                }
                else
                {
                    selfClass.groupOpenClose(name, 'open');
                }
            })
            setTimeout(function () {
                selfClass._trigger('afterGetContent', selfClass.alloptions);
                selfClass.setSize();
            }, 100);



        },
        openAllGroup: function (mode)
        {
            var allGroup = this.$element.find('tr[data-group-row]').not('.maze-grid-group-row')
            if (allGroup.is('tr'))
            {
                allGroup = allGroup.map(function (val) {
                    return $(this).attr('data-group-row')
                }).get();
                this.groupOpenClose(allGroup, mode);
            }
        },
        setSize: function (resize)
        {
            var width = this.$element.outerWidth(true);

            var selfClass = this;

            this.$element.find('.maze-grid-wrapp, .maze-grid-footer, .maze-grid-footer > table').width(width < selfClass.alloptions.minWidth ? selfClass.alloptions.minWidth : width);


            var sum = 0, name, obj, factor = 0;
            var $visibleCol = this.$element.find('.maze-grid-header td[data-grid-fild]').filter(function () {
                if ($.inArray($(this).attr('data-grid-fild'), selfClass.alloptions.colHide) !== -1)
                {
                    return false;
                }
                return true;
            });

            this.$element.find('.maze-grid-header td[data-grid-fild]').removeClass('maze-grid-header-cell-first maze-grid-header-cell-last')

            $visibleCol.slice(0, 1).addClass('maze-grid-header-cell-first');
            $visibleCol.slice($visibleCol.size() - 1).addClass('maze-grid-header-cell-last');

            if (resize == 'resize')
            {
                var widthC = this.$element.find('.maze-grid-content').width();
                if (widthC > width)
                {
                    var factorRes = width / widthC;
                    $visibleCol.each(function () {
                        name = $(this).attr('data-grid-fild');
                        obj = selfClass.gerModelName(name);

                        obj.width = Math.floor(obj.width * factorRes);

                    });

                }
            }

            $visibleCol.each(function () {
                name = $(this).attr('data-grid-fild');
                obj = selfClass.gerModelName(name);
                if (!obj.hasOwnProperty('width'))
                {
                    obj.width = $(this).width();
                }

                obj.width = obj.width < 28 ? 28 : obj.width;
                sum += obj.width;

            });



            if (sum >= width)
            {
                this.$element.find('.maze-grid-header, .maze-grid-content, .maze-grid-header > table, .maze-grid-content > table').width(sum < selfClass.alloptions.minWidth ? selfClass.alloptions.minWidth : sum);
            }

            if (sum < width)
            {
                this.$element.find('.maze-grid-header > table, .maze-grid-header, .maze-grid-content, .maze-grid-content > table').width(width < selfClass.alloptions.minWidth ? selfClass.alloptions.minWidth : width);
                factor = (width / sum).toFixed(2);
            }

            var correctionBorder = this.$element.find('.maze-grid-header > table').innerWidth() - width;
            var sumlog = 0;
            $visibleCol.each(function () {
                name = $(this).attr('data-grid-fild');
                obj = selfClass.gerModelName(name);

                obj.width = factor > 0 ? Math.floor(obj.width * factor) : obj.width;

                var res = obj.width - Math.ceil(correctionBorder / $visibleCol.size())
                res = res < 28 ? 28 : res;
                sumlog += res;
                selfClass.$element.find('td[data-grid-fild=' + obj.name + ']').width(res)
            })
            this.$element.find('.maze-grid-wrapp').mCustomScrollbar("update");
        },
        groupOpenClose: function (group, mode)
        {
            var selfClass = this;
            function groupAct(group, mode)
            {
                var row = selfClass.$element.find('tr[data-group-row]').not('.maze-grid-group-row').filter(function () {
                    if ($(this).attr('data-group-row') == group)
                        return true;
                    return false;
                })
                var toggleGroup = selfClass.$element.find('.maze-grid-group-row[data-group-row]').filter(function () {
                    if ($(this).attr('data-group-row') == group)
                        return true;
                    return false;
                })
                toggleGroup = toggleGroup.find('.maze-grid-group-toggle')
                if (mode == 'open')
                {
                    row.show();
                    toggleGroup.addClass('maze-grid-group-open')
                }
                else
                {

                    row.hide();
                    toggleGroup.removeClass('maze-grid-group-open')
                }

            }

            if ($.isArray(group))
            {

                $.each(group, function (i, name) {
                    groupAct(name, mode)
                })
            }
            else
            {
                groupAct(group, mode)
            }
        },
        updatePaginator: function ()
        {
            var selfClass = this;
            var $center = selfClass.$element.find('.maze-grid-footer .maze-grid-footer-center');
            var sizePage = selfClass.getSizePage();
            $center.find('.maze-grid-btn').removeClass('disable-page');

            if (selfClass.alloptions.page == 1)
            {
                $center.find('.maze-grid-page-first, .maze-grid-page-previous').addClass('disable-page');
            }
            if (selfClass.alloptions.page == sizePage)
            {
                $center.find('.maze-grid-page-next, .maze-grid-page-last').addClass('disable-page');
            }
            if (sizePage < selfClass.alloptions.page)
            {
                $center.find('.maze-grid-page-next, .maze-grid-page-last').addClass('disable-page');
            }
        },
        getPaginator: function ()
        {
            var selfClass = this;
            var $center = selfClass.$element.find('.maze-grid-footer .maze-grid-footer-center');


            selfClass.updatePaginator();

            $center.find('.maze-grid-page-first').click(function () {
                if ($(this).is('.disable-page'))
                    return false;
                selfClass.alloptions.page = 1;

                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass.updatePaginator();
                }
                else
                {
                    selfClass.loadData();
                }

            })

            $center.find('.maze-grid-page-previous').click(function () {
                if ($(this).is('.disable-page'))
                    return false;
                selfClass.alloptions.page = Number(selfClass.alloptions.page) - 1;
                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass.updatePaginator();
                }
                else
                {
                    selfClass.loadData();
                }

            })

            $center.find('.maze-grid-page-next').click(function () {
                if ($(this).is('.disable-page'))
                    return false;
                selfClass.alloptions.page = Number(selfClass.alloptions.page) + 1;
                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass.updatePaginator();
                }
                else
                {

                    selfClass.loadData();
                }

            })

            $center.find('.maze-grid-page-last').click(function () {
                if ($(this).is('.disable-page'))
                    return false;
                var sizePage = selfClass.getSizePage();
                selfClass.alloptions.page = sizePage;

                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass.updatePaginator();
                }
                else
                {
                    selfClass.loadData();
                }

            })

            $center.find('.maze-grid-page-number').bind('keypress keydown keyup focus', function (e) {
                var sizePage = selfClass.getSizePage();
                if (e.type == 'focus')
                {
                    this.select();
                }

                if ($.trim($(this).val()).search(/^\d+$/) == -1)
                {
                    $(this).val(selfClass.alloptions.page);
                }
                if (e.which !== 13)
                    return true;
                var value = Number($.trim($(this).val()));

                if (value > sizePage)
                {
                    value = sizePage;
                }
                if (value < 1)
                {
                    value = 1;
                }
                if ($(this).is('.disable-page') || selfClass.alloptions.page == value)
                    return false;

                selfClass.alloptions.page = value;

                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass.updatePaginator();
                }
                else
                {
                    selfClass.loadData();
                }

            })

            selfClass.$element.find('.maze-grid-footer .maze-grid-footer-list-page select').bind('change', function () {
                var value = Number($.trim($(this).val()));

                selfClass.alloptions.rowNum = value;
                var sizePage = selfClass.getSizePage();
                if (selfClass.alloptions.page > sizePage)
                {
                    selfClass.alloptions.page = 1;
                }
                if (selfClass.alloptions.datatype == "local")
                {
                    selfClass._getContent();
                    selfClass._createInfo();
                    selfClass._getPage();
                    selfClass.updatePaginator();
                }
                else
                {
                    selfClass.loadData();
                }

            })

        },
        selectSortFild: function ()
        {
            var selfClass = this;
            if (!selfClass.alloptions.sortfild)
                return false;
            var order = selfClass.alloptions.sortorder == 'asc' ? 'maze-grid-sort-asc' : 'maze-grid-sort-desc';
            var params = this.gerModelIndex(selfClass.alloptions.sortfild);

            if (!params)
                return false;

            var $td = selfClass.$element.find(".maze-grid-header td[data-grid-fild=" + params.name + "]");

            selfClass.$element.find(".maze-grid-sort-asc, .maze-grid-sort-desc, .maze-grid-header-active")
                    .removeClass("maze-grid-sort-asc maze-grid-sort-desc maze-grid-header-active");
            $td.addClass(order + ' maze-grid-header-active');
        },
        checkSortFild: function (name, order)
        {
            var selfClass = this;

            var params = this.gerModelIndex(selfClass.alloptions.sortfild);

            function setFild()
            {
                var paramCol = selfClass.gerModelName(name);
                selfClass.alloptions.sortfild = paramCol.index;

                selfClass.alloptions.sortfild = paramCol.index;
                selfClass.alloptions.sortorder = "asc";
            }
            if (params)
            {
                if (params.name == name && order == selfClass.alloptions.sortorder)
                    return false;

                if (params.name == name)
                {
                    selfClass.alloptions.sortorder = selfClass.alloptions.sortorder == "asc" ? "desc" : "asc";
                }
                else
                {
                    setFild();
                }
            }
            else
            {
                setFild();
            }


            if (order)
            {
                selfClass.alloptions.sortorder = order;
            }
            selfClass.selectSortFild();
            if (selfClass.alloptions.datatype == "local")
            {
                selfClass._getContent();
            }
            else
            {
                selfClass.loadData();
            }
        },
        setMenuFild: function ()
        {
            var selfClass = this;

            function dataMenu()
            {
                var name = this.parents('td[data-grid-fild]').attr('data-grid-fild')


                var params = selfClass.gerModelName(name);
                var dataMenuFild = [
                    {type: 'link', spriteClass: 'hmenu-asc', title: selfClass.alloptions.labelmenu.textasc, actions: function (target) {
                            var name = target.parents('.maze-grid-sorttable').attr('data-grid-fild');
                            selfClass.checkSortFild(name, 'asc');
                        }},
                    {type: 'link', spriteClass: 'hmenu-desc', title: selfClass.alloptions.labelmenu.textdesc, actions: function (target) {
                            var name = target.parents('.maze-grid-sorttable').attr('data-grid-fild');
                            selfClass.checkSortFild(name, 'desc');
                        }}
                ]

                if (selfClass.alloptions.grouping && params.hasOwnProperty('grouping') && params.grouping)
                {
                    var groupSum = Array();
                    if ('groupField' in selfClass.alloptions.groupingView)
                    {
                        groupSum = selfClass.alloptions.groupingView.groupField
                    }
                    dataMenuFild.push(
                            {type: 'siporator'},
                    {type: 'checkbox', checked: $.inArray(name, groupSum) !== -1 ? true : false,
                        title: selfClass.alloptions.labelmenu.textgroup, change: function () {
                            var geoupname = this.parents('td[data-grid-fild]').attr('data-grid-fild');
                            selfClass.setGroup(geoupname);
                            selfClass._getContent();
                        }}
                    )

                }
                return dataMenuFild;
            }


            var context = Array();
            $.each(selfClass.alloptions.colModel, function (i, val) {
                if ('hidefild' in val && val.hidefild)
                {
                    context.push({type: 'checkbox',
                        name: val.name,
                        checked: $.inArray(val.name, selfClass.alloptions.colHide) == -1 ? true : false,
                        value: 1,
                        title: val.title})
                }
            })
            this.$element.find('.maze-grid-header').mazeContext({
                position: {
                    my: 'left top',
                    at: 'left bottom',
                    of: 'event'
                },
                data: context,
                eventOpen: 'context'
            })
                    .bind('checked.mazecontext unchecked.mazecontext', function (e, meta) {
                        if (e.type == 'checked')
                        {
                            selfClass.hideShowCol(meta.obj.name, 'show');

                        }
                        else
                        {
                            selfClass.hideShowCol(meta.obj.name, 'hide');
                        }
                    })

            this.$element.find('.maze-grid-header .maze-grid-sorttable .maze-grid-header-arr')
                    .mazeContext({
                        position: {
                            my: 'left top',
                            at: 'left bottom'
                        },
                        data: dataMenu,
                        onAfterOpen: function () {
                            selfClass.$element.find('.maze-grid-header .maze-grid-header-hover').removeClass('maze-grid-header-hover');
                            this.parents('.maze-grid-sorttable').addClass('maze-grid-header-hover');
                        },
                        onAfterClose: function () {
                            selfClass.$element.find('.maze-grid-header .maze-grid-header-hover').removeClass('maze-grid-header-hover');
                        }
                    })

        },
        setGroup: function (name)
        {
            var params = this.gerModelName(name), selfClass = this;
            if ('grouping' in params && params.grouping)
            {
                if (!('groupField' in this.alloptions.groupingView))
                {
                    this.alloptions.groupingView.groupField = Array();
                }
                if ($.inArray(name, selfClass.alloptions.groupingView.groupField) !== -1)
                {
                    var index = $.inArray(name, selfClass.alloptions.groupingView.groupField);
                    selfClass.alloptions.groupingView.groupField.splice(index, 1);
                    selfClass._trigger('groupFild', {field: selfClass.alloptions.groupingView.groupField});
                }
                else
                {
                    this.alloptions.groupingView.groupField.push(name);
                    selfClass._trigger('groupFild', {field: selfClass.alloptions.groupingView.groupField});
                }
            }

        },
        hideShowCol: function (name, mode)
        {
            var selfClass = this;
            function actions(namef, mode)
            {
                if (mode == 'hide')
                {
                    selfClass.$element.find('td[data-grid-fild=' + namef + ']').hide();
                    var index = $.inArray(namef, selfClass.alloptions.colHide);
                    if (index == -1)
                    {
                        selfClass.alloptions.colHide.push(namef);
                        selfClass._trigger('hideFild', selfClass.$element.find('td[data-grid-fild=' + namef + ']'));
                        selfClass.setSize('resize');
                    }

                }
                else
                {
                    selfClass.$element.find('td[data-grid-fild=' + namef + ']').show();
                    var index = $.inArray(namef, selfClass.alloptions.colHide);
                    if (index !== -1)
                    {
                        selfClass.alloptions.colHide.splice(index, 1);
                        selfClass._trigger('showFild', selfClass.$element.find('td[data-grid-fild=' + namef + ']'));
                        selfClass.setSize('resize');
                    }

                }
                selfClass.$element.find('.maze-grid-group-row td').attr('colspan', selfClass.getLength())

            }

            if ($.isArray(name))
            {
                $.each(name, function (i, val) {
                    actions(val, mode);
                });

            }
            else
            {
                actions(name, mode);
            }
        },
        resizeCol: function ()
        {
            var selfClass = this;

            this.$element.find('.maze-grid-header .maze-grid-header-innert').each(function () {
                if (!$(this).find('.maze-grid-resize-col-handle').is('.maze-grid-resize-col-handle'))
                {
                    $(this).append('<div class="maze-grid-resize-col-handle"></div>');
                }
            })

            this.$element.find('.maze-grid-header .maze-grid-resize-col-handle').each(function () {
                var leftScroll = $(this).parents('.mCSB_container').position().left;
                var containment = Array();
                containment.push(Math.ceil($(this).parents('.maze-grid-wrapp').offset().left + $(this).parents('.maze-grid-header-innert').position().left + leftScroll + 20))
                containment.push(0);
                containment.push($(this).parents('.maze-grid-wrapp').offset().left + $(this).parents('.maze-grid-wrapp').width());
                containment.push(0);

                $(this).draggable({
                    axis: 'x',
                    helper: 'clone',
                    delay: 500,
                    start: function (e, ui) {
                        selfClass.$element.find('.maze-grid-wrapp').append('<div class="maze-grid-resize-col-helper"></div>')
                    },
                    drag: function (e, ui) {
                        var left = $(this).parents('.mCSB_container').position().left;

                        selfClass.$element.find('.maze-grid-wrapp .maze-grid-resize-col-helper').css('left', ui.position.left + left + $(this).parent().position().left)
                    },
                    containment: containment,
                    stop: function (e, ui) {
                        selfClass.$element.find('.maze-grid-wrapp .maze-grid-resize-col-helper').remove();
                        var $col = $(e.target).parents('td[data-grid-fild]');
                        var name = $col.attr('data-grid-fild');

                        var wc = selfClass.$element.find('.maze-grid-content').width();

                        var param = selfClass.gerModelName(name);
                        param.width = Math.floor(ui.position.left);

                        selfClass.setSize();
                        selfClass.resizeCol();

                    }
                })
            })


        },
        sortTableCol: function ()
        {
            if (!this.alloptions.sorttableCol)
                return false;
            var selfClass = this;
            this.$element.find('.maze-grid-header td').draggable({
                axis: 'x',
                delay: 500,
                helper: function () {
                    var css = {
                        width: $(this).width() - 3,
                        height: $(this).height() - 3,
                        top: $(this).offset().top
                    }
                    var $self = $(this)
                    return $('<div>').css(css).addClass('maze-grid-helper-empty-col').attr('data-grid-fild', $self.attr('data-grid-fild'));
                },
                drag: function (e, ui) {

                    var $self = $(this);
                    var $parent = $(this).parent()

                    $parent.children().each(function () {

                        if (Math.ceil($(this).offset().left) == Math.ceil(ui.offset.left) &&
                                $(this).attr('data-grid-fild') !== ui.helper.attr('data-grid-fild'))
                        {
                            $parent.find('.maze-grid-helper-empty-td').remove()
                            $self.hide();

                            $(this).before($('<td>').width(ui.helper.width()).addClass('maze-grid-helper-empty-td'));
                        }

                    })
                },
                containment: selfClass.$element.find('.maze-grid-header'),
                appendTo: 'body',
                stop: function (e, ui) {
                    $(this).show();
                }
            })

            this.$element.find('.maze-grid-header tr').droppable({
                drop: function (e, ui) {
                    var index = $(this).find('.maze-grid-helper-empty-td').index();
                    $(this).find('.maze-grid-helper-empty-td').remove()
                    if (ui.draggable.index() == index)
                    {
                        return false;
                    }

                    $(this).find('td').eq(index).before(ui.draggable);

                    selfClass.alloptions.sortCol = $(this).find('td')
                            .map(function (val) {
                                return $(this).attr('data-grid-fild')
                            }).get();
                    selfClass._trigger('afterSortFild', {elem: ui.draggable, fild: selfClass.alloptions.sortCol});
                    selfClass._sortCol();
                    selfClass._getContent();

                }
            })
        },
        update: function (param, addparam)
        {
            if (addparam) {
                this.alloptions.params = param;
            }
            this.loadData(param);
        },
        setOptions: function (options)
        {
            var selfClass = this;

            selfClass.alloptions = $.extend(selfClass.alloptions, options);
        }


    });

})(jQuery);
/*
 * ЧЕКБОКС -  выделене строк
 */
(function ($) {

    $.mazeGrid.addPlugin("checkbox", {
        version: 1.0,
        defaults: {
            fild: null,
            name: null,
            checked: [],
            real: true,
            change: $.noop
        },
        _init: function ()
        {
            var selfClass = this;
            if (!this.isInitCheckbox())
                return false;

            var fild = this.gerModelName(selfClass.options.fild);

            fild.width = 30;
            fild.align = "center";

            var options = selfClass.options;

            options.checked = $.map(options.checked, function (val) {
                return val.toString();
            })

            for (var name in fild)
            {
                if ($.inArray(name, ['sorttype', 'sorttable', 'grouping']) !== -1)
                {
                    delete fild[name];
                }
            }
            this.$element.find('.maze-grid-header [data-grid-fild=' + fild.name + ']')
                    .html('<span class="maze-grid-checkbox maze-grid-checkbox-all"></span>')
                    .css('text-align', 'center');
            this.$element.find('.maze-grid-header .maze-grid-checkbox-all').click(function () {
                if ($(this).is('.maze-grid-checked'))
                {
                    $(this).removeClass('maze-grid-checked');
                    selfClass.$element.find('.maze-grid-content .maze-grid-checkbox.maze-grid-checked').each(function () {
                        selfClass.change($(this));
                    })

                }
                else
                {
                    selfClass.$element.find('.maze-grid-content .maze-grid-checkbox').not('.maze-grid-checked').each(function () {
                        selfClass.change($(this));
                    })
                    $(this).addClass('maze-grid-checked');
                }

            });

            this.$element.bind('afterGetContent.mazegrid', function () {

                var $content = selfClass.$element.find('.maze-grid-content [data-grid-fild=' + fild.name + '] .maze-grid-content-innert');

                $content.each(function () {
                    var value = $.trim($(this).text());
                    var html = '';
                    var checked = $.inArray(value, options.checked) !== -1 ? true : false;

                    var $self = selfClass.$element;
                    var dataRow = $(this).parents('tr').data('gridRow');

                    var name = typeof options.name == 'function' ? options.name.call($self, dataRow) : options.name;

                    if (options.real)
                    {
                        html = '<input style="display:none;" type="checkbox" name="' + name + '" value="' + value + '" ' + (checked ? 'checked' : '') + ' />'
                    }
                    html += '<span data-checkbox-value="' + value + '" data-checkbox-name="' + name + '" class="maze-grid-checkbox' + (checked ? ' maze-grid-checked' : '') + '"></span>'

                    $(this).html(html);
                    if (checked)
                        $(this).parents('tr').addClass('maze-grid-row-active');
                })

                $content.find('.maze-grid-checkbox').click(function () {
                    selfClass.change($(this))
                })

                if (selfClass.$element.find('.maze-grid-header .maze-grid-checkbox-all').is('.maze-grid-checked'))
                {
                    selfClass.$element.find('.maze-grid-content .maze-grid-checkbox').not('.maze-grid-checked').each(function () {
                        selfClass.change($(this));
                    })
                    if (!selfClass.$element.find('.maze-grid-content .maze-grid-checkbox').is('.maze-grid-checkbox'))
                    {
                        selfClass.$element.find('.maze-grid-header .maze-grid-checkbox-all').removeClass('maze-grid-checked');
                    }
                }

                if (selfClass.$element.find('.maze-grid-content .maze-grid-checkbox.maze-grid-checked').size() <= 0)
                {
                    selfClass.$element.find('.maze-grid-header .maze-grid-checkbox-all').removeClass('maze-grid-checked');
                    selfClass._trigger('allunchecked');
                }

            })


        },
        change: function ($elem)
        {
            var options = this.options;
            var selfClass = this;
            var $parent = $elem.parents('tr');
            var $input = $elem.parent().find('input');
            var type = '';
            var value = $.trim($elem.attr('data-checkbox-value'));


            if ($elem.is('.maze-grid-checked'))
            {
                $elem.removeClass('maze-grid-checked');
                $parent.removeClass('maze-grid-row-active');
                $input.removeAttr('checked');
                selfClass._trigger('unchecked', {el: $elem, input: $input});
                type = 'unchecked';
                if ($.inArray(value, options.checked) !== -1)
                {
                    options.checked.splice($.inArray(value, options.checked), 1);
                }


            }
            else
            {
                $elem.addClass('maze-grid-checked');
                $parent.addClass('maze-grid-row-active');
                $input.attr('checked', true);
                selfClass._trigger('checked', {el: $elem, input: $input});
                type = 'checked'
                options.checked.push(value);
            }
            if (selfClass.$element.find('.maze-grid-content .maze-grid-checkbox.maze-grid-checked').size() <= 0)
            {
                selfClass.$element.find('.maze-grid-header .maze-grid-checkbox-all').removeClass('maze-grid-checked');
                selfClass._trigger('allunchecked');
            }

            options.change.call(selfClass.$element, type, {el: $elem, input: $input})
        },
        isInitCheckbox: function ()
        {
            var selfClass = this;
            if (this.options.fild !== null && this.gerModelName(selfClass.options.fild))
            {
                return true;
            }
            return false;
        }
    })

})(jQuery);
/*
 * Дерево
 * События
 ************************************
 * expandedTree - развернуть дерево @param target (jquery) развернутый узел
 *	collapsedTree - свернуть дерево @param target (jquery) свернутый узел
 */

(function ($) {

    $.mazeGrid.addPlugin("tree", {
        version: 1.0,
        prefix: 'maze-gred-tree-{ID}',
        loadTree: [],
        parents: {},
        defaults: {
            id: null,
            parent: null,
            open: [],
            is_child: true,
            fild_type: null,
            icon: {},
            json: {
                url: null,
                data: function (data) {
                    return data
                },
                param: function (id, elem) {
                    return {id_menu: id}
                }
            }
        },
        _init: function ()
        {
            var selfClass = this;

            if (selfClass.options.json.url == null)
            {
                selfClass.options.json.url = selfClass.alloptions.url;
            }
            if (this.alloptions.mode !== "tree")
                return false;

            this.$element.bind('beforeLoadData.mazegrid', function () {
                selfClass.loadTree = [];
                selfClass.parents = {};
            });

            this.$element.bind('beforeGetContent.mazegrid', function () {
                selfClass.parents = {};
                var $content = selfClass.$element.find('.maze-grid-content');
                var $header = selfClass.$element.find('.maze-grid-header .maze-grid-footer-center .maze-grid-page-size')

                var $table = $('<table cellspacing="0" cellpadding="0" border="0">').append('<tbody>');


                selfClass.createRow("0", $table, selfClass.getData(), 0)

                $content.html($table);

                $content.find('tr[data-parent]').hide();

                $content.find('.maze-grid-tree-arr').click(function (e) {
                    var $row = $(this).parents('tr');
                    var id = $row.attr('data-id');
                    if ($row.is('.maze-grid-tree-expanded'))
                    {
                        selfClass.openTree(id, 'close');
                    }
                    else
                    {
                        selfClass.openTree(id, 'open');
                    }
                    $row.toggleClass('maze-grid-tree-collapsed maze-grid-tree-expanded');
                })

                if ($.isArray(selfClass.alloptions.colHide))
                {
                    selfClass.$element.mazeGrid('core::hideShowCol', selfClass.alloptions.colHide, 'hide');
                }



                selfClass._trigger('afterGetContent', selfClass.alloptions);
                selfClass.$element.mazeGrid('core::setSize');
                selfClass.parents = $content.find('tr[data-parent]');
                selfClass.parents = $('<div>').append(selfClass.parents);
                selfClass.expandedTree(selfClass.options.open);

            })

        },
        openTree: function (id, mode)
        {
            var selfClass = this;
            function open(id, mode)
            {
                if (id == '')
                    return;
                var $content = selfClass.$element.find('.maze-grid-content');
                if (mode == "open") {                     
                    var $elem =$(selfClass.parents.find('[data-parent=' + id + ']').get().reverse());
                } else {
                    var $elem = $content.find('[data-parent=' + id + ']');
                }


                var method = mode == "open" ? 'show' : 'hide';
                var $target = $content.find('[data-id=' + id + ']');

                if (!$target.is('[data-id=' + id + ']'))
                    return false;

                if ($target.size() > 2)
                    return false;

                if (mode == "open")
                {
                    if (!$content.find('[data-id=' + id + ']').is('tr'))
                        return false;
                    selfClass.setOpen(id);

                }
                else
                {
                    selfClass.setOpen(id, true);
                    selfClass._trigger('collapsedTree', $target);
                }

                if ($elem.is('tr') || mode == 'close')
                {
                    if (mode == "open")
                        selfClass._trigger('expandedTree', $target);
                    
                    $elem.each(function () {
                        var $self = $(this);
                        $self[method]();
                       
                        //$self.is('.maze-grid-tree-expanded')
                        if (mode == "open")
                        {
                            selfClass.openTree($self.attr('data-id'), 'open');
                            $target.after($self);
                        }
                        if (mode == 'close')
                        {
                            selfClass.openTree($self.attr('data-id'), 'close');
                            selfClass.parents.append($self);
                        }

                    })
                }
                else
                {
                    var options = selfClass.options
                    var IDREAL = (new RegExp(selfClass.prefix.replace(/{ID}/, "([a-z0-9-_.]+)"))).exec(id)[1];

                    if (options.json.url && !selfClass.isLoad(id))
                    {
                        selfClass.setLoad(id);
                        var param = options.json.param.call(selfClass.$element, IDREAL, $target, $target.data('gridRow'));
                        param = $.extend({}, selfClass.alloptions.params || {}, param || {});

                        $.ajax({
                            url: options.json.url,
                            type: 'POST',
                            data: param,
                            dataType: 'json',
                            cache: false,
                            beforeSend: function () {
                                $target.find('.maze-grid-tree-arr').parent().addClass('maze-grid-tree-load');
                            },
                            success: function (data) {

                                $target.find('.maze-grid-tree-arr').parent().removeClass('maze-grid-tree-load');

                                if (typeof options.json.data == 'function')
                                {
                                    var data = options.json.data(data);

                                    if ($.isArray(data) && 0 in data)
                                    {
                                        selfClass.addTree(id, data);
                                    }
                                    else
                                    {
                                        $target.removeClass('maze-grid-tree-expanded')
                                        $target.find('.maze-grid-tree-arr').remove();
                                    }
                                }
                            },
                            error: function ()
                            {
                                $target.removeClass('maze-grid-tree-expanded')
                                $target.find('.maze-grid-tree-arr').remove();
                                $target.find('.maze-grid-tree-arr').parent().removeClass('maze-grid-tree-load');
                            }
                        })

                    }
                }

            }

            if ($.isArray(id))
            {
                $.each(id, function (i, val) {
                    open(val, mode);
                })

            }
            else
            {
                open(id, mode);
            }
        },
        expandedTree: function (id)
        {
            var selfClass = this;

            this.openTree(id, 'open');
            function toggle(id)
            {
                var $content = selfClass.$element.find('.maze-grid-content');
                var $row = $content.find('[data-id=' + id + ']');
                if ($row.is('.maze-grid-tree-collapsed'))
                {
                    $row.toggleClass('maze-grid-tree-collapsed maze-grid-tree-expanded');
                }
            }
            if ($.isArray(id))
            {
                $.each(id, function (i, val) {
                    toggle(val);
                })
            }
            else
            {
                toggle(id);
            }

        },
        collapsedTree: function (id)
        {
            this.openTree(id, 'close');

            var selfClass = this;

            function toggle(id)
            {
                var $content = selfClass.$element.find('.maze-grid-content');
                var $row = $content.find('[data-id=' + id + ']');
                if ($row.is('.maze-grid-tree-expanded'))
                {
                    $row.toggleClass('maze-grid-tree-collapsed maze-grid-tree-expanded');
                }
            }
            if ($.isArray(id))
            {
                $.each(id, function (i, val) {
                    toggle(val);
                })
            }
            else
            {
                toggle(id);
            }
        },
        addTree: function (id, data)
        {
            var $content = this.$element.find('.maze-grid-content');
            var IDOP = Array();

            function getOpenID(id, IDOP)
            {
                var $self = $content.find('[data-id=' + id + ']');
                var parent = $self.attr('data-parent');
                if (parent)
                {
                    IDOP.push(parent);
                    getOpenID(parent, IDOP)
                }
            }
            IDOP.push(id);
            getOpenID(id, IDOP);
            
           
            this.setOpen(IDOP.reverse())
            this.dataMerge(data);
            
            
            this.$element.mazeGrid('core::_getContent');
            this.$element.mazeGrid('core::setSize');
        },
        isLoad: function (id)
        {
            var selfClass = this;

            if ($.inArray(id, selfClass.loadTree) !== -1)
            {
                return true;
            }
            return false
        },
        setLoad: function (id)
        {
            var selfClass = this;

            if ($.inArray(id, selfClass.loadTree) == -1)
            {
                selfClass.loadTree.push(id);
            }
        },
        setOpen: function (array, del)
        {
            var options = this.options, selfClass = this;
            function add(id, del)
            {
                if (del)
                {
                    if ($.inArray(id, options.open) !== -1)
                    {
                        var index = $.inArray(id, options.open);
                        options.open.splice(index, 1);
                    }

                    return false;
                }
                if ($.inArray(id, options.open) == -1)
                {
                    options.open.push(id);
                }
            }

            if ($.isArray(array))
            {
                $.each(array, function (i, val) {
                    add(val, del)
                })
            }
            else
            {
                add(array, del)
            }

        },
        createRow: function (id, $table, array, level)
        {
            if (id in array)
            {
                if (id !== "0")
                    level++;
                var selfClass = this, options = this.options;

                $.each(array[id], function (i, row)
                {
                    var $htmlRow = $('<tr>').data('gridRow', row);
                    var idRow = selfClass.prefix.replace(/{ID}/, row[options.id]);
                    var is_child = typeof options.is_child == 'function' ? options.is_child(row) : options.is_child;
                    var isChildren = (row[options.id] in array || (options.json.url && !selfClass.isLoad(idRow))) && is_child ? true : false;



                    if (row[options.parent] !== 0)
                    {
                        $htmlRow.attr('data-parent', selfClass.prefix.replace(/{ID}/, row[options.parent]))
                    }

                    $htmlRow.attr('data-id', idRow)

                    if (isChildren)
                        $htmlRow.addClass('maze-grid-tree-collapsed');

                    $.each(selfClass.alloptions.colModel, function (i, params) {
                        $.each(row, function (name, value)
                        {
                            if (params.name == name)
                            {
                                var align = 'align' in params ? ' style="text-align:' + params.align + '"' : '';

                                if (options.target == name)
                                {
                                    var arr = isChildren ? '<a class="maze-grid-tree-arr"></a>' : '';

                                    $htmlRow.append('<td ' + align + ' data-grid-fild="' + name + '"><div style="margin-left:' + level * 20 + 'px;" class="maze-grid-content-innert maze-grid-content-innert-tree"><span class="maze-grid-tree-indenter">' + arr + '</span><span class="maze-grid-tree-folder">' + value + '</span></div></td>');
                                }
                                else
                                {
                                    $htmlRow.append('<td ' + align + ' data-grid-fild="' + name + '"><div class="maze-grid-content-innert">' + value + '</div></td>');
                                }
                                if (options.fild_type && options.icon.hasOwnProperty(row[options.fild_type]))
                                {
                                    $htmlRow.find('.maze-grid-tree-folder').css('background-image', 'url("' + options.icon[row[options.fild_type]] + '")');
                                }

                                return false;
                            }
                        })
                    })

                    $table.find('tbody').append($htmlRow);
                    selfClass.createRow(row[options.id], $table, array, level);

                })
            }
        },
        getData: function ()
        {
            var selfClass = this, options = this.options;

            var result = {};
            $.each(selfClass.alloptions.data, function (i, obj) {
                if (!(obj[options.parent] in result))
                {
                    result[obj[options.parent]] = Array();
                }
                result[obj[options.parent]].push(obj);

            })

            return result;

        }

    })

})(jQuery);

/**
 * Сортировка - перемещение
 */
(function ($) {

    $.mazeGrid.addPlugin("movesort", {
        version: 1.0,
        defaults: {
            sortgroup: [],
            sorttable: true,
            handle: null,
            move: {
                open:false,
                fildkey: null,
                accept: {}
            }
        },
        _init: function () {

            var selfClass = this, options = this.options;

            this.$element.bind('afterGetContent.mazegrid', function () {

                var sortStart;
                if (typeof options.sorttable == 'function')
                {
                    sortStart = options.sorttable.call(selfClass.$element, selfClass.alloptions)

                }
                else
                {
                    sortStart = options.sorttable
                }

                if (!sortStart)
                    return false;


                selfClass.$element.find('.maze-grid-content tbody tr').each(function () {
                    if ($(this).children('td').eq(0).is('[data-grid-fild]'))
                    {
                        $(this).addClass('maze-grid-movesort')
                    }
                });

                if (selfClass.alloptions.mode !== "tree")
                {
                    selfClass.sortDedault();
                }
                else
                {
                    selfClass.moveTree();
                }

            })
        },
        sortDedault: function ()
        {
            var selfClass = this;

            if (this.$element.find('.maze-grid-content tbody').is('.ui-sortable'))
            {
                $(this).find('.maze-grid-content tbody').sortable('refresh');
                return false;
            }


            function getKeygroup(obj)
            {
                var key = $.map(selfClass.options.sortgroup, function (fild) {
                    if (fild in obj)
                    {
                        return obj[fild];
                    }
                })
                if (0 in key)
                    return key.join('-');
                return false;
            }
            if (!selfClass.options.handle)
            {
                this.$element.find('.maze-grid-movesort').each(function () {
                    $(this).find('td').eq(0).find('.maze-grid-content-innert').addClass('maze-grid-movesort-handle')
                })
            }
            this.$element.find('.maze-grid-content tbody').sortable({
                axis: 'y',
                opacity: 1,
                cursor: 'move',
                items: '.maze-grid-movesort',
                handle: selfClass.options.handle || '.maze-grid-movesort-handle',
                placeholder: 'maze-grid-movesort-placeholder',
                beforeStop: function (e, ui) {
                },
                start: function (e, ui) {

                    ui.item.find('td').each(function () {
                        var $self = $(this);
                        ui.placeholder.append($('<td>').width($self.width()).height($self.height()))
                    });
                    ui.placeholder.height(ui.item.height() + 10).width(ui.item.width());

                    var selfData = ui.item.data('gridRow');


                    var keySelf = getKeygroup(selfData);
                    if (keySelf)
                    {
                        var newSort = $(this).find('tr.maze-grid-movesort').filter(function () {
                            if ($(this).data('gridRow'))
                            {
                                if (keySelf == getKeygroup($(this).data('gridRow')))
                                    return true;
                            }
                            return false;
                        })

                        $(this).find('tr').removeClass('maze-grid-movesort').addClass('maze-grid-movesort-disable');
                        newSort.removeClass('maze-grid-movesort-disable').addClass('maze-grid-movesort');
                    }

                    $(this).sortable('refresh');

                },
                stop: function (e, ui) {
                    $(this).find('tr').removeClass('maze-grid-movesort-disable').filter(function () {
                        return $(this).find('[data-grid-fild]').eq(0).is('td')
                    }).addClass('maze-grid-movesort')
                },
                update: function (e, ui) {
                    var result = Array();

                    var keySelf = getKeygroup(ui.item.data('gridRow'));
                    var groupSort = Array();

                    $(this).parents('.maze-grid-content').find('tr')
                            .filter(function () {
                                return $(this).find('td[data-grid-fild] .maze-grid-content-innert').eq(0).is('.maze-grid-content-innert')
                            })
                            .each(function () {
                                result.push($(this).data('gridRow'));
                                if (keySelf == getKeygroup($(this).data('gridRow')))
                                {
                                    groupSort.push($(this).data('gridRow'));
                                }
                            })

                    if (0 in result)
                    {
                        selfClass._trigger('sortrowupdate', {el: ui.item, item: ui.item.data('gridRow'), group: keySelf ? groupSort : result});
                        selfClass.alloptions.data = result;
                    }

                }
            })

        },
        moveTree: function ()
        {
            var selfClass = this, options = this.options;

            function getCildrenTree($elem, array)
            {
                var child = selfClass.$element.find('tr[data-parent=' + $elem.attr('data-id') + ']');
                child.each(function () {
                    if ($(this).is('tr'))
                    {
                        array.push($(this));
                        getCildrenTree($(this), array);
                    }
                })

            }

            function getCildrenID($elem)
            {
                var childId = Array();
                getCildrenTree($elem, childId);
                return $.map(childId, function (val) {
                    return  clearID(val.attr('data-id'))
                })
            }

            function clearID(ID)
            {
                if (typeof ID == 'string')
                {
                    return ID.replace(/maze-gred-tree-([a-z0-9-_.]+)/, '$1')
                }
                return 0;
            }

            function setChildrenMarginLeft($elem)
            {
                var parentLeft = Number($elem.find('.maze-grid-tree-indenter').parent().css('margin-left').replace(/-?(\d+)px?/, '$1'))
                var child = selfClass.$element.find('tr[data-parent=' + $elem.attr('data-id') + ']');
                child.each(function () {
                    if ($(this).is('tr'))
                    {
                        $(this).find('.maze-grid-tree-indenter').parent().css('margin-left', parentLeft + 20)
                        setChildrenMarginLeft($(this));
                    }
                })

            }

            function isAccept(self, target)
            {
                var selfType = self.data('gridRow');
                selfType = options.move.fildkey in selfType ? selfType[options.move.fildkey] : null;
                var targetType = target.data('gridRow');
                targetType = options.move.fildkey in targetType ? targetType[options.move.fildkey] : null;

                if (targetType == null || selfType == null)
                    return true;

                if (targetType in options.move.accept)
                {

                    if (options.move.accept[targetType] == 'none')
                    {
                        return false;
                    }
                    else if ($.isArray(options.move.accept[targetType]))
                    {
                        return $.inArray(selfType, options.move.accept[targetType]) == -1 ? false : true;
                    }
                }
                return true;
            }

            function isAcceptRoot(self)
            {
                var selfType = self.data('gridRow');
                selfType = options.move.fildkey in selfType ? selfType[options.move.fildkey] : null;

                if (selfType == null)
                    return true;

                if (options.move.accept.hasOwnProperty('root'))
                {
                    return $.inArray(selfType, options.move.accept.root) == -1 ? false : true;
                }

                return true;
            }

            this.$element.find('.maze-grid-movesort').each(function () {
                $(this).find('td').eq(0).find('.maze-grid-content-innert').addClass('maze-grid-movesort-handle')
            })

            $(".maze-grid-movesort-placeholder-tree").hide();
            $(".maze-grid-movesort-helper").remove();

            var $helper = $('<div>').addClass('maze-grid-movesort-placeholder-tree').append($('<span>')).appendTo('body').hide();

            function draggableRow()
            {


                selfClass.$element.find('.maze-grid-movesort').draggable({
                    handle: '.maze-grid-movesort-handle',
                    helper: function () {
                        var $self = $(this);

                        return $('<div>').addClass('maze-grid-movesort-helper').append(
                                $self.find('.maze-grid-tree-indenter').parent().text()
                                );
                    },
                    start: function (e, ui)
                    {
                        var child = Array();

                        getCildrenTree($(this), child);

                        $.each(child, function (i, val) {
                            $(this).removeClass('maze-grid-movesort').addClass('maze-grid-movesort-disable');
                        });
                        $(this).parent().find('tr:hidden').each(function () {
                            $(this).removeClass('maze-grid-movesort').addClass('maze-grid-movesort-disable');
                        })
                        draggableRow()
                    },
                    drag: function (e, ui) {

                        var $self = $(this);

                        var $parent = $(this).parent().find('.maze-grid-movesort').not(this)
                        var helpTop = Math.ceil(ui.offset.top);
                        var $parentDis = $(this).parent().find('.maze-grid-movesort-disable')
                        $parentDis.each(function () {
                            var $seldDis = $(this)


                            if (helpTop > Math.ceil($seldDis.offset().top) && helpTop < Math.ceil($seldDis.offset().top) + $seldDis.outerWidth(true))
                            {
                                $self.parent().find('tr').removeClass('after-insert-tree append-insert-tree before-insert-tree marker-insert-tree');
                                $helper.hide();
                            }
                        })
                        $parent.each(function () {
                            var selfArr = $(this).find('.maze-grid-tree-indenter');
                            var selfRow = selfArr.parent();
                            var isLeft = ui.offset.left > selfRow.offset().left && ui.offset.left < selfRow.offset().left + selfRow.outerWidth(true);
                            var flagDrop = true;
                            var $selfTarget = $(this);
                            $selfTarget.removeClass('after-insert-tree append-insert-tree before-insert-tree marker-insert-tree');

                            if (selfArr.is('.maze-grid-tree-indenter'))
                            {
                                var top = Math.ceil(selfArr.offset().top);
                                var bottom = top + selfArr.height();


                                if (helpTop > top && helpTop < bottom && isLeft)
                                {
                                    if (!isAccept($self, $selfTarget))
                                    {
                                        flagDrop = false;
                                        return false;
                                    }
                                    $selfTarget.removeClass('before-insert-tree  after-insert-tree').addClass('append-insert-tree marker-insert-tree');

                                    $helper
                                            .show()
                                            .width(selfArr.parent().width())
                                            .css({
                                                top: selfArr.offset().top + (selfArr.height() / 2),
                                                left: selfArr.offset().left
                                            });
                                     if(options.move.open){
                                         selfClass.$element.mazeGrid("expandedTree", $selfTarget.attr('data-id'));
                                     }       
                                    
                                }

                            }

                            if (selfRow.is('.maze-grid-content-innert'))
                            {


                                var topRow = Math.ceil(selfRow.offset().top);
                                var topBottomRow = Math.ceil(selfArr.offset().top);
                                var bottomTopRow = Math.ceil(selfArr.offset().top) + selfArr.height();
                                var bottomBotRow = Math.ceil(selfRow.offset().top) + selfRow.outerHeight(true);

                                if (helpTop > topRow && helpTop < topBottomRow && isLeft)
                                {

                                    if ($selfTarget.attr('data-parent'))
                                    {
                                        var parentTarget = $selfTarget.parent().find('tr[data-id=' + $selfTarget.attr('data-parent') + ']')

                                        if (parentTarget.is('tr'))
                                        {

                                            if (!isAccept($self, parentTarget))
                                            {
                                                flagDrop = false;
                                                return false;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if (!isAcceptRoot($self))
                                        {
                                            flagDrop = false;
                                            return false;
                                        }
                                    }

                                    $selfTarget.removeClass('after-insert-tree append-insert-tree').addClass('before-insert-tree marker-insert-tree');
                                    $helper
                                            .show()
                                            .width(selfArr.parent().width())
                                            .css({top: selfRow.offset().top, left: selfRow.offset().left})

                                }

                                else if (helpTop > bottomTopRow && helpTop < bottomBotRow && isLeft)
                                {
                                    if ($selfTarget.attr('data-parent'))
                                    {
                                        var parentTarget = $selfTarget.parent().find('tr[data-id=' + $selfTarget.attr('data-parent') + ']')
                                        if (parentTarget.is('tr'))
                                        {
                                            if (!isAccept($self, parentTarget))
                                            {
                                                flagDrop = false;
                                                return false;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if (!isAcceptRoot($self))
                                        {
                                            flagDrop = false;
                                            return false;
                                        }
                                    }

                                    var rr = Math.ceil(selfArr.offset().top) + selfArr.height();
                                    $selfTarget.removeClass('before-insert-tree append-insert-tree').addClass('after-insert-tree marker-insert-tree');
                                    $helper
                                            .show()
                                            .width(selfArr.parent().width())
                                            .css({top: rr + (((Math.ceil(selfRow.offset().top) + selfRow.outerHeight(true)) - rr) / 2), left: selfRow.offset().left})

                                }

                                if (!isLeft || !flagDrop)
                                {
                                    $helper.hide();
                                    $selfTarget.removeClass('after-insert-tree append-insert-tree before-insert-tree marker-insert-tree');
                                }


                            }

                        })
                    },
                    containment: selfClass.$element,
                    appendTo: 'body',
                    stop: function (e, ui) {
                        $(this).parent().find('.maze-grid-movesort-disable').each(function () {
                            $(this).removeClass('maze-grid-movesort-disable').addClass(' maze-grid-movesort');
                        });
                        draggableRow();
                        $helper.hide();
                    }
                })
            }

            draggableRow();


            this.$element.droppable({
                accept: function (el) {
                    if (el.attr('data-id'))
                        return true;
                    return false;
                },
                drop: function (e, ui) {

                    var marker = $(this).find('.marker-insert-tree').eq(0);

                    var result = Array();
                    var fildParent = selfClass.alloptions.tree.parent
                    var parent = marker.attr('data-parent');
                    var targetID = marker.attr('data-id');
                    if (!marker.is('.marker-insert-tree'))
                        return false;
                    var dataRow = ui.draggable.data('gridRow');
                    // прeдшествующий
                    if (marker.is('.before-insert-tree'))
                    {

                        if (parent)
                        {
                            dataRow[fildParent] = clearID(parent)
                            ui.draggable.attr('data-parent', parent);
                        }
                        else
                        {
                            dataRow[fildParent] = 0;
                            ui.draggable.removeAttr('data-parent');
                        }
                        ui.draggable.data('gridRow', dataRow);


                        marker.before(ui.draggable);

                        ui.draggable.find('.maze-grid-tree-indenter').parent().css('margin-left', marker.find('.maze-grid-tree-indenter').parent().css('margin-left'))

                    }
                    // последующий

                    if (marker.is('.after-insert-tree'))
                    {

                        if (parent)
                        {
                            dataRow[fildParent] = clearID(parent)
                            ui.draggable.attr('data-parent', parent);
                        }
                        else
                        {
                            dataRow[fildParent] = 0;
                            ui.draggable.removeAttr('data-parent');
                        }
                        ui.draggable.data('gridRow', dataRow);


                        marker.after(ui.draggable);

                        ui.draggable.find('.maze-grid-tree-indenter').parent().css('margin-left', marker.find('.maze-grid-tree-indenter').parent().css('margin-left'))
                    }

                    // дочерний
                    if (marker.is('.append-insert-tree'))
                    {

                        dataRow[fildParent] = clearID(targetID)
                        ui.draggable.attr('data-parent', targetID);

                        ui.draggable.data('gridRow', dataRow);


                        marker.after(ui.draggable);

                        var marginLeft = marker.find('.maze-grid-tree-indenter').parent().css('margin-left').replace(/-?(\d+)px?/, '$1');

                        ui.draggable.find('.maze-grid-tree-indenter').parent().css('margin-left', Number(marginLeft) + 20)

                    }

                    var children = Array();

                    getCildrenTree(ui.draggable, children);

                    $.each(children, function (i, el) {
                        ui.draggable.after(el);
                    })
                    setChildrenMarginLeft(ui.draggable)

                    marker.removeClass('after-insert-tree append-insert-tree before-insert-tree marker-insert-tree');

                    $(this).find('tr[data-id]').each(function () {
                        result.push($(this).data('gridRow'));
                    })

                    var levelRow;
                    if (ui.draggable.attr('data-parent')){

                        levelRow = $(this).find('.maze-grid-content tr[data-parent=' + ui.draggable.attr('data-parent') + ']')

                    }
                    else
                    {
                        levelRow = $(this).find('.maze-grid-content tr').not('[data-parent]')
                    }
                    var order = levelRow.map(function () {
                        return $(this).data('gridRow')
                    }).get();

                    
                    
                    if (result.hasOwnProperty(0))
                    {
                        $.each(selfClass.alloptions.data, function (i, val) {
                            $.each(result, function (ni, nval) {
                                if (val == nval) {
                                    selfClass.alloptions.data[i].mazegridsort = ni;                                   
                                }
                            })
                        })
                        
                        
                        
                        selfClass.alloptions.data.sort(function (a, b) {
                            if (a.hasOwnProperty('mazegridsort') && b.hasOwnProperty('mazegridsort')) {
                                if (a.mazegridsort < b.mazegridsort)
                                    return -1;
                                if (a.mazegridsort > b.mazegridsort)
                                    return 1;
                                return 0;
                            }

                        })
 
                        selfClass.alloptions.data.map(function (d) {
                            delete d['mazegridsort']
                            return d
                        })


                        setTimeout(function () {
                            $helper.remove();
                            selfClass.$element.mazeGrid('core::_getContent')
                            selfClass._trigger('move', {id: clearID(ui.draggable.attr('data-id')), parent: dataRow[fildParent], order: order})
                        }, 200);
                    }
                }
            })

        }
    })

})(jQuery);
/*
 * Контексное меню
 * 
 * {
 *  field_name:{
 *      items:'.menu-icon-handle',
 *      visible: function(data){if(data.locked == 1){ this.remove(); } return data.locked == 0;}
 *      data:[
 *          {"type": 'link', "spriteClass": 'menu-icon-edits', "href":'', title:'Заголовок'},
 *          {"type": 'link', "spriteClass": 'menu-icon-edits', "href":'', title:'Заголовок'}
 *      ]
 *  }
 * }
 */
(function ($) {

    $.mazeGrid.addPlugin("contextmenu", {
        version: 1.0,
        defaults: {},
        _init: function () {

            var selfClass = this, options = this.options;

            this.$element.bind('afterGetContent.mazegrid', function () {
                var $self = $(this)
                $self.find('.context-menu-maze').mazeContext('close').remove();
                $.each(options, function (fild, data) {
                    var $target = $self.find('.maze-grid-content [data-grid-fild=' + fild + '] .maze-grid-content-innert');
                    var items = 'items' in data ? data.items : false;

                    if ($target.is('.maze-grid-content-innert'))
                    {
                        if (items && $target.find(items).is(items))
                        {
                            $target = $target.find(items)
                        }
                        if (data)
                        {

                            $.each(data.data, function (i, links) {

                                if (!('actions' in links) && 'href' in links)
                                {

                                    links.actions = function (target, e)
                                    {
                                        var data = target.parents('tr').data('gridRow');
                                        var url = decodeURIComponent($(this).attr('href'));
                                        
                                        var prop = url.match(/\{\+\+([^\+]+)\+\+\}/g);

                                        $.each(prop, function (i, p) {
                                            var cp = p.replace(/\{\+\+([^\+]+)\+\+\}/, "$1");
                                            if (cp in data)
                                                url = url.replace((new RegExp("\\{\\+\\+" + cp + "\\+\\+\\}")), data[cp]);
                                        })
                                       
                                        document.location = url;
                                    }
                                }
                            })
                        }
                        if ('visible' in data) {
                            $target = $target.filter(function () {
                                return data.visible.call($(this), $(this).parents('tr').data('gridRow'))
                            })
                        }
                        $target.each(function () {
                            var itemsMenu = $.extend({}, data);
                            var $itemsTr = $(this)

                            itemsMenu.data = $.map(data.data, function (val) {

                                if ('visible' in val) {
                                    if (!val.visible.call($itemsTr, $itemsTr.parents('tr').data('gridRow'))) {
                                        return null
                                    }
                                }
                                return val;
                            })
                            $(this).mazeContext(
                                    $.extend({
                                        position: {
                                            my: 'center top+10',
                                            at: 'center bottom',
                                            using: function (position, feedback) {
                                                $(this).css(position);

                                                var leftT = Math.ceil(feedback.target.left - feedback.element.left + (feedback.target.width / 2) - 10);
                                                var leftE = Math.ceil((feedback.element.width / 2) - 10);

                                                if ($(this).find('.arrow').is('.arrow'))
                                                {
                                                    $(this).find('.arrow').remove()
                                                }

                                                $("<div>")
                                                        .addClass("arrow")
                                                        .css({left: Math.min(leftT, leftE)})
                                                        .addClass(feedback.vertical)
                                                        .addClass(feedback.horizontal)
                                                        .appendTo(this);

                                            }
                                        },
                                        appendTo: $self.get(),
                                        class_menu: 'default maze-grid-content-context',
                                        onAfterOpen: function () {
                                            this.parents('.maze-grid-content').find('.maze-grid-contextmenu-open').removeClass('maze-grid-contextmenu-open');
                                            this.addClass('maze-grid-contextmenu-open')
                                        },
                                        onAfterClose: function () {
                                            this.removeClass('maze-grid-contextmenu-open')
                                        }
                                    }, itemsMenu || {})
                                    )
                        })

                    }
                })

            })
        }

    })
})(jQuery);

/*
 * Подсказка
 */
(function ($) {

    $.mazeGrid.addPlugin("tooltip", {
        version: 1.0,
        defaults: {
            delay: 800,
            template: '<h1>{TITLE}</h1><span>{TEXT}</span>'
        },
        _init: function () {

            var selfClass = this, options = this.alloptions, opt = this.options;

            $.each(options.colModel, function (i, val) {
                if ('help' in val && val.help !== '')
                {
                    var target = selfClass.$element.find('.maze-grid-header td[data-grid-fild=' + val.name + '] .maze-grid-header-innert');
                    if (target.is('.maze-grid-header-innert'))
                    {
                        target.attr('title', opt.template.replace(/{TITLE}/, val.title).replace(/{TEXT}/, val.help))

                    }
                }

            })

            selfClass.$element.find('.maze-grid-header td[data-grid-fild]').tooltip({
                tooltipClass: 'maze-grid-tooltip',
                position: {
                    my: " bottom",
                    at: "top-10",
                    using: function (position, feedback) {
                        $(this).css(position);
                        $("<div>")
                                .addClass("arrow").css({left: Math.ceil(feedback.target.left - position.left + (feedback.target.width / 2) - 10)})
                                .addClass(feedback.vertical)
                                .addClass(feedback.horizontal)
                                .appendTo(this);
                    }
                },
                show: {
                    delay: selfClass.options.delay
                }
            })

        }

    })
})(jQuery);


/*
 * Кнопки
 */
(function ($) {

    $.mazeGrid.addPlugin("buttonfild", {
        version: 1.0,
        defaults: {},
        _init: function () {

            var selfClass = this, options = this.options, alloptions = this.alloptions;

            var defaultOpt = {
                icon: '',
                spriteClass: '',
                value: null,
                click: $.noop
            };

            function toggleBtn(btn, optionsBtn)
            {
                var change = btn.attr('data-change');

                if (optionsBtn.spriteClass !== '')
                {
                    if ($.isArray(optionsBtn.spriteClass))
                    {
                        if (change == 'enable')
                        {
                            btn.removeClass(optionsBtn.spriteClass[1]).addClass(optionsBtn.spriteClass[0]);
                        }
                        else
                        {
                            btn.removeClass(optionsBtn.spriteClass[0]).addClass(optionsBtn.spriteClass[1]);
                        }
                    }
                    else
                    {
                        btn.addClass(optionsBtn.spriteClass)
                    }
                }
                else if (optionsBtn.icon !== '')
                {
                    if ($.isArray(optionsBtn.icon))
                    {

                        if (change == 'enable')
                        {
                            btn.css('background-image', "url('" + optionsBtn.icon[0] + "')")
                        }
                        else
                        {
                            btn.css('background-image', "url('" + optionsBtn.icon[1] + "')")
                        }
                    }
                    else
                    {
                        btn.css('background-image', "url('" + optionsBtn.icon + "')")
                    }
                }

            }

            this.$element.bind('afterGetContent.mazegrid', function () {
                var $self = $(this)

                $.each(options, function (fild, data) {
                    var $target = $self.find('.maze-grid-content [data-grid-fild=' + fild + '] .maze-grid-content-innert');

                    $target.each(function () {
                        var $selfCl = $(this);
                        var value = $.trim($selfCl.text());
                        var btn = $('<a>', {href: '#'}).attr('data-value', value).addClass('maze-grid-content-btn');
                        var optionsBtn = $.extend({}, defaultOpt, data);
                        if (value == 1)
                        {
                            btn.attr('data-change', 'enable')
                        }
                        else
                        {
                            btn.attr('data-change', 'disable')
                        }
                        toggleBtn(btn, optionsBtn);

                        btn.data('buttonGrid', optionsBtn);

                        btn.click(function (e) {
                            var dataRow = $(this).parents('tr').data('gridRow');
                            var fild = $(this).parents('td[data-grid-fild]').attr('data-grid-fild')
                            if (!$(this).data('buttonGrid').click.call(selfClass.$element, e, $(this).attr('data-change'), dataRow))
                                return false;
                            if ($(this).attr('data-change') == 'enable')
                            {
                                $(this).attr('data-change', 'disable')
                            }
                            else
                            {
                                $(this).attr('data-change', 'enable')
                            }
                            toggleBtn($(this), $(this).data('buttonGrid'))


                            var $selfBtn = $(this);
                            $.each(alloptions.data, function (i, obj) {
                                if (obj == dataRow)
                                {
                                    obj[fild] = $selfBtn.attr('data-change') == 'enable' ? 1 : 0;
                                }
                            })


                            return false;
                        })

                        $selfCl.html(btn)
                    })

                })

            })
        }

    })
})(jQuery);

/*
 * Редактирование
 */
(function ($) {

    $.mazeGrid.addPlugin("edits", {
        version: 1.0,
        typeedits: {},
        defaults: {
            events: 'dblclick',
            filds: {}
        },
        _init: function () {

            var selfClass = this, options = this.options, alloptions = this.alloptions;

            this.$element.bind('afterGetContent.mazegrid', function () {
                var $self = $(this)

                $.each(options.filds, function (fild, data) {
                    var $target = $self.find('.maze-grid-content [data-grid-fild=' + fild + '] .maze-grid-content-innert');

                    $target.bind(options.events, function (e) {
                        var $selfCl = $(this);
                        if ($selfCl.attr('data-edits'))
                            return false;
                        var fild = $selfCl.parents('td[data-grid-fild]').attr('data-grid-fild');
                        var value = $.trim($selfCl.text());
                        var type = options.filds[fild];
                        var dataRow = $selfCl.parents('tr').data('gridRow')
                        if (typeof type !== 'object' || type.type == '')
                            return false;

                        if (!(type.type in selfClass.typeedits))
                            return false;

                        var objEdits = selfClass.typeedits[type.type];

                        var jQObj = objEdits._create.call($selfCl, $.extend(objEdits.options, 'opt' in type ? type.opt || {} : {}), value, dataRow)

                        var dataRow = $(this).parents('tr').data('gridRow');
                        $selfCl.attr('data-edits', 1);
                        $selfCl.html(jQObj);


                    })

                })

                $self.find('tr').click(function () {
                    selfClass.$element.find('.maze-grid-content tr').not(this).find('[data-edits]').each(function () {
                        var $self = $(this);
                        var fild = $self.parent().attr('data-grid-fild');
                        var type = options.filds[fild];

                        if (typeof type !== 'object' || type.type == '')
                            return false;

                        if (!(type.type in selfClass.typeedits))
                            return false;

                        var objEdits = selfClass.typeedits[type.type];
                        var dataRow = $self.parents('tr').data('gridRow')
                        var newValue = objEdits._save.call($self, $.extend(objEdits.options, 'opt' in type ? type.opt || {} : {}), $self.children(), dataRow, dataRow[fild]);

                        if (!newValue)
                            return false;

                        selfClass._trigger('saveedits', {type: type.type, fild: fild, val: newValue, oldval: dataRow[fild], data: dataRow, element: $self})

                        $.each(alloptions.data, function (i, obj) {
                            if (obj == dataRow)
                            {
                                obj[fild] = newValue;
                            }
                        })
                        $self.html(newValue).removeAttr('data-edits');


                    })

                })


            })
        },
        addEditsType: function (type, obj)
        {
            var opt = $.extend({
                options: {},
                _create: function (opttype, value) {
                },
                _save: function (opttype, $el) {
                }
            }, obj || {});

            if (type in this.typeedits)
                return false;
            this.typeedits[type] = opt;
        }

    });

// тестовое поле	
    $.mazeGrid.getPlugins('edits', 'addEditsType', 'inputtext', {
        options: {
            width: 100,
            callback: $.noop
        },
        _create: function (opt, value) {
            var left = this.position().left
            return $('<input>', {type: "text"})
                    .width(opt.width)
                    .val(value)
                    .addClass('maze-grid-edits-inputtext');
        },
        _save: function (opttype, $el, dataRow, oldValue) {
            var value = $el.val();
            if (!opttype.callback(value, $el, dataRow))
            {
                return false
            }
            return value;
        }
    });

    // селектор
    $.mazeGrid.getPlugins('edits', 'addEditsType', 'select', {
        options: {
            width: 100,
            options: {class: 'form-control'},
            getOption: $.noop,
            beforeSave: $.noop
        },
        _create: function (opt, value, dataRow) {
            var left = this.position().left, $self = this;

            function createSelect(option) {
                var self = this;
                var $select = $('<select>', opt.options);
                if ($.isArray(option)) {
                    $.each(option, function (i, obj) {
                        $select.append($('<option>', obj));
                    })
                }

                $self.html($select)
            }

            if (typeof opt.getOption == 'function') {
                opt.getOption(value, dataRow, createSelect)
            }

        },
        _save: function (opttype, $el, dataRow, oldValue) {
            var value = $el.val();

            return opttype.beforeSave(value, oldValue, dataRow, $el);
        }
    });
// тестовое поле AJAX	
    $.mazeGrid.getPlugins('edits', 'addEditsType', 'inputtextajax', {
        options: {
            width: 100,
            url: '',
            texterror: "Ошибка формата строки",
            textbtn: 'Выполнить',
            textload: 'Выполняю...',
            check: "[a-z0-9_.-]+",
            param: function (val, row) {
                return {name: val}
            },
            req: function (data, $wrap) {
                return data.flag
            },
            position: {my: "right center", at: 'left center'}
        },
        _create: function (opt, value, dataRow) {
            var left = this.position().left;
            var $selfTR = this.parents('tr');
            var $tr = this.parents('tbody').find('tr').not($selfTR).eq(0);

            var $wrap = $('<div>').addClass('input-append control-group');
            $wrap.append(
                    $('<input>', {type: "text"})
                    .width(opt.width)
                    .val(value)
                    .addClass('maze-grid-edits-inputtext')
                    )
                    .append(
                            $('<button>', {type: 'button'})
                            .addClass('btn btn-primary')
                            .text(opt.textbtn)
                            .click(function () {
                                var newValue = $(this).siblings('input').val();
                                var $self = $(this);
                                $(this).parent().tooltip({
                                    items: $(this).parent(),
                                    content: opt.texterror,
                                    position: opt.position,
                                    tooltipClass: 'error-input'
                                }).tooltip("disable")
                                if (!(new RegExp(opt.check)).test(newValue))
                                {
                                    $(this).parent().addClass('error')
                                    $(this).parent().tooltip("enable").tooltip("open")
                                }
                                else
                                {
                                    $wrap.find('button').text(opt.textload).attr('disabled', true)
                                    $.post(opt.url, opt.param(newValue, dataRow), function (data) {
                                        $wrap.find('button').text(opt.textbtn).removeAttr('disabled')
                                        if (!opt.req(data, $wrap))
                                        {
                                            $self.parent().addClass('error')
                                            $self.parent().tooltip("enable").tooltip("open")
                                        }
                                        else
                                        {
                                            $wrap.data('datainput-yes', 1);
                                            $tr.trigger('click')
                                        }
                                    }, 'json')
                                }
                            })
                            )
            return $wrap;
        },
        _save: function (opttype, $el, dataRow, oldValue) {
            var value = $el.find('input').val();
            if (oldValue == value)
                return oldValue;
            if (!$el.data('datainput-yes'))
                return false;
            return value;
        }
    });

})(jQuery);



/*
 * Подсказка в таблице
 */
(function ($) {

    $.mazeGrid.addPlugin("tooltip_content", {
        version: 1.0,
        typeedits: {},
        defaults: {
            template: '<div><strong>{LABEL}</strong>: {VALUE}</div>',
            delay: 800,
            filds: null,
        },
        _init: function () {

            var selfClass = this, options = this.options, alloptions = this.alloptions;

            this.$element.bind('afterGetContent.mazegrid', function () {
                var $self = $(this)

                if (options.filds == null)
                    return false;

                $.each(options.filds, function (fild, data) {
                    var $target = $self.find('.maze-grid-content [data-grid-fild=' + fild + '] .maze-grid-content-innert');

                    $target.each(function () {

                        var dataRow = $(this).parents('tr').data('gridRow');

                        if (dataRow && $(this).is('.maze-grid-content-innert'))
                        {
                            var help = '';

                            $.each(data, function (label, fild_val) {
                                if (dataRow.hasOwnProperty(fild_val))
                                {
                                    help += options.template.replace(/{LABEL}/, label).replace(/{VALUE}/, dataRow[fild_val]);
                                }
                            })

                            $(this).attr('title', help)
                        }
                        else
                        {
                            return true;
                        }

                    })

                    $target.tooltip({
                        tooltipClass: 'maze-grid-tooltip',
                        position: {
                            my: " bottom",
                            at: "top-10",
                            using: function (position, feedback) {
                                $(this).css(position);
                                $("<div>")
                                        .addClass("arrow").css({left: Math.ceil(feedback.target.left - position.left + (feedback.target.width / 2) - 10)})
                                        .addClass(feedback.vertical)
                                        .addClass(feedback.horizontal)
                                        .appendTo(this);
                            }
                        },
                        show: {
                            delay: options.delay
                        }
                    })


                })


            })
        }
    });


})(jQuery);					