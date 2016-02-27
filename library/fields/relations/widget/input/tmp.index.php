<?php

use maze\helpers\Html;
use maze\base\JsExpression;

ui\assets\AssetFieldBuinder::register();
$url = new URI(URI::instance());
$url->setVar('clear', 'ajax');
$url->setVar('field_exp_id', $widget->field->field_exp_id);
$widget->clientOptions['isAllremove'] = true;
$isMulti = ($widget->field->many_value == 0 || $widget->field->many_value > 1);
if ($isMulti) {
    $widget->clientOptions['onAfterAdd'] = new JsExpression('function(){$(this).find("input[type=hidden]").each(function(i){ $(this).attr("name", "' . $widget->field->field_name . '["+i+"][contents_id]' . '")})}');
    $widget->clientOptions['update'] = new JsExpression('function(){$(this).find("input[type=hidden]").each(function(i){ $(this).attr("name", "' . $widget->field->field_name . '["+i+"][contents_id]' . '")})}');
    $widget->clientOptions['addField'] = new JsExpression('function(count){return "<div class=\"form-group\"><input type=\"hidden\" name=\"\"/></div>"; }');
}else{
    $widget->clientOptions['addField'] = new JsExpression('function(count){return "<div class=\"form-group\"><input type=\"hidden\" name=\"'.$widget->field->field_name.'[contents_id]\"/></div>"; }');
}
?>

<script>
    jQuery(document).ready(function () {
        var $wrap = $("#<?= $widget->wrapp ?>");
        var $search = $("#<?= $widget->wrapp ?> .search-contents");
        $search.bind("keydown", function (event) {
            if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).autocomplete("instance").menu.active) {
                event.preventDefault();
            }
        })
                .autocomplete({
                    source: function (request, response) {
                        $.getJSON("<?= $url->toString() ?>", {
                            fieldrelations: request.term
                        }, response);
                    },
                    minLength: 4,
                    appendTo:$wrap,
                    focus: function () {
                        return false;
                    },
                    select: function (event, ui) {
                        $wrap.fieldBuilder('add')
                        <?php if($isMulti):?>
                        var td = $wrap.find('table tbody tr:last').find('td').eq(1);
                        td.find('input').val(ui.item.id)
                        td.append('<div class="label-search">'+ui.item.label+'</div>')
                        <?php else:?>
                        var td = $wrap.find('table tbody tr:last').find('td').eq(1);
                        td.find('input').val(ui.item.id)
                        if(!td.find('.label-search').is('.label-search')){
                            td.append('<div class="label-search">'+ui.item.label+'</div>')
                        }else{
                            td.find('.label-search').html(ui.item.label)
                        }
                        
                        <?php endif;?>
                        
                        return false;
                    }
                });
        $wrap.find('.refresh-search').click(function () {
            $search.autocomplete('search')
        })

    });
</script>
<div id="<?= $widget->wrapp ?>" class="panel panel-default">
    <div class="panel-heading">
        <?= ui\help\Tooltip::element(['content' => Text::_($widget->field->title), 'help' => $widget->field->prompt, 'teg' => 'span', 'htmlOptions' => ['class' => 'control-label']]); ?>
    </div>

    <div class="panel-body">
        <div class="input-group">
            <span class="input-group-addon"><span aria-hidden="true" class="glyphicon glyphicon-search"></span></span>
            <input type="text" class="form-control search-contents" placeholder="Искать по заголовку">
            <span class="input-group-btn">
                <button class="btn btn-default refresh-search" type="button"><span aria-hidden="true" class="glyphicon glyphicon-refresh"></span></button>
            </span>
        </div>
        <table class="table">
            <tbody>
                <?php if ($isMulti): ?>
                    <?php foreach ($widget->data as $id => $data): ?>
                        <tr>
                            <td class="text-cente align-middle" style="width: 16px;"><span aria-hidden="true" class="glyphicon glyphicon-move"></span></td>
                            <td class="text-cente align-middle">
                                <?= $form->beginField($data, "[$id]contents_id"); ?>
                                <div class="label-search"> <?= $data->title?></div>
                                <?= Html::hiddenInput(Html::getInputName($data, "[$id]contents_id"), Html::getAttributeValue($data, "[$id]contents_id"), ['class' => 'form-control', 'id' => Html::getInputId($data, "[$id]contents_id")]); ?>
                                <?= Html::error($data, "[$id]contents_id", ['id' => Html::getInputId($data, "[$id]contents_id") . "_message", 'class' => 'help-block']) ?>
                                <?= $form->endField(); ?>
                            </td>
                            <td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>   
                    <?php endforeach; ?>
                <?php else: ?>
                       <tr>
                            <td class="text-cente align-middle" style="width: 16px;"><span aria-hidden="true" class="glyphicon glyphicon-move"></span></td>
                            <td class="text-cente align-middle">
                                <?= $form->beginField($widget->data[0], "contents_id"); ?>
                                <div class="label-search"><?= $widget->data[0]->title?></div>
                                <?= Html::hiddenInput(Html::getInputName($widget->data[0], "contents_id"), Html::getAttributeValue($widget->data[0], "contents_id"), ['class' => 'form-control', 'id' => Html::getInputId($widget->data[0], "contents_id")]); ?>
                                <?= Html::error($widget->data[0], "contents_id", ['id' => Html::getInputId($widget->data[0], "contents_id") . "_message", 'class' => 'help-block']) ?>
                                <?= $form->endField(); ?>
                            </td>
                            <td class="align-middle" style="width: 16px;"><a class="btn btn-danger remove-widget-field"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>    
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>