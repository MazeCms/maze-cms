<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'menugroup-grid'],
            'model' => 'maze\table\MenuGroup',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("menu", "EDIT_MENU")],
                ["name" => "ordering", "index" => "ordering", "align" => "center", "width" => 20,
                    "sorttable" => true, "help" => Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_DES"), 'visible' => $this->access->roles("menu", "EDIT_MENU")],
                ["name" => "name", "title" => Text::_("EXP_MENU_TABLE_NAME_TITLE"),
                    "help" => Text::_("EXP_MENU_TABLE_NAME_DES"), "index" => "name", "hidefild" => true,
                    "width" => 200, "align" => "left", "sorttable" => true, "grouping" => true],
                ["name" => "items", "title" => Text::_("EXP_MENU_TABLE_ITEMS_TITLE"), "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => false],
                ["name" => "code", "title" => Text::_("EXP_MENU_GROUP_CODE"), "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => false],
                ["name" => "id_group", "title" => "ID", "index" => "id_group",
                    "help" => Text::_("EXP_MENU_TABLE_ID_DES"), "hidefild" => true, "width" => 100, "align" => "center", "sorttable" => true, "grouping" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_group[]"
));

if ($this->access->roles("menu", "EDIT_MENU")) {
    $grid->setPlugin("contextmenu", [
        "ordering" => [
            "items" => ".menu-icon-handle",
            "data" => [
                array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id_group'=>'{++id_group++}']]), "title" => Text::_("EXP_MENU_TITLEMENU_EDIT")),
                array("type" => 'link', "spriteClass" => 'menu-icon-copy', "href" =>Route::_([['run' => 'copy', 'id_group'=>['{++id_group++}']]]), "title" => Text::_("LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON")),
                array("type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt') , "href" =>Route::_([['run' => 'delete', 'id_group'=>['{++id_group++}']]]), "title" => Text::_("EXP_MENU_TITLEMENU_DEL")),
            ]]
    ]);
}

$grid->setPlugin("movesort", array(
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'ordering' && options.sortorder == 'asc' ? true : false}"),
    "handle" => ".menu-icon-handle"
));

ui\grid\MazeGrid::end();

$this->setTextScritp("				
$('#menugroup-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = obj.parent == 0 || page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {id_group:val.id_group, ordering:(i+1)+count}});
    \$.post(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){
    },'json');
})", ['wrap'=>Document::DOCREADY]);
?>