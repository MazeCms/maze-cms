<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'constructorblock-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_CONSTRUCTORBLOCK_LABEL_SWITCH")],
                ["name" => "menu", "title" => Text::_("EXP_CONSTRUCTORBLOCK_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_CONSTRUCTORBLOCK_LABEL_ACTION"), 'visible' => true],                
                ["name" => "title", "title" => Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_TITLE_LABEL"), "index" => "b.title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_DESCRIPTION_LABEL"), "width" => 300, "left" => "center", "hidefild" => true],
                ["name" => "typename", "title" => Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_BUNDLE_LABEL"), "index" => "b.bundle", "width" => 100, "align" => "center",  "hidefild" => true, "sorttable" => true],
                ["name" => "code", "title" => Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_CODE_LABEL"), "index" => "b.code", "width" => 50, "align" => "center",  "hidefild" => true, "sorttable" => true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "code[]"
));

$menu = [];
$menu[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'code' => '{++code++}']]), "title" => Text::_("EXP_CONSTRUCTORBLOCK_EDIT"));
$menu[] =  array("type" => 'link', "spriteClass" => 'menu-icon-trash',"actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'code' => ['{++code++}']]]), "title" => Text::_("EXP_CONSTRUCTORBLOCK_DELETE"));
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => $menu]
]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>