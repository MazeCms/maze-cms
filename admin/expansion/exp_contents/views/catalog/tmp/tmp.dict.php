<?php

use maze\base\JsExpression;


$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-catalog-dict-grid'],
            'mode' => 'tree',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => Text::_("EXP_CONTENTS_LABEL_SWITCH"), 'visible' => true],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "index" => "dt.sort", "align" => "center",
                    "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "sorttable" => true, 'visible' => true],               
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_TERM_LABEL_TITLE"), "width" => 250, "align" => "left", "hidefild" => true, "sorttable" => false],
                ["name" => "alias", "title" => Text::_("EXP_CONTENTS_ALIAS"), "index" => "route.alias", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "typename", "title" => Text::_("EXP_CONTENTS_LABEL_DICTIONARY"), "index" => "dt.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "langtitle", "title" => Text::_("EXP_CONTENTS_LABEL_LANG"), "index" => "l.id_lang", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "roletitle", "title" => Text::_("EXP_CONTENTS_ACCESS_ROLE"), "index" => "r.id_role", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "term_id", "title" => "ID", "index" => "dt.term_id", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "term_id[]"
));

$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_(['/admin/dictionary/term', ['run' => 'edit', 'term_id' => '{++term_id++}', 'bundle'=>$bundle]]), "title" => Text::_("EXP_DICTIONARY_EDIT")),            
            array("type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_(['/admin/dictionary/term', ['run' => 'delete', 'term_id' => ['{++term_id++}'], 'bundle'=>$bundle]]), "title" => Text::_("EXP_DICTIONARY_DELETE")),
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable" => false,
]);
$grid->setPlugin("tree", [
    "id" => "term_id",
    "parent" => "parent",
    "target" => "title",
    "fild_type" => "typetree",
    "icon" => ["term" => "/library/image/icons/blue-folder-horizontal.png"],
    "is_child" => true,
    "json" => [
        "param" => new JsExpression("function(id, elem, row){return {parent:row.term_id} }"),
        "data" => new JsExpression("function(data){ return data.html !== null ? data.html.data : null}")
    ]
]);
ui\grid\MazeGrid::end();
?>