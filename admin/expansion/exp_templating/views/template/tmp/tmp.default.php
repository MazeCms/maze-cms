<?php
use maze\base\JsExpression;
ui\assets\AssetFancybox::register();

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'template-grid'],
            'colModel' => [
                ["name" => "menu", "width" => 20, "title" => "Действия", 'visible' => $this->access->roles("templating", "EDIT_TMP")],
                ["name" => "title", "title" => Text::_("Заголовок"), "index" => "title",
                    "hidefild" => true, "width" => 150, "align" => "left", "sorttable" => true, "grouping" => true],
                ["name" => "description", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_DES"), "hidefild" => true, "width" => 200, "align" => "left"],
                ["name" => "front_name", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_FRONT"), "index" => "front_back",
                    "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "version", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_VER"), "hidefild" => true, "width" => 80, "align" => "center"],
                ["name" => "created", "title" => Text::_("EXP_TEMPLATING_TMP_TABLE_DATE"), "hidefild" => true, "width" => 150, "align" => "center"],
                ["name" => "author", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_AUTOR"), "hidefild" => true, "width" => 80, "align" => "center"],
                ["name" => "license", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_LIC"), "hidefild" => true, "width" => 80, "align" => "center"],
                ["name" => "email", "title" => "email", "hidefild" => true, "width" => 80, "align" => "center"],
                ["name" => "siteauthor", "title" => Text::_("EXP_TEMPLATING_STYLE_MODAL_SITE"), "hidefild" => true, "width" => 80, "align" => "center"]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_app[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'id_app' => '{++id_app++}']]), "title" => Text::_("EXP_TEMPLATING_TITLEMENU_EDIT")],
            ["type" => 'link', "spriteClass" => 'menu-icon-eye',
                "actions" => new JsExpression("function(target, e){var data = target.parents('tr').data('gridRow');  window.open((data.front_back == '0'? '/admin/':'/')+'?tmp_name='+data.name+'&wid_view=1');}"),
                "title" => Text::_("EXP_TEMPLATING_TITLEMENU_VIEW")],
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable" => false
]);

ui\grid\MazeGrid::end();
?>
<script>
jQuery().ready(function(){
    $("#template-grid").bind('afterGetContent.mazegrid', function(){
        $(".preview-tmp").fancybox();
    })
})
</script>		
