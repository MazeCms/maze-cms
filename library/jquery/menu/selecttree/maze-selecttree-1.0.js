/**
 * 
 * <select id="test"  name="test">
 *   <option data-icon="image.png"  value="1">один</option>
 *   <option selected="selected" value="2">два</option>
 *   <option data-parent="2" value="2.1">два один</option>
 *   <option value="3">три</option>
 *  </select>
 * @param {type} $
 * @returns {undefined}
 */
(function ($) {

    function MazeSelectTree(elem, options)
    {
        this.$element = $(elem);
        this.options = $.extend({}, $.fn.mazeSelectTree.defaults, options || {});
        this.$widget;
        this.pref = 'maze-select-tree-id-' + $('.maze-select-tree').size();
        this.init();

    }

    MazeSelectTree.prototype = {
        constructor: MazeSelectTree,
        init: function ()
        {
            this.createTree(this.parseData());
        },
        parseData: function () {
            var data = Array();
            var selfClass = this;
            var pref = this.pref;
            
            this.$element.find('option').each(function (i) {
                var $self = $(this);
                if($self.val() == '') return true;
                var obj = {
                    li_attr: {'data-id': $self.val()},
                    id: pref+'-' + $self.val(),
                    text: $self.text(), 
                    icon: $self.attr('data-icon') ? $self.attr('data-icon') : selfClass.options.defaulticon,
                    state:{selected:$self.is('[selected]'), opened:$self.is('[selected]'), disabled:$self.is('[disabled]')},
                };
                var parent = $self.attr('data-parent') ? ($self.attr('data-parent') == 0 ? '#' : $self.attr('data-parent')) : '#'
                
                if(parent !== '#')
                {
                    parent = pref+'-' + parent;
                }
                obj.parent = parent;
                data.push(obj);
            })
            return data;
        },
        
        createTree: function(dataObj)
        {
            var selfClass = this;
            this.$element.addClass('maze-select-tree');
            this.$widget = $('<div>').addClass('maze-select-tree-wrap')
            
            this.$element.after(selfClass.$widget);
           
            this.$widget.jstree( $.extend(true,{
                core: {
                    data: dataObj,
                    multiple:selfClass.$element.is('[multiple]')
                }                
            }, selfClass.options.tree || {}))
            .on('select_node.jstree deselect_node.jstree select_all.jstree deselect_all.jstree', function (e, data) {
                selfClass.$element.find('option').removeAttr('selected');       
                var valsel = $.map(data.instance.get_selected(true), function (val) {
                    return val.li_attr['data-id'];
                });
                
                selfClass.$element.val(valsel);
                if(selfClass.$element.val() === null){
                    selfClass.$element.val('')
                }
                
                selfClass.$element.trigger('selecttree',valsel);
            })

            this.$element.hide(); 
        },
        load: function(params){
            var selfClass = this;
            params = params || {};
            selfClass.$element.find('option').filter(function(){return $(this).val() !== ''}).remove();
            selfClass.update();
            $.get(selfClass.options.url, params, function(data){
               data = selfClass.options.dataFilter.call(selfClass, data);
               if(typeof data !== 'object')
               {
                   return false;
               }
               $.each(data, function(i, obj){
                   var $option = $('<option>');
                   if(obj.hasOwnProperty('value'));
                   {
                       $option.attr('value', obj.value);
                       delete obj.value;
                   }
                   
                   if(obj.hasOwnProperty('label'));
                   {
                       $option.text(obj.label);
                       delete obj.label;
                   }
                   
                   if(obj.hasOwnProperty('parent'));
                   {
                       $option.attr('data-parent', obj.parent);
                       delete obj.parent;
                   }
                   
                   if(obj.hasOwnProperty('icon'));
                   {
                       $option.attr('data-icon', obj.icon);
                       delete obj.icon;
                   }
                   
                   $option.attr(obj);
                   selfClass.$element.append($option);                   
               });
               
               selfClass.update();
            },'json')
        },
        update: function(){
            var tree = this.$widget.jstree(true);
            tree.settings.core.data = this.parseData();
            tree.refresh();
            this.$widget.one('refresh.jstree', function(){
                if($.isArray(tree.settings.core.data))
                {
                    $.each(tree.settings.core.data, function(i, obj){
                        if(obj.state.selected)
                        {
                           tree.select_node(obj.id);
                        }
                    })
                }
            })
        }
    }

    $.fn.mazeSelectTree = function (options) {
        var arg = arguments;

        var instanceSingl = $(this).eq(0).data('mazeSelectTree');
        if (instanceSingl)
        {
            if (typeof arg[0] == "string" && arg[0] == "widget")
            {
                return instanceSingl.$widget;
            }
        }

        return this.each(function () {

            var $this = $(this);

            var instance = $this.data('mazeSelectTree');

            if (instance)
            {
                if (typeof arg[0] == "string" && arg[0] == "options" && typeof arg[1] == "string" && 2 in arg)
                {
                    instance.options[arg[1]] = arg[2];
                }
                if (typeof arg[0] == "string" && arg[0] == "update")
                {
                    instance.update();
                }
                
                if (typeof arg[0] == "string" && arg[0] == "load")
                {
                    instance.load((arg.length == 2 ?  arg[1] : null));
                }

            }
            else
            {
                instance = new MazeSelectTree(this, options);
                $this.data('mazeSelectTree', instance);
            }
        })
    }

    $.fn.mazeSelectTree.defaults = {
        defaulticon: '/library/image/icons/blue-folder-horizontal.png',
        url:'',
        dataFilter: function(data){return data},
        tree:{
            core:{
                strings:{'Loading ...':'Загрузка...'}
            },
            checkbox:{
              three_state:false
            },
            plugins : [ "wholerow", "checkbox"]
        }
    }

})(jQuery);

