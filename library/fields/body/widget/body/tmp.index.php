<?php

use maze\helpers\Html;
use maze\base\JsExpression;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'teg' => 'span', 'htmlOptions' => ['class' => 'control-label']]); ?>        
    </div>
    <div class="panel-body">
<?= $form->beginField($widget->data[0], "text_prev"); ?>
<?= Html::label(Text::_('LIB_FIELDS_BODY_PREVTEXT_LABEL'), Html::getInputId($widget->data[0], "text_prev"), ['class' => 'control-label']) ?> 

<?php if ($widget->enableprev): ?>
    <?= ui\editor\Editor::element(['name' => Html::getInputName($widget->data[0], "text_prev"), 'value' => Html::getAttributeValue($widget->data[0], "text_prev"), 'options' => ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_prev")]]); ?>
<?php else: ?>
    <?= Html::textarea(Html::getInputName($widget->data[0], "text_prev"), Html::getAttributeValue($widget->data[0], "text_prev"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_prev")]) ?>
<?php endif; ?>
<?= Html::error($widget->data[0], "text_prev", ['id' => Html::getInputId($widget->data[0], "text_prev") . "_message", 'class' => 'help-block']) ?>
<?= $form->endField(); ?>

<?= $form->beginField($widget->data[0], "text_full"); ?>
<?= Html::label(Text::_('LIB_FIELDS_BODY_FULL_LABEL'), Html::getInputId($widget->data[0], "text_full"), ['class' => 'control-label']) ?> 
<?php if ($widget->enablefull): ?>
    <?= ui\editor\Editor::element(['name' => Html::getInputName($widget->data[0], "text_full"), 'value' => Html::getAttributeValue($widget->data[0], "text_full"), 'options' => ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_full")]]); ?>
<?php else: ?>
    <?= Html::textarea(Html::getInputName($widget->data[0], "text_full"), Html::getAttributeValue($widget->data[0], "text_full"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_full")]) ?>
<?php endif; ?>
<?= Html::error($widget->data[0], "text_full", ['id' => Html::getInputId($widget->data[0], "text_full") . "_message", 'class' => 'help-block']) ?>
<?= $form->endField(); ?>

<?= $form->beginField($widget->data[0], "text_format"); ?>
<?= Html::label(Text::_('LIB_FIELDS_BODY_FILTER_FORMAT'), Html::getInputId($widget->data[0], "text_format"), ['class' => 'control-label']) ?> 
<?= ui\select\Chosen::element(['name'=>Html::getInputName($widget->data[0], "text_format"), 'value'=>Html::getAttributeValue($widget->data[0], "text_format"), 'options'=>['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_format")],  'items'=>$widget->getListFilter()]) ?>
<?= Html::error($widget->data[0], "text_format", ['id' => Html::getInputId($widget->data[0], "text_full") . "_message", 'class' => 'help-block']) ?>
<?= $form->endField(); ?>
</div>
</div>