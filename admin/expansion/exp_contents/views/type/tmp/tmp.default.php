<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-type-grid'],
            'model' => 'maze\table\MenuGroup',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_CONTENTS_LABEL_SWITCH"), 'visible' => $this->access->roles("contents", "DELETE_TYPE_CONTENTS")],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), 'visible' => true],
                ["name" => "bundle", "title" => Text::_("EXP_CONTENTS_LABEL_CODETYPE"), "index" => "bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_LABEL_TITLETYPE"), "index" => "title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_CONTENTS_LABEL_DESTYPE"), "index" => "description", "width" => 300, "left" => "center", "hidefild" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "bundle[]"
));

$menu = [];
$menu[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'bundle' => '{++bundle++}']]), "title" => Text::_("EXP_CONTENTS_EDIT"));
if($this->access->roles("contents", "DELETE_TYPE_CONTENTS")){
  $menu[] =  array("type" => 'link', "spriteClass" => 'menu-icon-trash',"actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'bundle' => ['{++bundle++}']]]), "title" => Text::_("EXP_CONTENTS_DELETE"));
}
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