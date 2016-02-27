<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;

?>
<?php

$filter = FilterBuilder::begin([
    'model' => $modelFilter,
    'onFilter' => 'function(form){$("#applications-lang-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
    'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
]);
?>

<div class="row">        
    <div class="col-md-6">        
        <?= $filter->field('enabled')->element('ui\select\Chosen', ['items' => [Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_DISABLE"), Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_ENABLE")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('front_back')->element('ui\select\Chosen', ['items'=>[Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_ADMIN"), Text::_("EXP_LANGUAGES_APP_FILTER_LABEL_SITE")], 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        <?= $filter->field('id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('name') ?>
        <?= $filter->field('type')->element('ui\select\Chosen', ['items' =>$model->getTypeApp(),
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
       
    </div>
</div>
<?php FilterBuilder::end(); ?>

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'applications-lang-grid'],
            'model' => 'maze\table\LangApp',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name"=>"enabled", "title"=>Text::_("EXP_LANGUAGES_TABLE_ENABLED"), "index"=>"lapp.enabled", 
                    "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true, 'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name"=>"defaults", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_DEFAULT"), "index"=>"lapp.defaults", 
                    "hidefild"=>true, "width"=>20, "align"=>"center", "sorttable"=>true, 'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name" => "menu", "width" => 20, "title" => "Действия",  'visible' => $this->access->roles("languages", "EDIT_LANG_APP")],
                ["name"=>"app_name", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_NAME"), "index"=>"app.name", "hidefild"=>true, "width"=>250, "align"=>"left", "sorttable"=>true, "grouping"=>true],
                ["name"=>"type", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_TYPE"), "index"=>"app.type", "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"front_back", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"), "index"=>"app.front_back", "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"title", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_LANG"), "index"=>"lang.title", "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>false],                
                ["name"=>"lang_code", "title"=>Text::_("EXP_LANGUAGES_APP_TABLE_CODELANG"), "index"=>"lang.lang_code", "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true, "grouping"=>true],
            ]
    ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_lang_app[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" =>Route::_([['run' => 'delete', 'id_lang_app'=>['{++id_lang_app++}']]]), "title" => Text::_("EXP_LANGUAGES_DEL")],
    ]]
]);

$grid->setPlugin("movesort", [
    "sorttable"=>false,
]);

$grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_lang_app:[row.id_lang_app], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ],
        "defaults"=>[
            "spriteClass"=>["menu-icon-star", "menu-icon-star-empty"],
            "click"=>new JsExpression("function(e, type, row){
                if(type == 'enable') return false;
                var selfEl = this;    
                $.get(cms.getURL([{run:'defaults', id_lang_app:row.id_lang_app, clear:'ajax'}]), 
                function(){selfEl.mazeGrid('update');}, 'json');
		return true;
            }")
	]
    ]);

ui\grid\MazeGrid::end();
?>
