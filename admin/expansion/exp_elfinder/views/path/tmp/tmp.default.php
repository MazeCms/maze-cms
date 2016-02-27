<?php

use maze\base\JsExpression;
use maze\helpers\Html;

$this->setTextScritp("
$('#elfinder-dir-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {path_id:val.path_id, sort:(i+1)+count}})
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap'=>\Document::DOCREADY]);
?>

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'elfinder-dir-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель"],
                ["name" =>"menu", "width"=>20, "title"=>Text::_("EXP_ELFINDER_SORT"), "index"=>"sort", "sorttable"=>true],
                ["name"=>"path", "title"=>Text::_("EXP_ELFINDER_DIR_PATH"), "width"=>150, "align"=>"left", "index"=>"path", "sorttable"=>true,  "hidefild"=>true],
                ["name"=>"alias", "title"=>Text::_("EXP_ELFINDER_DIR_ALIAS"), "width"=>300, "align"=>"left",  "index"=>"alias", "sorttable"=>true, "hidefild"=>true],                          
                ["name"=>"path_id", "title"=>"ID", "index"=>"path_id", "width"=>50, "align"=>"center", "sorttable"=>true, "hidefild"=>true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "path_id[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'path_id'=>'{++path_id++}', 'profile_id'=>'{++profile_id++}']]), "title" => Text::_("EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT")],
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" =>Route::_([['run' => 'delete', 'path_id'=>['{++path_id++}'],  'profile_id'=>'{++profile_id++}']]), "title" => Text::_("EXP_ELFINDER_SETTING_TOOLBAR_BTN_RM")]
           
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable"=> new JsExpression("function(options){return options.sortfild == 'sort' && options.sortorder == 'asc' ? true : false}")
]);

ui\grid\MazeGrid::end();

?>

