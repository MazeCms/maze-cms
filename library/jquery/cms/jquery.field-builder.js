/*
 * События
 *
 * Методы
 * del - удалить
 * add - добавить
 * 
 * Опции
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *       // кнопка добавления нового элемента поля
 *       targetadd: '.add-widget-field',
 *       update: $.noop,
 *       // элемент играющий роль ручки при сортировки списка
 *       handle:'.glyphicon-move',
 *       //максималь разрешенное количество значений
 *       maxField:0,
 *       //куда будет добавлен новый эелемент
 *       appendTo:'.table > tbody',
 *       // html обертка элементов списка
 *       wrappList:'<tr><td style="vertical-align: middle; width: 16px;" class="text-cente"><span class="glyphicon glyphicon-move" aria-hidden="true"></span></td>'
 *                       +'<td style="vertical-align: middle" class="text-cente">{FIELD}</td>'
 *                       +'<td style="vertical-align: middle; width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>'
 *                       +'</tr>',
 *       // элемент сортирумего списка
 *       sortList:'.table',
 *       // сортирумеые эменты списка
 *       items:'tr',
 *       // родительский элемент списка
 *       parent:'tr',
 *       // ось перемещения
 *       axis:'y',
 *       // селектор - кнопка  удаления элемента списка списка
 *       delTarget:'.remove-widget-field',
 *       // функция должна возвращать элеменеты jquery или html код виджета
 *       addField:$.noop,
 *       // функция обратного вызова после добавления элемента
 *       onAfterAdd:$.noop
 */
(function ($) {

    function FieldBuilder(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.fieldBuilder.defaults, options || {});   
        this.count = 1;
        this.init();
    }

    FieldBuilder.prototype = {
        constructor: FieldBuilder,
        init: function ()
        {
            
            var selfClass = this, options = this.options;
            
            this.count = this.$element.find(options.items).size();
            this.$element.find(options.targetadd).on('click', function(){
                selfClass.add();
            })
            
            this.$element.find(options.delTarget).live('click', function(){
               var index = $(this).closest(options.parent).index();
                selfClass.del(index);
            })
            this.sortList();
            this.activeAdd();
        },
        
        add: function(){
            var options = this.options, selfClass = this;
        
            if(options.maxField == 0 || this.count < options.maxField){                
                var result = options.addField.call(selfClass.$element, selfClass.count);
                result = $(options.wrappList.replace(/{FIELD}/, result));
                selfClass.$element.find(options.appendTo).append(result);
                this.sortList();
                options.onAfterAdd.call(selfClass.$element);
                this.count++;
            }
            this.activeAdd();
        },
        
        activeAdd:function(){
             var options = this.options, selfClass = this;
            if(options.maxField == 0 || this.count < options.maxField){
                 this.$element.find(options.targetadd).show();
            }else{
                this.$element.find(options.targetadd).hide();
            }  
        },
        
        del: function(i){
            var options = this.options, selfClass = this;
            var $all = this.$element.find(options.items);
            
            if(options.isAllremove || $all.size() !== 1){
                this.$element.find(options.items).eq(i).remove();
                 this.count--;
            }
            this.activeAdd();
            options.update.call(selfClass.$element);
        },
        
        sortList: function(){
            var options = this.options, className = this;
            this.$element.find(this.options.sortList).sortable({
                items:options.items,
                handle:options.handle,
                axis:options.axis,
                update:function(e, ui){
                    options.update.call(className.$element, e, ui, this)
                }
            })    
        }
       

    }

    $.fn.fieldBuilder = function (options) {
        var arg = arguments;

        var $this = $(this);

        var instance = $this.data('fieldBuilder');

        if (instance)
        {
            if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
            {
                instance.options[arg[1]] = arg[2];
            }
            if (typeof arg[0] == "string" && arg[0] == "add")
            {
                instance.add();
            }
            if (typeof arg[0] == "string" && arg[0] == "del"  && 1 in arg)
            {
                instance.del(arg[1]);
            }
           

        }
        else
        {
            instance = new FieldBuilder(this, options);
            $this.data('fieldBuilder', instance);
        }

        return this;
    }

    $.fn.fieldBuilder.defaults = {
        // кнопка добавления нового элемента поля
        targetadd: '.add-widget-field',
        update: $.noop,
        // элемент играющий роль ручки при сортировки списка
        handle:'.glyphicon-move',
        //максималь разрешенное количество значений
        maxField:0,
        //куда будет добавлен новый эелемент
        appendTo:'.table > tbody',
        // html обертка элементов списка
        wrappList:'<tr><td style="width: 16px;" class="text-cente align-middle"><span class="glyphicon glyphicon-move" aria-hidden="true"></span></td>'
                        +'<td class="text-cente align-middle">{FIELD}</td>'
                        +'<td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>'
                        +'</tr>',
        // элемент сортирумего списка
        sortList:'.table',
        // сортирумеые эменты списка
        items:'tr',
        // родительский элемент списка
        parent:'tr',
        // ось перемещения
        axis:'y',
        // селектор - кнопка  удаления элемента списка списка
        delTarget:'.remove-widget-field',
        // функция должна возвращать элеменеты jquery или html код виджета
        addField:$.noop,
        // функция обратного вызова после добавления элемента
        onAfterAdd:$.noop,
        isAllremove:false
    }

})(jQuery);
