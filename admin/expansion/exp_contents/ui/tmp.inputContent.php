<?php

use maze\helpers\Html;
?>
<div style="display: none" id="content-filter-modal">
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'enabled', ['class' => 'control-label']); ?>
                <?=
                \ui\select\Chosen::element(['model' => $modelFilter, 'attribute' => 'enabled', 'items' => [Text::_("LIB_USERINTERFACE_FIELD_NO"), Text::_("LIB_USERINTERFACE_FIELD_YES")],
                    'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'bundle', ['class' => 'control-label']); ?>
                <?=
                \ui\select\Chosen::element(['model' => $modelFilter, 'attribute' => 'bundle', 'items' => maze\table\ContentType::getList(['expansion' => 'contents']),
                    'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'id_lang', ['class' => 'control-label']); ?>
                <?= \ui\lang\Langs::element(['model' => $modelFilter, 'attribute' => 'id_lang', 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'id_role', ['class' => 'control-label']); ?>
                <?= \ui\role\Roles::element(['model' => $modelFilter, 'attribute' => 'id_role', 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
            </div>

            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'home', ['class' => 'control-label']); ?>
                <?=
                \ui\select\Chosen::element(['model' => $modelFilter, 'attribute' => 'home', 'items' => [Text::_("LIB_USERINTERFACE_FIELD_NO"), Text::_("LIB_USERINTERFACE_FIELD_YES")],
                    'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
                ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($modelFilter, 'alias', ['class' => 'control-label']); ?>
                <?= Html::activeTextInput($modelFilter, 'alias', ['class' => 'form-control']); ?>
            </div>
        </div>
    </div>
</div>
