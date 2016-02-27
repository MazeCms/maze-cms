<?php

use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

ui\assets\AssetFieldBuinder::register();

$html = '<div class="input-group">';
$html .= '<span class="input-group-addon">Имя</span><div class="form-group"><input type="text" name="" class="form-control link-label-field"/></div>';
$html .= '<span class="input-group-addon">URL</span><div class="form-group"><input type="text" name="" class="form-control link-path-field"/></div>';
$html .= '</div>';

if ($widget->field->many_value != 0 && $widget->field->many_value != 1) {
    $updateFunc = 'function(){';
    $updateFunc .= '$(this).find(".input-group").each(function(i){ $(this).find(".link-label-field").attr("name", "' . $widget->field->field_name . '["+i+"][link_label]");';
    $updateFunc .= '$(this).find(".link-path-field").attr("name", "' . $widget->field->field_name . '["+i+"][link_url]");';
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
                        <?= $form->beginField($data, "[$id]link_url"); ?>
                        <div class="input-group">
                            <span class="input-group-addon">Имя</span>
                            <?= Html::textInput(Html::getInputName($data, "[$id]link_label"), Html::getAttributeValue($data, "[$id]link_label"), ['class' => 'form-control link-label-field', 'id' => Html::getInputId($data, "[$id]link_label")]); ?>
                            <span class="input-group-addon">URL</span>
                            <?= Html::textInput(Html::getInputName($data, "[$id]link_url"), Html::getAttributeValue($data, "[$id]link_url"), ['class' => 'form-control link-path-field', 'id' => Html::getInputId($data, "[$id]link_url")]); ?>
                        </div> 
                        <?= Html::error($data, "[$id]link_label", ['id' => Html::getInputId($data, "[$id]link_label") . "_message", 'class' => 'help-block']) ?>
                        <?= Html::error($data, "[$id]link_url", ['id' => Html::getInputId($data, "[$id]link_url") . "_message", 'class' => 'help-block']) ?>
                        <?= $form->endField(); ?>
                    </td>
                    <td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>
                </tr>   
            <?php endforeach; ?>  
        </table>
        <button type="button" class="btn btn-primary btn-block add-widget-field">Добавить</button>

    </div>
<?php else: ?>
    <?= $form->beginField($widget->data[0], "link_url"); ?>
    <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['for' => Html::getInputId($widget->data[0], "link_url"), 'class' => 'control-label']]); ?>
    <div class="input-group">
        <span class="input-group-addon">Имя</span>
        <?= Html::textInput(Html::getInputName($widget->data[0], "link_label"), Html::getAttributeValue($widget->data[0], "link_label"), ['class' => 'form-control link-label-field', 'id' => Html::getInputId($widget->data[0], "link_label")]); ?>
        <span class="input-group-addon">URL</span>    
        <?= Html::textInput(Html::getInputName($widget->data[0], "link_url"), Html::getAttributeValue($widget->data[0], "link_url"), ['class' => 'form-control link-path-field', 'id' => Html::getInputId($widget->data[0], "link_url")]); ?>
    </div>
    <?= Html::error($widget->data[0], "link_label", ['id' => Html::getInputId($widget->data[0], "link_label") . "_message", 'class' => 'help-block']) ?>
    <?= Html::error($widget->data[0], "link_url", ['id' => Html::getInputId($widget->data[0], "link_url") . "_message", 'class' => 'help-block']) ?>
    <?= $form->endField(); ?>
<?php endif; ?>
