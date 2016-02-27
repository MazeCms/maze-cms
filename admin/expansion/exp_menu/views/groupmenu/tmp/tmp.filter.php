<?php
use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
/**
 * Фильтр
 */
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#'.$tableId.'").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?=
        $filter->field('enabled')->element('ui\select\Chosen', ['items' => ['НЕТ', 'ДА'],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?=
        $filter->field('home')->element('ui\select\Chosen', ['items' => ['НЕТ', 'ДА'],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?=
        $filter->field('typeLink')->element('ui\select\Chosen', ['items' => $model->listTypeLink(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?=
        $filter->field('id_role')->element('ui\select\Chosen', ['items' => $model->listRole(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('name');?>
    </div>
    <div class="col-md-6">
        <?=
        $filter->field('id_lang')->element('ui\select\Chosen', ['items' => $model->listLang(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?=
        $filter->field('id_tmp')->element('ui\select\Chosen', ['items' => $model->listTmp(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
            <?=
            $filter->field('id_exp')->element('ui\select\Chosen', ['items' => $model->listExp(),
                'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
            ?>
        <?= $filter->beginField('time_active'); ?>
        <?= Html::activeLabel($modelFilter, 'time_active', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon" id="basic-addon1">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
<?= $filter->beginField('time_inactive'); ?>
<?= Html::activeLabel($modelFilter, 'time_inactive', ['class' => 'control-label']); ?>
        <div class="input-group">
<?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon" id="basic-addon1">-</span>
<?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
<?= $filter->endField(); ?>
    </div>
</div>

<?php FilterBuilder::end(); ?>