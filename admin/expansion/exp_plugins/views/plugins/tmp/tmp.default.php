<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;

$this->setTextScritp("
$('#plugins-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {id_plg:val.id_plg, ordering:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap'=>\Document::DOCREADY]);
?>
<?php

$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#plugins-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6"> 
        <?= $filter->field('front_back')->element('ui\select\Chosen', ['items' => [Text::_("EXP_PLUGINS_FILTER_FRONT_LABEL_ADMIN"), Text::_("EXP_PLUGINS_FILTER_FRONT_LABEL_SITE")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('enabled')->element('ui\select\Chosen', ['items' => [Text::_("EXP_PLUGINS_FILTER_ENABLE_LABEL_INACTIVE"), Text::_("EXP_PLUGINS_FILTER_ENABLE_LABEL_ACTIVE")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
       
    </div>
    <div class="col-md-6">
        <?= $filter->field('id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>        
        <?= $filter->field('group_name')->element('ui\select\Chosen', ['items' => $model->getGroupName(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        
    </div>
</div>
<?php FilterBuilder::end(); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'plugins-grid'],
            'model' => 'maze\table\Plugin',
            'colModel' => [
                ["name" => "ordering","index"=>"p.group_name, p.ordering", "title"=>"Сортировка", "align"=>"center", "width"=>20, "sorttable"=>true, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_DES")],                
                ["name"=>"id","width"=>20, "title"=>"Переключатель"],
                ["name"=>"enabled", "title"=>"Активность", "index"=>"p.enabled", "hidefild"=>true, "width"=>20,  "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"title", "title"=>Text::_("EXP_PLUGINS_TABLE_HEAD_TITLE"), "index"=>"title", "hidefild"=>true, "width"=>200,  "align"=>"left"],
                ["name"=>"group_name", "title"=>Text::_("EXP_PLUGINS_TABLE_HEAD_GROUP_TITLE"), "index"=>"p.group_name", "hidefild"=>true, "width"=>100,  "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"name", "title"=>Text::_("EXP_PLUGINS_TABLE_HEAD_TYPE_TITLE"), "help"=>Text::_("EXP_PLUGINS_TABLE_HEAD_TYPE_DES"),"index"=>"p.name", "hidefild"=>true, "width"=>100,  "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"role", "title"=>Text::_("EXP_PLUGINS_TABLE_HEAD_ACCESS_TITLE"), "index"=>"r.id_role", "hidefild"=>true, "width"=>150,  "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"front", "title"=>Text::_("EXP_PLUGINS_TABLE_HEAD_FRONT_TITLE"), "index"=>"ia.front_back", "hidefild"=>true, "width"=>150,  "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"id_plg", "title"=>"ID", "index"=>"p.id_plg", "hidefild"=>true, "width"=>20,  "align"=>"center", "sorttable"=>true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_plg[]"
));

$grid->setPlugin("movesort", [
    "sortgroup"=>["group_name", "front_back"],
    "sorttable"=>new JsExpression("function(options){return options.sortfild == 'p.group_name, p.ordering' && options.sortorder == 'asc' ? true : false}"),
]);
$grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_plg:[row.id_plg],  clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);

ui\grid\MazeGrid::end();

?>

