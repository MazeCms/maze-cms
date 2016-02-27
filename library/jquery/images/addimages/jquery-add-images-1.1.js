(function ($) { // модуль

    // общие настройки для всех экземпляров класса		
    var settings = {
        // текст Кнопки добавить
        text_btn: "Добавить Фото",
        // Имя массива галереи
        //(string)(function)  функция должна возвращать значение (string)
        // this - ссылка на текущую галерею
        name: "images",
        // Время анимации при удалении 
        time_remove: 500,
        // Время анимации при создании 
        time_in: 500,
        //(number)(function) максимальное количество изображений, функция должна возвращать значение (string) 
        // this - ссылка на текущую галерею
        max_img: 10,
        // прозрачность при сортировке
        opacity: 0.5,
        // курсор при перетаскивании
        cursor: 'move',
        // класс - пустое место
        placeholder: '',
        // класс доступ эелементов для сортировки
        sortClass: 'active-sort',
        // перефекс клааса
        prefix_class: "_skin",
        // обработчик кнопке добавить
        // this - ссылка на текущую галерею
        // obj(object) - текущий экземпляра класса
        add: function (obj, event) {
        },
        // обработчик - превышение количества изображений
        maximum: function (cout, bool) {
        },
        // обработчик - удалить изображения
        remove: function (obj) {
        },
        // обработчик - создание изображения
        create: function (arg) {
        },
        // событие окночания сортировки
        // e - event событие сортировки
        // ui - оригинальный объект UI update
        // this - ссылка на текущую галерею
        sortUpdate: function (e, ui) {
        },
        // оригинальный  метод sortable
        sortStart: function (e, ui) {
        },
        // оригинальный  метод sortable
        sort: function (e, ui) {
        },
        // оригинальный  метод sortable
        beforeStop: function (e, ui) {
        },
        // оригинальный  метод sortable
        stop: function (e, ui) {
        }
    }

    function AddImages(elem, options)
    {

        var self = this;
        this.$elemets = $(elem);
        this.count = 1;
        this.options = $.extend({}, settings, options || {});
        this.$elemets.addClass(self.getSelector('add-images-ui'));
        this.setGalImages();
        this.createAddImages();
        this.$addimagesBtn = this.$elemets.find(self.getSelector(".add-images-btn"))
        this.deleteImages();
        this.addImageBtn();
    }

    AddImages.prototype.refreshSettings = function (obj)
    {
        var self = this;
        this.options = $.extend(self.options, obj || {});
    }
    AddImages.prototype.setGalImages = function ()
    {
        var self = this;
        if (this.$elemets.find("li").is("li"))
        {
            this.$elemets.find("li").each(function () {
                var $item = $(this);
                self.createGalImages($item.attr("data-src"), $item.attr("data-min"));
                $item.remove();
            })
        }
    }
    AddImages.prototype.deleteImages = function ()
    {
        var self = this;
        function deleteImages()
        {
            self.count--;
            self.quotaImages()
            self.options.remove($(this).parents(self.getSelector('.b-photo-galery_item')));

            $(this).parents(self.getSelector('.b-photo-galery_item')).fadeOut(self.options.time_remove, function () {
                $(this).remove()
            });
        }
        this.$elemets.delegate(self.getSelector(".ico-close"), "click", deleteImages);
    }
    AddImages.prototype.addImageBtn = function ()
    {
        var self = this;
        function addHandler(e)
        {
            self.options.add.call(self.$elemets, self, e);
        }
        this.$elemets.delegate(self.getSelector(".add-images-btn"), "click", addHandler);
    }
    AddImages.prototype.getTitle = function (name)
    {
        if(name == '' || !name) return;
        var o_title = name.replace(/\/$/, "").match(/(?:.+\/)*(.+\.[a-z]{1,5})$/i);
        if (o_title == null)
            return false;
        o_title = typeof o_title[1] !== undefined ? o_title[1] : false;
        return 	o_title
    }

    AddImages.prototype.getSelector = function (celector)
    {
        return celector + this.options.prefix_class;
    }

    AddImages.prototype.createAddImages = function ()
    {

        var tmp = this.templateAddBtn()
                .replace(/{PREFIX}/g, this.options.prefix_class)
                .replace(/{BTNADD}/g, this.options.text_btn);
        this.$elemets.append(tmp);
        this.quotaImages();
    }

    AddImages.prototype.quotaImages = function ()
    {
        var self = this;
        var max_img;
        
        if (typeof this.options.max_img == "number")
        {
            max_img = this.options.max_img;
        }
        if (typeof this.options.max_img == "function")
        {
            max_img = this.options.max_img.call(this.$elemets);
            max_img = typeof max_img == "string" ? max_img : settings.max_img;
        }

        if (this.count > max_img)
        {
            this.$elemets.find(self.getSelector('.add-images-btn')).hide();
            this.options.maximum(this.count, true);
            return false;
        }
        if (this.$elemets.find(self.getSelector('.add-images-btn')).is(":hidden"))
        {
            this.options.maximum(this.count, false);
            this.$elemets.find(self.getSelector('.add-images-btn')).show();
        }
        return true;
    }

    AddImages.prototype.createGalImages = function (src, min)
    {
        if (!this.getTitle(src) || !this.quotaImages())
            return false;

        this.options.create.apply(this, arguments);

        var self = this;
        var name;
        if (typeof this.options.name == "string")
        {
            name = this.options.name;
        }
        if (typeof this.options.name == "function")
        {
            name = this.options.name.call(this.$elemets);
            name = typeof name == "string" ? name : settings.name;
        }
        this.$elemets.triggerHandler("createImages");
        var count = this.count++;
        var tmp = this.templateImages()
                .replace(/{PREFIX}/g, this.options.prefix_class)
                .replace(/{SRC}/g, min || src)
                .replace(/{TITLE}/g, this.getTitle(src))
        tmp = $(tmp);

        if (this.$elemets.find(self.getSelector('.add-images-btn')).is(self.getSelector('.add-images-btn')))
        {
            this.$elemets.find(self.getSelector('.add-images-btn')).before(tmp.hide().fadeIn(self.options.time_in));
        }
        else
        {
            this.$elemets.append(tmp);
        }
        var inputName = $('<input>', {type: 'hidden', name: name}).val(src);
        tmp.append(inputName);
        tmp.css('background-image', 'url(' + src + ')')
        this.sortImages();
        this.quotaImages();
    }

    AddImages.prototype.templateAddBtn = function ()
    {
        var html = '<li class="add-images-btn{PREFIX} b-photo-galery_item{PREFIX}">' +
                '<a><span class="i-dotted{PREFIX}">{BTNADD}</span></a>' +
                ' <div class="bg-add-btn-images{PREFIX}"></div>' +
                '</li>';
        return html;
    }
    AddImages.prototype.templateImages = function ()
    {
        var html = '<li class="b-photo-galery_item{PREFIX}">' +
                '<div class="tool-bar{PREFIX} tool-bar-top{PREFIX}">' +
                '<span class="title-image{PREFIX} original-title-images{PREFIX}">{TITLE}</span>' +
                '<i class="i-ico-set{PREFIX} ico-close{PREFIX}"></i>' +
                ' </div>' +
                '</li>';
        return html;
    }
    AddImages.prototype.sortImages = function ()
    {
        var self = this;
        var $sortElem = this.$elemets.children("li").not(self.getSelector('.add-images-btn')).addClass(self.options.sortClass);
        this.$elemets.sortable({
            items: "li." + self.options.sortClass,
            opacity: self.options.opacity,
            cursor: self.options.cursor,
            placeholder: self.options.placeholder,
            sort: self.options.sort,
            start: self.options.sortStart,
            beforeStop: self.options.beforeStop,
            stop: self.options.stop,
            update: function (e, ui) {

                self.options.sortUpdate.call(self.$elemets, e, ui);
            }
        })
    }


    $.fn.addImages = function (options) {
        var arg = arguments;

        return this.each(function () {
            var $this = $(this);
            var instance = $this.data('AddImages');
            if (instance)
            {
                // добавление картинки для текущего экземпляра класса
                if (typeof arg[0] == "string" && arg[0] == "createImages")
                {
                    Array.prototype.splice.call(arg, 0, 1);
                    instance.createGalImages.apply(instance, arg);
                }
                // перегрузка настроек для текущего экземпляра класса
                if (typeof arg[0] == "string" && arg[0] == "refreshSettings" && typeof arg[1] == "object")
                {
                    instance.refreshSettings.call(instance, arg[1]);
                }

            }
            else
            {
                var instance = new AddImages(this, options);
                $this.data('AddImages', instance);
            }
        })

    }

})(jQuery);
