<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;

$this->addStylesheet(RC::app()->getExpUrl("css/style.css"));
?>
<?php

$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#sessions-grid").mazeGrid("update", $(form).serializeObject()); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('id_user')->element('ui\select\Chosen', ['items' => [Text::_("EXP_USER_SESSIONS_FILTER_REG_LABEL_ANONIM"), Text::_("EXP_USER_SESSIONS_FILTER_REG_LABEL_USER")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('username'); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->beginField('time_start'); ?>
        <?= Html::activeLabel($modelFilter, 'time_start', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_start[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_start[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->beginField('time_last'); ?>
        <?= Html::activeLabel($modelFilter, 'time_last', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_last[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_last[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>
<?php FilterBuilder::end(); ?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'sessions-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("user", "DELETE_SESSIONS")],                
                ["name"=>"id_user", "title"=>Text::_("EXP_USER_TABLE_HEAD_NAME"), "index"=>"s.id_user", 
                    "hidefild"=>true, "width"=>200, "align"=>"left", "sorttable"=>true, "grouping"=>false],
                ["name"=>"ip", "title"=>"IP", "index"=>"s.ip",
                    "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"ossys", "title"=>Text::_("EXP_USER_SESSIONS_TABLE_OS"), "hidefild"=>true, "width"=>150, "align"=>"center"],
                ["name"=>"browser", "title"=>Text::_("EXP_USER_SESSIONS_TABLE_BROWSER"), "hidefild"=>true, "width"=>150, "align"=>"center"],
                ["name"=>"time_start", "title"=>Text::_("EXP_USER_SESSIONS_TABLE_STARTSES"), "index"=>"s.time_start", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>false],                
                ["name"=>"time_last", "title"=>Text::_("EXP_USER_TABLE_HEAD_DATELAST"), "index"=>"s.time_last", "hidefild"=>true, "width"=>150, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false],
                ["name"=>"id_sess", "title"=>"ID", "index"=>"s.id_sess", "hidefild"=>true, "width"=>80, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_sess[]"
));



$grid->setPlugin("movesort", [
    "sorttable" =>false
]);

ui\grid\MazeGrid::end();									 
?>	

