
var adminBar = {
    desktopForm: function (elem) {
        function addColonum(value, $elem) {

            var $form = $elem.closest('form');
            var $colonum = $form.find(".colonum-width");
            var sizecolonum = $colonum.size();
            var size, count;
            if (sizecolonum > value)
            {
                size = sizecolonum - value;
                count = sizecolonum - 1;
                for (var i = 0; i < size; i++)
                {
                    $($colonum[count--]).remove();
                }
            }
            else
            {
                size = value - sizecolonum;
                count = sizecolonum + 1;
                for (var i = 0; i < size; i++)
                {
                    var $newColonum = $($colonum[0]).clone();
                    var $title = $newColonum.find(".control-label");
                    $newColonum.find("input[type=text]").val('');
                    $title.text($title.text().replace(/(.+-).+/g, "$1 " + count++));

                    $form.find(".size-colonum-group").append($newColonum);
                }
            }
        }
        cms.loadDialog({
            url: $(elem).attr('href'),
            title:cms.getLang('EXP_ADMIN_TITLE'),
            buttons: [
                {
                    label: cms.getLang('EXP_ADMIN_SAVE'),
                    class_btn: "maze-btn-success",
                    class_icon: "",
                    action: function (e) {
                        var $self = this;
                        if ($self.find('form').is('form')) {
                            cms.btnFormAction($self.find('form').get());
                        }
                    },
                },
                {
                    label: cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                    class_btn: "maze-btn-warning",
                    class_icon: "",
                    action: function (e, obj) {
                        this.mazeDialog('close')
                    }
                }
            ],
            callback: function () {
                $(".colonum-desktop").slider({
                    min: 1,
                    max: 8,
                    value: $("#desktop-colonum").val(),
                    slide: function (e, ui) {
                        $(this).closest('.form-group').find("#desktop-colonum").val(ui.value);
                        addColonum(ui.value, $(this))
                    }
                });

                $(".size-colonum").live("change keydown keyup keypress focusin focusout", function () {
                    var valueW = $(this).val().match(/[^\d]+/g);
                    var result = $(this).val();
                    if (valueW !== null)
                    {
                        $.map(valueW, function (val) {
                            result = result.replace((new RegExp(val)), "");
                        })
                    }
                    else
                    {
                        if (result > 100)
                            result = 100;
                    }
                    $(this).val(result)

                })
            }
        });
        return false;
    },
    editGadget: function (elem) {
        cms.loadDialog({
            url: $(elem).attr('href'),
            title:cms.getLang('EXP_ADMIN_FORM_SETTINGSGADGET_ALERT'),
            buttons: [
                {
                    label: cms.getLang('EXP_ADMIN_SAVE'),
                    class_btn: "maze-btn-success",
                    class_icon: "",
                    action: function (e) {
                        var $self = this;
                        if ($self.find('form').is('form')) {
                            cms.btnFormAction($self.find('form').get());
                        }
                    },
                },
                {
                    label: cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                    class_btn: "maze-btn-warning",
                    class_icon: "",
                    action: function (e, obj) {
                        this.mazeDialog('close')
                    }
                }
            ]
        });
        return false;
    },
    deleteGadget: function (elem, parent) {
        cms.alertPromt({
            title: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE"),
            text: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT"),
            close: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON"),
            ok: cms.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
            h: 'auto',
            callback: function () {
                $.get($(elem).attr('href') + '&clear=ajax', function () {
                    $(elem).closest(parent).fadeOut(500, function (data) {
                        if (data.message.hasOwnProperty('type')) {
                            if (data.message.type == 'success') {
                                $(this).remove();
                            }
                        }

                    })
                }, 'json');
            }
        })
        return false;
    },
    sortGadgets: function (selector) {

        $(selector).sortable({
            cursor: 'move',
            items: '.gadget-box',
            handle: '.panel-heading',
            connectWith: '.colonum-gadget',
            placeholder: 'empty-gadget',
            sort: function (e, ui) {
                ui.placeholder.css({height: ui.helper.height() + 10})
            },
            beforeStop: function (e, ui) {
                ui.placeholder.css('height', '');
            },
            update: function (e, ui) {

                var result = Array();
                var $self = ui.item;
                var colonum = $self.parent('.colonum-gadget').index();

                $self.parent('.colonum-gadget').find('.gadget-box').each(function (n) {
                    result.push({id_gad: $(this).attr('data-id_gad'), colonum: colonum, ordering: n + 1})
                })
                $.get(cms.getURL(['/admin/admin', {run:'sortGadgets', sort:result, clear:'ajax'}]))

            }

        })

    },
    deleteDesktop:function(elem){
        cms.alertPromt({
            title: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE"),
            text: cms.getLang("LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT"),
            close: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON"),
            ok: cms.getLang("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
            h: 'auto',
            callback: function () {
               cms.redirect($(elem).attr('href'))
            }
        })
        return false;
    }
};


