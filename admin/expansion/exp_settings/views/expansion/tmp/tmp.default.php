<?php

use maze\base\JsExpression;
use maze\helpers\Html;

?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'settings-expansion-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель"],
                ["name"=>"enabled", "title"=>Text::_("EXP_SETTINGS_ENABLED"), "index"=>"e.enabled", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],
                ["name" => "menu", "width" => 20, "title" => Text::_("EXP_SETTINGS_ACTIONS")],
                ["name"=>"title", "title"=>Text::_("EXP_SETTINGS_EXT_NAME"), "hidefild"=>true, "width"=>150, "align"=>"left"],
                ["name"=>"description", "title"=>Text::_("EXP_SETTINGS_EXT_DES"),  "hidefild"=>true, "width"=>300, "align"=>"left"],                          
                ["name"=>"id_exp", "title"=>"ID", "index"=>"e.id_exp", "hidefild"=>true, "width"=>50, "align"=>"center", "sorttable"=>true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_exp[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-wrench', "href" =>Route::_([['run' => 'edit', 'id_exp'=>'{++id_exp++}']]), "title" => Text::_("EXP_SETTINGS_EDIT")],
           ["type" => 'link', "spriteClass" => 'menu-icon-refresh', "href" =>Route::_([['run' => 'refresh', 'id_exp'=>['{++id_exp++}']]]), "title" => Text::_("EXP_SETTINGS_EXPANSION_BTN_DEFAULT")],
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" =>Route::_([['run' => 'clear', 'id_exp'=>['{++id_exp++}']]]), "title" => Text::_("EXP_SETTINGS_EXPANSION_BTN_CLEARCACHE")]
           
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
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_exp:[row.id_exp], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);


ui\grid\MazeGrid::end();

?>

