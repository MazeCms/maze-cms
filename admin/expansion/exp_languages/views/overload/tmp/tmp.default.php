
<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;

$this->addScript(RC::app()->getExpUrl("/js/packs.js"));
?>

<?php
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#overload-languages-grid").mazeGrid("update", $(form).serializeObject(), true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>

<div class="row">        
    <div class="col-md-6">        
        <?= $filter->field('front')->element('ui\select\Chosen', ['items' => [Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_ADMIN"), Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_SITE")], 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        <?= $filter->field('id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('constant') ?>
        <?= $filter->field('constValue') ?>       
    </div>
</div>
<?php FilterBuilder::end(); ?>
<blockquote id="applications-pack-indexlang" style="display: none">
    <p><?=Text::_("EXP_LANGUAGES_PACKS_INDEXLANG")?></p>
    <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            0%
        </div>
    </div>
    <footer><?=Text::_("EXP_LANGUAGES_PACKS_INDEXSEARCH")?></footer>
</blockquote>

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'overload-languages-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name" => "menu", "width" => 20, "title" => "Действия", 'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name" => "constant", "title" => Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"), "index" => "lo.constant", "hidefild" => true, "width" => 150, "align" => "left", "sorttable" => true, "grouping" => false],
                ["name" => "value", "title" => Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL"), "index" => "lo.value", "hidefild" => true, "width" => 250, "align" => "left", "sorttable" => true, "grouping" => true],
                ["name" => "title", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_LANG"), "index" => "lo.id_lang", "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "front", "title" => Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"), "index" => "lo.front", "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => true, "grouping" => true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
             ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id'=>'{++id++}']]), "title" => Text::_("EXP_LANGUAGES_EDIT")],           
            ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" => Route::_([['run' => 'delete', 'id' => ['{++id++}']]]), "title" => Text::_("EXP_LANGUAGES_DEL")],
        ]]
]);

$grid->setPlugin("movesort", [
    "sorttable" => false,
]);
$grid->setPlugin("tooltip_content", [
    "template" => "<div>{VALUE}</div>",
    "filds" => [
        "constant" => ['' => "constant"]
    ]
]);
ui\grid\MazeGrid::end();
?>

