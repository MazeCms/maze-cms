
function file_edit_template(type, path, name, id_app)
{

    var editor;
    var id = "color-code-" + Math.round(Math.random() * 100);

    var optionDialog = {
        title: cms.getLang("LIB_USERINTERFACE_TOOLBAR_EDIT_BUTTON") + " - " + name,
        position: 'top',
        resize: true,
        draggable: true,
        minWidth: 300,
        miHeight: 100,
        maxHeight: $(window).height(),
        width: 800,
        zIndex: 100000,
        dragstart: function (obj) {
            $(this).animate({opacity: '0.6'}, 200);
        },
        dragstop: function (e, ui) {
            $(this).animate({opacity: '1'}, 200);
        },
        resizestart: function (e, ui) {
            $(this).css({border: "3px dashed #737A87"});
            $(this).animate({opacity: '0.8'}, 200);
        },
        resizestop: function (e, ui) {
            $(this).css({border: "none"});
            $(this).animate({opacity: '1'}, 200);
        },
        buttons: [
            {
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON"),
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e, obj) {
                    var $elem = $(e.target)
                    $.post(cms.getURL(['/admin/templating/template/', {run: 'editfile', clear: 'ajax', id_app: id_app, path: path}]), {
                        text: editor.getValue(),
                        csrf: $('meta[name=csrf-token]').attr('content')
                    }, function (data) {
                        $elem.preloaderBtn("close");
                        if (data.hasOwnProperty('ok') && data.ok) {

                        }
                    }, 'json');

                    $elem.preloaderBtn("start");

                }
            },
            {
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON"),
                class_btn: "maze-btn-success",
                class_icon: "",
                action: function (e, obj) {
                    var $elem = $(e.target)
                    var dialog = $(this);
                    $.post(cms.getURL(['/admin/templating/template/', {run: 'editfile', clear: 'ajax', id_app: id_app, path: path}]), {
                        text: editor.getValue(),
                        csrf: $('meta[name=csrf-token]').attr('content')
                    }, function (data) {
                        $elem.preloaderBtn("close");
                        dialog.mazeDialog('close');
                    }, 'json');
                    $elem.preloaderBtn("start");

                }
            },
            {
                label: cms.getLang("LIB_USERINTERFACE_TOOLBAR_CLOSE"),
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    $(this).mazeDialog('close');
                }
            }

        ]

    }


    if (type == "css" || type == "js" || type == "php" || type == "lang")
    {

        $.get(cms.getURL(['/admin/templating/template', {'run': 'editfile', clear: 'ajax', id_app: id_app, path: path}]), function (data) {
            if (typeof data.message == 'object')
            {
                $("#tool-bar-site").toolBarSite('setMessage', data.message.text, data.message.type);
                return;
            }
            var textArea = $("<textarea>").attr('id', id)
                    .mazeDialog(optionDialog)
                    .css({resize: 'none', width: 790})
                    .one('closeDialog', function (event, ui) {
                        $(this).mazeDialog('destroy');
                    })
                    .bind('resizable fulldialog minidialog', function (e, obj) {
                        var $self = $(this);
                        $self.next('.CodeMirror').css({height: $self.mazeDialog('dialog').height() - 89});
                        editor.refresh();
                    })

            textArea.one('openDialog', function(){
                 editor = CodeMirror.fromTextArea(document.getElementById(id), {
                mode: 'application/x-httpd-php',
                lineNumbers: true,
                viewportMargin: Infinity,
                lineWrapping: true,
                foldGutter: true,
                extraKeys: {"Ctrl-Q": function (cm) {
                        cm.foldCode(cm.getCursor());
                    }},
                gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                tabSize: 4,
                indentUnit: 4,
                indentWithTabs: true
            })
             editor.setValue(data.html);
            })
           
        }, 'json')


    }
    return false;
}
