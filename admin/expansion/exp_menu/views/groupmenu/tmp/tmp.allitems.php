<?php

use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;
?>
<?= $this->render('filter', ['modelFilter'=>$modelFilter, 'model'=>$model, 'tableId'=>$tableId]);?>
<?php
/**
 * Таблица
 */
$grid = MazeGrid::begin([
            'settings' => ['id' => 'groupmenu-all-grid'],
            'filterData' => new JsExpression("function(data){return data.html}"),
            'model' => 'maze\table\Menu',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("menu", "EDIT_ITEM")],
                ["name" => "enabled", "index" => "m.enabled", "hidefild" => true, "width" => 20,
                    "help" => Text::_("EXP_MENU_TABLE_ENABLED_DES"), "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "ordering", "index" => "m.ordering", "align" => "center", "width" => 20,
                    "sorttable" => true, "help" => Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_DES"), 'visible' => $this->access->roles("menu", "EDIT_ITEM")],
                ["name" => "home", "index" => "m.home", "hidefild" => true,
                    "width" => 30, "help" => Text::_("EXP_MENU_VIEW_GROUP_TABL_HOME_TITLE"), "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "name", "index" => "m.name", "hidefild" => true, "width" => 200, "align" => "left", "sorttable" => true, "grouping" => false],
                ["name" => "id_group", "index" => "m.id_group", "align" => "center", "width" => 150, "sorttable" => true],
                ["name" => "alias", "index" => "route.alias", "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => true],
                ["name" => "size", "title" => Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_TITLE"), "hidefild" => true, "width" => 30,
                    "align" => "center", "sorttable" => false, "grouping" => true],
                ["name" => "lang", "title" => Text::_("EXP_MENU_ADD_ITEM_FORM_LANG"), "index" => "m.id_lang", "hidefild" => true,
                    "width" => 100, "align" => "center", "sorttable" => true],
                ["name" => "title_role", "title" => Text::_("EXP_MENU_TABLE_ACCSE_TITLE"),
                    "index" => "r.name", "hidefild" => true, "width" => 100, "align" => "center", "sorttable" => true],
                ["name" => "id_menu", "title" => "ID", "index" => "m.id_menu", "hidefild" => true,
                    "width" => 100, "help" => Text::_("EXP_MENU_TABLE_ID_DES"), "align" => "center", "sorttable" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_menu[]"
));


$grid->setPlugin("contextmenu", [
    "ordering" => [
        "items" => ".menu-icon-handle",
        "data" => [
            ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'id_menu' => '{++id_menu++}']]), "title" => Text::_("EXP_MENU_TITLEMENU_EDIT")],
            ["type" => 'link', "spriteClass" => 'menu-icon-copy', "href" => Route::_([['run' => 'copy', 'id_menu' => ['{++id_menu++}']]]), "title" => Text::_("EXP_MENU_TITLEMENU_COPY")],
            ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'id_menu' => ['{++id_menu++}']]]), "title" => Text::_("EXP_MENU_TITLEMENU_DEL")]
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable" => false
]);
if ($this->access->roles("menu", "EDIT_ITEM")) {
    $grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_menu:[row.id_menu], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ],
        "home" => [
            "spriteClass" => ["menu-icon-home-plus", "menu-icon-home-minus"],
            "click" => new JsExpression("function(e, type, row){
				if(row.typeLink == 'separator' ) return false;
				var selfEl = this;
				$.get(cms.getURL([{run:'home', id_menu:row.id_menu, clear:'ajax'}]), function(){
                                    selfEl.mazeGrid('update');
                                }, 'json');		
				return true;
			}")
        ]
    ]);
}
$grid->setPlugin("tooltip_content", array(
    "filds" => array(
        "name" => array(
            Text::_("EXP_MENU_ITEMS_PARAMS_VIEW") => "paramLink"
        )
    )
));

ui\grid\MazeGrid::end();
?>