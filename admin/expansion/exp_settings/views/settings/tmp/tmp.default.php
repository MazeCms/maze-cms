<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'settings-form',
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
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-settings-tabs']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_SETTINGS_TABS_SITE")); ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($modelForm, 'site_name'); ?>
        <?= $form->field($modelForm, 'enable_site', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?= $form->field($modelForm, 'offline_mess', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?= $form->field($modelForm, 'role_user')->element('ui\role\Roles', [ 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        <?= $form->field($modelForm, 'format_date')->element('ui\date\FormatDate', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('format_date') . ' --']]); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($modelForm, 'page_number'); ?>
        <?= $form->field($modelForm, 'editor_admin')->element('ui\editor\Lists', [ 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_admin') . ' --']]); ?>
        <?= $form->field($modelForm, 'editor_site')->element('ui\editor\Lists', ['front' => 1, 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_site') . ' --']]); ?>
        <?= $form->field($modelForm, 'captcha')->element('ui\captcha\Lists', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('captcha') . ' --']]); ?>
        <?= $form->field($modelForm, 'calendar')->element('ui\date\Lists', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('calendar') . ' --']]); ?>

    </div>
</div>
<?= $form->field($modelForm, 'text_offline')->element('ui\editor\Editor'); ?>
<div class="panel panel-default">
    <div class="panel-heading">SEO</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">        
                <?= $form->field($modelForm, 'meta_author'); ?>
                <?= $form->field($modelForm, 'show_author', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($modelForm, 'prefix'); ?>
                <?= $form->field($modelForm, 'enab_prefix', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            </div>
        </div>
        <?= $form->field($modelForm, 'meta_robots')->element('ui\meta\Robots'); ?>
        <?= $form->field($modelForm, 'meta_keys')->textarea(); ?>
        <?= $form->field($modelForm, 'meta_desc')->textarea(); ?>
    </div>
</div>

<?php $tabs->endTab(); ?>

<?php $tabs->beginTab(Text::_("EXP_SETTINGS_TABS_SYSTEM")); ?>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Настройка отладки</div>
            <div class="panel-body">
                <?= $form->field($modelForm, 'error_reporting')->element('ui\select\Chosen', ['items' => $model->getErrorReporting(), 'options' => ['class' => 'form-control']]); ?>
                <?= $form->field($modelForm, 'path_log'); ?>
                <?= $form->field($modelForm, 'debug', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'viewstyle', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'viewposition', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'log_enable', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'logWrite')->element('ui\select\Chosen', ['items' => $model->getTypelog(), 'options' => ['class' => 'form-control',  'multiple' => 'multiple']]); ?>
                <?= $form->field($modelForm, 'log_maxsize')->element('ui\text\InputSpinner', ['settings' => ['max' => 20]]); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Локализация</div>
            <div class="panel-body">
                <?= $form->field($modelForm, 'timezone')->element('ui\date\TimeZone'); ?>
                <?php // $form->field($modelForm, 'charset')->element('ui\lang\Encoding'); ?>
                <?= $form->field($modelForm, 'language')->element('ui\lang\Local'); ?>
                <?= $form->field($modelForm, 'autolang', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            </div>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">Настройка безопасноти</div>
            <div class="panel-body">
                <?= $form->field($modelForm, 'ses_name'); ?>
                <?= $form->field($modelForm, 'ses_path'); ?>
                <?= $form->field($modelForm, 'ses_time')->element('ui\text\InputSpinner', ['settings' => ['min' => 1]]); ?>                
                <?=
                $form->field($modelForm, 'enableCsrfValidation', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn');
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">

        
        <div class="panel panel-default">
            <div class="panel-heading">Почта</div>
            <div class="panel-body">
                <?= $form->field($modelForm, 'useFileTransport', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'fromname'); ?>
<?= $form->field($modelForm, 'mailfrom'); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Настройки быстродействия</div>
            <div class="panel-body">
                <?= $form->field($modelForm, 'enable_cache', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?= $form->field($modelForm, 'type_cache')->element('ui\select\Chosen', ['items' =>['file'=>Text::_('EXP_SETTINGS_SERVER_LABEL_CACHEFILE'), 'memcache'=>Text::_('EXP_SETTINGS_SERVER_LABEL_CACHEMEMCACHE')], 'options' => ['class' => 'form-control']]); ?>
                <?= $form->field($modelForm, 'memcache_host'); ?>
                <?= $form->field($modelForm, 'memcache_port'); ?>
                <?= $form->field($modelForm, 'memcache_username'); ?>
                <?= $form->field($modelForm, 'memcache_password'); ?>
                <?= $form->field($modelForm, 'time_cache')->element('ui\text\InputSpinner', ['settings' => ['min' => 10, 'max' => 1000000]]); ?>
                <?= $form->field($modelForm, 'gzip', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>                
            </div>
        </div>
    </div>
</div>
<?php $tabs->endTab(); ?>


<?php JqTabs::end(); ?>

<?php ui\form\FormBuilder::end(); ?>  