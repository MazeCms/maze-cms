<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>
<blockquote>
    <h4><?php echo $xmlParams->get('name'); ?></h4>
    <small><?php echo $xmlParams->get('description'); ?></small>    
</blockquote>

    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'expansion-app-form',
                'groupClass' => 'form-group has-feedback',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
                'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
                'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
                'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
    ]);
    ?>

<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-settins-exp-tabs']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_SETTINGS_EXPANSION_TABS_ONE")); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelForm, 'id_tmp')->element('ui\tmp\Style', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_tmp') . ' --']]); ?>
            <?= $form->field($modelForm, 'time_cache')->element('ui\text\InputSpinner', ['settings'=>['min'=>10, 'max'=>1000000]]); ?>
            <?= $form->field($modelForm, 'enable_cache', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>            
        </div>        
    </div> 
<?php $tabs->endTab(); ?>

<?php if (isset($params->tabs)): ?>
    <?php foreach ($params->tabs as $fielset): ?>
        <?php $tabs->beginTab(Text::_($fielset["title"])); ?>
<div class="form-horizontal">
        <?php foreach ($fielset as $element): ?>
            <div class="form-group">
                <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions'=>['class'=>'col-sm-3 control-label']]); ?>
                <div class="col-sm-9"><?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'param')) ?></div>
            </div>
        <?php endforeach; ?>
</div>
        <?php $tabs->endTab(); ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php JqTabs::end(); ?>
<?php ui\form\FormBuilder::end(); ?>  