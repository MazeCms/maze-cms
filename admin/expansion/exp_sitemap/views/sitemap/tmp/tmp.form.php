<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\fields\FieldHelper;
use maze\helpers\ArrayHelper;
use maze\helpers\Json;
use ui\tabs\JqTabs;

ui\assets\AssetTreeTable::register();
ui\assets\AssetiCheck::register();

$dataLink = $model->link ? Json::encode(ArrayHelper::toArray($model->link)) : '';

$this->addStylesheet(RC::app()->getExpUrl('css/style.css'));
$this->addScript(RC::app()->getExpUrl("/js/form.js"));
$this->setTextScritp('initTableTree('.$dataLink.');packHendler();$(".import-toggel").mazeSwitch({"draggable": false});$( "#sitemap-form-import" ).sortable({axis: "y"});', ['wrap' => \Document::DOCREADY]);
?>
<div class="wrapp-form-tabs clearxif">
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-tabs-sitemaps']]); ?> 
<?php $tabs->beginTab(Text::_("катра сайта")); ?> 

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'sitemap-form',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}'
        ]);
?>
   
<div class="row">
    <div class="col-sm-8 col-md-8">
        <?= $form->field($model->map, 'title'); ?>
    </div>
    <div class="col-sm-4 col-md-4">
        <?= $form->field($model->routes, 'alias'); ?>
    </div>
</div>

<?= $form->field($model->map, 'description')->textarea(); ?>
<?= $form->field($model->map, 'enable_xml', ['template' => '{input}{error}'])->element('ui\checkbox\SwitchBtn'); ?>
<?= $form->field($model->map, 'enable_html', ['template' => '{input}{error}'])->element('ui\checkbox\SwitchBtn'); ?>
     <?php if(!$model->map->isNewRecord):?>
    <div class="alert alert-warning">
        <div style="margin-bottom: 10px;"><strong>Адрес HTML карта:</strong> <a href="<?= RC::app()->request->getBaseUrl(). '/'.$model->getUrl()?>" target="_blank"><?= RC::app()->request->getBaseUrl(). '/'.$model->getUrl()?></a></div>
        <div><strong>Адрес XML карта:</strong> <a href="<?= RC::app()->request->getBaseUrl(). '/'.$model->getUrl()?>.xml" target="_blank"><?= RC::app()->request->getBaseUrl(). '/'.$model->getUrl()?>.xml</a></div>
    </div>
    <?php endif;?>
<table id="maps-link-table" class="table">
    <thead>
        <tr>
            <th><input type="checkbox"></th>
            <th>Заголовок</th>
            <th>Активность</th>
            <th>Ссылка  </th>
            <th>Дата изменения</th>
            <th>Частота изменения</th>
            <th>Приоритетность</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?php ui\form\FormBuilder::end(); ?>
<?php $tabs->endTab(); ?>

<?php $tabs->beginTab(Text::_("Настройки импортирования")); ?>

<?php echo Html::beginForm([['run'=>'import']], 'post', ['id'=>'sitemap-form-import']);?>
<?php
$types = $modelImport->getListExp();
foreach ($types as $type => $name) :
    $metaView = $modelImport->getFormModule($type);
    $viewModel = $modelImport->getModelModule($type);
    ?>
    <div  class="panel panel-default">
        <div class="panel-heading"><?= $name ?> <?= Html::input("checkbox", "enable[$type]", 1, ['class'=>'import-toggel', "checked"=>$modelImport->getEnableType($type)])?></div>
        <?php if($metaView && $viewModel):?>
        <div class="panel-body">
            <div class="form-horizontal">
                <?php foreach ($metaView->getParams()->element as $element): ?>
                <div class="form-group">
                    <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions' => ['class' => 'col-sm-3 control-label']]); ?>
                    <div class="col-sm-9"><?php echo $metaView->elemenet($element, $viewModel) ?></div>
                    <?= Html::error($viewModel, $element['name']); ?>
                </div>    
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif;?>
        <?= Html::hiddenInput('sort[]', $type)?>
    </div>
<?php endforeach; ?>
<?php echo Html::endForm();?>
<?php $tabs->endTab(); ?>
<?php JqTabs::end(); ?>   
</div>