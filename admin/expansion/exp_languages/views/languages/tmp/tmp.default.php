<?php

use maze\base\JsExpression;
use maze\helpers\Html;

$this->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'languages-grid'],
            'model' => 'maze\table\Languages',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("languages", "EDIT_LANG")],
                ["name"=>"enabled", "title"=>Text::_("EXP_LANGUAGES_TABLE_ENABLED"), "index"=>"enabled", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],
                ["name" => "menu", "width" => 20, "title" => "Действия",  'visible' => $this->access->roles("languages", "EDIT_LANG")],
                ["name"=>"title", "title"=>Text::_("EXP_LANGUAGES_TABLE_TITLE"), "index"=>"title", "hidefild"=>true, "width"=>250, "align"=>"left", "sorttable"=>true],
                ["name"=>"lang_code", "title"=>Text::_("EXP_LANGUAGES_TABLE_LANGCODE"), "index"=>"lang_code", "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true],
                ["name"=>"reduce", "title"=>Text::_("EXP_LANGUAGES_TABLE_REDUCE"), "index"=>"reduce", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],
                ["name"=>"img", "title"=>Text::_("EXP_LANGUAGES_TABLE_ICON"), "index"=>"img", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],                
                ["name"=>"id_lang", "title"=>"ID", "index"=>"id_lang", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_lang[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id_lang'=>'{++id_lang++}']]), "title" => Text::_("EXP_LANGUAGES_EDIT")],           
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" =>Route::_([['run' => 'delete', 'id_lang'=>['{++id_lang++}']]]), "title" => Text::_("EXP_LANGUAGES_DEL")],
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
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_lang:[row.id_lang], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);


ui\grid\MazeGrid::end();

?>
