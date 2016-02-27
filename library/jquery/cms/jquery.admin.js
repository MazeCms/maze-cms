(function () {

    var cms = (function () {

        function CMS()
        {
            this.options = null;

            this.lang = null;

            this.init();

            this.ajaxError();
        }

        CMS.prototype.init = function (options)
        {
            this.options = $.extend({
                id_message: 'system-labyrinth-message'
            }, options || {});
        }
        

        CMS.prototype.createObject = function (superClass)
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
        
        CMS.prototype.rangeInt = function (len){
            var result = [];
            for(i=0; i<len; i++){
                result.push(Math.ceil(Math.random()*10))
            }
            return result.join('');
        }

        CMS.prototype.setLang = function (name, value)
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

        CMS.prototype.getLang = function (name, propText)
        {
            if (this.lang == null)
                return false;

            if (this.lang.hasOwnProperty(name))
            {

                if(typeof propText == 'object'){
                    var text = this.lang[name];

                    $.each(propText, function (key, val) {
                        var patern = new RegExp("\\{"+key+"\\}", "g");
                        text = text.replace(patern, val);
                    })

                    return text; 
                }
                if (1 in arguments)
                {
                    var proper = Array.prototype.slice.call(arguments, 1);

                    if ($.isArray(proper))
                    {
                        var text = this.lang[name];

                        $.each(proper, function (i, val) {
                            text = text.replace(/%s/i, val);
                        })

                        return text;
                    }
                }

                return this.lang[name];
            }

            return name;
        }

        CMS.prototype.unserialize = function (serializedString) {
            var str = decodeURI(serializedString);
            var pairs = str.split('&');
            var obj = {}, p, idx, val;
            for (var i = 0, n = pairs.length; i < n; i++) {
                p = pairs[i].split('=');
                idx = p[0];

                if (idx.indexOf("[]") == (idx.length - 2)) {
                    // Eh um vetor
                    var ind = idx.substring(0, idx.length - 2)
                    if (obj[ind] === undefined) {
                        obj[ind] = [];
                    }
                    obj[ind].push(p[1]);
                }
                else {
                    obj[idx] = p[1];
                }
            }
            return obj;
        };

        CMS.prototype.minArr = function (arr)
        {
            if (!$.isArray(arr))
                return false;
            var min = Number.POSITIVE_INFINITY;
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i] < min)
                    min = arr[i]
            }
            return  Number(min);
        }


        CMS.prototype.getURL = function (alias)
        {
            var url;
            if ($.isArray(alias))
            {

                if (alias.length == 2)
                {

                    url = this.URI(alias[0] + '?' + $.param(alias[1]));

                }
                else if (alias.length == 1)
                {
                    if (typeof alias[0] == 'object')
                    {
                        url = this.URI();
                        url.setQuery(url.parse_str($.param(alias[0]), url.getQuery(true)));
                    }
                    else {
                        url = this.URI(alias[0]);
                    }

                }
            }
            else if (this.isSelectror(alias))
            {
                if ($(alias).is(alias) && $(alias).attr('action'))
                {
                    url = this.URI($(alias).attr('action'));
                }
            }
            else if (this.isURL(alias))
            {
                url = this.URI(alias);
            }
            else if (this.isAlias(alias))
            {
                var arr = alias.split('.');
                if (arr.length >= 2)
                {
                    var action = arr.splice(-1, 1);
                    url = this.URI('/' + arr.join('/'));
                    url.setVar('run', action[0])
                }
                else
                {
                    url = this.URI('/' + arr.join('/'));
                }
            }
            return url.toString();
        }

        CMS.prototype.redirect = function (alias)
        {
            document.location = this.getURL(alias);
            return false;
        }
        CMS.prototype.isIDSelectror = function (str)
        {
            if (typeof str !== 'string')
                return false;

            return str.match(/^#[a-z0-9-\.]+$/i);
        }
        CMS.prototype.isClassSelectror = function (str)
        {
            if (typeof str !== 'string')
                return false;
            return str.match(/^\.[a-z0-9-\.]+$/i);
        }

        CMS.prototype.isSelectror = function (str)
        {
            if (typeof str !== 'string')
                return false;
            return this.isIDSelectror(str) || this.isClassSelectror(str) ? true : false;
        }
        CMS.prototype.isURL = function (str)
        {
            if (typeof str !== 'string')
                return false;
            return str.indexOf('/') !== -1;
        }

        CMS.prototype.isAlias = function (str)
        {
            if (typeof str !== 'string')
                return false;
            return str.indexOf('.') > 1;
        }

        CMS.prototype.btnFormAction = function (id, action)
        {
            var self = this;
            $(id).find('.maze-form-preload').remove();
            $(id).unbind('success.mazeForm').mazeForm('check').one('success.mazeForm', function () {
                var url = $(this).attr('action');
                $(this).preloader('end')
                $(this).find('.maze-form-preload').remove();
                url = self.URI(url);
                if (typeof action == 'object')
                {
                    $.each(action, function (name, val) {
                        url.setVar(name, val);
                    })
                }
                $(this).preloader('end');
                $(this).attr('action', url.toString());
                $(this).submit();
            }).one('error.mazeForm', function () {
                $(this).preloader('end');
                $(this).removeClass('maze-form-load');
                $(this).unbind('success.mazeForm');
                
            }).preloader('start')

            return false;
        }

        CMS.prototype.btnGridAction = function (id, action)
        {
            var val = $(id).find('tbody input[type=checkbox]').serialize();
            var self = this;

            if (val !== '')
            {
                if (typeof action == 'function')
                {
                    var arg = Array();
                    if (arguments.length > 2)
                    {
                        arg = Array.prototype.slice.call(arguments, 2);
                    }
                    arg.push(val);
                    action.apply($(id), arg);
                }
                else
                {
                    var url = this.URI(action);
                    url.setQuery(url.parse_str(val, url.getQuery(true)));
                    CMS.prototype.redirect(url.toString());
                }

            }
            else
            {
                if ($('#tool-bar-admin').is('#tool-bar-admin')) {
                    $('#tool-bar-admin').toolBarAdmin('setMessage', self.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TEXT'), 'error')
                }


            }

            return false;
        },

        CMS.prototype.itemMenuDeletePromt = function (el, e){

            var data = $(el).parents('tr').data('gridRow');
            var url = decodeURIComponent($(e.target).attr('href'));
            var prop = url.match(/\{\+\+([^\+]+)\+\+\}/g);
            $.each(prop, function (i, p) {
                var cp = p.replace(/\{\+\+([^\+]+)\+\+\}/, "$1");
                if (cp in data)
                    url = url.replace((new RegExp("\\{\\+\\+" + cp + "\\+\\+\\}")), data[cp]);
            })


            window.cms.alertPromt({
                close: window.cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                text: window.cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT'),
                title: window.cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE'),
                ok: window.cms.getLang('LIB_USERINTERFACE_TOOLBAR_PACK_SEND'),
                h: 'auto',
                w: 300,
                callback: function (task) {
                    window.cms.redirect(url);
                }
            })
                    
            return false;
        },
        
        CMS.prototype.btnGridActionPromt = function (id, action)
        {
            var self = this;
            var val = $(id).find('tbody input[type=checkbox]').serialize();
            if (val == '')
            {
                if ($('#tool-bar-admin').is('#tool-bar-admin')) {
                    $('#tool-bar-admin').toolBarAdmin('setMessage', self.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TEXT'), 'error')
                }
                return false;
            }


            this.alertPromt({
                close: self.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                text: self.getLang('LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT'),
                title: self.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE'),
                ok: self.getLang('LIB_USERINTERFACE_TOOLBAR_PACK_SEND'),
                h: 'auto',
                w: 300,
                callback: function (task) {
                    self.btnGridAction(id, action);
                }
            })
            return false;
        }

        CMS.prototype.btnGridHandler = function (id, action, options)
        {
            var self = this;
            self.btnGridAction(id, function (val) {
                self.loadDialog(
                        $.extend({
                            url: action,
                            params: val,
                            buttons: [
                                {
                                    label: self.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
                                    class_btn: "maze-btn-success",
                                    class_icon: "",
                                    action: function (e) {
                                        var $self = this;
                                        if ($self.find('form').is('form'))
                                        {
                                            self.btnFormAction($self.find('form').get())
                                        }

                                    }
                                },
                                {
                                    label: self.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE"),
                                    class_btn: "maze-btn-warning",
                                    class_icon: "",
                                    action: function (e, obj) {
                                        this.mazeDialog('close')
                                    }
                                }
                            ]
                        }, options)
                        )
            });

            return false;
        }

        // Асинхронная загрузка скриптов, стилей с помощью jQuery
        CMS.prototype.loadAsync = function (teg, obj, append)
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
            if (append)
            {
                if (typeof append == 'string') {
                    append = $(append);
                }

            }
            else
            {
                append = $("head");
            }
            append.append(teg);

        }

        CMS.prototype.isLoadLib = function (teg, str)
        {
            var result = false;
            $("head").find(teg).each(function () {
                var src = (teg == "script") ? $(this).attr("src") : $(this).attr("href");
                if (src !== undefined && src.indexOf(str) !== -1)
                    result = true;
            })
            return result;
        }

        // Асинхронная загрузка всех скриптов и стилей из объекта (object)data.head

        CMS.prototype.loadHeader = function (data, callback, append, filter)
        {
            var self = this;

            if (data && "head" in data)
            {
                if ($.isArray(filter))
                {
                    $.each(data.head, function (type, obj) {
                        if ($.inArray(type, filter) == -1)
                        {
                            delete data.head[type]
                        }
                    })
                }
                if ("stylesheet" in data.head && data.head.stylesheet)
                {
                    $.each(data.head.stylesheet, function (n) {
                        self.loadAsync("link", data.head.stylesheet[n], append);
                    })
                }

                if ("script" in data.head && data.head.script)
                {
                    $.each(data.head.script, function (n) {
                        self.loadAsync("script", data.head.script[n], append);
                    })

                }

                if ("textcss" in data.head && data.head.textcss)
                {
                    self.loadAsync("style", data.head.textcss, append);
                }

                if ("textscript" in data.head && data.head.textscript)
                {
                    self.loadAsync("script", data.head.textscript, append);
                }
            }
            if (typeof callback == "function")
                callback.call(this);
        }

        CMS.prototype.loadDialog = function (settings)
        {
            var selfClass = this;
            var settings = $.extend({
                url: null,
                title: selfClass.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
                buttons: null,
                height: "auto",
                width: "auto",
                minHeight: 350,
                minWidth: 550,
                mode: "absolute",
                params: {},
                loadHeader: true,
                callback: $.noop
            }, settings || {});

            var url = this.URI(selfClass.getURL(settings.url));
            if (typeof settings.params == 'object')
            {
                url.mergeVar(settings.params);
            }
            else if (typeof settings.params == 'string')
            {
                url.mergeVar(url.parse_str(settings.params))
            }

            url.setVar('clear', 'ajax');
            var toolbarhead = {minidialog: null};
            if(settings.mode == 'fixed'){
               toolbarhead.fulldialog = null;
            }
            
            var $dialog = $('<div>').mazeDialog({
                title: settings.title,
                draggable: true,
                resize: false,
                height: settings.height,
                width: settings.width,
                minHeight: settings.minHeight,
                minWidth: settings.minWidth,
                modal: true, 
                mode:settings.mode,
                toolbarhead: toolbarhead,
                buttons: settings.buttons,
                open: function () {
                    $dialog.mazeDialog('dialog').preloader('start');
                },
                close: function () {
                    this.mazeDialog('destroy');
                    $dialog = null;
                    xhr.abort();
                }
            });

            var xhr = $.get(url.toString(), function (data) {
                if ($dialog == null)
                    return;
                $dialog.html(data.html)
                $dialog.mazeDialog('dialog').preloader('end');
                if (settings.loadHeader) {
                    selfClass.loadHeader(data);
                }
                $dialog.mazeDialog('update');
                if ($.isFunction(settings.callback))
                    settings.callback(data);

            }, 'json');
            return $dialog;
        }

        CMS.prototype.alertBtn = function (title, text, h, w)
        {
            var selfClass = this;

            var h = h || 200;
            var w = w || 300;
            if (typeof text == 'object')
            {
                var textConcat = '<ul>';
                $.each(text, function (i, mess) {
                    textConcat += '<li class=""><span aria-hidden="true" class="glyphicon glyphicon-remove"></span> ' + mess.mess + '</li>';
                })
                textConcat += '<ul>'
                text = textConcat
            }
            $('<div>').append("<div class=\"helper-message-tip\">" + text + "</div>").mazeDialog({
                title: title,
                draggable: false,
                resize: false,
                height: h || 'auto',
                width: w || 500,
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

        CMS.prototype.alertPromt = function (options)
        {
            var self = this;
            var set = $.extend({
                close: "Close",
                text: "",
                title: "Заголовок",
                ok: "OK",
                h: 180,
                w: 300,
                task: "",
                callback: function (task) {

                },
            }, options || {});


            $('<div>').append("<div class=\"helper-message-tip\">" + set.text + "</div>").mazeDialog({
                title: set.title,
                draggable: false,
                resize: false,
                height: set.h,
                width: set.w,
                modal: true,
                mode: "float",
                toolbarhead: {minidialog: null, fulldialog: null},
                buttons: [
                    {
                        label: set.ok,
                        class_btn: "maze-btn-success",
                        class_icon: "",
                        action: function (e) {
                            set.callback(set.task);
                            this.mazeDialog('close');
                        }
                    },
                    {
                        label: set.close,
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



        CMS.prototype.loadFileManager = function (handler, options, id, url)
        {
            var settings = $.extend({
                autoOpen: false,
                height: 450,
                width: 900,
                title: this.getLang('LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG'),
                modal: false,
                draggable: true,
                resize: true,
                multi: false,
                onlyURL: true,
                dialogClass: 'maze-dialog-content-padding-null',
                iframeLoad: function () {
                },
                startLoad: function () {
                },
                close: function () {
                    $("#" + id).contents().find('body').html('<div id="elfinder-elem"></div>')
                }
            }, options || {})

            var id = id || 'file-manager-dialog';
            var url = url || '/admin/elfinder/?run=loadDialog';
            url = this.URI(url);
            url.setVar('clear', 'iframe');
            url.setVar('nonescript', '1');
            if ($("#" + id).is("#" + id))
            {
                $("#" + id).parent('.file-dialog').mazeDialog("open");
                window.frames[id].dialogFile(handler, settings.multi, settings.onlyURL);
                return false;
            }
            settings.startLoad();

            $('<div>').addClass('file-dialog')
                    .css('height', '100%')
                    .append($('<iframe>')
                            .attr('id', id)
                            .attr('name', id)
                            .attr("src", url)
                            .attr("hspace", 0)
                            .attr("frameborder", 0)
                            .attr("scrolling", "auto")
                            )
                    .mazeDialog(settings)

            $("#" + id).bind('load', function (e) {
                settings.iframeLoad(this, e);
                $(this).css({width: '100%', height: '99%'})
                $(this).parent('.file-dialog').mazeDialog('open')
                window.frames[id].dialogFile(handler, settings.multi, settings.onlyURL);
            })

        }

        CMS.prototype.createPlugin = function (name, plugin)
        {
            if (name in CMS.prototype)
            {
                throw new Error("current plugin name is already taken");
            }
            CMS.prototype[name] = plugin;
        }

        CMS.prototype.ajaxError = function ()
        {
            var self = this;
            var authoriz = false;
            jQuery(document).bind('ajaxSuccess', function(e, xhr, res, data){
                if(typeof data == 'object'){
                    if(data.hasOwnProperty('message') && data.message){
                        if($('#tool-bar-admin').is('#tool-bar-admin')){
                            $('#tool-bar-admin').toolBarAdmin('setMessage', data.message.text, data.message.type);
                        }
                        
                    }
                }
            });
            
            jQuery(document).bind('ajaxError', function (e, xhr) {

                if (xhr.status == 401)
                {
                    if ($('.authorization-user-admin').is('.authorization-user-admin'))
                    {
                        $('.authorization-user-admin').mazeDialog('open');
                        return false;
                    }
                    $(tmpForm()).mazeDialog({
                        title: self.getLang('LIB_FRAMEWORK_VIEW_AJAX_AUTHORIZATION_TITLE'),
                        width: 400,
                        show: 'fade',
                        hide: 'fade',
                        modal: true,
                        draggable: true,
                        resize: false,
                        mode: 'static',
                        toolbarhead: {minidialog: null, fulldialog: null},
                        buttons: [
                            {
                                label: self.getLang('LIB_USERINTERFACE_TOOLBAR_PACK_SEND'),
                                class_btn: "maze-btn-success",
                                class_icon: "",
                                action: function (e) {
                                    var $content = this;
                                    self.ajaxSend({
                                        task: 'login',
                                        preloadblock: $content.mazeDialog('dialog'),
                                        action: '.authorization-user-admin',
                                        handler: function (data)
                                        {
                                            var text = "";
                                            if (data.errorlogo)
                                            {
                                                text += data.errorlogo;
                                            }
                                            if (data.errorpass)
                                            {
                                                text += "<br>" + data.errorpass;
                                            }
                                            if (data.redirect)
                                            {
                                                authoriz = true;
                                                $content.mazeDialog("close");
                                                return true;
                                            }
                                            $content.prepend(self.message(text, 'error'))
                                            $content.mazeDialog("refresh");
                                        }

                                    })
                                }
                            }
                        ],
                        open: function () {
                            $(this).prepend(self.message(self.getLang('LIB_FRAMEWORK_VIEW_AJAX_REDIRECT'), 'warning'));
                        },
                        beforeClose: function () {
                            if (authoriz)
                                return true;
                            return false;
                        }
                    })
                }
                else if (xhr.status > 400)
                {
                    var text = "<strong>" + self.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE') + "</strong><br/>"
                            + self.getLang('LIB_FRAMEWORK_VIEW_AJAX_ERROR', xhr.status);
                    if ($('#tool-bar-admin').is('#tool-bar-admin')) {
                        $('#tool-bar-admin').toolBarAdmin('setMessage', text, 'error')
                    }

                }

            })

            function tmpForm()
            {
                return'<form class="authorization-user-admin" action="/user/" method="post">'
                        + '<table class="table-form-filds">'
                        + '<tbody>'
                        + '<tr>'
                        + '<td>'
                        + self.getLang('LIB_USERINTERFACE_FIELD_LOGIN')
                        + '<td>'
                        + '<td>'
                        + '<input class="input-xlarge" type="text" placeholder="' + self.getLang('LIB_USERINTERFACE_FIELD_LOGIN')
                        + '" value="" name="login">'
                        + '<td>'
                        + '</tr>'
                        + '<tr>'
                        + '<td>'
                        + self.getLang('LIB_USERINTERFACE_FIELD_PASS')
                        + '<td>'
                        + '<td>'
                        + '<input class="input-xlarge" type="password" placeholder="' + self.getLang('LIB_USERINTERFACE_FIELD_PASS')
                        + '" value="" name="password">'
                        + '<td>'
                        + '</tr>'
                        + '</tbody>'
                        + '</table>'
                        + '</form>'
            }
        }
        return  CMS;
    })()
    this.cms = new cms();
}).call(this);

/*
 Класс для работы URL
 */
(function () {

    cms.createPlugin('URI', function (url) {

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
                var url = '';
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

                var result = typeof vars == 'object' ? vars : {};
                var str = decodeURI(query);
                var pairs = str.split('&');
                var pushes = {};
                var pattern = {
                    validate: /^[a-z][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
                    key: /[a-z0-9_]+|(?=\[\])/gi,
                    push: /^$/,
                    fixed: /^\d+$/,
                    named: /^[a-z0-9_]+$/i
                }
                function build(base, key, value) {
                    base[key] = value;
                    return base;
                }
                function incrementPush(key) {
                    if (pushes[key] === undefined) {
                        pushes[key] = 0;
                    }
                    return pushes[key]++;
                }
                function makeObject(name, val)
                {

                    if (pattern.validate.test(name))
                    {
                        var keys = name.match(pattern.key);
                        var value = val;
                        var k;

                        while ((k = keys.pop()) !== undefined) {
                            if (pattern.push.test(k)) {
                                var idx = incrementPush(name.replace(/\[\]$/, ''));
                                value = build([], idx, value);
                            }
                            else if (pattern.fixed.test(k)) {
                                value = build([], k, value);
                            }

                            else if (pattern.named.test(k)) {
                                value = build({}, k, value);
                            }
                        }
                    }

                    return value;
                }

                $.each(pairs, function (i, name) {
                    var p = name.split('=');
                    if (p.length == 2)
                    {
                        result = $.extend(true, result, makeObject(p[0], p[1]));
                    }
                    else
                    {
                        result = $.extend(true, result, makeObject(p[0], null));
                    }
                })

                return result;
            },
            setVar: function (name, value)
            {
                if (this.vars == null)
                    this.vars = {};

                var old = name in this.vars ? this.vars[name] : null;
                this.vars[name] = value;
                return old;
            },
            mergeVar: function (vars) {
                var oldVar = this.getQuery(true);
                this.vars = $.extend(oldVar, vars || {});
            },
            getVar: function (name)
            {
                if (typeof this.vars == 'object')
                {
                    return  this.vars.hasOwnProperty(name) ? this.vars[name] : null;
                }
                return null;
            },
            hasVar: function (name)
            {
                if (typeof this.vars == 'object')
                {
                    return  this.vars.hasOwnProperty(name) ? true : false;
                }
                return false;
            },
            delVar: function (name)
            {
                if (this.vars.hasOwnProperty(name))
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
                if (this.vars == null)
                    return null;

                if (toArray)
                {
                    return this.vars
                }
                else
                {
                    var data = this.vars;
                    return $.param(data);
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

}());

/**
 * Перезагрузить окно
 */
(function () {
    cms.createPlugin('resetWindow', function () {
        setTimeout(function () {
        cms.alertPromt({
            title: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE"),
            text: cms.getLang("LIB_USERINTERFACE_TOOLBAR_UPDATEPAGE"),
            close: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON"),
            ok: cms.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
            callback: function () {
                var url = cms.URI();
                document.location = document.location.href = url.toString(['scheme', 'user', 'pass', 'host', 'port', 'path', 'query'])
            }
        })
    }, 800);
    })
    
}());

/**
 * Отправка формы Ajax
 * @param {int|elem} id - целевой элементы формы
 * @param {object} action -   дополнительные параметры URL
 * @param {function} callback - функция обратного вызова при успешной отправки формы
 */
(function () {
    cms.createPlugin('submitAjaxForm', function (id, action, callback) {
        $(id).find('.maze-form-preload').remove();
            $(id).unbind('success.mazeForm').mazeForm('check').one('success.mazeForm', function () {
                $("body").submit();
                var url = $(this).attr('action');
                var $self = $(this);
                $(this).preloader('end')
                $(this).find('.maze-form-preload').remove();
                url = cms.URI(url);
                if (typeof action == 'object')
                {
                    $.each(action, function (name, val) {
                        url.setVar(name, val);
                    })
                }

                $(this).attr('action', url.toString());
   
                $(this).preloader('start')
                $.post(url.toString(),  $.extend({csrf: $('meta[name=csrf-token]').attr('content')}, $(this).serializeObject()) , function (data) {
                      $self.preloader('end')
                      if(typeof callback == 'function'){
                        callback.call(this, this, data)
                    }
                }, 'json');
                
             
            }).one('error.mazeForm', function () {
                $(this).removeClass('maze-form-load');
                $(this).unbind('success.mazeForm');
                $(this).preloader('end')
            }).preloader('start')
    })
    
}());

/**
 * Загрузка диалога с функцией сохранения
 * 
 * @param {elem} elem - целевой элемент ссылки 
 */
(function () {

    cms.createPlugin('formDialogSave', function (elem, options) {
        
        var options = $.extend({            
            saveTask: null,
            savecloseTask: null,
            check: null,
            form: '.admin-form',
            mode:"fixed",
            title:null, 
            url:$(elem).attr('href'),  
            width:950, 
            buttons:[{
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON"),
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {
                   cms.submitAjaxForm( $(this).find('form').get(), false, function(){
                        saveFlag = true;
                   })
                  
                }
            },
            {
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON"),
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {                    
                    var dialog = $(this);
                    cms.submitAjaxForm(dialog.find('form').get(), false, function(){
                        saveFlag = true;
                        dialog.mazeDialog('close');                       
                    })
                    
                }
            },
            {
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE"),
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close')
                }
            }
        ]
        }, options || {})
        
        var saveFlag = false;
    
        cms.loadDialog(options).one('closeDialog', function(){
            if(saveFlag) cms.resetWindow();
        });

    })

}());

/*
 * Удаление блока
 */
(function () {

    cms.createPlugin('deleteBlock', function (elem, selector) {

        var url = $(elem).attr('href')
        if (!url)
            return false;
        var url = cms.URI(url);
        url.setVar('clear', 'ajax');

        cms.alertPromt({
            title: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE"),
            text: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT"),
            close: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON"),
            ok: cms.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
            h:'auto',
            callback: function(){
                 $.get(url, function(data){
                     if(data.hasOwnProperty('message')){
                         if(data.message.type !== 'error'){
                             $(selector).toolBarElem('close').remove();
                         }
                         
                     }
                    
                }, 'json')
            }
        })

    })

}());


/**
 * jQuery c
 * @copyright 2014, macek <paulmacek@gmail.com>
 * @link https://github.com/macek/jquery-serialize-object
 * @license BSD
 * @version 2.2.0
 */
(function (root, factory) {

    // AMD
    if (typeof define === "function" && define.amd) {
        define(["jquery", "exports"], function ($, exports) {
            factory(root, exports, $);
        });
    }

    // CommonJS
    else if (typeof exports !== "undefined") {
        var $ = require("jquery");
        factory(root, exports, $);
    }

    // Browser
    else {
        root.FormSerializer = factory(root, {}, (root.jQuery || root.Zepto || root.ender || root.$));
    }

}(this, function (root, exports, $) {

    var FormSerializer = exports.FormSerializer = function FormSerializer(helper) {

        // private variables
        var data = {},
                pushes = {};

        // private API
        function build(base, key, value) {
            base[key] = value;
            return base;
        }

        function makeObject(root, value) {

            var keys = root.match(FormSerializer.patterns.key), k;

            // nest, nest, ..., nest
            while ((k = keys.pop()) !== undefined) {
                // foo[]
                if (FormSerializer.patterns.push.test(k)) {
                    var idx = incrementPush(root.replace(/\[\]$/, ''));
                    value = build([], idx, value);
                }

                // foo[n]
                else if (FormSerializer.patterns.fixed.test(k)) {
                    value = build([], k, value);
                }

                // foo; foo[bar]
                else if (FormSerializer.patterns.named.test(k)) {
                    value = build({}, k, value);
                }
            }

            return value;
        }

        function incrementPush(key) {
            if (pushes[key] === undefined) {
                pushes[key] = 0;
            }
            return pushes[key]++;
        }

        function addPair(pair) {
            if (!FormSerializer.patterns.validate.test(pair.name))
                return this;
            var obj = makeObject(pair.name, pair.value);
            data = helper.extend(true, data, obj);
            return this;
        }

        function addPairs(pairs) {
            if (!helper.isArray(pairs)) {
                throw new Error("formSerializer.addPairs expects an Array");
            }
            for (var i = 0, len = pairs.length; i < len; i++) {
                this.addPair(pairs[i]);
            }
            return this;
        }

        function serialize() {
            return data;
        }

        function serializeJSON() {
            return JSON.stringify(serialize());
        }

        // public API
        this.addPair = addPair;
        this.addPairs = addPairs;
        this.serialize = serialize;
        this.serializeJSON = serializeJSON;
    };

    FormSerializer.patterns = {
        validate: /^[a-z][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
        key: /[a-z0-9_]+|(?=\[\])/gi,
        push: /^$/,
        fixed: /^\d+$/,
        named: /^[a-z0-9_]+$/i
    };

    FormSerializer.serializeObject = function serializeObject() {
        if (this.length > 1) {
            return new Error("jquery-serialize-object can only serialize one form at a time");
        }
        return new FormSerializer($).
                addPairs(this.serializeArray()).
                serialize();
    };

    FormSerializer.serializeJSON = function serializeJSON() {
        if (this.length > 1) {
            return new Error("jquery-serialize-object can only serialize one form at a time");
        }
        return new FormSerializer($).
                addPairs(this.serializeArray()).
                serializeJSON();
    };

    if (typeof $.fn !== "undefined") {
        $.fn.serializeObject = FormSerializer.serializeObject;
        $.fn.serializeJSON = FormSerializer.serializeJSON;
    }

    return FormSerializer;
}));




