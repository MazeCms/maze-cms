<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id'=>'widget-form-moving',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck'=>'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
        ]);
?>
<?= $form->field($modelForm, 'id_tmp')->element('ui\select\Chosen', ['items' => $model->getTemplate(['front' => $front]), 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_tmp') . ' --']]); ?>
<?= $form->field($modelForm, 'position')->element('ui\select\Chosen', ['items' => $model->getPosition(null), 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('position') . ' --']]); ?>
<?php ui\form\FormBuilder::end(); ?> 
<script type="text/javascript">
    jQuery(document).ready(function () {
        var $position = $('#formmoving-position')
        $('#formmoving-id_tmp').bind("change", function (e) {
            var $self = $(this);
            $position.find("option").not(function () {
                return $(this).val() == ""
            }).remove();
            $position.trigger("chosen:updated").trigger("change");
            if ($self.val() == "")
                return false;

            $.get(cms.getURL([{run: 'position', id_tmp: $self.val(), clear: 'ajax'}]), function (data) {
                $position.append(data);

                $position.trigger("chosen:updated");
            })


        });
    })
</script>