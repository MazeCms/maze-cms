function initTableTree(data)
{
    var $table = $("#maps-link-table");
    var html;
    var result = {};
    $table.find('tbody').html('');
    data = data || {};
    
    
    $.each(data, function (alias, val) {
        var parent = $.trim(val.loc) !== '/' && $.trim(val.loc).split('/').length > 1 ? $.trim(val.loc).split('/').slice(0, -1) : null;

        if (parent) {
            parent = parent.join('/');
            if (result[parent] == null)
                result[parent] = [];
            val.parent = parent;
            result[parent].push(val)
        } else {
            if (result['root'] == null)
                result['root'] = [];
            result['root'].push(val)
        }
    })

    function renderRow(dataRow) {

        $.each(dataRow, function (i, val) {
            var $tr = $('<tr>').attr('data-tt-id', val.loc);
            if (val.hasOwnProperty('parent')) {
                $tr.attr('data-tt-parent-id', val.parent);
            }
            if (val.enabled == 0) {
                $tr.addClass('danger')
            }
            $tr.attr('data-link', $.toJSON(val));
            $tr.append($('<td>').append($('<input>', {type: 'checkbox'})))
            $tr.append($('<td>').append(val.title))
            $tr.append($('<td>').addClass('edit-enabled text-center').append((val.enabled == 1 ? '<span aria-hidden="true" class="glyphicon glyphicon-ok"></span>' : '<span aria-hidden="true" class="glyphicon glyphicon-remove"></span>')))
            $tr.append($('<td>').append(val.loc))
            $tr.append($('<td>').addClass('edit-lastmod text-center').append(val.lastmod))
            $tr.append($('<td>').addClass('edit-changefreq text-center').append(val.changefreq))
            $tr.append($('<td>').addClass('edit-priority text-center').append(val.priority))
            $table.find('tbody').append($tr);
            if (result.hasOwnProperty(val.loc)) {
                
                renderRow(result[val.loc])
            }
        })
    }
    if (result.hasOwnProperty('root')) {
        renderRow(result['root'])
    }

    $table.find('input').iCheck({
        checkboxClass: 'icheckbox_flat-grey',
        radioClass: 'iradio_flat-grey'
    }).on('ifChecked ifUnchecked', function () {
        if ($(this).closest('thead').is('thead'))
            return false;
        $(this).closest('tr').toggleClass('selected')
    });
    $table.find('thead input').on('ifChecked ifUnchecked', function (e) {
        $('#maps-link-table tbody input').iCheck((e.type == 'ifChecked' ? 'check' : 'uncheck'));
    });


    if ($table.is('.treetable')) {
        $table.treetable('destroy')
    }

    $table.treetable({
        expandable: true,
        column: 1
    });

    $('#admin-tabs-sitemaps').click(function () {
        $table.find('.active-edit').each(function () {
            var value;
            if ($(this).find('input').is('input')) {
                value = $(this).find('input').val()
            } else if ($(this).find('select').is('select')) {
                value = $(this).find('select').val()
            }
            $(this).html(value).removeClass('active-edit');
        })
    })



    $('.edit-lastmod').click(function () {
        if ($(this).is('.active-edit'))
            return false;
        $(this).addClass('active-edit')
        var $input = $('<input>', {'type': 'text', 'class': 'form-control', value: $.trim($(this).text())})
        $(this).html($input);
        $input.datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: "HH:mm:ss",
            stepHour: 1,
            stepMinute: 1,
            stepSecond: 1
        });
        return false;
    })

    $('.edit-changefreq').click(function () {
        if ($(this).is('.active-edit'))
            return false;
        $(this).addClass('active-edit');
        $(this).html(createSelectChangefreq($(this).text()));
        return false;
    })

    $('.edit-priority').click(function () {
        if ($(this).is('.active-edit'))
            return false;
        $(this).addClass('active-edit');
        $(this).html(createSelectPriority($(this).text()));
        return false;
    })

    $('.edit-enabled').click(function () {

        $(this).find('.glyphicon').toggleClass('glyphicon-ok glyphicon-remove')
        if ($(this).find('.glyphicon-ok').is('.glyphicon-ok')) {
            $(this).closest('tr').removeClass('danger')
        } else {
            $(this).closest('tr').addClass('danger')
        }
    })
}

function createSelect(value, options)
{
    var html = '<select class="form-control">';
    $.each(options, function (key, val) {
        var selected = $.trim(value) == key ? 'selected' : '';
        html += '<option ' + selected + ' value="' + key + '">' + val + '</option>';
    })
    html += '</select>';
    return $(html);
}

function createSelectChangefreq(val) {
    return createSelect(val, {
        always: 'always',
        hourly: 'hourly',
        daily: 'daily',
        weekly: 'weekly',
        monthly: 'monthly',
        yearly: 'yearly',
        never: 'never'
    });
}
function createSelectPriority(val) {
    return createSelect(val, {
        '0,0': '0.0',
        '0.1': '0.1',
        '0.2': '0.3',
        '0.4': '0.4',
        '0.5': '0.5',
        '0.6': '0.6',
        '0.7': '0.7',
        '0.8': '0.8',
        '0.9': '0.9',
        '1.0': '1.0'
    });
}

function enableTreeLink() {
    $("#maps-link-table").find('tbody .selected').each(function () {
        $(this).find('.glyphicon').removeClass('glyphicon-remove').addClass('glyphicon-ok')
        $(this).removeClass('danger')
    })
    return false;
}

function disableTreeLink() {
    $("#maps-link-table").find('tbody .selected').each(function () {
        $(this).find('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-remove')
        $(this).addClass('danger')
    })
    return false;
}

function packHendler() {
    $("#edit-tree-table a").eq(0).mazePopover({
        trigger: 'click',
        height: 290,
        distance:10,
        classBlock: 'default padding-box',
        content: function () {
            var $form = $('<div>').addClass('edit-tree-table-form')
            var $input = $('<input>', {'type': 'text', 'class': 'form-control'})
            $form.append($("<div>").addClass('form-group').append($('<label>').text('Дата изменения')).append($input));
            $form.append($("<div>").addClass('form-group').append($('<label>').text('Частота изменения')).append(createSelectChangefreq()));
            $form.append($("<div>").addClass('form-group').append($('<label>').text('Приоритетность')).append(createSelectPriority()));
            $input.datetimepicker({
                dateFormat: "yy-mm-dd",
                timeFormat: "HH:mm:ss",
                stepHour: 1,
                stepMinute: 1,
                stepSecond: 1
            });
            return $form;
        },
        buttons: [
            {
                label: 'Выполнить',
                class_btn: "btn btn-primary btn-block",
                class_icon: "",
                action: function (e) {
                    var $self = this;
                    var date = $('.edit-tree-table-form').find('input').val()
                    var changefreq = $('.edit-tree-table-form').find('select').eq(0).val()
                    var priority = $('.edit-tree-table-form').find('select').eq(1).val()
                    $("#maps-link-table").find('tbody .selected').each(function () {
                        if(date) $(this).find('.edit-lastmod').html(date)
                        if(changefreq) $(this).find('.edit-changefreq').html(changefreq)
                        if(priority) $(this).find('.edit-priority').html(priority)
                    })
                }
            },
        ]
    })
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


        var linkParam = $("#maps-link-table").find('tbody tr').map(function () {
            var selfParam = $.secureEvalJSON($(this).attr('data-link'));
            selfParam.enabled = $(this).find('.edit-enabled .glyphicon').is('.glyphicon-ok') ? 1 : 0;
            selfParam.lastmod = $.trim($(this).find('.edit-lastmod').text());
            selfParam.changefreq = $.trim($(this).find('.edit-changefreq').text());
            selfParam.priority = $.trim($(this).find('.edit-priority').text());
            return selfParam;
        }).get()



        var params = $.extend(true, {SitemapLink: linkParam}, $(this).serializeObject(), {Sitemap:{params:$('#sitemap-form-import').serializeObject()}});

        delete params.Sitemap.params.csrf;
        
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
function  actionImportLinks(id, action) {

    var $form = $(id);
    var $tabs = $form.closest('.wrapp-form-tabs');
    var url = $form.attr('action');
    $tabs.preloader('end')
    $tabs.find('.maze-form-preload').remove();
    url = cms.URI(url);
    if (typeof action == 'object') {
        $.each(action, function (name, val) {
            url.setVar(name, val);
        })
    }
    url.setVar('clear', 'ajax');
    $form.attr('action', url.toString());
    $tabs.preloader('start');

    $.post(url.toString(), $form.serialize(), function (data, res, xhr) {
        $tabs.preloader('end');
        $tabs.removeClass('maze-form-load');
        initTableTree(data.html);
    }, 'json');

    return false;
}