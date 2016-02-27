<?php

use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

ui\assets\AssetFieldBuinder::register();
$settingsFile = [
    'multi' => false,
    'onlyURL' => false,
    'title' => Text::_("LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG")
];



$handler = 'function handler(files, fm){ selfObj.closest(".input-group").find(".file-path-field").val(files.url)}';
$fileLoad = 'var selfObj = $(this); $(this).addClass("active-file-load"); cms.loadFileManager(' . $handler . ',' . Json::encode($settingsFile) . ');';

$html = '<div class="input-group">';
$html .= '<span class="input-group-addon">Имя</span><div class="form-group"><input type="text" name="" class="form-control file-label-field"/></div>';
$html .= '<span class="input-group-addon">Путь</span><div class="form-group"><input type="text" name="" class="form-control file-path-field"/></div>';
$html .= '<span class="input-group-btn">';
$html .= '<button class="btn btn-primary" type="button" ' . Html::renderTagAttributes(['onclick' => $fileLoad]) . '><span aria-hidden="true" class="glyphicon glyphicon-plus"></span></button>';
$html .= '</div>';

if ($widget->field->many_value != 0 && $widget->field->many_value != 1) {
    $updateFunc = 'function(){';
    $updateFunc .= '$(this).find(".input-group").each(function(i){ $(this).find(".file-label-field").attr("name", "' . $widget->field->field_name . '["+i+"][label_file]");';
    $updateFunc .= '$(this).find(".file-path-field").attr("name", "' . $widget->field->field_name . '["+i+"][path_file]");';
    $updateFunc .= '})';
    $updateFunc .='}';
    $widget->clientOptions['onAfterAdd'] = new JsExpression($updateFunc);
    $widget->clientOptions['update'] = new JsExpression($updateFunc);
    $widget->clientOptions['addField'] = new JsExpression("function(count){return '" . $html . "'; }");
}
?>

    
<?php if ($widget->field->many_value == 0 || $widget->field->many_value > 1): ?>
    <div id="<?= $widget->wrapp ?>" class="panel panel-default">
        <div class="panel-heading">
            <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'teg' => 'span', 'htmlOptions' => ['class' => 'control-label']]); ?>
        </div>
        <table class="table">
            <?php foreach ($widget->data as $id => $data): ?>
                <tr>
                    <td class="text-cente align-middle" style="width: 16px;"><span aria-hidden="true" class="glyphicon glyphicon-move"></span></td>
                    <td class="text-cente align-middle">
                        <?= $form->beginField($data, "[$id]path_file"); ?>
                        <div class="input-group">
                            <span class="input-group-addon">Имя</span>
                            <?= Html::textInput(Html::getInputName($data, "[$id]label_file"), Html::getAttributeValue($data, "[$id]label_file"), ['class' => 'form-control file-label-field', 'id' => Html::getInputId($data, "[$id]label_file")]); ?>
                            <span class="input-group-addon">Путь</span>
                            <?= Html::textInput(Html::getInputName($data, "[$id]path_file"), Html::getAttributeValue($data, "[$id]path_file"), ['class' => 'form-control file-path-field', 'id' => Html::getInputId($data, "[$id]path_file")]); ?>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" <?= Html::renderTagAttributes(['onclick' => $fileLoad]) ?>><span aria-hidden="true" class="glyphicon glyphicon-plus"></span></button>
                            </span>
                        </div> 
                        <?= Html::error($data, "[$id]path_file", ['id' => Html::getInputId($data, "[$id]path_file") . "_message", 'class' => 'help-block']) ?>
                        <?= $form->endField(); ?>
                    </td>
                    <td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>
                </tr>   
            <?php endforeach; ?>  
        </table>
        <button type="button" class="btn btn-primary btn-block add-widget-field">Добавить</button>

    </div>
<?php else: ?>
    <?= $form->beginField($widget->data[0], "path_file"); ?>
    <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['for' => Html::getInputId($widget->data[0], "text_value"), 'class' => 'control-label']]); ?>
    <div class="input-group">
        <span class="input-group-addon">Имя</span>
        <?= Html::textInput(Html::getInputName($widget->data[0], "label_file"), Html::getAttributeValue($widget->data[0], "label_file"), ['class' => 'form-control file-label-field', 'id' => Html::getInputId($widget->data[0], "label_file")]); ?>
        <span class="input-group-addon">Путь</span>    
        <?= Html::textInput(Html::getInputName($widget->data[0], "path_file"), Html::getAttributeValue($widget->data[0], "path_file"), ['class' => 'form-control file-path-field', 'id' => Html::getInputId($widget->data[0], "path_file")]); ?>
        <span class="input-group-btn">
            <button class="btn btn-primary" type="button" <?= Html::renderTagAttributes(['onclick' => $fileLoad]) ?>><span aria-hidden="true" class="glyphicon glyphicon-plus"></span></button>
        </span>
    </div>

    <?= Html::error($widget->data[0], "path_file", ['id' => Html::getInputId($widget->data[0], "path_file") . "_message", 'class' => 'help-block']) ?>
    <?= $form->endField(); ?>
<?php endif; ?>
