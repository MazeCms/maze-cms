(function ($) { // модуль
    "use strict"
    // общие настройки для всех экземпляров класса		
    var settings = {
        // Автоматическое открытие диалого после создвания
        autoOpen: true,
        // Обработчик перед закрытием диалога
        beforeClose: function () {
            return true;
        },
        // Обработичк после закрытия диалога
        close: function () {
        },
        // CSS Классы диалога 
        dialogClass: "",
        // Устанавливает способность к перетаскиванию
        draggable: true,
        // Обработчик события drag
        drag: function (e, ui) {
        },
        // Обработчик события dragStart
        dragstart: function (e, ui) {
        },
        // Обработчик события dragStop
        dragstop: function (e, ui) {
        },
        // При получении фокуса выводится поверх остальных окно
        stack: true,
        // Обработчик события dialogFocus
        focus: function (dialog, obj) {
        },
        // Анимационный эффект при закрытии диалога (string|function)
        // контекст - обернутый набор диалога
        hide: "",
        // Анимационный эффект при открытии диалога(string|function)
        // контекст - обернутый набор диалога
        show: "",
        // Время выполнения анимации
        speed: 500,
        // Высота диалога
        height: "auto",
        // Ширина диалога
        width: "auto",
        maxHeight: null,
        maxWidth: null,
        minHeight: 100,
        minWidth: 300,
        // Делает окно модальным
        modal: false,
        // возбуждается после открытия диалога, событие openDialog
        // контекст this текущий обернутный элемент
        open: function (obj) {
        },
        // (array|string|function) первое значение спещение по горизонтали, второе по вертикали
        // например: [left, top] | [left+30, center+50] | left, top....
        position: "center",
        // Разрешает изменение размеров
        resize: true,
        // Обработчик события resizable
        resizable: function () {
        },
        // Обработчик события resizestart
        resizestart: function () {
        },
        // Обработчик события resizestop
        resizestop: function () {
        },
        zIndex: 1000,
        // (object|function) 
        buttons: function (elem) {
            return {};
        },
        toolbarhead: {
            minidialog: {
                label: "Свернуть диалог",
                icon: "",
                class: "maze-icon miniscreen-icon",
                action: function (e, obj) {
                    var $this = $(e.target).parent();
                    var optionsMini;
                    if (obj.get('miniDialog'))
                    {
                        $this.removeClass('active');
                        optionsMini = obj.get('miniDialog');
                        obj.set('miniDialog', null);
                    }
                    else
                    {
                        $this.addClass('active');
                        obj.set('miniDialog', {
                            modal: obj.options.modal,
                            mode: obj.options.mode,
                            position: obj.options.position,
                            width: obj.options.width,
                            height: obj.options.height
                        });
                        if (obj.options.mode == 'fixed')
                        {
                            this.mazeDialog('refreshSettings', {mode: "static"}).mazeDialog('refresh');
                        }
                        optionsMini = {
                            modal: false,
                            mode: "static",
                            position: ['center', 'bottom'],
                            width: $(window).width(),
                            height: $(window).height() / 3
                        };
                    }
                    this
                            .mazeDialog('refreshSettings', optionsMini)
                            .mazeDialog('refresh')
                            .trigger('minidialog', optionsMini);
                }
            },
            fulldialog: {
                label: "Развернуть на весь экран",
                icon: "",
                class: "maze-icon fullscreen-icon",
                action: function (e, obj) {
                    var $this = $(e.target).parent();
                    var optionsFull;
                    if (obj.get("fullscreen"))
                    {
                        $this.removeClass('active');
                        optionsFull = obj.get("fullscreen");
                        obj.set("fullscreen", null);
                        $('body').css('overflow', 'auto');
                    }
                    else
                    {
                        $this.addClass('active');
                        obj.set('fullscreen', {
                            modal: obj.options.modal,
                            mode: obj.options.mode,
                            position: obj.options.position,
                            width: obj.options.width,
                            height: obj.options.height
                        });
                        $('body').css('overflow', 'hidden');
                        if (obj.options.mode == 'fixed')
                        {
                            this.mazeDialog('refreshSettings', {mode: "static"}).mazeDialog('refresh');
                        }
                        optionsFull = {
                            modal: false,
                            mode: "static",
                            position: 'left',
                            width: $(window).width(),
                            height: $(window).height()
                        };
                    }
                    this
                            .mazeDialog('refreshSettings', optionsFull)
                            .mazeDialog('refresh')
                            .trigger('fulldialog', optionsFull);
                    this.one('closeDialog', function () {
                        $('body').css('overflow', 'auto');
                    });
                }
            },
            closedialog: {
                label: "закрыть диалог",
                icon: "",
                class: "maze-icon close-icon",
                action: function (e, obj) {
                    obj.closeDialog();
                }
            }
        },
        //	Режим диалога : "absolute" , "float", "fixed", "static"
        mode: "absolute",
        // Отсутп от верхнего или нижнего края при mode = "float"
        marginfloat: 10,
        // Вставить после....селектор
        appendTo: 'body',
        // Заголовок диалога
        title: ""

    }

    var staticCountFixed = 0;

    function MazeDialog(elem, options)
    {
        var self = this;
        this.$elemets = $(elem);
        this.elemets = elem;
        this.$dialog = null;
        this.$overlay = null;
        this.$content = null;
        this.vars = null;
        this.originalCss = null;
        this.originalPosition = null;
        this.blockDialog = false;

        this.options = $.extend({}, settings, options || {});

        this.getOriginalStyle();

        this.createDialog();

        this.init();

        if (this.options.autoOpen)
        {
            this.openDialog();
        }

        $(document).bind("scroll", function () {
            self.$elemets.trigger("mazeScroll");
        });

        $(document).bind("resize", function () {
            self.$elemets.trigger("mazeResize");
        });
    }

    MazeDialog.prototype.init = function ()
    {
        var self = this;
        if (this.options.mode == "absolute")
        {
            this.$dialog
                    .css("position", "absolute")
                    .addClass("mode-absolute");
        }
        else if (this.options.mode == "fixed")
        {
            this.$dialog
                    .wrap('<div class="maze-dialog-fixed-wrap"></div>')
                    .addClass("mode-fixed")
                    .parent(".maze-dialog-fixed-wrap").css("z-index", self.$dialog.css("z-index"));

            if (this.options.width == "auto")
            {
                this.options.width = null;
                this.options.height = "auto";
                this.setSize();
            }

        }
        else if (this.options.mode == "static")
        {
            this.$dialog
                    .css("position", "fixed")
                    .addClass("mode-static");
        }
        else if (this.options.mode == "float")
        {
            this.$dialog
                    .css("position", "absolute")
                    .addClass("mode-float");
            self.$elemets.bind("openDialog", self.setFloat);
        }
        // событие получения фокуса
        if (this.options.stack && this.options.mode !== "fixed")
        {
            this.$dialog.bind("mousedown", function () {
                self.setFocusDilalog();
                self.options.focus.call(self.$elemets, this, self);
                self.$elemets.trigger("dialogFocus", this, self);
            });
        }
        else
        {
            this.$dialog.unbind("mousedown");
        }
        // буксировка
        if (this.options.draggable && this.options.mode !== "fixed")
        {
            this.$dialog.draggable({
                handle: ".maze-header",
                containment: "document",
                drag: function (e, ui)
                {
                    self.options.drag.call(this, e, ui);
                    self.$elemets.trigger("drag", {event: e, ui: ui});
                },
                start: function (e, ui)
                {
                    self.options.dragstart.call(this, e, ui);
                    self.$elemets.trigger("dragStart", {event: e, ui: ui});
                },
                stop: function (e, ui)
                {
                    self.options.position = [
                        ui.position.left - $(document).scrollLeft(),
                        ui.position.top - $(document).scrollTop()
                    ];
                    self.options.dragstop.call(this, e, ui);
                    self.$elemets.trigger("dragStop", {event: e, ui: ui});
                },
            });
        }

        if (this.options.resize && this.options.mode !== "fixed")
        {
            this.$dialog.resizable({
                cancel: self.$elemets,
                containment: "document",
                maxWidth: self.options.maxWidth,
                maxHeight: self.options.maxHeight,
                minWidth: self.options.minWidth,
                minHeight: self.getMinHeight(),
                alsoResize: self.$content,
                handles: "n,e,s,w,se,sw,ne,nw",
                start: function (e, ui) {
                    $(this).addClass("maze-dialog-resizing");
                    self.options.resizestart.call(this, {event: e, ui: ui});
                    self.$elemets.trigger("resizestart", {event: e, ui: ui});
                },
                resize: function (e, ui) {
                    if (self.options.mode == "static")
                    {
                        $(this).css("position", "fixed");
                    }
                    self.options.resizable.call(this, {event: e, ui: ui});
                    self.$elemets.trigger("resizable", {event: e, ui: ui});
                },
                stop: function (e, ui) {

                    self.options.height = $(this).height();
                    self.options.width = $(this).width();
                    $(this).removeClass("maze-dialog-resizing");
                    self.options.resizestop.call(this, e, ui);
                    self.$elemets.trigger("resizestop", {event: e, ui: ui});
                }
            });
        }

        $(window).resize(function () {
            self.setPosition();
        });


    }
    MazeDialog.prototype.getOriginalStyle = function ()
    {
        this.originalCss =
                {
                    display: this.elemets.style.display,
                    width: this.elemets.style.width,
                    minHeight: this.elemets.style.minHeight,
                    maxHeight: this.elemets.style.maxHeight,
                    height: this.elemets.style.height
                };

        this.originalPosition =
                {
                    parent: this.$elemets.parent(),
                    index: this.$elemets.parent().children().index(this.$elemets)
                };
    }
    MazeDialog.prototype.set = function (name, value)
    {
        if (this.vars == null){
             this.vars = {};
        }
           

        if (this.vars.hasOwnProperty(name) && value == null)
        {
            delete this.vars[name];
            return value;
        }
        else if (value == null)
        {
            return false;
        }

        this.vars[name] = value;

        return value;

    }
    MazeDialog.prototype.get = function (name)
    {
        if (this.vars == null){
            return false;
        }
            

        if (this.vars.hasOwnProperty(name))
        {
            return this.vars[name];
        }

        return false;

    }
    MazeDialog.prototype.refreshSettings = function (obj)
    {
        var self = this;
        this.options = $.extend(self.options, obj || {});
    }
    MazeDialog.prototype.refresh = function ()
    {
        var self = this;
        this.$dialog
                .removeClass("mode-absolute mode-fixed mode-static mode-float")
                .css("position", "absolute");

        this.$elemets.unbind("openDialog", self.setFloat);
        this.$elemets.unbind("mazeScroll");

        this.$dialog.unbind("mousedown");
        if (this.$dialog.parent().is('.maze-dialog-fixed-wrap'))
        {
            this.$dialog.unwrap();
            staticCountFixed--;
            if (staticCountFixed <= 0)
            {
                $("body").removeClass("maze-dialog-fixed-open");
            }
        }

        if (this.$dialog.is(".ui-draggable")){
            this.$dialog.draggable("destroy");
        }
            
        if (this.$dialog.is(".ui-resizable")){
            this.$dialog.resizable("destroy");
        }
            

        if (this.$overlay !== null){
            this.$overlay.remove();
        }
            

        this.setSize();

        this.init();

        this.createOverlay();

        this.setFocusDilalog();

        if (this.options.mode == "fixed")
        {
            $("body").addClass("maze-dialog-fixed-open");
            staticCountFixed++;
            this.$dialog.parent().show();
            this.$dialog.css({top: 0, left: 0});
        }

        this.setPosition();

        if (this.options.mode == "float")
        {
            self.setFloat(null, self);
        }

        if (self.options.mode == "fixed"){
             this.$dialog.css({top: 0});
        }
           

    }
    MazeDialog.prototype.createToolBarTop = function ()
    {
        if (typeof this.options.toolbarhead !== "object"){
            return false;
        }
            
        var wrapHeaderBar = $("<div>").addClass("maze-toolbar-header");
        var count = 0;
        var self = this;
        this.options.toolbarhead = $.extend({}, settings.toolbarhead, this.options.toolbarhead || {});
        $.each(this.options.toolbarhead, function (name, props)
        {
            if (typeof props !== "object" || $.isEmptyObject(props)){
                return true;
            }
                

            var buttonIcon = $("<span>").addClass("control-button-icon");
            if ("class" in props && props.class !== "")
            {
                buttonIcon.addClass(props.class);
            }
            if ("icon" in props && props.icon !== "")
            {
                buttonIcon.css("background-image", "url('" + props.icon + "')");
            }

            var prop = {
                class: "control-button",
                title: props.label,
                href: "#",
                click: props.action,
                mouseover: function (e) {
                    e.stopImmediatePropagation();
                    $(e.target).parent(".control-button").addClass("hover");
                },
                mouseout: function (e) {
                    e.stopImmediatePropagation();
                    $(e.target).parent(".control-button").removeClass("hover");
                },
                mousedown: function (e) {
                    e.stopImmediatePropagation();
                    $(e.target).parent(".control-button").removeClass("hover");
                    $(e.target).parent(".control-button").addClass("active");
                },
                mouseup: function (e) {
                    e.stopImmediatePropagation();
                    $(e.target).parent(".control-button").removeClass("active");
                    $(e.target).parent(".control-button").addClass("hover");
                }
            };
            var click = prop.click;
            prop.click = function (e) {
                click.call(self.$elemets, e, self);
                e.preventDefault();
            };
            $("<a>", prop)
                    .append(buttonIcon)
                    .appendTo(wrapHeaderBar);
            count++;
        });
        return 	count > 0 ? wrapHeaderBar : false;
    }
    MazeDialog.prototype.createToolBarButton = function ()
    {
        var btn = false,
                self = this;

        if ($.isArray(this.options.buttons))
        {
            if (typeof this.options.buttons !== "object" || $.isEmptyObject(this.options.buttons[0])){
                return false;
            }
                

            btn = this.options.buttons;
        }
        else if (typeof this.options.buttons == "function")
        {
            btn = this.options.buttons.call(this.$elemets, this);
            if (typeof btn !== "object" || $.isEmptyObject(btn[0])){
                 return false;
            }
               
        }

        if (!btn){
            return false;
        }
            

        var footer = $('<div class="maze-footer">').append('<div class="button-set-footer">');
        var count = 0;
        $.each(btn, function (i, value)
        {
            if (typeof value !== "object")
                return false;

            var buttonIcon = $("<span>").addClass("maze-btn-icon");
            var icon_check = false;
            if ("class" in value && value.class_icon !== "")
            {
                buttonIcon.addClass("maze-btn-icon " + btn.class_icon);
                icon_check = true;
            }
            if ("icon" in value && value.icon !== "")
            {
                buttonIcon.css("background-image", "url('" + value.icon + "')");
                icon_check = true;
            }
            var props =
                    {
                        class: "maze-btn " + ("class_btn" in value ? value.class_btn : ""),
                        click: value.action,
                        mouseover: function (e) {
                            e.stopImmediatePropagation();
                            $(e.target).addClass("hover");
                        },
                        mouseout: function (e) {
                            e.stopImmediatePropagation();
                            $(e.target).removeClass("hover");
                        },
                        mousedown: function (e) {
                            e.stopImmediatePropagation();
                            $(e.target).removeClass("hover");
                            $(e.target).addClass("active");
                        },
                        mouseup: function (e) {
                            e.stopImmediatePropagation();
                            $(e.target).removeClass("active");
                            $(e.target).addClass("hover");
                        }
                    };
            var click = props.click;
            props.click = function (e) {
                click.call(self.$elemets, e, self);
            };
            var button = $("<button>", props);
            if (icon_check)
            {
                button.append(buttonIcon);
            }
            button
                    .append(value.label)
                    .appendTo(footer.children(".button-set-footer"));
            count++;
        });
        return 	count > 0 ? footer : false;
    }
    MazeDialog.prototype.createOverlay = function ()
    {
        if (!this.options.modal){
            return false;
        }
           

        var $overlay = $("<div>").addClass("maze-overlay");

        var appendTo = this.options.mode == "fixed" ? this.$dialog.parent() : this.$dialog;

        $overlay.insertAfter(appendTo);

        this.$overlay = $overlay;
    }
    MazeDialog.prototype.createDialog = function ()
    {
        var self = this;
        var title = '<div class="maze-title">' + this.options.title + '</div>';
        var toolbarTop = this.createToolBarTop();
        toolbarTop = toolbarTop ? toolbarTop : '';
        var content = $('<div class="maze-content">').append(this.$elemets.css("display", "block"));
        var footer = this.createToolBarButton();
        footer = footer ? footer : "";
        var $dialog = $("<div>")
                .addClass("maze-dialog " + self.options.dialogClass)
                .css('zIndex', self.options.zIndex + $('.maze-dialog').size());

        $("<div>").addClass("maze-header").append(title).append(toolbarTop)
                .appendTo($dialog);
        content.appendTo($dialog);

        if (footer){
            footer.appendTo($dialog);
        }
            

        $dialog.hide().appendTo(self.options.appendTo);

        this.$content = content;
        this.$dialog = $dialog;
        return this.$dialog;
    }
    MazeDialog.prototype.getMinHeight = function ()
    {
        return this.options.height === "auto" ?
                this.options.minHeight : Math.min(this.options.minHeight, this.options.height);
    }
    MazeDialog.prototype.setSize = function ()
    {
        var nonContentHeight, minContentHeight, maxContentHeight,
                self = this;

        if (self.options.minWidth > self.options.width)
        {
            self.options.width = self.options.minWidth;
        }
        this.$content.show().css({
            width: "auto",
            minHeight: 0,
            maxHeight: "none",
            height: 0
        });

        nonContentHeight = this.$dialog.css({
            height: "auto",
            width: self.options.width,
            minWidth: self.options.minWidth
        }).outerHeight();

        minContentHeight = Math.max(0, self.options.minHeight - nonContentHeight);
        maxContentHeight = typeof self.options.maxHeight === "number" ?
                Math.max(0, self.options.maxHeight - nonContentHeight) :
                "none";

        if (self.options.height === "auto")
        {
            this.$content.css({
                minHeight: minContentHeight,
                maxHeight: maxContentHeight,
                height: "auto"
            });
        }
        else
        {
            this.$content.height(Math.max(0, self.$content.height() - (nonContentHeight - self.options.height)));
        }

    }
    MazeDialog.prototype.openDialog = function ()
    {
        if (this.blockDialog){
            return true;
        }
            

        var $dialog = null;
        if (!this.isCreate())
        {
            $dialog = this.createDialog();
            this.init();
        }
        else
        {
            $dialog = this.$dialog;
        }

        if (this.isOpen()){
             return false;
        }
           

        this.blockDialog = true;

        this.setSize();

        var self = this;

        this.createOverlay();
        this.setFocusDilalog();

        if (this.options.mode == "fixed")
        {
            $("body").addClass("maze-dialog-fixed-open");
            staticCountFixed++;
            $dialog.parent().show();
            $dialog.css({top: 0, left: 0});
        }

        $dialog.show();
        this.setPosition();
        $dialog.hide();

        if (self.options.mode == "fixed")
            $dialog.css({top: 0});

        self.showDialog().done(function () {
            self.blockDialog = false;
            self.options.open.call(self.$elemets, self);
            self.$elemets.trigger("openDialog", self);
        });
    }
    MazeDialog.prototype.closeDialog = function ()
    {

        if (this.blockDialog){
            return false;
        }
            

        if (!this.isCreate() || !this.isOpen()){
            return false;
        }
            

        if (!this.options.beforeClose.call(this.$elemets, self)){
            return false;
        }
            

        this.blockDialog = true;

        var self = this;
        this.hideDialog().done(function () {

            if (self.options.modal)
            {
                self.$overlay.remove();
            }
            if (self.options.mode == "fixed")
            {
                staticCountFixed--;
                self.$dialog.parent().hide();
                if (staticCountFixed <= 0)
                {
                    $("body").removeClass("maze-dialog-fixed-open");
                }
            }
            self.blockDialog = false;
            self.options.close.call(self.$elemets, self);
            self.$elemets.trigger("closeDialog", self);
        });

    }
    MazeDialog.prototype.destroy = function ()
    {
        var self = this;

        this.$elemets
                .css(self.originalCss)
                .detach();

        if (this.options.modal)
        {
            this.$overlay.remove();
            this.$overlay = null;
        }

        if (this.options.mode == "fixed")
        {
            staticCountFixed--;
            if (staticCountFixed === 0)
            {
                $("body").removeClass("maze-dialog-fixed-open");
            }
            this.$dialog.parent().remove();
        }
        this.$elemets.unbind("mazeResize mazeScroll");
        this.$elemets.unbind("openDialog", self.setFloat);
        this.$dialog.unbind("dragStop");
        this.$dialog.unbind("mousedown");

        this.$dialog.stop(true, true).remove();

        this.$dialog = null;

        var next = this.originalPosition.parent.children().eq(self.originalPosition.index);

        if (next !== this.$elemets)
        {
            next.before(self.$elemets);
        }
        else
        {
            this.originalPosition.parent.append(self.$elemets);
        }
    }
    MazeDialog.prototype.isOpen = function ()
    {
        if (this.$dialog.is(":visible")){
            return true;
        }
            
        return false;
    }
    MazeDialog.prototype.isCreate = function ()
    {
        if (this.$dialog == null){
            return false;
        }
            
        return true;
    }
    MazeDialog.prototype.isOverlay = function ()
    {
        if (this.$overlay == null){
             return false;
        }
           
        return true;
    }
    MazeDialog.prototype.getActiveDilalog = function ()
    {
        var self = this;
        var $dialog = $('.maze-dialog').not(self.$dialog);
        if ($dialog.size() === 0)
            return false;
        return $dialog;
    }
    MazeDialog.prototype.setFocusDilalog = function ()
    {
        var self = this,
                zindexArr = Array(),
                zIndex = null,
                maxZIndex = null,
                $activeDialog = null;


        $activeDialog = $('.maze-dialog').not(self.$dialog);
        if ($activeDialog.size() !== 0)
        {
            $activeDialog.each(function () {
                zindexArr.push($(this).css("z-index"));
            });
            maxZIndex = Math.max.apply(Math, zindexArr);
        }

        zIndex = maxZIndex == null ? this.options.zIndex : maxZIndex;

        if (this.isOverlay())
        {
            zIndex += 1;
            this.$overlay.css("z-index", zIndex);
        }
        this.$dialog.css("z-index", zIndex + 1);
        if (this.options.mode == "fixed")
        {
            this.$dialog.parent(".maze-dialog-fixed-wrap").css("z-index", zIndex + 1);
        }

    }
    MazeDialog.prototype.setPosition = function ()
    {
        if (!this.isCreate()){
            return false;
        }
            
        var position = this.options.position,
                self = this,
                myAt = [],
                offset = [0, 0];

        if (typeof position == "function")
        {
            position = position.call(this.$elemets, this);
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
            of: window,
            collision: "fit",
            using: function (pos) {
                var topOffset = $(this).css(pos).offset().top;
                if (topOffset < 0) {
                    $(this).css("top", pos.top - topOffset);
                }
            }
        });

    }
    MazeDialog.prototype.showDialog = function ()
    {
        if (this.isOpen()){
            return false;
        }
            

        var self = this;
        var def = $.Deferred();
        if (typeof this.options.show == "string" && this.options.show !== "")
        {
            this.$dialog.effect(this.options.show, {mode: "show"}, this.options.speed, def.resolve);
        }
        else if (typeof this.options.show == "function")
        {
            this.options.show.call(this.$dialog, this, def.resolve);
        }
        else
        {
            this.$dialog.show(1, def.resolve);
        }
        return def;
    }
    MazeDialog.prototype.hideDialog = function ()
    {
        if (!this.isOpen()){
            return false;
        }
            

        var self = this;
        var def = $.Deferred();

        if (typeof this.options.hide == "string" && this.options.hide !== "")
        {
            this.$dialog.effect(this.options.hide, {mode: "hide"}, this.options.speed, def.resolve);
        }
        else if (typeof this.options.hide == "function")
        {
            this.options.hide.call(this.$dialog, this, def.resolve);
        }
        else
        {
            this.$dialog.stop().hide();
            def.resolve();
        }
        return def;
    }
    MazeDialog.prototype.setFloat = function (e, obj)
    {
        obj.set('windowTop', obj.$dialog.offset().top - $(document).scrollTop());
        obj.set('height', obj.$dialog.outerHeight());
        obj.set('offsetDialog', obj.$dialog.offset());
        var windowTop, height, offsetDialog, windowHeight;

        obj.$elemets.bind("mazeScroll", function (e) {
            windowTop = obj.get('windowTop');
            height = obj.get('height');
            offsetDialog = obj.get('offsetDialog');
            windowHeight = $(window).height();

            if (height > windowHeight)
            {
                if (obj.$dialog.offset().top + height > windowHeight + $(document).scrollTop())
                {
                    if ((obj.$dialog.offset().top - $(document).scrollTop()) > windowTop)
                    {
                        obj.$dialog.stop().animate({top: Math.min(80, obj.options.marginfloat) + $(document).scrollTop()}, obj.options.speed);
                    }

                    return false;
                }
                else
                {
                    obj.$dialog.stop().animate({top: $(document).scrollTop() - (height - windowHeight) - Math.min(80, obj.options.marginfloat)}, obj.options.speed);
                }
            }
            else
            {
                obj.$dialog.stop().animate({top: windowTop + $(document).scrollTop()}, obj.options.speed);
            }
        });

        obj.$elemets.bind("mazeResize", function () {
            obj.set('windowTop', obj.$dialog.offset().top - $(document).scrollTop());
            obj.set('height', obj.$dialog.outerHeight());
            obj.set('offsetDialog', obj.$dialog.offset());
        });

        obj.$dialog.bind("dragStop", function () {
            obj.set('windowTop', obj.$dialog.offset().top - $(document).scrollTop());
            obj.set('height', obj.$dialog.outerHeight());
            obj.set('offsetDialog', obj.$dialog.offset());
        });

    }
    $.fn.mazeDialog = function (options) {
        var arg = arguments, mazeDialogInstants = null;
        if (typeof arg[0] == "string" && arg[0] == "dialog")
        {
            mazeDialogInstants = $(this).eq(0).data('mazeDialog');
            if (!mazeDialogInstants){
                return false;
            }
                
            return mazeDialogInstants.$dialog;
        }
        if (typeof arg[0] == "string" && arg[0] == "instance")
        {
            mazeDialogInstants = $(this).eq(0).data('mazeDialog');
            if (!mazeDialogInstants){
                return false;
            }
                
            return mazeDialogInstants;
        }
        if (typeof arg[0] == "string" && arg[0] == "options")
        {
            mazeDialogInstants = $(this).eq(0).data('mazeDialog');
            if (!mazeDialogInstants){
                return false;
            }
                
            return mazeDialogInstants.options[arg[1]];
        }
        return this.each(function () {

            mazeDialogInstants = $(this).data('mazeDialog');
            if (mazeDialogInstants)
            {
                if (typeof arg[0] == "string" && arg[0] == "open")
                {
                    mazeDialogInstants.openDialog();
                }
                if (typeof arg[0] == "string" && arg[0] == "close")
                {
                    mazeDialogInstants.closeDialog();
                }
                if (typeof arg[0] == "string" && arg[0] == "destroy")
                {
                    mazeDialogInstants.destroy();
                }
                if (typeof arg[0] == "string" && arg[0] == "refresh")
                {
                    mazeDialogInstants.refresh();
                }
                if (typeof arg[0] == "string" && arg[0] == "update")
                {
                    mazeDialogInstants.setPosition();
                }
                // перегрузка настроек для текущего экземпляра класса
                if (typeof arg[0] == "string" && arg[0] == "refreshSettings" && typeof arg[1] == "object")
                {
                    mazeDialogInstants.refreshSettings(arg[1]);
                }

            }
            else
            {
                mazeDialogInstants = new MazeDialog(this, options);
                return $(this).data('mazeDialog', mazeDialogInstants);
            }
        });

    }


})(jQuery);
