(function () {

    var MAZE = (function () {

        function MAZE()
        {
            this.options = null;

            this.lang = null;

            this.ajaxRequestCount = 0;

            this.init();

        }

        MAZE.prototype.init = function (options)
        {
            this.options = $.extend({
                id_message: 'system-labyrinth-message',
                startPreload: $.noop,
                endPreload: $.noop,
                endAllPreload: $.noop,
                preloader: true,
                async: true,
                serialize: false,
                action: false,
                dataType: "json",
                type: "POST",
            }, options || {});
        }

        MAZE.prototype.createPlugin = function (name, plugin)
        {
            if (name in MAZE.prototype)
            {
                throw new Error("current plugin name is already taken");
            }
            MAZE.prototype[name] = plugin;
        }

        MAZE.prototype.numRandom = function (length)
        {
            var result = Array();

            for (i = 0; i < length; i++)
            {
                result.push(Math.ceil(Math.random() * 10));
            }
            return result.join('')
        }

        MAZE.prototype.setLang = function (name, value)
        {
            if (this.lang == null)
                this.lang = {};

            var self = this;

            if (typeof name == "object")
            {
                $.each(name, function (cons, valLang)
                {
                    self.setLang(cons, valLang);
                })

                return true;
            }

            if (this.lang.hasOwnProperty(name) && value == null)
            {
                delete this.lang[name];
                return value;
            }
            else if (value == null)
            {
                return false;
            }

            this.lang[name] = value;

            return value;
        }

        MAZE.prototype.getLang = function (name)
        {
            if (this.lang == null)
                return false;

            if (this.lang.hasOwnProperty(name))
            {
                return this.lang[name];
            }

            return false;
        }

        MAZE.prototype.alertBtn = function (title, text, h, w)
        {
            var selfClass = this;

            var h = h || 200;
            var w = w || 300;

            $('<div>').append("<div class=\"helper-message-tip\">" + text + "</div>").mazeDialog({
                title: title,
                draggable: false,
                resize: false,
                height: h,
                width: w,
                modal: true,
                mode: "float",
                toolbarhead: {minidialog: null, fulldialog: null},
                buttons: [
                    {
                        label: selfClass.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE'),
                        class_btn: "maze-btn-warning",
                        class_icon: "",
                        action: function (e, obj) {
                            this.mazeDialog('close')
                        }
                    }
                ],
                close: function () {
                    this.mazeDialog('destroy')
                }
            });

        }

        MAZE.prototype.ajax = function (options)
        {
            var optionsGlobal = this.options, self = this;

            var options = $.extend({
                url: '',
                before: function () {
                },
                handler: function (data) {
                },
                param: {},
                preloader: optionsGlobal.preloader,
                async: optionsGlobal.async,
                serialize: optionsGlobal.serialize,
                action: optionsGlobal.action,
                dataType: optionsGlobal.dataType,
                type: optionsGlobal.type,
                error: function (xhr, e) {
                }
            }, options || {})
            var url, set_data = '', dataForm;

            if (options.url !== '')
            {
                url = options.url;
            }
            else if (options.action)
            {
                url = $(options.action).attr('action');
            }
            else
            {
                return false;
            }

            if (options.serialize && options.action)
            {
                dataForm = $(options.action).serialize();
                if (dataForm !== "")
                {
                    set_data += dataForm + '&';
                }
            }

            if (typeof options.param == "object" && !$.isEmptyObject(options.param))
            {
                set_data += $.param(options.param);
            }
            else if (typeof options.param == "string") {
                set_data += options.param;
            }

            url = url.replace(/\//, "/").replace(/^(.+)\/$/, '$1');

            set_data += 'clear=ajax';
            this.ajaxRequestCount++;
            $.ajax({
                url: url,
                data: set_data,
                type: options.type,
                async: options.async,
                dataType: options.dataType,
                error: function (xhr, e) {

                    if (typeof options.error == "function") {
                        options.error(xhr, e);
                    }

                    self.ajaxRequestCount--;

                    if (options.preloader) {
                        optionsGlobal.endPreload();
                        if (self.ajaxRequestCount == 0)
                        {
                            optionsGlobal.endAllPreload()
                        }
                    }

                },
                cache: false,
                beforeSend: function () {
                    if (options.preloader)
                    {
                        optionsGlobal.startPreload()
                    }
                    options.before();
                },
                success: function (data) {

                    self.ajaxRequestCount--;

                    if (options.preloader) {
                        optionsGlobal.endPreload();
                        if (self.ajaxRequestCount == 0)
                        {
                            optionsGlobal.endAllPreload()
                        }
                    }
                    if (typeof options.handler == "function") {
                        options.handler(data);
                    }
                }
            })
        }

        MAZE.prototype.loadAsync = function (teg, obj)
        {

            var teg = $("<" + teg + ">");

            if (typeof obj !== "object")
                return false;

            for (var name in obj)
            {
                if (!obj.hasOwnProperty(name))
                    continue;
                if (typeof obj[name] === "function")
                    continue;
                if (name == "innerHTML")
                {
                    teg.text(obj[name]);
                    continue;
                }
                teg.attr(name, obj[name])
            }
            $("head").append(teg);

        }

        MAZE.prototype.loadHeader = function (data, callback)
        {
            var self = this;

            if (data && "head" in data)
            {
                if ("stylesheet" in data.head && data.head.stylesheet)
                {
                    $.each(data.head.stylesheet, function (n) {
                        self.loadAsync("link", data.head.stylesheet[n]);
                    })
                }

                if ("script" in data.head && data.head.script)
                {
                    $.each(data.head.script, function (n) {
                        self.loadAsync("script", data.head.script[n]);
                    })

                }

                if ("textcss" in data.head && data.head.textcss)
                {
                    self.loadAsync("style", data.head.textcss);
                }

                if ("textscript" in data.head && data.head.textscript)
                {
                    self.loadAsync("script", data.head.textscript);
                }
            }
            if (typeof callback == "function")
                callback.call(this);
        }

        return  MAZE;

    })()



    this.MAZE = new MAZE();

}).call(this);
/*
 Класс для работы URL
 */
(function () {

    MAZE.createPlugin('URI', function (url) {

        function URI(url)
        {
            this.scheme = null;
            this.user = null;
            this.pass = null;
            this.host = null;
            this.port = null;
            this.path = null;
            this.query = null;
            this.fragment = null;
            this.vars = null;

            url = url || document.location.toString();

            this.parseUrl(url)
        }

        URI.prototype = {
            constructor: URI,
            parseUrl: function (url)
            {
                var pattern = "^(?:([^://]+)://)?(?:([^:@]+):([^@]+)@)?([^:/\\?#]*)?(?::([^/\\?#]*))?([^\\?#]*)?(?:\\?([^#]*))?(?:#(.*))?$";
                var rExp = new RegExp(pattern);
                var parts = rExp.exec(url);

                this.scheme = parts[1] || null;
                this.user = parts[2] || null;
                this.pass = parts[3] || null;
                this.host = parts[4] || null;
                this.port = parts[5] || null;
                this.path = parts[6] || null;
                this.query = parts[7] || null;
                this.fragment = parts[8] || null;

                if (this.query !== null)
                {
                    this.vars = this.parse_str(this.query, this.vars)
                }

            },
            toString: function (param)
            {
                param = param || ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'];

                var query = this.getQuery();

                url = '';
                url += $.inArray('scheme', param) !== -1 ? (this.scheme !== null ? this.scheme + '://' : '') : '';
                url += $.inArray('user', param) !== -1 ? (this.user !== null ? this.user : '') : '';
                url += $.inArray('pass', param) !== -1 ? (this.pass !== null ? ":" + this.pass + (this.user !== null ? "@" : '') : '') : '';
                url += $.inArray('host', param) !== -1 ? (this.host !== null ? this.host : '') : '';
                url += $.inArray('port', param) !== -1 ? (this.port !== null ? ':' + this.port : '') : '';
                url += $.inArray('path', param) !== -1 ? (this.path !== null ? '/' + this.clearPath(this.path) : '') : '';
                url += $.inArray('query', param) !== -1 ? (query !== null ? '?' + query : '') : '';
                url += $.inArray('fragment', param) !== -1 ? (this.fragment !== null ? '#' + this.fragment : '') : '';

                return url;
            },
            parse_str: function (query, vars)
            {
                var pairs = query.split('&');

                if (vars == null)
                {
                    vars = {};
                }

                vars = typeof vars == 'object' ? vars : {};

                for (var i = 0; i < pairs.length; i++)
                {
                    var pos = pairs[i].indexOf('=');
                    if (pos == -1)
                        continue;
                    var name = pairs[i].substring(0, pos);
                    var value = pairs[i].substring(pos + 1);
                    value = decodeURIComponent(value);
                    vars[name] = value;
                }

                return vars;
            },
            setVar: function (name, value)
            {
                if (this.vars == null)
                    this.vars = {};

                var old = name in this.vars ? this.vars[name] : null;
                this.vars[name] = value;
                return old;
            },
            getVar: function (name)
            {
                if (this.vars !== null)
                {
                    return name in this.vars ? this.vars[name] : null;
                }
                return null;
            },
            hasVar: function (name)
            {
                if (this.vars !== null)
                {
                    return name in this.vars ? true : false;
                }
                return false;
            },
            delVar: function (name)
            {
                if (name in this.vars)
                {
                    delete this.vars[name];
                }
            },
            setQuery: function (query)
            {
                if (typeof query == 'object')
                {
                    this.vars = query;
                }
                else
                {
                    this.vars = this.parse_str(query, this.vars)
                }
            },
            getQuery: function (toArray)
            {
                if (toArray)
                {
                    return this.vars
                }
                else
                {
                    var data = this.vars;
                    var pairs = Array();
                    for (name in data)
                    {
                        if (data.hasOwnProperty())
                            continue;
                        if (typeof data[name] == 'function')
                            continue;
                        var value = data[name].toString();
                        name = decodeURIComponent(name.replace("%20", "+"));
                        value = decodeURIComponent(value.replace("%20", "+"));
                        pairs.push(name + '=' + value);
                    }

                    return this.query = pairs.join('&');
                }

                return null;
            },
            clearPath: function (path)
            {
                return path.replace(/(\/+)/, "/").replace(/^\//, "").replace(/\/$/, "");
            }

        }

        return new URI(url);

    })

}())

        /*
         Подсказка
         */
        ;
(function () {

    MAZE.createPlugin('tooltip', function (elem, options) {

        options = $.extend({classTooltip: 'maze-tooltip-faq-warning', position: 'top'}, options || {});
        var my, at;
        switch (options.position)
        {
            case 'right':
                my = "right";
                at = "left-16";
                break;

            case 'left':
                my = "left";
                at = "right+16";
                break;

            case 'bottom':
                my = "bottom";
                at = "top-16";
                break;

            case 'top':
                my = "top";
                at = "bottom+16";
                break;
        }
        var target = typeof elem == 'oblect' ? elem : $(elem);
        return target.tooltip({
            tooltipClass: 'maze-tooltip-arrow ' + options.classTooltip,
            position: {
                my: my,
                at: at,
                using: function (position, feedback) {
                    $(this).css(position);
                    var $arr = $("<div>")
                            .addClass("arrow")
                            .addClass(feedback.vertical)
                            .addClass(feedback.horizontal)
                            .appendTo(this);

                    if (feedback.vertical == 'middle' && feedback.horizontal == 'right')
                    {
                        $arr.css({top: Math.ceil(feedback.target.top - position.top + (feedback.target.height / 2) - 28), left: '100%'})
                    }

                    else if (feedback.vertical == 'middle' && feedback.horizontal == 'left')
                    {
                        $arr.css({top: Math.ceil(feedback.target.top - position.top + (feedback.target.height / 2) - 28), left: '-32px'})
                    }

                    else if (feedback.vertical == 'bottom' && feedback.horizontal == 'center')
                    {
                        $arr.css({left: Math.ceil(feedback.target.left - position.left + (feedback.target.width / 2) - 16), top: '100%'})
                    }

                    else if (feedback.vertical == 'top' && feedback.horizontal == 'center')
                    {
                        $arr.css({left: Math.ceil(feedback.target.left - position.left + (feedback.target.width / 2) - 16), top: '-32px'})
                    }

                }
            }
        })
    })

}())



