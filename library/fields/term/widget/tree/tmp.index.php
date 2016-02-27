<?php

use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

ui\assets\AssetTree::register();
RC::app()->document->setTextScritp('$( "#'.$widget->wrapp. '" ).jstree('.Json::encode($options).');',
            ['wrap'=>Document::DOCREADY]);

?>
<?= $form->beginField($widget->data[0], "term_id"); ?>
<?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'htmlOptions' => ['for' => Html::getInputId($widget->data[0], "term_id"), 'class' => 'control-label']]); ?>
<div id="<?=$widget->wrapp?>"></div>
<?php if ($widget->field->many_value == 0 || $widget->field->many_value > 1): ?>
<?php foreach($widget->data as $id=>$data):?>
 <?= Html::hiddenInput(Html::getInputName($data, "[$id]term_id"), Html::getAttributeValue($data, "[$id]term_id"), ['class' => 'form-control', 'id' => Html::getInputId($data, "[$id]term_id")]); ?>
<?php endforeach;?>
<?php else: ?>
     <?= Html::hiddenInput(Html::getInputName($widget->data[0], "term_id"), Html::getAttributeValue($widget->data[0], "term_id"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "term_id")]); ?>
<?php endif; ?>
<?= Html::error($widget->data[0], "term_id", ['id' => Html::getInputId($widget->data[0], "term_id") . "_message", 'class' => 'help-block']) ?>
<?= $form->endField(); ?>
<script>
 $( "#<?=$widget->wrapp?>" ).on('select_node.jstree deselect_node.jstree select_all.jstree deselect_all.jstree', function (e, data) {
        var manyValue = <?= $widget->field->many_value; ?>; 
        var valsel = $.map(data.instance.get_selected(true), function (val) { return val.li_attr['data-id']; });  
        var $group = $(this).closest('.form-group');
        var obj = data.instance.get_selected(true);
        if(manyValue != 0 && obj.length > manyValue){
            obj.slice(0, manyValue)
            data.instance.deselect_all();
            data.instance.select_node(obj.reverse().slice(0, manyValue));
            return false;
        }
        
        <?php if ($widget->field->many_value == 0 || $widget->field->many_value > 1): ?>
            $group.find('input[type=hidden]').remove();
        <?php else:?>
            $group.find('input[type=hidden]').val(valsel);
        <?php endif; ?>
        
        $.each(valsel, function(i, val){
             <?php if ($widget->field->many_value == 0 || $widget->field->many_value > 1): ?>
                 $group.append($('<input>',{id:'<?=$widget->field->field_name?>-'+i+'-term_id', 'type':'hidden', 'name':'<?=$widget->field->field_name?>['+i+'][term_id]'}).val(val))
            <?php endif; ?>
        })

})
</script>