function setTree()
{
    $("#menu-tree").jstree({
        "core": {
            //open_parents:true,
            "strings": {
                "multiple_selection": "мульти выбор"
            }
        },
        "types": {
            "valid_children": ["root"],
            "types": {
                "root": {
                    "icon": {
                        "image": "/library/image/icons/sitemap-application-blue.png"
                    },
                    "valid_children": ["default", "home", "disable"],
                },
                "default": {
                    "valid_children": ["default", "home", "disable"],
                    "icon": {
                        "image": "/library/image/icons/blue-folder-horizontal.png"
                    }
                },
                "home": {
                    "valid_children": ["default", "disable"],
                    "icon": {
                        "image": "/library/image/icons/home.png"
                    }
                },
                "disable": {
                    "valid_children": ["default", "home"],
                    "icon": {
                        "image": "/library/image/custom/blue-folder-horizontal-lock.png"
                    }
                }

            }
        },
        "contextmenu": {
            items: function (obj) {
                var $link = obj.children('a')
                var menu = {};

                if (obj.attr('rel') == 'root')
                {
                    menu["edit"] = {
                        "separator_before": false,
                        "separator_after": false,
                        "label": settings.edit,
                        "icon": "/library/image/icons/document--pencil.png",
                        "action": function (obj) {
                            var url = "/admin/menu/?run=edit&id_group=" + obj.children('a').attr("data-idgroup");
                            cms.request(false, url);
                        }
                    }
                    return menu;
                }


                menu = {
                    "viewposition": {
                        "separator_before": false,
                        "icon": false,
                        "separator_after": false,
                        "label": settings.viewlink,
                        "icon"             	: "/library/image/icons/eye.png",
                                "action": function (obj) {
                                    var url = document.location.protocol + "//" + document.location.host;
                                    var alias = Array();

                                    alias.push(obj.children('a').attr('data-alias'));
                                    obj.parents('li').not("[rel=root]").each(function () {
                                        alias.push($(this).children('a').attr('data-alias'));
                                    })

                                    url += "/" + alias.reverse().join('/') + settings.suffix;
                                    window.open(url);
                                }
                    },
                    "edit": {
                        "separator_before": false,
                        "separator_after": false,
                        "label": settings.edit,
                        "icon": "/library/image/icons/document--pencil.png",
                        "action": function (obj) {
                            var url = "/admin/menu/groupmenu/?run=edit&id_menu=" + obj.children('a').attr("data-id");
                            cms.request(false, url)

                        }
                    },
                    "publick": {
                        "separator_before": true,
                        "separator_after": false,
                        "label": settings.enable,
                        "icon": "/library/image/icons/lock.png",
                        "action": function (obj) {
                            obj.attr("rel", "default");
                            var id = obj.children('a').attr("data-id")

                            cms.ajaxSend({
                                task: 'groupmenu.publish',
                                param: {cid: id, get_table: 1}
                            });
                        }
                    }
                    ,
                    "unpublick": {
                        "separator_before": false,
                        "separator_after": true,
                        "label": settings.disable,
                        "icon": "/library/image/icons/lock-unlock.png",
                        "action": function (obj) {
                            obj.attr("rel", "disable");
                            var id = obj.children('a').attr("data-id")

                            cms.ajaxSend({
                                task: 'groupmenu.unpublish',
                                param: {cid: id, get_table: 1},
                            });
                        }
                    }
                }


                return  menu;

            }
      },
        "plugins": settings.access_edit ? ["themes", "html_data", "cookies", "types", "crrm", "ui", "dnd", "contextmenu"] : ["themes", "html_data", "cookies", "types", "crrm", "ui"]

    })

            .bind('move_node.jstree', function (e, data) {

                var $elemParent = data.rslt.np.children('a');
                var $elem = data.rslt.o.children('a');


                var $param = {}
                var $order = {};
                var $order_group = {};


                $elem.each(function () {
                    var $this = $(this);
                    var id = $elemParent.attr('data-id')
                    var elemId = $this.attr('data-id')

                    if (elemId == 0) {
                        orderGroup();
                        return false;
                    }

                    var parentId = $this.attr('data-parent', id)
                    var groupId = $this.parents("li[rel='root']").children('a').attr('data-idgroup')
                    var $item = {id: elemId, parent: id, group: groupId};

                    Array.prototype.push.call($param, $item)

                    var $child = $this.next("ul").find("a");
                    if ($child.is("a"))
                    {
                        $child.each(function () {

                            var $this = $(this);
                            var elemId = $this.attr('data-id');
                            var $item = {id: elemId, group: groupId};
                            Array.prototype.push.call($param, $item)
                        })
                    }


                })

                data.rslt.np.children('ul').children('li').each(function (n) {
                    if ($elem.attr('data-id') == 0)
                        return false;
                    var $this = $(this).children('a');
                    var elemId = $this.attr('data-id');
                    var $item = {id: elemId, order: n + 1};
                    Array.prototype.push.call($order, $item)

                })

                function orderGroup() {
                    $("#menu-tree > ul > li").each(function (n) {
                        var $this = $(this).children('a');
                        var groupId = $this.attr('data-idgroup')
                        var $item = {group: groupId, order: n + 1};
                        Array.prototype.push.call($order_group, $item)

                    })
                }
                var sendParam = {}
                if (!$.isEmptyObject($order_group))
                {
                    delete $order_group['length']
                    sendParam["order_group"] = $order_group
                }
                if (!$.isEmptyObject($order))
                {
                    delete $order['length']
                    sendParam["order_item"] = $order
                }
                if (!$.isEmptyObject($param))
                {
                    delete $param['length']
                    sendParam["param_item"] = $param
                }


                if (!$.isEmptyObject(sendParam))
                {
                    cms.ajaxSend({
                        task: 'groupmenu.treemenu',
                        param: sendParam
                    });

                }



            })
}
function menu_tree_structure()
{
    cms.loadDialog(cms.getLang('EXP_MENU_PANELSITE_STRUCTUR'), '/admin/menu/groupmenu?run=getTreeForm',
            [
                {
                    label: cms.getLang('EXP_MENU_PANELSITE_EXPAND'),
                    class_btn: "maze-btn-success",
                    class_icon: "",
                    action: function (e) {
                        $("#menu-tree").jstree("open_all", false, 500)
                    }
                },
                {
                    label: cms.getLang('EXP_MENU_PANELSITE_COLLAPSE'),
                    class_btn: "maze-btn-warning",
                    class_icon: "",
                    action: function (e, obj) {
                        $("#menu-tree").jstree("close_all", false, 500)
                    }
                }
            ]
            )
}
