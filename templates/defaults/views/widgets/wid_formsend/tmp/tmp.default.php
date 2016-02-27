<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\base\JsExpression;
?>
<div <?= Html::renderTagAttributes(["class" => "wrapp-widget-form-send wrapp-widget-form-id-$id " . $params->getVar("css_class")]) ?>>
    <section id="contact">

        <?php
        $form = FormBuilder::begin([
                    'ajaxSubmit' => false,
                    'id' => $id_css,
                    'options'=>['onsubmit'=>new JsExpression('return true;')],
                    'groupClass' => 'form-group has-feedback',
                    'dataFilter' => 'function(data){return data.errors}'
        ]);
        ?>
        
            <?php echo $form->field($modelForm, 'name'); ?>
            <?php echo $form->field($modelForm, 'email'); ?>
            <?php echo $form->field($modelForm, 'phone'); ?>
            <?php echo $form->field($modelForm, 'text')->textarea(); ?>
            <?php echo $form->field($modelForm, 'idform', ['template' => '{input}'])->hiddenInput(); ?>


        <input type="submit" class="submit" id="submit" value="<?php echo Text::_("WID_FORMSEND_FORM_LABEL_SUBMIT") ?>" />
        <div class="clearfix"></div>
        <?php FormBuilder::end(); ?>

    </section>
</div>