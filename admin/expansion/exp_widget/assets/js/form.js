jQuery(document).ready(function () {

    function sortWidget()
    {
        $('.widget-elements-sort').sortable({
            axis: 'y',
            handle: $('.widget-elements-sort .glyphicon-move'),
            opacity: 0.5,
            update:function(e, ui){
                updateIndexPosition();
            }

        })
    }
    
    function updateIndexPosition(){
        $('.widget-elements-sort > li').each(function(i){
            $(this).find('input').each(function(){
                var name = $(this).attr('name');
                $(this).attr('name', name.replace(/(\[\d+\])/, '[' + i + ']'));
            })
            
        })
    }

    sortWidget();

    var $title = $('#formwidget-title'),
            $position = $('#formwidget-position'),
            $tmp = $('#formwidget-id_tmp');

    $title.bind("change keydown keypress focusin focusout", function (e) {
        $('.widget-elements-sort .active').find('.title-widget-sort').text($title.val());
    });

    $tmp.bind("change", function (e) {
        var $self = $(this);
        $position.find("option").not(function () {
            return $(this).val() == ""
        }).remove();
        $position.trigger("chosen:updated").trigger("change");
        if ($self.val() == "")
            return false;

        $.get(cms.getURL(['/admin/widget',{run: 'position', id_tmp: $self.val(), clear: 'ajax'}]), function (data) {
            $position.append(data);

            $position.trigger("chosen:updated");
        })


    });

    $position.bind("change", function (e) {
        var $self = $(this),
                $sort = $('.widget-elements-sort');

        $sort.find("li").not(function () {
            return $(this).hasClass('active')
        }).remove();


        if ($self.val() == "")
            return false;

        $.get(cms.getURL(['/admin/widget',{run: 'widgetPosition', id_tmp: $tmp.val(), position: $self.val(), clear: 'ajax'}]), function (data) {

            $.each(data.html, function (i, obj) {
                var $row = $sort.find('.active').clone();
                if (obj.id_wid == $row.attr('data-field-id_wid'))
                    return true;
                $row.attr('data-field-id_wid', obj.id_wid).removeClass('active').find('.title-widget-sort').text(obj.title);
                $row.find('input[type=hidden]').val(obj.id_wid)
                $sort.append($row);
                sortWidget();
                updateIndexPosition();
            })

        }, 'json')

    })
    function sortConditionUrl()
    {
        $('.widget-condition-url-block').sortable({
            axis: 'y',
            items: '> .widget-condition-url',
            handle: '.widget-condition-url-sort',
            opacity: 0.5,
            update:function(e, ui){
                updateIndex();
            }

        })
    }
   
    sortConditionUrl();
    
    function updateIndex() {
        $('#widget-form').find('.widget-condition-url').each(function (i) {
            $(this).find('input, select').each(function () {
                var name = $(this).attr('name');

                $(this).attr('name', name.replace(/(\[\d+\])/, '[' + i + ']'));
            })
        })
    }
    $('#widget-form-btn-add-url').click(function () {
        var params = $('#widget-form').find('.widget-condition-url').eq(0).clone();
        params.find('input').val('');
        params.find('.widget-condition-url-param, .widget-condition-url-equally').removeAttr('style');

        params.find('.hide').removeClass('hide');

        $('#widget-form').find('.widget-condition-url').eq(0).parent().append(params);
        sortConditionUrl();
        updateIndex()
    });

    $('.widget-condition-url-method').live('click', function () {
        var $parent = $(this).closest('.widget-condition-url'),
                $self = $(this),
                method = 'show';

        if ($self.val() == 'url') {
            method = 'hide'
        }
        else{
            $parent.find('.hide').removeClass('hide')
        }
        $parent.find('.widget-condition-url-param')[method]();
        $parent.find('.widget-condition-url-equally')[method]();

    })

    $('.widget-condition-url-delete').live('click', function () {

        if ($('#widget-form').find('.widget-condition-url').size() == 1) {
            var $parent = $('#widget-form').find('.widget-condition-url').eq(0)
            $parent.find('input').val('');
            $parent.find('.widget-condition-url-method').val('get');
            $parent.find('.widget-condition-url-param, .widget-condition-url-equally').removeAttr('style');
            $parent.find('.hide').removeClass('hide');
            return false;
        }
        $(this).closest('.widget-condition-url').remove();
    })

})




