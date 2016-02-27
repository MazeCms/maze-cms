<?php
use maze\helpers\Html;

$form = ui\form\FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'admin-desktop-form',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
echo $form->field($modelForm, 'title');
echo $form->field($modelForm, 'description')->textarea();
echo $form->field($modelForm, 'defaults')->element('ui\checkbox\Toggle', ['settings' => ['option' => ["0" => "EXP_ADMIN_NO", "1" => "EXP_ADMIN_YES"]]]);

?>
<div class="form-group">
    <?php echo Html::activeLabel($modelForm, "colonum"); ?>
    <div class="colonum-desktop"></div>
    <?php echo Html::activeHiddenInput($modelForm, "colonum"); ?>
</div>       
<div class="form-group size-colonum-group">
    <?php if ($modelForm->width): ?>
        <?php $i=0; foreach ($modelForm->width as $w): $i++?>
            <div class="form-group form-inline colonum-width">
                <label class="control-label"><?php echo Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_COLONUM") ?> - <?php echo $i; ?></label>            
                <div class="input-group">
                    <input type="text" value="<?php echo $w ?>"  class="form-control size-colonum" name="Desktop[width][]" ><span class="input-group-addon">%</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="form-group form-inline colonum-width">
            <label class="control-label"><?php echo Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_COLONUM") ?> - 1</label>            
            <div class="input-group">
                <input type="text" class="form-control size-colonum" name="Desktop[width][]"><span class="input-group-addon">%</span>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php ui\form\FormBuilder::end(); ?>