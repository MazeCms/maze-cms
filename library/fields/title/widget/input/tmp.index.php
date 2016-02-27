<?php
    use maze\helpers\Html; 
?>

    <?= $form->beginField($widget->data[0], "title_value"); ?>
    <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['for' => Html::getInputId($widget->data[0], "title_value"), 'class' => 'control-label']]); ?>
    <?= Html::textInput(Html::getInputName($widget->data[0], "title_value"), Html::getAttributeValue($widget->data[0], "title_value"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "title_value")]); ?>
    <?= Html::error($widget->data[0], "title_value", ['id' => Html::getInputId($widget->data[0], "title_value") . "_message", 'class' => 'help-block']) ?>
    <?= $form->endField(); ?>