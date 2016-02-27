<?php
use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;
use maze\helpers\Json;
       
?>

<?php
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#logs-db-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('user_id')->element('ui\users\UsersList', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
        <?= $filter->field('query'); ?>
        <?= $filter->field('ip'); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->beginField('time'); ?>
        <?= Html::activeLabel($modelFilter, 'time', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= Html::activeTextInput($modelFilter, 'time[0]', ['class' => 'form-control']) ?>
            <span class="input-group-addon" id="basic-addon1">-</span>
            <?= Html::activeTextInput($modelFilter, 'time[1]', ['class' => 'form-control']) ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->field('category'); ?>
        
        <?= $filter->beginField('datetime'); ?>
        <?= Html::activeLabel($modelFilter, 'datetime', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datetimepicker::element(['model' => $modelFilter, 'attribute' => 'datetime[0]', 'settings'=>['stepMinute'=>1,'stepSecond'=>1], 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon" id="basic-addon1">-</span>
            <?= ui\date\Datetimepicker::element(['model' => $modelFilter, 'attribute' => 'datetime[1]', 'settings'=>['stepMinute'=>1,'stepSecond'=>1], 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>

<?php FilterBuilder::end(); ?>

<?php

$grid = MazeGrid::begin([
            'settings' => ['id' => 'logs-db-grid'],
            'colModel' => [
                ["name" => "menu", "width" => 20, "title" => Text::_("EXP_LOGS_ACTION_LABEL")],
                ["name"=>"datetime", "title"=>Text::_("EXP_LOGS_DATETIME_LABEL"), "index"=>"datetime", "width"=>100, "align"=>"center", "sorttable"=>true, "hidefild"=>false],
                ["name"=>"ip", "title"=>Text::_("EXP_LOGS_IP_LABE"), "index"=>"ip", "width"=>80, "align"=>"center", "sorttable"=>true, "hidefild"=>false],
                ["name"=>"time", "title"=>Text::_("EXP_LOGS_DB_TIME_LABEL"), "help"=>Text::_("EXP_LOGS_DB_TIME_LABEL"), "index"=>"time", "hidefild"=>true, "width"=>80,  "align"=>"center", "sorttable"=>true],                
                ["name"=>"query", "title"=>Text::_("EXP_LOGS_DB_QUERY_LABEL"), "index"=>"query", "hidefild"=>true, "width"=>250, "align"=>"left", "sorttable"=>true, "grouping"=>true],
                ["name"=>"category", "title"=>Text::_("EXP_LOGS_CATEGORY_LABEL"), "index"=>"category", "hidefild"=>true, "width"=>150,"align"=>"center", "sorttable"=>true, "grouping"=>true]
            ]
        ]);


$grid->setPlugin("movesort", [
    "sorttable" =>false
]);
$grid->setPlugin("buttonfild", [
    "menu" => [
        "spriteClass" => ["menu-icon-folder-horizontal", "menu-icon-folder-horizontal"],
        "click" => new JsExpression("function (e, type, row){
                cms.redirect([{run:'edit', id:row.id}]);
		return true;
	}")
    ]
]);
ui\grid\MazeGrid::end();
?>
<style>
    .traces-mark{
        font-size: 12px;
        color: #868686;
    }
</style>