<?php

use maze\base\JsExpression;
use maze\helpers\Html;

ui\assets\AssetHighcharts::register(['publishOptions' => ['drilldown']]);
$this->addScript(RC::app()->getExpUrl("/js/visits.js"));
?>

<div class="clearfix">
    <div class="col-md-6 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">Число посещений XML карт роботами</div>
            <div class="panel-body">
                <div id="robots-type-xml"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">Число посещений HTML карт роботами</div>
            <div class="panel-body">
                <div id="robots-type-html"></div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="col-md-6 col-sm-6">
        <div id="type-map-robots-xlm" class="panel panel-default">
            <div class="panel-heading">Статистика по роботам XML карты</div>
            <div class="panel-body">
                <div class="input-group">
                    <?= Html::dropDownList('sitemap_id', null, $listMap, ['class' => 'form-control']) ?>
                    <span class="input-group-addon">-</span>
                    <?= ui\date\Datepicker::element(['name' => 'in_date_visits', 'options' => ['class' => 'form-control']]); ?>
                    <span class="input-group-addon">-</span>
                    <?= ui\date\Datepicker::element(['name' => 'out_date_visits', 'options' => ['class' => 'form-control']]); ?>
                </div>
                <div class="body-map"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6">
        <div id="type-map-robots-html" class="panel panel-default">
            <div class="panel-heading">Статистика по роботам HTML карты</div>
            <div class="panel-body">
                <div class="input-group">
                    <?= Html::dropDownList('sitemap_id', null, $listMap, ['class' => 'form-control']) ?>
                    <span class="input-group-addon">-</span>
                    <?= ui\date\Datepicker::element(['name' => 'in_date_visits', 'options' => ['class' => 'form-control']]); ?>
                    <span class="input-group-addon">-</span>
                    <?= ui\date\Datepicker::element(['name' => 'out_date_visits', 'options' => ['class' => 'form-control']]); ?>
                </div>
                <div class="body-map"></div>
            </div>
        </div>
    </div>
</div>