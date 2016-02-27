<?php

use maze\helpers\Html;
use maze\base\JsExpression;
?>
<div class="form-group">
    <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['class' => 'control-label']]); ?>        
    <?php if ($widget->field->many_value == 0 || $widget->field->many_value > 1): ?>
        <?=
        ui\images\AddImage::element([
            'name' => Html::getInputName($widget->data[0], "[]path_image"),
            'value' => $data,
            'settings' => [
                'max_img' => ($widget->field->many_value == 0 ? 60000 : $widget->field->many_value)                
            ],
            'format' => $types,
            'options' => ['id' => Html::getInputId($widget->data[0], "path_image")]
        ]);
        ?>
    <?php else: ?>
        <?=
        ui\images\AddImage::element([
            'name' => Html::getInputName($widget->data[0], "path_image"),
            'value' => Html::getAttributeValue($widget->data[0], "path_image"),
            'multi' => false,
            'settings' => ['max_img' => 1],
            'format' => $types,
            'options' => ['id' => Html::getInputId($widget->data[0], "path_image")]
        ]);
        ?>
<?php endif; ?>
</div>