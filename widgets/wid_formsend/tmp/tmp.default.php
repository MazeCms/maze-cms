<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
?>
<div <?= Html::renderTagAttributes(["class" => "wrapp-widget-form-send wrapp-widget-form-id-$id " . $params->getVar("css_class")]) ?>>
    <?php
    
    $form = FormBuilder::begin([
            'ajaxSubmit' => false,
            'id'=>$id_css,
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}'
        ]);
    ?>
    <?php echo $form->field($modelForm, 'name');?>
    <?php echo $form->field($modelForm, 'email');?>
    <?php echo $form->field($modelForm, 'phone')->element('ui\text\TextInputMask', ['mask'=>'+7(999) 999-99-99', 'options'=>['class'=>'form-control']]); ?>
    <?php echo $form->field($modelForm, 'text')->textarea();?>
    <?php echo $form->field($modelForm, 'idform', ['template' => '{input}'])->hiddenInput();?>
    <button class="btn btn-green" type="submit"><?php echo Text::_("WID_FORMSEND_FORM_LABEL_SUBMIT")?></button>
    <?php FormBuilder::end();?>
</div>

