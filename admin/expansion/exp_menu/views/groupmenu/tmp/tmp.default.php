<?php

use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;

$this->setTextScritp("
$('#groupmenu-grid').bind('move.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = obj.parent == 0 || page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.order, function(val, i){return {id_menu:val.id_menu, ordering:(i+1)+count}})    
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param, id_menu:obj.id, parent:obj.parent}, function(data){},'json');
})					
", ['wrap' => Document::DOCREADY]);
?>
<script>
    function redirectMenuItems(el, e){
        var $target = $(el).parents('tr')
        var url = [];
        function getParentAlias($el){
            
            if($el.attr('data-parent')){
                var newEl = $el.closest('.maze-grid-content').find('tr[data-id='+$el.attr('data-parent')+']');
                if(newEl.is('tr')){
                    getParentAlias(newEl);
                }
            }
           url.push($el.data('gridRow').link);
        }
        getParentAlias($target);
        window.open('/'+url.join('/'));
        return false;
    }
</script>
<?= $this->render('filter', ['modelFilter'=>$modelFilter, 'model'=>$model, 'tableId'=>$tableId]);?>

<?php
/**
 * Таблица
 */
$grid = MazeGrid::begin([
            'settings' => ['id' => 'groupmenu-grid'],
            'filterData' => new JsExpression("function(data){return data.html}"),
            'model' => 'maze\table\Menu',
            'mode' => 'tree',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("menu", "EDIT_ITEM")],
                ["name" => "enabled", "index" => "m.enabled", "hidefild" => true, "width" => 20,
                    "help" => Text::_("EXP_MENU_TABLE_ENABLED_DES"), "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "ordering", "index" => "m.ordering", "align" => "center", "width" => 20,
                    "sorttable" => true, "help" => Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_DES"), 'visible' => $this->access->roles("menu", "EDIT_ITEM")],
                ["name" => "home", "index" => "m.home", "hidefild" => true,
                    "width" => 30, "help" => Text::_("EXP_MENU_VIEW_GROUP_TABL_HOME_TITLE"), "align" => "center", "sorttable" => true, "grouping" => true],
                ["name" => "name", "index" => "m.name", "hidefild" => true, "width" => 200, "align" => "left", "sorttable" => true, "grouping" => false],
                ["name" => "id_group", "index" => "m.id_group", "align" => "center", "width" => 150, "sorttable" => true],
                ["name" => "alias", "index" => "route.alias", "hidefild" => true, "width" => 200, "align" => "center", "sorttable" => true],
                ["name" => "size", "title" => Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_TITLE"), "hidefild" => true, "width" => 30,
                    "align" => "center", "sorttable" => false, "grouping" => true],
                ["name" => "lang", "title" => Text::_("EXP_MENU_ADD_ITEM_FORM_LANG"), "index" => "m.id_lang", "hidefild" => true,
                    "width" => 100, "align" => "center", "sorttable" => true],
                ["name" => "title_role", "title" => Text::_("EXP_MENU_TABLE_ACCSE_TITLE"),
                    "index" => "r.name", "hidefild" => true, "width" => 100, "align" => "center", "sorttable" => true],
                ["name" => "id_menu", "title" => "ID", "index" => "m.id_menu", "hidefild" => true,
                    "width" => 100, "help" => Text::_("EXP_MENU_TABLE_ID_DES"), "align" => "center", "sorttable" => true]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_menu[]"
));


$grid->setPlugin("contextmenu", [
    "ordering" => [
        "items" => ".menu-icon-handle",
        "data" => [
            ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'id_menu' => '{++id_menu++}']]), "title" => Text::_("EXP_MENU_TITLEMENU_EDIT")],
            ["type" => 'link', "spriteClass" => 'menu-icon-copy', "href" => Route::_([['run' => 'copy', 'id_menu' => ['{++id_menu++}']]]), "title" => Text::_("EXP_MENU_TITLEMENU_COPY")],
            ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'id_menu' => ['{++id_menu++}']]]), "title" => Text::_("EXP_MENU_TITLEMENU_DEL")],
            ["type" => 'link', "spriteClass" =>"menu-icon-eye", "href" =>"#", "title" => Text::_("EXP_MENU_MENU_VIEWLINK"), "actions"=>new JsExpression("redirectMenuItems")]
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'm.ordering' && options.sortorder == 'asc' ? true : false}"),
    "move" => ["fildkey" => "typetree", "accept" => ["root" => ["article"]]],
]);
if ($this->access->roles("menu", "EDIT_ITEM")) {
    $grid->setPlugin("buttonfild", [
        "enabled" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_menu:[row.id_menu], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ],
        "home" => [
            "spriteClass" => ["menu-icon-home-plus", "menu-icon-home-minus"],
            "click" => new JsExpression("function(e, type, row){
				if(row.typeLink == 'separator' ) return false;
				var selfEl = this;
				$.get(cms.getURL([{run:'home', id_menu:row.id_menu, clear:'ajax'}]), function(){
                                    selfEl.mazeGrid('update');
                                }, 'json');		
				return true;
			}")
        ]
    ]);
}
$grid->setPlugin("tooltip_content", array(
    "filds" => array(
        "name" => array(
            Text::_("EXP_MENU_ITEMS_PARAMS_VIEW") => "paramLink"
        )
    )
));

$grid->setPlugin("tree", [
    "id" => "id_menu",
    "parent" => "parent",
    "target" => "name",
    "fild_type" => "typetree",
    "icon" => ["article" => "/library/image/icons/blue-folder-horizontal.png"],
    "is_child" => true,
    "json" => [
        "param" => new JsExpression("function(id, elem, row){return {parent:row.id_menu} }"),
        "data" => new JsExpression("function(data){ return data.html !== null ? data.html.data : null}")
    ]
]);
ui\grid\MazeGrid::end();
?>
