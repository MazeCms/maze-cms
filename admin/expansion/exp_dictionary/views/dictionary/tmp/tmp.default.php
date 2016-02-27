<?php

use maze\base\JsExpression;


$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'dictionary-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_DICTIONARY_LABEL_SWITCH"), 
                    'visible' => ($this->access->roles("dictionary", "EDIT_DICTIONARY") || $this->access->roles("dictionary", "DELETE_DICTIONARY"))],
                ["name" => "menu", "title" => Text::_("EXP_DICTIONARY_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), 
                    'visible' => ($this->access->roles("dictionary", "EDIT_DICTIONARY") || $this->access->roles("dictionary", "DELETE_DICTIONARY"))],
                ["name" => "bundle", "title" => Text::_("EXP_DICTIONARY_LABEL_CODETYPE"), "index" => "bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_DICTIONARY_LABEL_TITLETYPE"), "index" => "title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_DICTIONARY_LABEL_DESTYPE"), "index" => "description", "width" => 300, "align" => "center", "hidefild" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "bundle[]"
));

$item = [];
if($this->access->roles("dictionary", "EDIT_DICTIONARY")){
    $item[] = ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'bundle' => '{++bundle++}']]), "title" => Text::_("EXP_DICTIONARY_EDIT")];
}
if($this->access->roles("dictionary", "DELETE_DICTIONARY")){
  $item[] =  ["type" => 'link', "spriteClass" => 'menu-icon-trash',"actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'bundle' => ['{++bundle++}']]]), "title" => Text::_("EXP_DICTIONARY_DELETE")];
}
$item[] = ["type" => 'link', "spriteClass" =>"menu-icon-eye", "href" => Route::_(['/admin/dictionary/term',['run' => 'term', 'bundle' =>'{++bundle++}']]), "title" => Text::_("EXP_DICTIONARY_TERM_CONT")];
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => $item
    ]
]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>

