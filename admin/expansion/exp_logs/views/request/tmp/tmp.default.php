<?php
use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;
use maze\helpers\Json;

$this->setTextScritp("
$('#logs-request-grid').bind('afterGetContent.mazegrid', function(e, obj){
    var row = obj.data;
    var tr = $(this).find('tbody tr');
    tr.each(function(){
        var trTarget = $(this);
        var data = $(this).data('gridRow');
        $.each(row, function(i, val){
            if(data == val){
                if(val.statusCode >= 400 && val.statusCode <= 500){
                    trTarget.addClass('danger');
                }
                if(val.statusCode >= 300 && val.statusCode <= 310){
                    trTarget.addClass('warning');
                }
                if(val.statusCode == 200){
                    trTarget.addClass('success');
                }
                return false;
            }
            
        })
    }) 
})					
", ['wrap' => Document::DOCREADY]);
?>

<?php
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#logs-request-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('user_id')->element('ui\users\UsersList', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
        <?= $filter->field('route'); ?>
        <?= $filter->field('ip'); ?>
        <?= $filter->field('statusCode'); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('statusText'); ?>
        <?= $filter->field('action'); ?>
        <?= $filter->field('controller'); ?>
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
            'settings' => ['id' => 'logs-request-grid'],
            'colModel' => [
                ["name" => "menu", "width" => 20, "title" => Text::_("EXP_LOGS_ACTION_LABEL")],
                ["name"=>"datetime", "title"=>Text::_("EXP_LOGS_DATETIME_LABEL"), "index"=>"datetime", "width"=>100, "align"=>"center", "sorttable"=>true, "hidefild"=>false],
                ["name"=>"ip", "title"=>Text::_("EXP_LOGS_IP_LABE"), "index"=>"ip", "width"=>80, "align"=>"center", "sorttable"=>true, "hidefild"=>false],                
                ["name"=>"route", "title"=>Text::_("EXP_LOGS_REQUEST_ROUTE_LABEL"), "index"=>"route", "hidefild"=>true, "width"=>150, "align"=>"left", "sorttable"=>true, "grouping"=>true],
                ["name"=>"statusText", "title"=>Text::_("EXP_LOGS_REQUEST_STATUSTEXT_LABEL"), "index"=>"statusText", "hidefild"=>true, "width"=>150, "align"=>"left", "sorttable"=>true, "grouping"=>true],
                ["name"=>"statusCode", "title"=>Text::_("EXP_LOGS_REQUEST_STATUSCODE_LABEL"), "index"=>"statusCode", "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true, "grouping"=>true],
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
    #logs-request-grid .danger td{
        background-color: #f2dede;
    }
    #logs-request-grid .warning td{
        background-color: #fcf8e3;
    }
    #logs-request-grid .success td{
        background-color: #dff0d8;
    }
</style>