<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\base\JsExpression;
?>

<div class="wrap-form">    
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'role-form',
                'groupClass' => 'form-group has-feedback',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
                'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
                'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
                'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
    ]);
    ?>
    <?= $form->field($modelForm, 'name'); ?>
    <?= $form->field($modelForm, 'description')->textarea(); ?>
    <?php $grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'role-form-grid'],
            'datatype'=>'local',
            'data'=>  $private,
            'rowList'=>[count($private)],
            'opengroup'=>false,
            'buttons'=>[],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель"],
                ["name" => "name", "title" => Text::_("КОД"), "hidefild" =>false, "width" => 200, "align" => "center", "sorttable" => true, "grouping" => false],
                ["name" => "title", "title" => Text::_("Разрешение"), "hidefild" => true, "width" => 200, "align" => "left", "sorttable" => false, "grouping" => false],
                ["name" => "description", "title" => Text::_("Описание"), "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => false, "grouping" => false],
                ["name" => "exp_name", "title" => "Приложение", "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => true, "grouping" => true]
            ]
        ]);
$grid->setGroup('groupField', ['exp_name']);
$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "Role[private][]",
    "checked"=>$modelForm->private ? $modelForm->private : []
));
$grid->setPlugin("movesort", [
    "sorttable" => false
]);

ui\grid\MazeGrid::end();
?>
    <?php ui\form\FormBuilder::end(); ?> 
</div>
