<?php

use maze\base\JsExpression;
?>
<?php echo $this->render('filter', ['modelFilter'=>$modelFilter, 'id'=>'dictionary-term-grid']); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'dictionary-term-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => Text::_("EXP_DICTIONARY_LABEL_SWITCH"), 'visible' => ($this->access->roles("dictionary", "EDIT_TERM") || $this->access->roles("dictionary", "DELETE_TERM") )],
                ["name" => "menu", "title" => Text::_("EXP_DICTIONARY_LABEL_ACTION"), "index" => "dt.bundle, dt.sort", "align" => "center",
                    "width" => 20, "help" => Text::_("EXP_DICTIONARY_LABEL_ACTION"), "sorttable" => true, 'visible' =>  ($this->access->roles("dictionary", "EDIT_TERM") || $this->access->roles("dictionary", "DELETE_TERM") )],
                ["name" => "enabled", "title" => Text::_("EXP_DICTIONARY_LABEL_ENABLED"), "index" => "dt.enabled", "width" => 20, "align" => "center", 
                    "hidefild" => true, 'visible' =>$this->access->roles("dictionary", "EDIT_TERM"), "sorttable" => true],
                
                ["name" => "title", "title" => Text::_("EXP_DICTIONARY_TERM_LABEL_TITLE"), "width" => 250, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "alias", "title" => Text::_("EXP_DICTIONARY_ALIAS"), "index" => "route.alias", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "typename", "title" => Text::_("EXP_DICTIONARY_LABEL_TITLETYPE"), "index" => "dt.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "langtitle", "title" => Text::_("EXP_DICTIONARY_LABEL_LANG"), "index" => "l.id_lang", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "roletitle", "title" => Text::_("EXP_DICTIONARY_ACCESS_ROLE"), "index" => "r.id_role", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "term_id", "title" => "ID", "index" => "dt.term_id", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "term_id[]"
));
$items = [];
if($this->access->roles("dictionary", "EDIT_TERM")){
    $items[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'term_id' => '{++term_id++}']]), "title" => Text::_("EXP_DICTIONARY_EDIT"));           
}
if($this->access->roles("dictionary", "DELETE_TERM")){
    $items[] = array("type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'term_id' => ['{++term_id++}']]]), "title" => Text::_("EXP_DICTIONARY_DELETE"));
}
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => $items
        ]
]);

$grid->setPlugin("buttonfild", [
    "enabled" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), term_id:[row.term_id], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
    ]
]);

;
$grid->setPlugin("movesort", [
    "sorttable" => false
]);

ui\grid\MazeGrid::end();
?>
