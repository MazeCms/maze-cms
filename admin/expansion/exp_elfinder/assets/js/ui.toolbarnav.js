$(document).ready(function () {

    var tools_name = $("#elfinder-tool-bar").attr("data-name");
    $('.elfinder-tools-elem, .elfinder-panel').draggable({
        helper: "clone",
        revert: "invalid",
        appendTo: "body"

    });

    $('.elfinder-panel-active')
            .droppable({
                accept: function (elem)
                {
                    return elem.hasClass("elfinder-tools-elem")
                },
                activeClass: "elfinder-panel-hover",
                drop: function (event, ui)
                {
                    var $self = $(this);
                    var elem = ui.draggable.clone()
                    elem.removeClass("elfinder-tools-elem")
                            .append($("<input>", {type: "hidden", name: tools_name + "[" + $self.attr("data-id-panel") + "][" + $(this).find("input").size() + "]"}).val(elem.attr("data-elem")))
                            .addClass("elfinder-tools-elem-active")
                            .appendTo(this)
                }
            })
            .sortable({
                update: function (e, ui)
                {
                    var id_panel = ui.item.parent(".elfinder-panel-active").attr("data-id-panel")
                    ui.item.parent(".elfinder-panel-active").find("input[type=hidden]").each(function (n) {
                        $(this).attr("name", tools_name + "[" + id_panel + "][" + n + "]")
                    })
                }
            });




    $("#elfinder-tool-bar").droppable({
        accept: function (elem)
        {
            return elem.hasClass("elfinder-panel")
        },
        drop: function (event, ui)
        {
            $(this).find(".placeholder").remove();

            $("<ul>").addClass("elfinder-panel-active").attr("data-id-panel", $(".elfinder-panel-active").size()).appendTo(this)
                    .droppable({
                        accept: function (elem)
                        {
                            return elem.hasClass("elfinder-tools-elem")
                        },
                        activeClass: "elfinder-panel-hover",
                        drop: function (event, ui)
                        {
                            var $self = $(this);
                            var elem = ui.draggable.clone()
                            elem.removeClass("elfinder-tools-elem")
                                    .append($("<input>", {type: "hidden", name: tools_name + "[" + $self.attr("data-id-panel") + "][" + $(this).find("input").size() + "]"}).val(elem.attr("data-elem")))
                                    .addClass("elfinder-tools-elem-active")
                                    .appendTo(this)
                        }
                    })
                    .sortable({
                        update: function (e, ui)
                        {
                            var id_panel = ui.item.parent(".elfinder-panel-active").attr("data-id-panel")
                            ui.item.parent(".elfinder-panel-active").find("input[type=hidden]").each(function (n) {
                                $(this).attr("name", tools_name + "[" + id_panel + "][" + n + "]")
                            })
                        }
                    });

        },
        activeClass: "elfinder-tool-bar-hover"
    })
            .sortable({
                update: function (e, ui)
                {
                    ui.item.parent("#elfinder-tool-bar").find(".elfinder-panel-active")
                            .each(function (n) {
                                $(this).attr("data-id-panel", n);
                                $(this).find("input[type=hidden]").each(function (nn) {
                                    $(this).attr("name", tools_name + "[" + n + "][" + nn + "]")
                                })
                            })

                }
            });

    $(".elfinder-tools, elfinder-panel").droppable({
        accept: function (elem)
        {
            return elem.hasClass("elfinder-panel-active") || elem.hasClass("elfinder-tools-elem-active");
        },
        drop: function (event, ui)
        {
            ui.draggable.remove();
        }
    })
    $(".elfinder-tools-elem, .elfinder-panel").tooltip({
        tooltipClass:'form-tooltip',
        show:{effect:'fadeIn'},
        hide:{effect:'fadeOut'},
        position:{my:'top bottom', at:'top'}
    });


})