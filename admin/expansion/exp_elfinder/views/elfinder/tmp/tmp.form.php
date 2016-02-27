<?php

use maze\helpers\Html;
use ui\form\FormBuilder;

$this->addScript(RC::app()->getExpUrl("js/ui.toolbarnav.js"));
$this->addStylesheet(RC::app()->getExpUrl("css/ui.toolbatnav.css"));
?>


<div class="wrap-form">    
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'elfinder-profile-form',
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
    <?= $form->field($modelForm, 'title'); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
            <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>  
            <?= $form->field($modelForm, 'rememberLastDir', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelForm, 'useBrowserHistory', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelForm, 'resizable', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelForm, 'notifyDelay')->element('ui\text\InputSpinner', ['settings' => ['min' => 100, 'max' => 10000]]); ?>
            <?=
            $form->field($modelForm, 'ui')->element('ui\select\Chosen', ['items' => $model->getListUI(),
                'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
            ?> 
        </div>
        <div class="col-md-6">
            <?= $form->field($modelForm, 'requestType')->element('ui\select\Chosen', ['items' => ['get' => 'GET', 'post' => 'POST'], 'options' => ['class' => 'form-control']]); ?> 
            <?= $form->field($modelForm, 'validName'); ?>
            <?= $form->field($modelForm, 'cssClass'); ?> 
            <?= $form->field($modelForm, 'loadTmbs')->element('ui\text\InputSpinner', ['settings' => ['min' => 1, 'max' => 10000]]); ?>
            <?= $form->field($modelForm, 'showFiles')->element('ui\text\InputSpinner', ['settings' => ['min' => 1, 'max' => 10000]]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($modelForm, 'commands')->element('ui\select\Chosen', ['items' =>
                $model->getListCommand(),
                'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
            ?> 
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><?= Text::_("EXP_ELFINDER_CONTEXTMENU") ?></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <?=
                    $form->field($modelForm, 'navbar')->element('ui\select\Chosen', ['items' => $model->getListNavbar(),
                        'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                    ?> 
                </div>
                <div class="col-md-6">
                    <?=
                    $form->field($modelForm, 'cwd')->element('ui\select\Chosen', ['items' => $model->getListCwd(),
                        'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                    ?> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?=
                    $form->field($modelForm, 'files')->element('ui\select\Chosen', ['items' =>
                        $model->getListFiles(),
                        'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                    ?> 
                </div>
            </div>

        </div>
    </div>
    <div class="alert alert-warning"><?= Text::_('EXP_ELFINDER_SETTING_TITLE_TOOBAR_HELP') ?></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Text::_("EXP_ELFINDER_PROFILE_TOOLBAR") ?></div>
                <div class="panel-body">

                    <div class="elfinde-tool-bar-wrap">
                        <ul class="elfinder-tools">
                            <?php foreach ($button as $btn): ?>
                                <li title="<?= Text::_("EXP_ELFINDER_SETTING_TOOLBAR_BTN_" . strtoupper($btn)) ?>" class="elfinder-tools-elem elfinder-<?= $btn ?>" data-elem="<?= $btn ?>">
                                    <i></i>
                                </li>
                            <?php endforeach; ?>
                            <li data-content="<?= Text::_("EXP_ELFINDER_SETTING_TOOLBAR_PANEL") ?>" class="elfinder-panel"><i></i></li>
                        </ul>
                    </div>
                    <div data-name="FormProfile[toolbar]" id="elfinder-tool-bar">
                        <?php if (is_array($modelForm->toolbar)): ?>
                            <?php foreach ($modelForm->toolbar as $key => $bar): ?>
                                <ul class="elfinder-panel-active" data-id-panel="<?= $key ?>">
                                    <?php foreach ($bar as $order => $btn): ?>
                                        <li class="elfinder-<?= $btn ?> elfinder-tools-elem-active" data-elem="<?= $btn ?>">
                                            <i></i>
                                            <?= Html::activeHiddenInput($modelForm, "toolbar[$key][$order]") ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </div>



    <?php FormBuilder::end(); ?>  
</div>
