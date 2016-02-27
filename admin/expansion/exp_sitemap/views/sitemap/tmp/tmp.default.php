<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'sitemap-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" =>Text::_("EXP_SITEMAP_LABEL_SWITCH")],
                ["name" => "enable_xml", "title" => Text::_("EXP_SITEMAP_LABEL_ENABLE_XML"),"help" =>Text::_("EXP_SITEMAP_LABEL_ENABLE_XML"), "index" => "m.enable_xml", "width" => 20,  "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "enable_html", "title" => Text::_("EXP_SITEMAP_LABEL_ENABLE_HTML"),"help" =>Text::_("EXP_SITEMAP_LABEL_ENABLE_HTML"),  "index" => "m.enable_html", "width" => 20,  "align" => "center", "hidefild" => true, "sorttable" => true],
                ["name" => "menu", "title" => Text::_("EXP_SITEMAP_LABEL_ACTION"), "align" => "center", "width" => 20, "help" => Text::_("EXP_SITEMAP_LABEL_ACTION"), 'visible' => true],                
                ["name" => "title", "title" => Text::_("EXP_SITEMAP_LINK_LABEL_TITLE"), "index" => "m.title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "countlink", "title" => Text::_("EXP_SITEMAP_LABEL_COUNTLINK"), "width" => 50, "align" => "center", "hidefild" => true],
                ["name" => "visits_xml", "title" => Text::_("EXP_SITEMAP_LABEL_DATEVISITXML"), "width" => 150, "align" => "center", "hidefild" => true],
                ["name" => "visits_html", "title" => Text::_("EXP_SITEMAP_LABEL_DATEVISITHTML"), "width" => 150, "align" => "center", "hidefild" => true],
                ["name" => "sitemap_id", "title"=>"ID", "index" => "m.sitemap_id", "width" => 50, "align" => "сenter", "hidefild" => true, "sorttable" => true]
            ]
   ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "sitemap_id[]"
));

$grid->setPlugin("buttonfild", [
    "enable_xml" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), sitemap_id:[row.sitemap_id], type:'enable_xml', clear:'ajax'}]), function(){}, 'json');
		return true;
	}")
    ],
    "enable_html" => [
        "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
        "click" => new JsExpression("function(e, type, row){				
            var selfEl = this;
            $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), sitemap_id:[row.sitemap_id], type:'enable_html', clear:'ajax'}]), function(){}, 'json');		
            return true;
	}")
    ]
]);
$menu = [];
$menu[] = array("type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'sitemap_id' => '{++sitemap_id++}']]), "title" => Text::_("EXP_SITEMAP_EDIT"));
$menu[] = array("type" => 'link', "spriteClass" => 'menu-icon-refresh', "href" => Route::_([['run' => 'update', 'sitemap_id' => '{++sitemap_id++}']]), "title" => Text::_("EXP_SITEMAP_REINDEX"));
$menu[] =  array("type" => 'link', "spriteClass" => 'menu-icon-trash',"actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'sitemap_id' => ['{++sitemap_id++}']]]), "title" => Text::_("EXP_SITEMAP_DELETE"));
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