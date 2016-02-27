<?php

use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;
use maze\helpers\Json;

ui\assets\AssetHighcharts::register(['publishOptions' => ['drilldown']]);
$this->addScript(RC::app()->getExpUrl("/js/logs.js"));
$this->setLangTextScritp([
    'EXP_LOGS_CHART_FILESIZE_TITLEY',
    'EXP_LOGS_CHART_FILESIZE_TOTLTIPSIZE',
    'EXP_LOGS_CHART_FILESIZE_BYITE'
]);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_LOGS_CHART_FILESIZE_TITLE"); ?></div>
                <div id="log-file-size"></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_LOGS_CHART_REQUEST_TITLE"); ?></div>
                <div id="log-type-request"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_LOGS_CHART_EXP_TITLE"); ?></div>
                <div id="log-type-exp"></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_LOGS_CHART_ERROR_TITLE"); ?></div>
                <div id="log-type-error"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_LOGS_CHART_DBCOUNT_TITLE"); ?></div>
                <div id="log-type-dbcount"></div>
            </div>
        </div>
    </div>
</div>
