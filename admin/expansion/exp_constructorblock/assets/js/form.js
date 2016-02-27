jQuery(document).ready(function () {


    var $exp = $('#block-expansion'),
            $bundle = $('#block-bundle'),
            $filter = $('#constructorblock-filter-field')

    if ($bundle.val() == '') {
        $('.bundle-block').hide()
    }

    $exp.bind("change", function (e) {
        var $self = $(this);
        $bundle.find("option").not(function () {
            return $(this).val() == ""
        }).remove();
        $bundle.trigger("chosen:updated").trigger("change");
        if ($self.val() == "")
            return false;

        $.get(cms.getURL(['/admin/constructorblock', {run: 'bundle', expansion: $self.val(), clear: 'ajax'}]), function (data) {
            $bundle.append(data);

            $bundle.trigger("chosen:updated");
        })


    });

    $bundle.bind("change", function (e) {
        var $self = $(this)

        if ($self.val() == "") {
            $('.bundle-block').hide()
        } else {
            $('.bundle-block').show();
        }

        $('#constructorblock-filter-field').find('.list-group > li').remove()
        $('#constructorblock-sort-field').find('.list-group  > li').remove()
        $('#constructorblock-field-field').find('.list-group  > li').remove()

    })

    $('#constructorblock-filter-field')
            .find('.action-btn')
            .mazePopover({
                title: 'Фильтр',
                trigger: 'click',
                width: $('#constructorblock-form .panel').width(),
                distance: 10,
                height: $(window).height() / 2,
                loadone: false,
                position: 'bottom',
                url: function () {
                    var not = $('#constructorblock-filter-field .list-group-item').map(function () {
                        var t = $.secureEvalJSON($(this).attr('data-param'));
                        return t.field;
                    }).get()

                    return cms.getURL(['/admin/constructorblock', {run: 'filters', expansion: $exp.val(), bundle: $bundle.val(), not: not, clear: 'ajax'}]);
                },
                filter: function (data) {

                    var html = '<div class="list-group" id="constructorblock-filter-condition">'
                    $.each(data.html, function (i, val) {
                        val.expansion = $exp.val();
                        val.bundle = $bundle.val();
                        var attr = $.toJSON(val);

                        html += '<a href="#" onclick="return addConditionFilter(this);" data-param=\'' + attr + '\' class="list-group-item">' + val.label + ' <small>[поле: ' + val.field + ']</small></a>'
                    })
                    html += '</div>';
                    return html;
                }
            })

    $('#constructorblock-sort-field')
            .find('.action-btn')
            .mazePopover({
                title: 'Сортировка',
                trigger: 'click',
                width: $('#constructorblock-form  .panel').width(),
                distance: 10,
                height: $(window).height() / 2,
                loadone: false,
                position: 'bottom',
                url: function () {
                    var not = $('#constructorblock-sort-field .list-group-item').map(function () {
                        var t = $.secureEvalJSON($(this).attr('data-param'));
                        return t.field;
                    }).get()

                    return cms.getURL(['/admin/constructorblock', {run: 'sorts', expansion: $exp.val(), bundle: $bundle.val(), not: not, clear: 'ajax'}]);
                },
                filter: function (data) {

                    var html = '<div class="list-group" id="constructorblock-sorts-condition">'
                    $.each(data.html, function (i, val) {
                        val.expansion = $exp.val();
                        val.bundle = $bundle.val();
                        var attr = $.toJSON(val);

                        html += '<a href="#" onclick="return addConditionSort(this);" data-param=\'' + attr + '\' class="list-group-item">' + val.label + ' <small>[поле: ' + val.field + ']</small></a>'
                    })
                    html += '</div>';
                    return html;
                }
            });

    $('#constructorblock-field-field')
            .find('.action-btn')
            .mazePopover({
                title: 'Поля',
                trigger: 'click',
                width: $('#constructorblock-form  .panel').width(),
                distance: 10,
                height: $(window).height() / 2,
                loadone: false,
                position: 'bottom',
                url: function () {
                    var not = $('#constructorblock-field-field .list-group-item').map(function () {
                        var t = $.secureEvalJSON($(this).attr('data-param'));
                        return t.field;
                    }).get()

                    return cms.getURL(['/admin/constructorblock', {run: 'fields', expansion: $exp.val(), bundle: $bundle.val(), not: not, clear: 'ajax'}]);
                },
                filter: function (data) {

                    var html = '<div class="list-group" id="constructorblock-fields-condition">'
                    $.each(data.html, function (i, val) {
                        val.expansion = $exp.val();
                        val.bundle = $bundle.val();
                        var attr = $.toJSON(val);

                        html += '<a href="#" onclick="return addFieldCondition(this);" data-param=\'' + attr + '\' class="list-group-item">' + val.label + ' <small>[поле: ' + val.field + ']</small></a>'
                    })
                    html += '</div>';
                    return html;
                }
            })
            sortViewField()
            
})

function sortViewField()
{
    $('#constructorblock-field-field .list-group').sortable({
        axis: 'y',
        handle: $('.list-group-item'),
        opacity: 0.5
    })
}

function addConditionFilter(elem) {
    var param = $.secureEvalJSON($(elem).attr('data-param'));

    $('#constructorblock-filter-field .action-btn').mazePopover('close')
    cms.loadDialog({
        url: cms.getURL(['/admin/constructorblock', {run: 'filter', filter: param.filter, params: param, clear: 'ajax'}]),
        height: 'auto',
        minWidth: 650,
        buttons: [
            {
                label: 'Добавить',
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {
                    var $self = this;
                    $('body').trigger('submiteditor')
                    cms.submitAjaxForm($(this).find('form').get(), false, function (form, data) {
                        delete param.table
                        param.queryFilter = data.html;
                        var $filter = $('#constructorblock-filter-field .panel-body .list-group');

                        var attr = $.toJSON(param);

                        var html = '<li  data-param=\'' + attr + '\' class="list-group-item">' + param.label + ' <small>[поле: ' + param.field + ']</small> '
                                + '<div class="btn-group edit-filter-btn" role="group"><button onclick="return editConditionFilter(this);" type="button" class="btn btn-default"><span aria-hidden="true" class="glyphicon glyphicon-pencil"></span></button> <button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button></div></li>'

                        $filter.append(html)

                        $self.mazeDialog('close');
                    })

                }
            },
            {
                label: "Закрыть",
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close')
                }
            }
        ],
    })


    return false;
}

function editConditionFilter(elem) {
    var param = $.secureEvalJSON($(elem).closest('li').attr('data-param'));

    cms.loadDialog({
        url: cms.getURL(['/admin/constructorblock', {run: 'filter', filter: param.filter, params: param.queryFilter, clear: 'ajax'}]),
        height: 'auto',
        minWidth: 650,
        buttons: [
            {
                label: 'Добавить',
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {
                    var $self = this;
                    cms.submitAjaxForm($(this).find('form').get(), false, function (form, data) {
                        param.queryFilter = data.html;
                        var attr = $.toJSON(param);

                        $(elem).closest('li').attr('data-param', attr)

                        $self.mazeDialog('close');
                    })

                }
            },
            {
                label: "Закрыть",
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close')
                }
            }
        ],
    })
    return false;
}



function deleteCondition(elem) {
    $(elem).closest('li').remove();
    return false;
}


function addConditionSort(elem) {
    var param = $.secureEvalJSON($(elem).attr('data-param'));

    $('#constructorblock-sort-field .action-btn').mazePopover('close')
    var $sort = $('#constructorblock-sort-field .panel-body .list-group');
    param.order = 'ASC'
    var attr = $.toJSON(param);
    var html = '<li  data-param=\'' + attr + '\' class="list-group-item">' + param.label + ' <small>[поле: ' + param.field + ']</small> '
            + '<div class="input-group edit-filter-btn"><select class="form-control" onchange="return sortConditionOrder(this);"><option value="ASC">ASC</option><option value="DESC">DESC</option></select> <span class="input-group-btn"><button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button></span></div></li>'

    $sort.append(html);

    return false;
}

function sortConditionOrder(elem) {
    var param = $.secureEvalJSON($(elem).closest('li').attr('data-param'));
    param.order = $(elem).val();
    var attr = $.toJSON(param);
    $(elem).closest('li').attr('data-param', attr)
    return false;
}

function addFieldCondition(elem) {
    var param = $.secureEvalJSON($(elem).attr('data-param'));

    $('#constructorblock-field-field .action-btn').mazePopover('close')
    cms.loadDialog({
        title: "Настройка вида поля",
        url: cms.getURL(['/admin/constructorblock', {run: 'field', field_exp_id: param.field_exp_id, expansion: param.expansion, params: param, clear: 'ajax'}]),
        minWidth: 650,
        buttons: [
            {
                label: 'Добавить',
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {
                    var $self = this;
                    cms.submitAjaxForm($(this).find('form').get(), false, function (form, data) {


                        var $field = $('#constructorblock-field-field .panel-body .list-group');
                        data.html.field = param
                        param = data.html;

                        var attr = $.toJSON(param);

                        var html = '<li  data-param=\'' + attr + '\' class="list-group-item">' + param.field.label + ' <small>[поле: ' + param.field.field + ']</small> '
                                + '<div class="btn-group edit-filter-btn" role="group"><button onclick="return editConditionField(this);" type="button" class="btn btn-default"><span aria-hidden="true" class="glyphicon glyphicon-pencil"></span></button> <button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button></div></li>'

                        $field.append(html)
                        sortViewField()
                        $self.mazeDialog('close');
                    })

                }
            },
            {
                label: "Закрыть",
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close')
                }
            }
        ],
    })


    return false;
}

function editConditionField(elem) {
    var param = $.secureEvalJSON($(elem).closest('li').attr('data-param'));

    cms.loadDialog({
        url: cms.getURL(['/admin/constructorblock', {run: 'field', field_exp_id: param.field_exp_id, expansion: param.expansion, params: param, clear: 'ajax'}]),
        height: 'auto',
        minWidth: 650,
        buttons: [
            {
                label: 'Добавить',
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e) {
                    var $self = this;
                    cms.submitAjaxForm($(this).find('form').get(), false, function (form, data) {
                        data.html.field = param.field
                        param = data.html;
                        var attr = $.toJSON(param);

                        $(elem).closest('li').attr('data-param', attr)

                        $self.mazeDialog('close');
                    })

                }
            },
            {
                label: "Закрыть",
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close')
                }
            }
        ],
    })
    return false;
}

function  saveFormBlock(id, action) {


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

        url.setVar('clear', 'ajax');

        $(this).attr('action', url.toString());

        $(this).preloader('start');

        var filter = $('#constructorblock-filter-field .list-group-item').map(function () {
            return $.secureEvalJSON($(this).attr('data-param'));
        }).get()
        var sort = $('#constructorblock-sort-field .list-group-item').map(function () {
            return $.secureEvalJSON($(this).attr('data-param'));
        }).get()

        var view = $('#constructorblock-field-field .list-group-item').map(function () {
            return $.secureEvalJSON($(this).attr('data-param'));
        }).get()

        
        var params = $.extend({FilterBlock: filter, SortBlock: sort, ViewBlock: view}, $(this).serializeObject());
        $.post(url.toString(), params, function (data, res, xhr) {
            $self.preloader('end');
            cms.redirect(xhr.getResponseHeader("X-Redirect"));
        }, 'json')


    }).one('error.mazeForm', function () {
        $(this).removeClass('maze-form-load');
        $(this).unbind('success.mazeForm');
        $(this).preloader('end')
    }).preloader('start')

}