/**
 * Валидатор форм
 *
 * @param (string) eventCheck -  события при которых происходит проверка формы по умолчанию: 'submit change'
 * @param (string) groupError - css класс родителя проверяемого элемента присваивается при ошибке по умолчанию: 'has-error'
 * @param (string) groupSuccess - css класс родителя проверяемого элемента присваивается при успехе по умолчанию:'has-success',
 * @param (string|function) errorSummary - css селектор вывода всех ошибок, к контексте формы  '.alert-danger'; function(errors, allError, e)
 errors (object) - объект всех ошибок,  allError (string) html всех ошибок, e (event) -  объект события
 * @param (string) action - url формы для ajax запросов по умолчанию из action="" формы       
 * @param (string) varCheck имя переменной при отправки ajax запроса
 * @param (string) type тип запроса GET или POST 
 * @param (function) filter возвращает объект ошибок для дальнейшей обработки вида {id:text}, аргумент data - ответ от сервера  
 * @param (array) elements - массив объектов параметров проверки элементов вида: 
 {
 validate: 'string', - класс валидатора
 elem: '#textfield', - id элемента откуда будет браться значение
 options: {} - настройки валидатор 
 }
 *
 * @param (function) onAfterCheck - функция обратного вызова function(errors, e) { return true; },
 * @param (function) onBeforeCheck - функция обратного вызова function() { return true; },
 * @param (function) onErrorAjax - функция обратного вызова function(){},
 * @param (function) onErrorElem - функция обратного вызова function(elem, mess){ },
 * @param (function) onSuccessElem - функция обратного вызова function(elem, mess){ }
 * @param (function) onReset - функция обратного вызова
 * @param (function) onBeforeSubmit - функция обратного вызова должна возвращать true для отправки формы или false для отмены
 * 
 * @method options - установка настройки options $('selector').mazeForm('options', 'настройка', 'значение');
 * @method reset - сброс проверки формы
 * @method refresh - обновление формы, если нужно переопределить настройки формы:
 *                   $('selector').mazeForm('options', 'настройка', 'значение').mazeForm('refresh')
 * @method destroy - уничтожение валидатора формы 
 * 
 * @event success.mazeForm - успешная обработка формы  
 * @event error.mazeForm - ошибка отправки формы
 * @event beforeAjax.mazeForm - Перед отправкой формы  
 * @event afterSubmit.mazeForm - после отправки формы            
 */
(function ($) {

    $.mazeForm = {
        validator: Array(),
        addValidator: function (name, object)
        {
            if (name in $.mazeForm.validator)
            {
                if ('version' in object)
                {
                    if ($.mazeForm.validator[name].prototype.version > object.version)
                    {
                        return false;
                    }
                }
            }
            $.mazeForm.validator[name] = function () {
                AbstractValidator.apply(this, arguments)
            };
            $.mazeForm.validator[name].prototype = $.mazeForm.createObject(AbstractValidator.prototype);
            $.mazeForm.validator[name].prototype.constructor = $.mazeForm.validator[name];

            $.extend($.mazeForm.validator[name].prototype, object);
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
        }
    }



    function AbstractValidator($form, element, options, name, alloptions)
    {

        var selfClass = this;
        this.$form = $form;
        this.className = name;
        this.$element = $(element);
        this.alloptions = alloptions;
        this.options = $.extend({}, selfClass.defaults, options || {});
        this.error = Array();
        this.init();
        this.deferred = null;
    }

    AbstractValidator.prototype = {
        constructor: AbstractValidator,
        defaults: {},
        _trigger: function (eventTypee, data) {

            this.$element.triggerHandler(eventTypee + '.mazeForm', data);

        },
        init: $.noop,
        check: function () {
            throw TypeError("метод 'check' обязатель для реализации");
        },
        setError: function (text, params)
        {
            if ($.isPlainObject(params))
            {
                $.each(params, function (name, val) {
                    text = text.replace((new RegExp("\\{" + name + "\\}", 'ig')), val);
                })
            }
            this.error.push(text);
        },
        cleraError: function () {
            this.error = Array();
        },
        hasError: function () {
            return this.error.length > 0 ? true : false;
        },
        getCheckElem: function () {
            if (this.$element.is('div') || this.$element.is('span') || this.$element.is('.mazeFormInputGroup'))
            {
                var checkbox = this.$element.find('input[type=checkbox]');
                var radio = this.$element.find('input[type=radio]');
                var target = $();

                if (checkbox.size() > 0)
                {
                    target = checkbox
                }
                else if (radio.size() > 0)
                {
                    target = radio
                }
                return target;
            }
            return this.$element;
        },
        getVal: function () {
            if (typeof this.$element == 'object')
            {
                if (this.$element.is('div') || this.$element.is('span') || this.$element.is('.mazeFormInputGroup'))
                {
                    var checkbox = this.$element.find('input[type=checkbox]');
                    var radio = this.$element.find('input[type=radio]');
                    var target = $();

                    if (checkbox.size() > 0)
                    {
                        target = checkbox
                    }
                    else if (radio.size() > 0)
                    {
                        target = radio
                    }

                    if (target.filter(':checked').size() == 0)
                        return null;

                    if (target.size() == 1)
                    {
                        return target.filter(':checked').val();
                    }
                    return target.filter(':checked').map(function () {
                        return $(this).val()
                    }).get();

                }
                else
                {
                    return this.$element.val();
                }
            }
            return null;
        },
        isEmpty: function (value) {

            value = value || this.getVal();

            if (value === null)
                return true;

            if (!$.isArray(value))
            {
                value = $.trim(value);
                value = value == '' ? true : false;
            }
            else if ($.isArray(value))
            {

                value = $.grep(value, function (thisVal) {
                    return $.trim(thisVal) !== '' ? true : false
                });
                value = value.length == 0 ? true : false;

            }

            return value;
        },
        inArray: function (value, array, strict) {
            var index = -1;
            $.each(array, function (i, val) {
                if (!isNaN(value))
                {
                    var exp = strict ? (value === val) : (value == val);
                    if (exp)
                    {
                        index = i;
                        return false;
                    }
                }
                else if (typeof value == 'string')
                {
                    var flag = strict ? "g" : 'ig';
                    if ((new RegExp(value, flag)).test(val))
                    {
                        index = i;
                        return false;
                    }
                }

            })

            return index;
        }
    }


    function MazeForm(form, options)
    {
        this.$form = $(form);
        this.options = $.extend({}, $.fn.mazeForm.defaults, options || {});
        this.validateObject = Array();
        this.errors = Array();
        this.success = Array();
        this.checkFlag = true;
        this.init();
    }

    MazeForm.prototype = {
        constructor: MazeForm,
        init: function ()
        {
            this.createValidator();
            this.initForm();
        },
        createValidator: function ()
        {
            var options = this.options, self = this;
            if (!$.isArray(options.elements))
            {
                throw TypeError("Текущее значение elements проверяемых элементов не является массивом");
            }

            $.each(options.elements, function (i, obj) {

                if (!$.isPlainObject(obj))
                    return true;
                if (!obj.hasOwnProperty('validate') || !obj.hasOwnProperty('elem'))
                    return true;
                if (!$.mazeForm.validator.hasOwnProperty(obj.validate))
                {
                    throw TypeError("Текущее класса " + obj.validate + " валидатора не существует");
                }
                if (typeof obj.elem == 'string')
                {

                    if (self.$form.find(obj.elem).is(obj.elem))
                    {
                        var target = self.$form.find(obj.elem).eq(0);
                        var option = obj.hasOwnProperty('options') ? obj.options : {};
                        var objectValid = new $.mazeForm.validator[obj.validate](self.$form, target, option, obj.validate, self.options);
                        self.validateObject.push(objectValid);
                    }
                }

            })

        },
        setError: function (id, elem, mess) {

            this.clearById(this.success, id);
            this.clearById(this.errors, id);
            this.errors.push({
                id: id,
                elem: elem,
                mess: mess
            });
        },
        setSuccess: function (id, elem)
        {
            this.clearById(this.success, id);
            this.clearById(this.errors, id);
            this.success.push({
                id: id,
                elem: elem
            })
        },
        isError: function (elem)
        {
            var self = this, result = false;
            $.each(self.errors, function (i, obj) {
                if (obj.elem.is(elem))
                {
                    result = true;
                    return false;
                }
            })
            return result;

        },
        isValidate: function (elem, type)
        {
            var self = this, result = false;

            $.each(self.validateObject, function (i, obj) {
                if (obj.$element.is(elem))
                {
                    if (type)
                    {
                        if (obj.className == type)
                        {
                            result = true;
                            return false;
                        }
                    }
                    else
                    {
                        result = true;
                        return false;
                    }
                }
            });

            return result;
        },
        clearById: function (array, id)
        {
            if (!$.isArray(array))
                return;
            var self = this;
            $.each(array, function (i, obj) {
                if (obj.id == id)
                {
                    array.splice(i, 1);
                    return false;
                }
            })
        },
        ajaxSend: function () {

            var options = this.options, self = this, def = $.Deferred();

            var url = options.action == null ? self.$form.attr('action') : options.action;
            url += url.indexOf('?') == -1 ? '?' : '&';
            url += options.varCheck + '=' + self.$form.attr('id');
            if(typeof options.paramsAjax == 'object')
            {
                 url += '&'+$.param(options.paramsAjax);
            }
           
            self.$form.triggerHandler('beforeAjax.mazeForm');
            $.ajax({
                url: url,
                type: options.type,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    var data = options.filter(data);
                    var typeError = [];
                    
                    self.errors = [];
                    self.success = [];
                    
                    if ($.isPlainObject(data))
                    {                  
                        $.each(data, function (id, text) {
                            if (!self.isValidate($('#' + id)))
                            {
                                var target = self.$form.find('#' + id).eq(0);
                                if (!target.is('#' + id))
                                {
                                    self.$form.append($('<div>', {id: id}).hide());
                                    target = self.$form.find('#' + id).eq(0);
                                }
                                if (target.is('#' + id))
                                {
                                    var objectValid = new $.mazeForm.validator['inline'](self.$form, target, {}, 'inline', self.options);
                                   
                                    self.validateObject.push(objectValid);
                                }
                            }
                        })
                    
                        $.each(self.validateObject, function (i, obj) {
                            obj.cleraError();
                            var name = obj.$element.attr('id');
                                
                            if ($.inArray(name, typeError) !== -1){
                                return true;
                            }
                            
                            if (data.hasOwnProperty(name))
                            {
                             
                                if ($.isArray(data[name]))
                                {
                                    $.each(data[name], function (i, textErr) {
                                        obj.setError(textErr);
                                    })
                                }
                                else
                                {
                                    obj.setError(data[name]);
                                }
                                typeError.push(name);

                            }

                            if (obj.hasError())
                            {
                                self.setError(i, obj.$element, obj.error);
                            }
                            else
                            {
                                self.setSuccess(i, obj.$element)
                            }
                        })

                    }
                    else
                    {      
                        $.each(self.validateObject, function (i, obj) {
                            obj.cleraError();
                           if (!obj.hasError())
                           {
                               self.setSuccess(i, obj.$element)
                           }
                        })
                    }
                    def.resolve();
                },
                error: function () {
                    options.onErrorAjax.apply(self.$form, arguments);
                    def.resolve();
                },
                data: self.$form.serialize()
            })

            return def;
        },
        renderMessage: function (e) {
            var options = this.options, self = this, allError;

            $.each(self.validateObject, function (i, obj) {
                self.$form.find('#' + obj.$element.attr('id') + '_message').html("").hide();
            })


            if (self.errors.length > 0)
            {
                
                allError = '';
                $.each(self.errors, function (i, err) {
                    err.elem.closest(options.groupClass).addClass(options.groupError).removeClass(options.groupSuccess);
                    var text = '';
                    $.each(err.mess, function (i, tx) {
                        text += '<li>' + tx + '</li>';
                        allError += '<li>' + tx + '</li>';
                    })
                    text = '<ul>' + text + '</ul>';
                    if (self.$form.find('#' + err.elem.attr('id') + '_message').is('#' + err.elem.attr('id') + '_message'))
                    {
                        if (self.isError(err.elem))
                        {
                            var elem = self.$form.find('#' + err.elem.attr('id') + '_message');
                            elem.html($(text).append(elem.find('li'))).show();
                        }
                        else
                        {
                            self.$form.find('#' + err.elem.attr('id') + '_message').html(text).show();
                        }
                    }
                    options.onErrorElem.call(self.$form, err.elem, err.mess)
                })
                if (typeof options.errorSummary == 'string')
                {
                    if (self.$form.find(options.errorSummary).is(options.errorSummary))
                    {
                        self.$form.find(options.errorSummary).html('<ul>' + allError + '</ul>').show();
                    }
                }
                else if (typeof options.errorSummary == 'function')
                {
                    options.errorSummary.call(self.$form, self.errors, allError, e);
                }
            }

            if (self.success.length > 0)
            {
                $.each(self.success, function (i, su) {

                    if (self.isError(su.elem))
                        return true;
                    if (self.validateObject.hasOwnProperty(su.id))
                    {
                        if (self.validateObject[su.id].options.hasOwnProperty('skipOnEmpty'))
                        {

                            if (!self.validateObject[su.id].options.skipOnEmpty && self.validateObject[su.id].isEmpty())
                            {
                                su.elem.closest(options.groupClass).removeClass(options.groupError + ' ' + options.groupSuccess);
                                if (self.$form.find('#' + su.elem.attr('id') + '_message').is('#' + su.elem.attr('id') + '_message'))
                                {
                                    self.$form.find('#' + su.elem.attr('id') + '_message').html("").hide();
                                }
                                options.onSuccessElem.call(self.$form, su.elem, true);
                                return true;
                            }
                        }

                    }

                    su.elem.closest(options.groupClass).addClass(options.groupSuccess).removeClass(options.groupError);
                    if (self.$form.find('#' + su.elem.attr('id') + '_message').is('#' + su.elem.attr('id') + '_message'))
                    {
                        self.$form.find('#' + su.elem.attr('id') + '_message').html("").hide();
                    }
                    options.onSuccessElem.call(self.$form, su.elem)
                })

                if (self.errors.length == 0)
                {
                    if (typeof options.errorSummary == 'string')
                    {
                        if (self.$form.find(options.errorSummary).is(options.errorSummary))
                        {
                            self.$form.find(options.errorSummary).html('').hide();
                        }
                    }
                }
            }
        },
        resetError: function () {
            var self = this, options = this.options;

            if (self.errors.length > 0)
            {

                $.each(self.validateObject, function (i, obj) {
                    obj.cleraError();

                    obj.$element.closest(options.groupClass).removeClass(options.groupError);
                    self.$form.find('#' + obj.$element.attr('id') + '_message').hide();
                })
                self.errors = Array();
                if (typeof options.errorSummary == 'string')
                {
                    if (self.$form.find(options.errorSummary).is(options.errorSummary))
                    {
                        self.$form.find(options.errorSummary).html('').hide();
                    }
                }
            }
        },
        resetSuccess: function () {
            var self = this, options = this.options;

            if (self.success.length > 0)
            {
                $.each(self.success, function (i, su) {
                    su.elem.closest(options.groupClass).removeClass(options.groupSuccess);
                })
                self.success = Array();
            }
        },
        reset: function () {
            var self = this, options = this.options;
            this.resetError();
            this.resetSuccess();
            options.onReset.call(self.$form)
        },
        refresh: function () {

            this.reset();
            this.validateObject = Array();
            this.$form.unbind();
            this.init();

        },
        destroy: function () {
            this.reset();
            this.validateObject = Array();
            this.$form.unbind();
        },
        check:function(){
            var options = this.options, self = this;  
            this.checkFlag = false;         
            this.$form.submit();
            this.$form.one('afterSubmit.mazeForm', function(){
               self.checkFlag = true;
            }) 
        },
        initForm: function () {
            var options = this.options, self = this, deferred = Array(), switchSubmit = false;

            if (typeof options.errorSummary == 'string')
            {
                if (self.$form.find(options.errorSummary).is(options.errorSummary))
                {
                    self.$form.find(options.errorSummary).hide();
                }
            }
            $.each(self.validateObject, function (i, obj) {
                self.$form.find('#' + obj.$element.attr('id') + '_message').hide();
            })

            this.$form.bind('reset', function () {
                self.reset()
            });

            this.$form.bind(options.eventCheck, function (e) {
                
                if (!switchSubmit) {
                    e.preventDefault();

                }
                else
                {
                    if (self.errors.length == 0)
                    {
                        return;
                    }
                }
                var defAjax = null;

                $.each(self.validateObject, function (i, obj) {

                    if (!$(e.target).is('form') && !$(e.target).is(obj.getCheckElem()))
                        return true;

                    obj.check();

                    if (obj.hasError())
                    {
                        self.setError(i, obj.$element, obj.error);
                    }
                    else
                    {
                        self.setSuccess(i, obj.$element)
                    }

                    if (obj.deferred !== null)
                    {
                        deferred.push(obj.deferred);
                    }
                });

                function submitForm() {

                    self.renderMessage(e);
                    if (e.type == 'submit')
                    {
                        if (self.errors.length == 0)
                        {
                            self.$form.triggerHandler('success.mazeForm');
                        }
                        else
                        {
                            self.$form.triggerHandler('error.mazeForm', {error: self.errors});
                        }
                    }
                    
                    
                    if (options.onAfterCheck.call(self.$form, self.errors, e) && e.type == 'submit' && self.checkFlag)
                    {                       
                        if (self.errors.length == 0)
                        {
                            var eventBeforeSubmit = $.Event('beforeSubmit.mazeForm');                    
                            self.$form.triggerHandler(eventBeforeSubmit,self.errors);
                            if(!eventBeforeSubmit.result)
                            {
                                switchSubmit = true;       
                                if(options.onBeforeSubmit.call(self.$form, self.errors, e))
                                {
                                    self.$form.trigger('submit');
                                }     
                            }
                                                   
                            return false;
                        }
                    }
                   
                   self.$form.triggerHandler('afterSubmit.mazeForm');
                   
                   switchSubmit = false;
                }

                if (e.type == 'submit' && options.ajaxSubmit)
                {
                    defAjax = self.ajaxSend();
                    deferred.push(defAjax);
                }

                if (deferred.length > 0)
                {
                    $.when.apply($, deferred).done(function () {
                        if (deferred.hasOwnProperty(0))
                        {
                            $.each(deferred, function (i, def) {
                                if (def == undefined)
                                    return true;

                                if (def.state() == 'resolved') {

                                    deferred.splice(i, 1);
                                }
                            })
                        }

                        submitForm();
                    })
                }
                else
                {
                    submitForm();
                }


                if (!switchSubmit)
                    return false;
            })
        }
    }

    $.fn.mazeForm = function (options) {
        var arg = arguments;

        return this.each(function () {
            var instance = $(this).data('mazeForm');

            if (instance)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instance.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "reset")
                {
                    instance.reset();
                }
                if (typeof arg[0] == "string" && arg[0] == "refresh")
                {
                    instance.refresh();
                }
                if (typeof arg[0] == "string" && arg[0] == "destroy")
                {
                    instance.destroy();
                }
                
                if (typeof arg[0] == "string" && arg[0] == "check")
                {
                    instance.check();
                }
            }
            else
            {
                instance = new MazeForm(this, options);
                $(this).data('mazeForm', instance);
            }
        })
    }

    $.fn.mazeForm.defaults = {
        eventCheck: 'submit change',
        groupError: 'has-error',
        groupSuccess: 'has-success',
        errorSummary: '.alert-danger',
        groupClass: '.form-group',       
        ajaxSubmit: true,
        action: null,
        varCheck: 'checkform',
        type: 'POST',
        paramsAjax:{clear:'ajax'},
        filter: function (data) {
            return data
        },
        elements: [],
        onAfterCheck: function (errors, e) {
            return true;
        },
        onBeforeCheck: function () {
            return true;
        },
        onBeforeSubmit : function(){return true},
        onErrorAjax: function () {
        },
        onErrorElem: function (elem, mess) {
        },
        onSuccessElem: function (elem, mess) {
        },
        onReset: function () {
        }
    }


})(jQuery);

(function ($) {
    $.mazeForm.addValidator('inline', {
        version: 1.0,
        defaults: {},
        check: function () {
        }
    })
})(jQuery);
/**
 * string Проверка строки
 */
(function ($) {
    $.mazeForm.addValidator('string', {
        version: 1.0,
        defaults: {
            length: null,
            skipOnEmpty: false,
            max: null,
            min: null,
            message: ' должна быть строка',
            tooShort: 'должны содержать по крайней мере {min} символа.',
            tooLong: 'должен содержать не более {max} символа.',
            notEqual: 'должен содержать {length} символа'
        },
        check: function () {

            this.cleraError();
            var self = this, val = self.getVal(),
                    length = val.length,
                    options = this.options;
            if (typeof val !== 'string')
            {
                this.setError(options.message);
                return;
            }
            val = $.trim(val);

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            if (options.min !== null && length < options.min)
            {
                this.setError(options.tooShort, {min: options.min});
            }

            if (options.max !== null && length > options.max)
            {
                this.setError(options.tooLong, {max: options.max});
            }

            if (options.length !== null && length !== options.length)
            {
                this.setError(options.notEqual, {length: options.length});
            }

        }
    })
})(jQuery);

/**
 * required Проверка на пустоту
 */
(function ($) {
    $.mazeForm.addValidator('required', {
        version: 1.0,
        defaults: {
            requiredValue: null,
            strict: false,
            message: ' не может быть пустым',
            messageValue: ' должно быть {requiredValue}'
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if (options.requiredValue === null)
            {
                if (options.strict && val !== null || !options.strict && !this.isEmpty(val))
                {
                    return;
                }
            }
            else if (!options.strict && val == options.requiredValue || options.strict && val === options.requiredValue)
            {
                return;
            }

            this.setError((options.requiredValue === null ? options.message : options.messageValue), {messageValue: options.requiredValue});


        }
    })
})(jQuery);

/**
 * range Проверка диапазона значений
 */
(function ($) {
    $.mazeForm.addValidator('range', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            allowArray: false,
            not: false,
            strict: false,
            range: [],
            message: 'является недействительным'
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if (!$.isArray(options.range))
                return;

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            if (!options.allowArray && $.isArray(val))
            {
                this.setError(options.message);
                return;
            }

            var inRes = true;

            if ($.isArray(val))
            {
                $.each(val, function (i, v) {
                    if (self.inArray(v, options.range, options.strict) == -1)
                    {
                        inRes = false;
                        return false;
                    }

                })
            }
            else if (typeof val == 'string')
            {
                if (this.inArray(val, options.range, options.strict) == -1)
                {
                    inRes = false;
                }
            }

            if (options.not == inRes)
            {
                this.setError(options.message);
            }

        }
    })
})(jQuery);
/**
 * number Проверка  значения на число
 */
(function ($) {
    $.mazeForm.addValidator('number', {
        version: 1.0,
        defaults: {
            max: null,
            min: null,
            skipOnEmpty: false,
            tooBig: 'не должно быть больше, чем {max}',
            tooSmall: 'не должно быть меньше {min}',
            message: 'должно быть целым числом',
            pattern: /^\s*[+-]?\d+\s*$/
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if ($.isArray(val))
                return;

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            if (val.search(options.pattern) == -1)
            {               
                this.setError(options.message);
                return;
            }

            if (options.min !== null && val < options.min)
            {
                this.setError(options.tooSmall, {min: options.min});
            }
            else if (options.max !== null && val > options.max)
            {
                this.setError(options.tooBig, {max: options.max});
            }

        }
    })
})(jQuery);

/**
 * compare Сравнение значений
 */
(function ($) {
    $.mazeForm.addValidator('compare', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            compareAttribute: null,
            compareValue: null,
            operator: '==',
            message: 'необходимо повторить в точности.',
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if ($.isArray(val)) {
                this.setError(options.message);
                return;
            }

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            var compareValue, compareAttribute;
            if (options.compareValue !== null)
            {
                compareValue = options.compareValue;
            }
            else
            {
                if (typeof options.compareAttribute !== 'string')
                    return;
                compareAttribute = this.$form.find(options.compareAttribute);
                if (!compareAttribute.is(options.compareAttribute))
                    return;
                compareValue = compareAttribute.val();
            }
            var exp;

            switch (options.operator)
            {
                case '==':
                    exp = val == compareValue;
                    break;
                case '===':
                    exp = val === compareValue;
                    break;
                case '!=':
                    exp = val != compareValue;
                    break;
                case '!==':
                    exp = val !== compareValue;
                    break;
                case '>':
                    exp = val > compareValue;
                    break;
                case '>=':
                    exp = val >= compareValue;
                    break;
                case '<':
                    exp = val < compareValue;
                    break;
                case '<=':
                    exp = val <= compareValue;
                    break;
                default:
                    return false;
            }
            if (!exp)
            {
                this.setError(options.message);
            }


        }
    })
})(jQuery);

/**
 * boolean Булевы значения
 */
(function ($) {
    $.mazeForm.addValidator('boolean', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            trueValue: '1',
            falseValue: '0',
            strict: false,
            message: 'должны быть либо "{true}" или "{false}".',
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            var exp = options.strict ?
                    (val === options.trueValue || val === options.falseValue) :
                    (val == options.trueValue || val == options.falseValue);
            if (!exp)
            {
                this.setError(options.message, {'true': options.trueValue, 'false': options.falseValue});
            }


        }
    })
})(jQuery);
/**
 * expression Регулярное выражение
 */
(function ($) {
    $.mazeForm.addValidator('expression', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            pattern: null,
            not: false,
            message: 'является недействительным.',
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if ($.isArray(val))
            {
                this.setError(options.message);
                return;
            }

            if (options.skipOnEmpty && this.isEmpty(val))
                return;

            if ((!options.not && val.search(options.pattern) == -1) || (options.not && val.search(options.pattern) !== -1))
            {
                this.setError(options.message);
            }



        }
    })
})(jQuery);
/**
 * email Проверка email
 */
(function ($) {
    $.mazeForm.addValidator('email', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            pattern: /^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,
            fullPattern: /^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,
            allowName: false,
            enableIDN: false,
            message: 'не является действительным адресом электронной почты.',
        },
        check: function () {

            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;

            if ($.isArray(val))
            {
                this.setError(options.message);
                return;
            }

            if (options.skipOnEmpty && this.isEmpty(val))
                return;
            var mail = val.match(/^(.*<?)(.*)@(.*)(>?)$/)
            if (mail)
            {
                var domain = mail[3];
                if (options.enableIDN)
                {
                    val = mail[1] + mail[2] + '@' + mail[3] + mail[4]
                }
                var valid = val.search(options.pattern) !== -1 || (options.allowName && val.search(options.fullPattern) !== -1);

                if (!valid)
                {
                    this.setError(options.message);
                }

            }
            else
            {
                this.setError(options.message);
            }
        }
    })
})(jQuery);
/**
 * callback Функция обратного вызова
 */
(function ($) {
    $.mazeForm.addValidator('callback', {
        version: 1.0,
        defaults: {
            skipOnEmpty: false,
            onCheck: function () {
            }
        },
        check: function () {
            this.cleraError();
            var val = this.getVal(),
                    options = this.options,
                    self = this;
            if (options.skipOnEmpty && this.isEmpty(val))
                return;
            if (typeof options.onCheck == 'function')
            {
                options.onCheck.call(self);
            }
        }
    })
})(jQuery);
/**
 * ajax TODO: Проверка асинхронная
 */
(function ($) {
    $.mazeForm.addValidator('ajax', {
        version: 1.0,
        defaults: {},
        check: function () {
        }
    })
})(jQuery);