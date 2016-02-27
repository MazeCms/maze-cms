<?php

use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;

$this->setTextScritp("
$('#contents-home-grid').bind('sortrowupdate.mazegrid', function(e, obj){
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
<?php echo $this->render('@exp/exp_contents/views/contents/tmp/filter', ['modelFilter'=>$modelFilter, 'id'=>'contents-home-grid']); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-home-grid'],
            'model' => 'maze\table\MenuGroup',
            'colModel' => [
                ["name" => "ordering", "width" => 20, "index" => "h.sort", "title" => Text::_("EXP_CONTENTS_LABEL_SWITCH"),"sorttable" => true,  'visible' => ($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS"))],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"),  "align" => "center",
                    "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), 'visible' => ($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS"))],
                ["name" => "enabled", "title" => Text::_("EXP_CONTENTS_ENABLED"), "index" => "c.enabled", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "home", "title" => Text::_("EXP_CONTENTS_HOMEIN"), "index" => "c.home", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_LABEL_TITLE"), "width" => 250, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "alias", "title" => Text::_("EXP_CONTENTS_ALIAS"), "index" => "route.alias", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "typename", "title" => Text::_("EXP_CONTENTS_TYPE"), "index" => "c.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "roletitle", "title" => Text::_("EXP_CONTENTS_ACCESS_ROLE"), "index" => "r.id_role", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "langtitle", "title" => Text::_("EXP_CONTENTS_LABEL_LANG"), "index" => "l.id_lang", "width" => 150, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "contents_id", "title" => "ID", "index" => "c.contents_id", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
            ]
        ]);



$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_(['/admin/contents',['run' => 'edit', 'contents_id' => '{++contents_id++}']]), "title" => Text::_("EXP_CONTENTS_EDIT")),            
            array("type" => 'link', "spriteClass" => 'menu-icon-trash', 
                "actions" => new JsExpression('cms.itemMenuDeletePromt'), 
                "href" => Route::_(['/admin/contents',['run' => 'delete', 'contents_id' => ['{++contents_id++}']]]), 
                "title" => Text::_("EXP_CONTENTS_DELETE"), "visible"=>new JsExpression('function(data){return data.isDelete; }')),
        ]]
]);

$grid->setPlugin("buttonfild", [
    "enabled" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL(['/admin/contents', {run:(type == 'enable' ? 'unpublish' : 'publish'), contents_id:[row.contents_id], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
    ],
    "home" => [
        "spriteClass" => ["menu-icon-home-plus", "menu-icon-home-minus"],
        "click" => new JsExpression("function(e, type, row){				
				var selfEl = this;
				$.get(cms.getURL(['/admin/contents', {run:(type == 'enable' ? 'unhome' : 'home'), contents_id:[row.contents_id], clear:'ajax'}]), function(){
                                     selfEl.mazeGrid('update');
                                }, 'json');		
				return true;
			}")
    ]
]);

;
$grid->setPlugin("movesort", [
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'h.sort' && options.sortorder == 'asc' ? true : false}"),
]);

ui\grid\MazeGrid::end();
?>
