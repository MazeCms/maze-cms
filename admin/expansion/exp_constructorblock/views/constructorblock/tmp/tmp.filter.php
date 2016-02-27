<?php

use maze\helpers\Html;
use ui\form\FormBuilder;

?>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'constructorblock-filter-form',
                'groupClass' => 'form-group',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}'
    ]);
    ?>
    
    <div class="form-horizontal">
        <?php foreach ($formXml->getParams()->element as $element): ?>
            <?php echo $form->beginField($modelForm, $element['name']); ?>
            <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions' => ['class' => 'col-sm-3 control-label']]); ?>
            <div class="col-sm-9"><?php echo $formXml->elemenet($element, $modelForm) ?></div>
            <?= Html::error($modelForm, $element['name']); ?>
            <?php echo $form->endField(); ?>
        <?php endforeach; ?>
    </div>
    
    <?= $form->field($modelForm, 'table', ['template' => '{input}'])->hiddenInput();?>
    
<?php ui\form\FormBuilder::end(); ?>
</div>