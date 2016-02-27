<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;


$this->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
$this->setTextScritp("
$('#widgets-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {id_wid:val.id_wid, ordering:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap'=>\Document::DOCREADY]);
?>
<?php

$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#widgets-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">        
        <?= $filter->field('enabled')->element('ui\select\Chosen', ['items' => [Text::_("EXP_WIDGET_WIDGETS_FILTER_UNPUBLISH_LABEL"), Text::_("EXP_WIDGET_WIDGETS_FILTER_PUBLISH_LABEL")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        
        <?= $filter->field('id_tmp')->element('ui\select\Chosen', ['items'=>$model->getTemplate(['front'=>$front]), 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        <?= $filter->field('position')->element('ui\select\Chosen', ['items' => $model->getAllPosition($front),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
        <?= $filter->field('id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('title') ?>
        <?= $filter->field('title_show')->element('ui\select\Chosen', ['items' => [Text::_("EXP_WIDGET_NO"), Text::_("EXP_WIDGET_YES")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('name')->element('ui\select\Chosen', ['items' => $model->getWidgetList($front),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->beginField('time_active'); ?>
        <?= Html::activeLabel($modelFilter, 'time_active', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->beginField('time_inactive'); ?>
        <?= Html::activeLabel($modelFilter, 'time_inactive', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>
<?php FilterBuilder::end(); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'widgets-grid'],
            'model' => 'maze\table\Widgets',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("widget", "EDIT_WIDGET")],                
                ["name"=>"enabled", "title"=>Text::_("Активность"), "index"=>"w.enabled", 
                    "hidefild"=>true, "width"=>20, "align"=>"center", "sorttable"=>true, "grouping"=>false],
                ["name" => "menu", "width" => 20, "title" => "Действия", "help"=>Text::_('EXP_WIDGET_WIDGETS_TABLE_HEAD_TOOLTIP'), "index"=>"w.id_tmp, w.position, w.ordering", 
                    "sorttable"=>true, 'visible' => $this->access->roles("widget", "EDIT_WIDGET")],
                ["name"=>"title", "title"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_TITLE"), "index"=>"w.title",
                    "hidefild"=>true, "width"=>250, "align"=>"left", "sorttable"=>true, "grouping"=>false],
                ["name"=>"name", "title"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_TYPE"), "index"=>"w.name", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"tmp_name", "title"=>Text::_("EXP_WIDGET_FORM_LABEL_TMP"), "index"=>"w.id_tmp", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"position", "title"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_POSITION"), "index"=>"w.position", 
                    "hidefild"=>true, "width"=>100, "help"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_POSITION_TOOLTIP"), "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"role", "title"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_ACCESS"), "index"=>"r.name", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"time_active", "title"=>Text::_("EXP_WIDGET_FORM_LABEL_ACTIVEDATE"), "index"=>"w.time_active", 
                    "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],                
                ["name"=>"time_inactive", "title"=>Text::_("EXP_WIDGET_FORM_LABEL_INACTIVEDATE"), "index"=>"w.time_inactive", 
                    "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"lang_title", "title"=>Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_LANG"), "index"=>"w.id_lang", 
                    "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"id_wid", "title"=>"ID", "index"=>"w.id_wid", "hidefild"=>true, "width"=>80, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_wid[]"
));

$items = [
    ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id_wid'=>'{++id_wid++}', 'front'=>$front]]), "title" => Text::_("EXP_WIDGET_WIDGETS_TABLE_BODY_EDIT")],
    ["type" => 'link', "spriteClass" => 'menu-icon-copy', "href" =>Route::_([['run' => 'copy', 'id_wid'=>['{++id_wid++}'], 'front'=>$front]]), "title" => Text::_("EXP_WIDGET_WIDGETS_TABLE_BODY_COPY")],
];
if($this->access->roles("widget", "DELET_WIDGET") ){
    $items[] = ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" =>Route::_([['run' => 'delete', 'id_wid'=>['{++id_wid++}'], 'front'=>$front]]), "title" => Text::_("EXP_WIDGET_WIDGETS_TABLE_BODY_DEL")];
}

    $grid->setPlugin("contextmenu", [
        "menu" => [
            "items" => ".menu-icon-handle",
            "data" => $items
            ]
    ]);

$grid->setPlugin("movesort", [
    "sortgroup"=>["id_tmp", "position"],
    "sorttable"=>new JsExpression("function(options){return options.sortfild == 'w.id_tmp, w.position, w.ordering' && options.sortorder == 'asc' ? true : false}"),
]);
$grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_wid:[row.id_wid], front:'".$front."',  clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);

// редактируем позицию виджета

$grid->setPlugin("edits", [
    'filds'=>[
        'position'=>[
            'type'=>'select',
            'opt'=>[
                'getOption'=>new JsExpression("function(val, dataRow, callback){ "
                        . "$.get(cms.getURL([{run:'position', id_tmp:dataRow.id_tmp, type:'json', clear:'ajax'}]), function(data){"
                        . " callback($.map(data.html, function(label, val){return {value:val, text:label, selected:(dataRow.position == val)}}));  },'json');"
                        . "}"),
                'beforeSave'=>new JsExpression("function(value,  oldValue, dataRow, el){"
                        . "if(dataRow.position == value) return oldValue; "
                        . "$.get(cms.getURL([{run:'saveposition', id_wid:dataRow.id_wid, position:value, clear:'ajax'}]), function(data){
                           $('#widgets-grid').mazeGrid('update'); }, 'json');"
                        . " return value;}"),
            ]
            
        ]
    ]
]);
ui\grid\MazeGrid::end();

?>
