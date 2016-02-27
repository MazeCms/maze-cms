<?php
use maze\base\JsExpression;
use ui\form\FormBuilder;
use maze\helpers\Json;
use maze\helpers\Html;
use ui\grid\MazeGrid;

$this->addStylesheet(RC::app()->getExpUrl('css/contents.css'));
?>
<div class="filter-wrapp filter-wrapp-view-contents">
    <ul class="filter-form-tabs-link">
        <li<?= $mode == 0 ? ' class="active"' : '' ?>>
            <?= Html::a(Text::_("EXP_CONTENTS_VIEW_PREVIEW"), [['run' => 'edit', 'bundle' => $bundle, 'expansion' => $expansion, 'mode' => 0]]) ?>
        </li>
        <li<?= $mode == 1 ? ' class="active"' : '' ?>>
            <?= Html::a(Text::_("EXP_CONTENTS_VIEW_FULLTEXT"), [['run' => 'edit', 'bundle' => $bundle, 'expansion' => $expansion, 'mode' => 1]]) ?>
        </li>
    </ul>
   

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-view-type-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_CONTENTS_LABEL_SWITCH"), 'visible' => true],
                ["name" => "enabled", "title" => Text::_("EXP_CONTENTS_ENABLED"), "index" => "ctv.enabled", "width" => 20, "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "menu", "title" => Text::_("EXP_CONTENTS_LABEL_ACTION"),"index" => "ctv.group_name, ctv.sort",  "align" => "center", "width" => 20, "help" => Text::_("EXP_CONTENTS_LABEL_ACTION"), "sorttable" => true, 'visible' => true],
                ["name" => "field_title", "title" => Text::_("EXP_CONTENTS_VIEW_TITLE_FIELD"), "index" => "ctv.field_exp_id", "width" => 100, "align" => "center", "hidefild" => true,  "sorttable" => true],
                ["name" => "field_type", "title" => Text::_("EXP_CONTENTS_FIELD_TYPE"), "index" => "f.type", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "group_name", "title" => Text::_("EXP_CONTENTS_VIEW_GROUP_FIELD"), "index" => "ctv.group_name", "width" => 100, "align" => "left", "hidefild" => true,  "sorttable" => true],
                ["name" => "show_label", "title" => Text::_("EXP_CONTENTS_VIEW_SHOW_TITLE_FIELD"), "index" => "ctv.show_label", "width" => 150, "align" => "center", "hidefild" => true,  "sorttable" => true],
                ["name" => "field_view", "title" => Text::_("EXP_CONTENTS_VIEW_FIELD_LABEL"), "index" => "ctv.field_view", "width" => 150, "align" => "center", "hidefild" => true,  "sorttable" => true],
                ["name" => "view_name", "title" => Text::_("EXP_CONTENTS_VIEW_CODE_FIELD_LABEL"), "index" => "ctv.view_name", "width" => 100, "align" => "center", "hidefild" => true,  "sorttable" => true]
                
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "view_name[]"
));

$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'editField', 'view_name' => '{++view_name++}', 'bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]]), "title" => Text::_("EXP_CONTENTS_EDIT")),
            array("type" => 'link', "spriteClass" => 'menu-icon-trash', 
                "href" => Route::_([['run' => 'fieldDelete', 'view_name' => ['{++view_name++}'], 'bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]]), 
                "title" => Text::_("EXP_CONTENTS_DELETE"), "actions" => new JsExpression('cms.itemMenuDeletePromt')),
        ]]
]);

$grid->setPlugin("buttonfild", [
    "enabled" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), view_name:[row.view_name],bundle:'$bundle' ,expansion:'$expansion', mode:'$mode', clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
    ]
]);
$grid->setPlugin("movesort", array(
    "sortgroup"=>["group_name"],
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'ctv.group_name, ctv.sort' && options.sortorder == 'asc' ? true : false}"),
));

ui\grid\MazeGrid::end();

$this->setTextScritp("
$('#contents-view-type-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {view_name:val.view_name, sort:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap' => \Document::DOCREADY]);
?>
</div>