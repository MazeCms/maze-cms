<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
?>
<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'plugin-form',
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
<blockquote>
    <h4><?php echo $xmlParams->get('name'); ?></h4>
    <small><?php echo $xmlParams->get('description'); ?></small>    
</blockquote>
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-plugin-tabs']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_PLUGINS_STYLE_FORM_TABS_ONE")); ?>
<ul class="list-group">
    <li class="list-group-item">
        <span class="badge"><?= $modelTable->name; ?></span>
<?= Text::_('EXP_PLUGINS_FORM_LABEL_TYPE') ?>
    </li>
    <li class="list-group-item">
        <span class="badge"><?= $modelTable->group_name; ?></span>
<?= Text::_('EXP_PLUGINS_FORM_LABEL_GROUP') ?>
    </li>
    <li class="list-group-item">
        <span class="badge"><?= $modelTable->installApp->front_back ? Text::_("EXP_PLUGINS_SITE") : Text::_("EXP_PLUGINS_ADMIN"); ?></span>
<?= Text::_('EXP_PLUGINS_TABLE_HEAD_FRONT_TITLE') ?>
    </li>
</ul>

<?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
<?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', [ 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
<?php $tabs->endTab(); ?>

<?php if (isset($params->tabs)): ?>
    <?php foreach ($params->tabs as $fielset): ?>
        <?php $tabs->beginTab(Text::_($fielset["title"])); ?>
        <div class="form-horizontal">
            <?php foreach ($fielset as $element): ?>
            <div class="form-group">
                <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? $element['description'] : null), 'htmlOptions'=>['class'=>'col-sm-3 control-label']]); ?>
                <div class="col-sm-9"><?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'param')) ?></div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php $tabs->endTab(); ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php JqTabs::end(); ?>

<?php ui\form\FormBuilder::end(); ?>  