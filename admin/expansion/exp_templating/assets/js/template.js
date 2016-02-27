var appTemplate = {
    /**
     * Конфигурация дерева файлов шаблона
     * @type object
     */

    settingsTree: {
        core: {
            check_callback: true,
            multiple: true,
            data: function (obj, callback) {
                var self = this;
                $.get(cms.getURL([{run: 'tree', clear: 'ajax'}]), function (data) {
                    callback.call(self, data.html);
                }, 'json');
            },
            strings: {
                "Loading ...": 'Загрузка...'
            }
        },
        types: {
            "#": {
                "valid_children": ["root"]
            },
            "root": {
                "valid_children": ["default", "php", "folder", "img", "media", "office", "zip", "js", "css", "lang", "pdf"],
                "icon": "/library/image/icons/server.png"

            },
            "folder": {
                "valid_children": ["default", "php", "folder", "img", "media", "office", "zip", "js", "css", "lang", "pdf"],
                "icon": "/library/image/icons/blue-folder-horizontal.png"
            },
            "php": {
                "valid_children": [],
                "icon": "/library/image/icons/document-php.png"
            },
            "img": {
                "valid_children": [],
                "icon": "/library/image/icons/document-image.png"
            },
            "media": {
                "valid_children": [],
                "icon": "/library/image/icons/document-music.png"
            },
            "zip": {
                "valid_children": [],
                "icon": "/library/image/icons/document-zipper.png"
            },
            "office": {
                "valid_children": [],
                "icon": "/library/image/icons/document-office.png"
            },
            "default": {
                "valid_children": [],
                "icon": "/library/image/icons/document.png"
            },
            "js": {
                "valid_children": [],
                "icon": "/library/image/icons/document-code.png"
            },
            "css": {
                "valid_children": [],
                "icon": "/library/image/icons/document-globe.png"
            },
            "lang": {
                "valid_children": [],
                "icon": "/library/image/icons/language-document.png"
            },
            "pdf": {
                "valid_children": [],
                "icon": "/library/image/icons/document-pdf.png"
            }

        },
        contextmenu: {
            items: function (obj) {
                var menu = {};
                var $tree = $('#tmp-tree');
                if ($.inArray(obj.type, ['php', 'lang', 'css', 'js', 'txt']) !== -1) {
                    menu['edit'] = {
                        icon: '/library/image/icons/pencil.png',
                        label: cms.getLang('EXP_TEMPLATING_TITLEMENU_EDIT'),
                        action: function (obj) {                            
                            appTemplate.addFileEdit(appTemplate.getPath(obj.reference), obj.reference.text())
                        }
                    }
                }

                if ($.inArray(obj.type, ['root', 'folder']) !== -1) {
                    menu['paste'] = {
                        icon: '/library/image/icons/clipboard-empty.png',
                        label: cms.getLang('EXP_TEMPLATING_TMP_FORM_JS_TREE_PASTE'),
                        action: function (obj) {
                            $tree.jstree(true).paste(obj.reference);
                        }
                    }
                }
                
                if(obj.type == 'img'){
                  menu['view'] = {
                        icon: '/library/image/icons/eye.png',
                        label: cms.getLang('EXP_TEMPLATING_TMP_FORM_JS_TREE_VIEW'),
                        action: function (obj) {                            
                            $.fancybox( appTemplate.src +'/'+appTemplate.getPath(obj.reference));
                        }
                    }  
                }
                if (obj.type !== 'root') {
                    menu['copy'] = {
                        icon: '/library/image/icons/document-copy.png',
                        label: cms.getLang('EXP_TEMPLATING_TITLEMENU_COPY'),
                        action: function (obj) {
                            $tree.jstree(true).copy(obj.reference);
                        }
                    }
                    menu['cut'] = {
                        icon: '/library/image/icons/scissors-blue.png',
                        label: cms.getLang('EXP_TEMPLATING_TITLEMENU_CUT'),
                        action: function (obj) {
                            $tree.jstree(true).cut(obj.reference);
                        }
                    }
                    menu['rename'] = {
                        icon: '/library/image/icons/application-rename.png',
                        label: cms.getLang('EXP_TEMPLATING_TMP_FORM_JS_TREE_RENAME'),
                        action: function (obj) {
                            $tree.jstree(true).edit(obj.reference);
                        }
                    }
                    menu['delete'] = {
                        icon: '/library/image/custom/trash.png',
                        label: cms.getLang('EXP_TEMPLATING_TMP_FORM_JS_TREE_DELETE'),
                        action: function (obj) {
                            cms.alertPromt({
                                close: cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                                text: cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT'),
                                title: cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE'),
                                ok: cms.getLang('LIB_USERINTERFACE_TOOLBAR_PACK_SEND'),
                                h: 'auto',
                                w: 300,
                                callback: function () {
                                    $tree.jstree(true).delete_node(obj.reference);
                                }
                            })

                        }
                    }
                }

                return menu;
            }
        },
        plugins: ["contextmenu", "dnd", "wholerow", "state", "types"]
    },
    getPath: function (id) {
        var instance = $('#tmp-tree').jstree(true),
                node = instance.get_node(id);

        var path = $.map(node.parents, function (val) {
            var pnode = instance.get_node(val).a_attr;
            if (typeof pnode == 'object')
                return pnode.href == '/' ? null : pnode.href;

        })
        path.reverse().push(node.a_attr.href);
        return path.join('/');
    },
    rename: function () {
        var ref = this.selectNode();
        if (ref) {
            var sel = ref.get_selected();
            sel = sel[0];
            ref.edit(sel);
        }

    },
    selectNode: function () {
        var ref = $('#tmp-tree').jstree(true),
                sel = ref.get_selected();
        if (!sel.length) {
            $('#tool-bar-admin').toolBarAdmin('setMessage', cms.getLang('EXP_TEMPLATING_TMP_FORM_MESS_NO_SELECT'), 'error');
            return false;
        }
        return ref;
    },
    createFile: function (type) {
        var ref = this.selectNode();

        if (ref) {
            var sel = ref.get_selected();
            sel = sel[0];
            sel = ref.create_node(sel, {"type": type || "php"});
            if (sel) {
                ref.edit(sel);
            }
        }

    },
    deleteNode: function () {
        var ref = this.selectNode();

        if (ref) {
            var sel = ref.get_selected();
            cms.alertPromt({
                close: cms.getLang('LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON'),
                text: cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT'),
                title: cms.getLang('LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE'),
                ok: cms.getLang('LIB_USERINTERFACE_TOOLBAR_PACK_SEND'),
                h: 'auto',
                w: 300,
                callback: function () {
                    ref.delete_node(sel);
                }
            })
        }


    },
    cutNode: function () {
        var ref = this.selectNode();
        if (ref) {
            var sel = ref.get_selected();
            if (sel) {
                ref.cut(sel);
            }
        }
    },
    copyNode: function () {
        var ref = this.selectNode();
        if (ref) {
            var sel = ref.get_selected();
            if (sel) {
                ref.copy(sel);
            }
        }
    },
    pasteNode: function () {
        var ref = this.selectNode();
        if (ref) {
            var sel = ref.get_selected();
            sel = sel[0];
            if (sel) {
                ref.paste(sel);
            }
        }
    },
    closeTable: function (id) {
        $("#code-editor").find('li[aria-controls=' + id + ']').remove();
        $("#" + id).remove();
        $("#code-editor").tabs("refresh");
    },
    saveFile: function (callback) {
        var id = $("#code-editor").find('.ui-tabs-active').attr('aria-controls');
        if (!$('#' + id).find('input[name=path]').is('input') || $('#' + id).find('input[name=path]').val() == '') {
            $('#tool-bar-admin').toolBarAdmin('setMessage', cms.getLang('EXP_TEMPLATING_TMP_FORM_MESS_NOFILE'), 'error');
            return false;
        }
        $('#wrapp-tabs').preloader('start');
        $.post(cms.getURL([{run: 'editfile', path: $('#' + id).find('input[name=path]').val(), clear: 'ajax'}]), {
            text: $('#' + id).find('textarea[name=code]').data('codemirror').getValue(),
            csrf:$('meta[name=csrf-token]').attr('content')
        }, function (data) {
            $('#wrapp-tabs').preloader('end');
            if (data.hasOwnProperty('ok') && data.ok) {
                if ($.isFunction(callback)) {
                    callback.call($("#code-editor"), id);
                }
            }
        }, 'json');

    },
    /**
     * Добавить файл для редактирования
     * 
     * @param string path - путь к файлу
     * @param string label - название файла
     * @returns null
     */
    addFileEdit: function (path, label) {

        var tabTemplate = '<li><a href="#{href}">{label}</a><div class="close-tabs"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></div></li>',
                $tabs = $('#code-editor'),
                tabCounter = $tabs.find(".ui-tabs-nav > li").size() + 1,
                id = "tabs-" + tabCounter,
                li = $(tabTemplate.replace(/\{href\}/g, id).replace(/\{label\}/g, label)),
                tabContentHtml = '<div class="form-group input-group"><span class="input-group-addon" >' + cms.getLang('EXP_TEMPLATING_TMP_PACH_FILE') + '</span><input type="text" value="' + path + '" name="path" readonly="readonly" class="form-control"></div>';
        tabContentHtml += '<div class="form-group"><textarea name="code"></textarea></div>';
        $tabs.find(".ui-tabs-nav").append(li);
        $tabs.append("<div id='" + id + "'>" + tabContentHtml + "</div>");
        $tabs.tabs("refresh");
        $tabs.find('a[href=#' + id + ']').trigger('click');
        var codemirror = CodeMirror.fromTextArea($('#' + id).find('textarea[name=code]').get(0), {
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
        $('#wrapp-tabs').preloader('start');
        $('#' + id).find('textarea[name=code]').data('codemirror', codemirror);
        $.get(cms.getURL([{run: 'editfile', path: path, clear: 'ajax'}]), function (data) {
            $('#' + id).find('textarea[name=code]').val(data.html);
            codemirror.setValue(data.html);
            $('#wrapp-tabs').preloader('end')
        }, 'json')

    }
}


jQuery(document).ready(function () {

    $("#tmp-tree").jstree(appTemplate.settingsTree)
            .bind('rename_node.jstree', function (e, obj) {

                var query = {name: obj.text, run: 'rename', clear: 'ajax'};

                if (obj.node.a_attr.href == '#') {
                    query.run = 'create';
                    query.path = appTemplate.getPath(obj.node.parent);
                    query.type = obj.node.type == 'folder' ? 'folder' : 'file';
                }
                else {
                    if (obj.text == obj.old)
                        return false;
                    query.path = appTemplate.getPath(obj.node);
                }
         
                $.get(cms.getURL([query]), function (data) {          
                    obj.node.a_attr.href = data.html.name;
                    obj.node.text = data.html.name;
                    obj.instance.set_type(obj.node, data.html.type) ;
                    obj.instance.redraw_node(obj.node);
                   
                }, 'json');
            })
            .bind('create_node.jstree', function (e, obj) {
                if (obj.node.type !== 'folder') {
                    var type = obj.node.type == 'lang' ? 'ini' : obj.node.type;
                    obj.instance.set_text(obj.node, 'newFile.' + type);
                }

            })
            .bind('move_node.jstree copy_node.jstree', function (e, obj) {
                var query = {
                    run:'move', 
                    path: appTemplate.getPath(obj.old_parent)+'/'+obj.node.a_attr.href, 
                    target: appTemplate.getPath(obj.parent), 
                    clear:'ajax'
                };
                if(e.type ==  'copy_node'){
                   query.copy = 1; 
                }
                
                $.get(cms.getURL([query]), function (data) {   
                    obj.node.a_attr.href = data.html.name;
                    obj.node.text = data.html.name;
                    obj.instance.set_type(obj.node, data.html.type) ;
                    obj.instance.redraw_node(obj.node);
                }, 'json');
            })
            .bind('delete_node.jstree', function (e, obj) {
                var query = {
                    run:'delete', 
                    path: appTemplate.getPath(obj.node), 
                    clear:'ajax'
                };
                $.get(cms.getURL([query]), function (data) {}, 'json');
            })
            

    $("#code-editor").tabs().delegate('.close-tabs', 'click', function () {
        appTemplate.closeTable($(this).closest('li').attr("aria-controls"));
    })


    $("#tmp-tree").on("ready.jstree after_open.jstree", function (e, data) {
        
        $("#tmp-tree [title]").each(function () {
            if ($(this).attr('title') == '')
                return true;
            $(this).tooltip({
                tooltipClass: 'dark-tooltip-bar',
                show: {effect: 'fade'},
                hide: {effect: 'fade'},
                position: {
                    my: 'left top',
                    at: 'right top'
                }
            })
        })
    })

    /* end ready */
})