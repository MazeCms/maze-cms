<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-view-grid'],
            'model' => 'maze\table\MenuGroup',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_CONTENTS_LABEL_SWITCH"), 'visible' => true],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), 'visible' => true],
                ["name" => "bundle", "title" => Text::_("EXP_CONTENTS_LABEL_CODETYPE"), "index" => "bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_LABEL_TITLETYPE"), "index" => "title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_CONTENTS_LABEL_DESTYPE"), "index" => "description", "width" => 300, "align" => "left", "hidefild" => true],
                ["name" => "countpreview", "title" => Text::_("EXP_CONTENTS_VIEW_COUNTPREVIEW"), "width" => 150, "align" => "center", "hidefild" => true],
                ["name" => "countfull", "title" => Text::_("EXP_CONTENTS_VIEW_COUNTFULL"), "width" => 150, "align" => "center", "hidefild" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "bundle[]"
));

$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'bundle' => '{++bundle++}', 'mode'=>0, 'expansion'=>$expansion]]), "title" => Text::_("EXP_CONTENTS_EDIT")),
            array("type" => 'link', "spriteClass" => 'menu-icon-trash', 
                "href" => Route::_([['run' => 'delete', 'bundle' => ['{++bundle++}'], 'mode'=>0, 'expansion'=>$expansion]]), 
                "title" => Text::_("EXP_CONTENTS_DELETE"),
                "actions" => new JsExpression('cms.itemMenuDeletePromt')),
        ]]
]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>