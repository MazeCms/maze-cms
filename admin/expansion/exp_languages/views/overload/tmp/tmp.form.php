<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\form\FormBuilder;
?>
<script>
function languagesOverloadSearch(){
    
    $('#applications-ovreloadsearch-grid').mazeGrid('update',{search:$('#search-overload-text').val()}, true);
    return false;
}

jQuery().ready(function(){
    
    $('#search-overload-text').on('keyup', function(e){
        if(e.keyCode == 13){
           languagesOverloadSearch();
        }
    })
    
    $('#applications-ovreloadsearch-grid').bind('afterGetContent.mazegrid', function (e) {
       $(this).find('.maze-grid-content tbody tr').click(function(){
            var data = $(this).data('gridRow');
            if(typeof data == 'object'){
                $('#formoverload-constant').val(data.constant);
                $('#formoverload-value').val(data.value);
                $('#formoverload-id_lang').val(data.id_lang).trigger("chosen:updated");;
                $('#formoverload-front').val(data.front).trigger("chosen:updated");;
            }
       })
    })
    
})
</script>
<style>
    #applications-ovreloadsearch-grid .maze-grid-content tr:hover{
        background-color: #F9E795;
        cursor: pointer;
    }
</style>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'languages-overload-form',
                'groupClass' => 'form-group has-feedback',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
                'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
                'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
                'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
                'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
    ]);
    ?>
    <?php if ($modelForm->scenario == 'create'): ?>
        <?=
        $form->field($modelForm, 'front')->element('ui\select\Chosen', ['items' => [Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN"), Text::_("EXP_LANGUAGES_APP_TABLE_SITE")],
            'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('front') . ' --']]);
        ?>
        <?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]); ?>
    <?php endif; ?>
    <?= $form->field($modelForm, 'constant')->textInput(['disabled' => ($modelForm->scenario !== 'create')]); ?>
    <?= $form->field($modelForm, 'value')->textarea(); ?>


    <?php if ($modelForm->scenario == 'create'): ?>
    <div class="form-group">
        <label><?= Text::_("EXP_LANGUAGES_OVERLOAD_FORM_FIELDSET_SEARCH") ?></label>
        <div class="input-group">
            <input type="text" class="form-control" id="search-overload-text" placeholder="<?= Text::_("EXP_LANGUAGES_PACKS_SEARCH_PLACEHOLDER") ?>">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button" onclick="return languagesOverloadSearch()"><span aria-hidden="true" class="glyphicon glyphicon-search"></span></button>
            </span>
        </div>

    </div>
    <?php
    $grid = ui\grid\MazeGrid::begin([
                'settings' => ['id' => 'applications-ovreloadsearch-grid'],
                'url' => [['run' => 'search']],
                'colModel' => [
                    ["name" => "constant", "title" => Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"), "index" => "lca.constant", "hidefild" => true, "width" => 150, "align" => "left", "sorttable" => true, "grouping" => false],
                    ["name" => "value", "title" => Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL"), "index" => "lca.value", "hidefild" => true, "width" => 250, "align" => "left", "sorttable" => true, "grouping" => true],
                    ["name" => "title", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_LANG"), "index" => "lca.id_lang", "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
                    ["name" => "app_name", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_NAME"), "index" => "app.name", "hidefild" => true, "width" => 80, "align" => "left", "sorttable" => true, "grouping" => true],
                    ["name" => "type", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_TYPE"), "index" => "app.type", "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
                    ["name" => "front_back", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"), "index" => "app.front_back", "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
                ]
    ]);

    $grid->setPlugin("movesort", [
        "sorttable" => false,
    ]);

    ui\grid\MazeGrid::end();
    ?>
    <?php endif; ?>
</div>
<?php ui\form\FormBuilder::end(); ?>  