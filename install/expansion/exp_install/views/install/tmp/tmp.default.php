<?php

use maze\helpers\Html;

$this->addScript("/install/expansion/exp_install/js/install.js");
?>

<table class="table-install">
    <tbody>
        <tr>
            <td class="header-install" colspan="2">
                <div class="title-install"> <?= Text::_("LIB_FRAMEWORK_INSTALL_HEAD_TITLE") ?> <div id="logo-cms"></div></div>
            </td>
        </tr>
        <tr>
            <td class="left-col">
                <a class="btn-action-step <?= $step == 0 ? "active" : "inactive" ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_LANG") ?></a>
                <a class="btn-action-step <?= $step == 1 ? "active" : ($step > 1 ? "inactive" : "") ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_START") ?></a>
                <a class="btn-action-step <?= $step == 2 ? "active" : ($step > 2 ? "inactive" : "") ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_DB") ?></a>
                <a class="btn-action-step <?= $step == 3 ? "active" : ($step > 3 ? "inactive" : "") ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_ACCUNT") ?></a>
                <a class="btn-action-step <?= $step == 4 && !$wizardStep ? "active" : ($step > 4 || $wizardStep ? "inactive" : "") ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_PROFILE") ?></a>
                <?php if ($wizardInstall): ?>
                    <?php foreach ($wizardInstall->getSteps() as $stepMaster): ?>
                        <a class="btn-action-step <?= $wizardStep == $stepMaster['name'] && !$profileModel ? "active" : ($wizardInstall->getIsInActive($stepMaster['name'], $wizardStep) || $profileModel ? "inactive" : "") ?>" href="#"><?= Text::_($stepMaster['title']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a class="btn-action-step <?= $step == 5 ? "active" : ($step > 5 ? "inactive" : "") ?>" href="#"><?= Text::_("LIB_FRAMEWORK_INSTALL_STEP_INSTALL") ?></a>
            </td>
            <td class="right-col">

                <?php
                $form = ui\form\FormBuilder::begin([
                            'ajaxSubmit' => false,
                            'id' => 'form-install',
                            'groupClass' => 'form-group has-feedback',
                            'dataFilter' => 'function(data){return data.errors}',
                            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){} return true;}',
                            'onErrorElem' => ' function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
                            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
                            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
                ]);
                ?>
                <div style="margin-bottom:20px;">
                    <?=
                    $this->render('step-' . $step, [
                        'curentModel' => $curentModel,
                        'langs' => $langs,
                        'form' => $form,
                        'stepModel' => $stepModel,
                        'wizardStep' => $wizardStep,
                        'wizardInstall' => $wizardInstall,
                        'totalStep' => $totalStep
                    ]);
                    ?>                   
                </div>
                 <?php foreach ($allModel as $model): if (!$profileModel && $model->formName() == $curentModel->formName()) continue; ?>
                        <?php foreach ($model->attributes as $attribute => $val): ?>
                            <?= Html::activeHiddenInput($model, $attribute) ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <div>
                    <a id="prev-install" data-step="<?= $wizardInstall && !$profileModel ? ($wizardInstall->getPrevStep($wizardStep) ? 5 : 4) : $step - 1 ?>" data-wizard="<?= $wizardInstall && !$profileModel ? $wizardInstall->getPrevStep($wizardStep) : "" ?>" class="btn btn-default<?= $step == 0 ? ' disabled' : "" ?>"><span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span><?= Text::_("LIB_FRAMEWORK_INSTALL_PREV") ?></a>
                    <a id="next-install" data-step="<?= $step + 1 ?>" data-wizard="<?= $wizardInstall ? $wizardInstall->getNextStep($wizardStep) : "" ?>" class="btn btn-success"><?= Text::_("LIB_FRAMEWORK_INSTALL_NEXT") ?><span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span></a>

                </div>
                <?php ui\form\FormBuilder::end(); ?>           
            </td>   
        </tr>
    </tbody>
</table>