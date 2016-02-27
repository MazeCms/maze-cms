<?php

use maze\base\JsExpression;
use maze\helpers\Html;

$this->setTextScritp("
$('#elfinder-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {profile_id:val.profile_id, sort:(i+1)+count}})
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap'=>\Document::DOCREADY]);
?>
<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'elfinder-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", "visible"=>$this->access->roles("elfinder", "EDIT_PROFILE")],
                ["name"=>"enabled", "title"=>Text::_("EXP_ELFINDER_ACTIVE"), "index"=>"ef.enabled", "width"=>20,
                    "align"=>"center", "hidefild"=>true, "sorttable"=>true, "visible"=>$this->access->roles("elfinder", "EDIT_PROFILE")],
                ["name" =>"menu", "width"=>20, "title"=>Text::_("EXP_ELFINDER_SORT"), "index"=>"ef.sort", 
                    "sorttable"=>true, "visible"=>$this->access->roles("elfinder", "EDIT_PROFILE")],
                ["name"=>"title", "title"=>Text::_("EXP_ELFINDER_PROFILE_TITLE"), "width"=>150, "align"=>"left", "hidefild"=>true],
                ["name"=>"roles", "title"=>Text::_("EXP_ELFINDER_MODEL_PRIFILE_ROLE"), "width"=>300, "align"=>"left",  "hidefild"=>true],                          
                ["name"=>"profile_id", "title"=>"ID", "index"=>"ef.profile_id", "width"=>50, "align"=>"center", "sorttable"=>true, "hidefild"=>true],
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "profile_id[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'profile_id'=>'{++profile_id++}']]), "title" => Text::_("EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT")],
           ["type" => 'link', "href" =>Route::_(['/admin/elfinder/path',['profile_id'=>'{++profile_id++}']]), "title" => Text::_("EXP_ELFINDER_DIR")],
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "href" =>Route::_([['run' => 'delete', 'profile_id'=>['{++profile_id++}']]]), "title" => Text::_("EXP_ELFINDER_SETTING_TOOLBAR_BTN_RM")]
           
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable"=> new JsExpression("function(options){return options.sortfild == 'ef.sort' && options.sortorder == 'asc' ? true : false}")
]);

$grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), profile_id:[row.profile_id], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);


ui\grid\MazeGrid::end();

?>

