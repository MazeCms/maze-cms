<?php

use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;

$this->setTextScritp("
$('#contents-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {contents_id:val.contents_id, sort:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap' => \Document::DOCREADY]);
?>
<?php echo $this->render('filter', ['modelFilter'=>$modelFilter, 'id'=>'contents-grid']); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-grid'],
            'model' => 'maze\table\MenuGroup',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => Text::_("EXP_CONTENTS_LABEL_SWITCH"), 'visible' =>($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS"))],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "index" => "c.bundle, c.sort", "align" => "center",
                    "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "sorttable" => true, 'visible' =>($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS"))],
                ["name" => "enabled", "title" => Text::_("EXP_CONTENTS_ENABLED"), "index" => "c.enabled", "width" => 20, 
                    "align" => "center", "hidefild" => true, "sorttable" => true, 'visible' =>$this->access->roles("contents", "EDIT_CONTENTS")],
                ["name" => "home", "title" => Text::_("EXP_CONTENTS_HOMEIN"), "index" => "c.home", 
                    "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true, 'visible' =>$this->access->roles("contents", "EDIT_CONTENTS")],
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_LABEL_TITLE"), "width" => 250, "align" => "left", "hidefild" => true, "sorttable" => false],
                ["name" => "alias", "title" => Text::_("EXP_CONTENTS_ALIAS"), "index" => "route.alias", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "typename", "title" => Text::_("EXP_CONTENTS_TYPE"), "index" => "c.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "langtitle", "title" => Text::_("EXP_CONTENTS_LABEL_LANG"), "index" => "l.id_lang", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "roletitle", "title" => Text::_("EXP_CONTENTS_ACCESS_ROLE"), "index" => "ar.id_role", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "contents_id", "title" => "ID", "index" => "c.contents_id", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "contents_id[]"
));

$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'contents_id' => '{++contents_id++}']]), "title" => Text::_("EXP_CONTENTS_EDIT")),
            array("type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), 
                "href" => Route::_([['run' => 'delete', 'contents_id' => ['{++contents_id++}']]]), 
                "title" => Text::_("EXP_CONTENTS_DELETE"), "visible"=>new JsExpression('function(data){return data.isDelete; }')),
        ]]
]);

$grid->setPlugin("buttonfild", [
    "enabled" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), contents_id:[row.contents_id], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
    ],
    "home" => [
        "spriteClass" => ["menu-icon-home-plus", "menu-icon-home-minus"],
        "click" => new JsExpression("function(e, type, row){				
				var selfEl = this;
				$.get(cms.getURL([{run:(type == 'enable' ? 'unhome' : 'home'), contents_id:[row.contents_id], clear:'ajax'}]), function(){
                                }, 'json');		
				return true;
			}")
    ]
]);


$grid->setPlugin("movesort", [
    "sortgroup" => ["bundle"],
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'c.bundle, c.sort' && options.sortorder == 'asc' ? true : false}"),
]);

ui\grid\MazeGrid::end();
?>
