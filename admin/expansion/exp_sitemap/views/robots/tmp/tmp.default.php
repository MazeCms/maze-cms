<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'sitemap-robots-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_SITEMAP_LABEL_SWITCH")],
                ["name" => "menu", "title" => Text::_("EXP_SITEMAP_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_SITEMAP_LABEL_ACTION"), 'visible' => true], 
                ["name" => "images", "title" => Text::_("EXP_SITEMAP_ROBOTS_LABEL_IMAGES"), "width" => 50, "align" => "center", "hidefild" => true],
                ["name" => "title", "index" => "title", "title" => Text::_("EXP_SITEMAP_LINK_LABEL_TITLE"),  "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],                
                ["name" => "search", "index" => "search", "title" => Text::_("EXP_SITEMAP_ROBOTS_LABEL_SEARCH"), "width" => 150, "align" => "center",  "sorttable" => true, "hidefild" => true],
                ["name" => "robots_id", "index" => "robots_id", "title"=>"ID",  "width" => 50, "align" => "сenter", "hidefild" => true, "sorttable" => true]
            ]
   ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "robots_id[]"
));

$menu = [];
$menu[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'robots_id' => '{++robots_id++}']]), "title" => Text::_("EXP_SITEMAP_EDIT"));
$menu[] =  array("type" => 'link', "spriteClass" => 'menu-icon-trash',"actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'robots_id' => ['{++robots_id++}']]]), "title" => Text::_("EXP_SITEMAP_DELETE"));
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => $menu]
]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>