<?php

use maze\helpers\Html;
use maze\base\JsExpression;

ui\assets\AssetFieldBuinder::register();

if ($widget->field->many_value !== 0 && $widget->field->many_value !== 1) {
    $widget->clientOptions['onAfterAdd'] = new JsExpression('function(){$(this).find("textarea").each(function(i){ $(this).attr("name", "' . $widget->field->field_name . '["+i+"][text_value]' . '")})}');
    $widget->clientOptions['update'] = new JsExpression('function(){$(this).find("textarea").each(function(i){ $(this).attr("name", "' . $widget->field->field_name . '["+i+"][text_value]' . '")})}');
    $widget->clientOptions['addField'] = new JsExpression('function(count){return "<div class=\"form-group\"><textarea name=\"\" class=\"form-control\"/></div>"; }');
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
                        <?= $form->beginField($data, "[$id]text_value"); ?>
                        <?= Html::textarea(Html::getInputName($data, "[$id]text_value"), Html::getAttributeValue($data, "[$id]text_value"), ['class' => 'form-control', 'id' => Html::getInputId($data, "[$id]text_value")]); ?>
                        <?= Html::error($data, "[$id]text_value", ['id' => Html::getInputId($data, "[$id]text_value") . "_message", 'class' => 'help-block']) ?>
                        <?= $form->endField(); ?>
                    </td>
                    <td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>
                </tr>   
            <?php endforeach; ?>  
        </table>
        <button type="button" class="btn btn-primary btn-block add-widget-field">Добавить</button>

    </div>
<?php else: ?>
    <?= $form->beginField($widget->data[0], "text_value"); ?>
    <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['for' => Html::getInputId($widget->data[0], "text_value"), 'class' => 'control-label']]); ?>
    <?= Html::textarea(Html::getInputName($widget->data[0], "text_value"), Html::getAttributeValue($widget->data[0], "text_value"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "text_value")]); ?>
    <?= Html::error($widget->data[0], "text_value", ['id' => Html::getInputId($widget->data[0], "text_value") . "_message", 'class' => 'help-block']) ?>
    <?= $form->endField(); ?>
<?php endif; ?>
