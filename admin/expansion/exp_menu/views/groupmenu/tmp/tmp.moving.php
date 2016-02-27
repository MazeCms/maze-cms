<?php
use ui\form\FormBuilder;

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id'=>'menu-form-moving',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck'=>'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
        ]);
?>
<?= $form->field($modelForm,'id_group')->element('ui\select\Chosen', ['items' => $modelMenu->listMenu(),
        'options' => ['class' => 'form-control',  'prompt'=>'-- '.$modelForm->getAttributeLabel('id_group').' --']]);
        ?>
<?= $form->field($modelForm, 'parent')->element('ui\menu\SelectTree', 
        ['items'=> $modelMenu->listItems($modelForm->id_group, $modelForm->id_menu),
            'settings'=>['url'=>[['run'=>'parent', 'clear'=>'ajax']]], 'options' => ['class' => 'form-control'] ]);?>

<?php ui\form\FormBuilder::end();?>
<script>
    jQuery().ready(function(){
         $('#moving-id_group').change(function(){
            var $self = $(this)
            if($self.val() == ''){
                $('#moving-parent').find('option').filter(function(){return $(this).val() !== ''}).remove()
                $('#moving-parent').mazeSelectTree('update');
                return;
            }
           var url = cms.URI($('#menu-form-moving').attr('action'));
            $('#moving-parent').mazeSelectTree('load',{id_group:$self.val(), id_menu:url.getVar('id_menu')});
        })
    })
</script>