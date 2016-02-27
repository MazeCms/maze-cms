<?php
use ui\filter\FilterBuilder;
use maze\helpers\Html;

$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#'.$id.'").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">        
        <?= $filter->field('enabled')->element('ui\select\Chosen', ['items' => [Text::_("LIB_USERINTERFACE_FIELD_NO"), Text::_("LIB_USERINTERFACE_FIELD_YES")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('bundle')->element('ui\select\Chosen', ['items' => maze\table\ContentType::getList(['expansion'=>'contents']),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
        <?= $filter->field('id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
    </div>
    <div class="col-md-6">
        
        <?= $filter->field('home')->element('ui\select\Chosen', ['items' => [Text::_("LIB_USERINTERFACE_FIELD_NO"), Text::_("LIB_USERINTERFACE_FIELD_YES")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('alias') ?>
        <?= $filter->beginField('time_active'); ?>
        <?= Html::activeLabel($modelFilter, 'time_active', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->beginField('time_inactive'); ?>
        <?= Html::activeLabel($modelFilter, 'time_inactive', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>
<?php FilterBuilder::end(); ?>