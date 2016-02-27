
cms.createPlugin('viewController', function () {
    
    function createField(target, model, title, type, disable) {
        var $filed = $('<div>', {'data-field_exp_id': model.field_exp_id, 'class': 'view-canvas-field panel panel-default'})
        $filed.append($('<div>', {'class': 'panel-heading'}).text(title));
        var btn = '<div class="input-group-btn">';
        btn += '<a href="#" class="btn btn-default view-canvas-edit-field"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span></a>'
        btn += '<a href="#" class="btn btn-danger view-canvas-del-field"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></a>'
        btn += '</div>'

        var panel = $('<div>', {'class': 'input-group'})
        $filed.data('disable', disable)

        if (cms.viewController().options.views) {
            var select = $('<select>', {'class': 'form-control view-canvas-setview-field'})

            if (cms.viewController().options.views.hasOwnProperty(type)) {
                $.each(cms.viewController().options.views[type], function (i, val) {
                    select.append($('<option>', {value: i}).text(val))
                })
                panel.append(select);
            }
        }
        panel.append(btn)
        $filed.append(panel)
        target.append($filed);
        if (cms.viewController().options.views) {
            if (model.field_view) {
                select.val(model.field_view)
            }
            if ($.inArray(select.val(), disable) !== -1) {
                $filed.find('.view-canvas-edit-field').hide();
            }

            model.field_view = select.val();
        }

        $filed.data('field', model);
        target.trigger('addField');
    }

    
    return {
        init: function (options) {
          
            $('.view-canvas-del-field').live('click', function () {
                var target = $(this).closest('.view-canvas-col-grid');
                $(this).closest('.view-canvas-field').remove()
                target.trigger('updateCol')
            })


//            $('.view-canvas-edit-field').live('click', function () {
//
//                var $view = $(this).closest('.view-canvas-field');
//
//                cms.loadDialog({
//                    url: [{
//                            run: 'field',
//                            view_id: '',
//                            field_exp_id: $view.attr('data-field_exp_id'),
//                            view: $view.find('select').val(),
//                            param: $view.data('field').param
//                        }],
//                    title: cms.getLang('EXP_CONTENTS_VIEW_SETTINGS_FIELD'),
//                    callback: function () {
//
//                        $('#contents-field-view-settings').bind('beforeSubmit.mazeForm', function (e) {
//                            var form = $(this).serializeObject();
//                            var data = $view.data('field')
//                            data.param = form.Params;
//                            $view.data('field', data);
//                            $(this).closest('div').mazeDialog('close')
//                            return true;
//                        })
//                    },
//                    minHeight: 150,
//                    buttons: [
//                        {
//                            label: cms.getLang('EXP_CONTENTS_SAVE'),
//                            class_btn: "maze-btn-success",
//                            class_icon: "",
//                            action: function (e, obj) {
//                                $('#contents-field-view-settings').submit();
//                            }
//                        },
//                        {
//                            label: cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE'),
//                            class_btn: "maze-btn-warning",
//                            class_icon: "",
//                            action: function (e, obj) {
//                                this.mazeDialog('close')
//                            }
//                        }
//                    ]
//                })
//
//                return false;
//            })
             $('#view-canvas').preloader('start')
            $.get(cms.getURL([{run: 'edit', clear: 'ajax'}]), function (data) {
                $('#view-canvas').preloader('end')
 
                if (data.html.hasOwnProperty('field')) {
                    $.each(data.html.field, function (n, val) {
                        
                        //createField($row.find('.view-canvas-col-grid').eq(val.model.col), val.model, val.title, val.type, val.disable);
                    })
                }
            }, 'json');
           
        },
        save: function (close) {
            updateAllField()
            var models = $('#view-canvas').find('.view-canvas-field').map(function () {
                return $(this).data('field')
            }).get();
            var grid = [];
            $('.view-canvas-row-grid').each(function () {
                grid.push({row: $(this).index(), col: $(this).find('.view-canvas-col-grid').size()})
            })

            $('.view-canvas-field').find('.alert-danger').remove();
            $('#view-canvas').preloader('start')
            $.post(cms.getURL([{run: 'edit', clear: 'ajax', checkform: 'contents-view-form'}]), {'ContentTypeView': models, 'ContentTypeViewGrid': grid, csrf: $('meta[name=csrf-token]').attr('content')}, function (response) {
                $('#view-canvas').preloader('end')
                if (response.hasOwnProperty('errors') && !$.isArray(response.errors)) {

                    var err = $.map(response.errors, function (v) {
                        return {mess: v}
                    });
                    cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), err, "auto", 400)
                    $.each(response.errors, function (i, val) {
                        var result = i.match(/contenttypeview-(\d+)-mode/)
                        if (result !== null && result.hasOwnProperty(1)) {
                            var $field = $('.view-canvas-field').eq(result[1]);
                            var error = $.map(val, function (v) {
                                return '<li>' + v + '</li>'
                            });

                            $field.append('<div class="alert alert-danger" role="alert"><ul>' + error + '</ul></div>');
                        }
                    })
                } else {
                    var param = {run: 'edit', clear: 'ajax'};
                    if (close) {
                        param.action = 'saveClose';
                    }

                    $.ajax({
                        url: cms.getURL([param]),
                        type: 'POST',
                        data: {'ContentTypeView': models, 'ContentTypeViewGrid': grid, csrf: $('meta[name=csrf-token]').attr('content')},
                        dataType: 'json',
                        error: function (xhr) {
                            if (xhr.status == 302) {
                                cms.redirect(xhr.getResponseHeader('X-Redirect'));
                            }
                        }
                    });
                }
            }, 'json')
            return  false;
        },
        addFiled: function () {
            
//            if (!$('#view-canvas').find('.select-col').is('.select-col')) {
//                cms.alertBtn(cms.getLang('EXP_CONTENTS_VIEW_ALERT_TITLE'), cms.getLang('EXP_CONTENTS_VIEW_ALERT_HELP'))
//                return false;
//            }
            //createField($('#view-canvas').find('.select-col'), model, title, type, disable);
        }

    }

})









