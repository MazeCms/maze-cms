<?php

use maze\base\JsExpression;

$this->setTextScritp("
$('#dictionary-field-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {field_exp_id:val.field_exp_id, sort:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap' => \Document::DOCREADY]);

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'dictionary-field-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_DICTIONARY_LABEL_SWITCH"), 'visible' => ($this->access->roles("dictionary", "EDIT_FIELD") || $this->access->roles("dictionary", "DELETE_FIELD"))],
                ["name" => "menu", "title" => Text::_("EXP_DICTIONARY_LABEL_ACTION"), "index"=>"fe.sort", "align" => "center", 
                    "width" => 20, "help" => Text::_("EXP_DICTIONARY_LABEL_ACTION"),"sorttable" => true, 'visible' => ($this->access->roles("dictionary", "EDIT_FIELD") || $this->access->roles("dictionary", "DELETE_FIELD"))],
                ["name"=>"active", "title"=>Text::_("EXP_DICTIONARY_LABEL_ENABLED"), "index"=>"fe.active",  "width"=>50, "align"=>"center",
                    "hidefild"=>true, "sorttable"=>true, 'visible' => $this->access->roles("dictionary", "EDIT_FIELD")],
                ["name" => "field_name", "title" => Text::_("EXP_DICTIONARY_FIELD_NAME"), "index" => "fe.field_name", "width" => 100, "align" => "center","sorttable" => true, "hidefild" => true],
                ["name" => "title", "title" => Text::_("EXP_DICTIONARY_FIELD_LABEL"), "index" => "fe.title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "type", "title" => Text::_("EXP_DICTIONARY_FIELD_TYPE"), "index" => "f.type", "width" => 100, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "widget", "title" => Text::_("EXP_DICTIONARY_FIELD_WID"), "width" => 150, "align" => "center", "index" => "fe.widget_name","sorttable" => true, "hidefild" => true], 
                ["name" => "field_exp_id", "title" =>"ID", "width" => 50, "index" => "fe.field_exp_id", "align" => "center", "sorttable" => true, "hidefild" => true]
            ]
        ]);


$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "field_exp_id[]"
));
$items = [];
if($this->access->roles("dictionary", "EDIT_FIELD")){
    $items[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'field_exp_id' => '{++field_exp_id++}']]), "title" => Text::_("EXP_DICTIONARY_EDIT"));
}
if($this->access->roles("dictionary", "DELETE_FIELD")){
    $items[] = array("type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'field_exp_id' => ['{++field_exp_id++}'],  'bundle'=>$bundle]]), "title" => Text::_("EXP_DICTIONARY_DELETE"));
}
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "visible"=>new JsExpression("function(data){if(data.locked == 1){this.remove();}return data.locked == 0;}"),
        "data" => $items
        ]
]);
$grid->setPlugin("buttonfild", [
        "active" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                if(row.locked == 1) return false;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), field_exp_id:[row.field_exp_id], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);

$grid->setPlugin("movesort", [
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'fe.sort' && options.sortorder == 'asc' ? true : false}"),
]);

ui\grid\MazeGrid::end();
?>