<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;

?>
<?php

$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#style-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('front')->element('ui\select\Chosen', ['items' => [Text::_("EXP_TEMPLATING_STYLE_FILTER_ADMIN_LABEL"), Text::_("EXP_TEMPLATING_STYLE_FILTER_SITE_LABEL")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('tmpname')->element('ui\tmp\Template', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->beginField('time_active'); ?>
        <?= Html::activeLabel($modelFilter, 'time_active', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_active[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->beginField('time_inactive'); ?>
        <?= Html::activeLabel($modelFilter, 'time_inactive', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'time_inactive[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>
<?php FilterBuilder::end(); ?>

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'style-grid'],
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("templating", "EDIT_STYLE")],
                ["name" => "menu", "width" => 20, "title" => "Действия", 'visible' => $this->access->roles("templating", "EDIT_STYLE")],
                ["name"=>"title", "title"=>Text::_("EXP_TEMPLATING_STYLE_TABLE_TITLE"), "index"=>"title", 
                    "hidefild"=>true, "width"=>200, "align"=>"left", "sorttable"=>true, "grouping"=>false],
                ["name"=>"name", "title"=>Text::_("EXP_TEMPLATING_STYLE_TABLE_TMPNAME"), "index"=>"name",
                    "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"home", "title"=>Text::_("EXP_TEMPLATING_STYLE_TABLE_HOME"), "index"=>"home",  "help"=>Text::_("EXP_TEMPLATING_STYLE_TABLE_HOME"),
                    "hidefild"=>true, "width"=>20, "align"=>"center"],
                ["name"=>"front_name", "title"=>Text::_("EXP_TEMPLATING_STYLE_TABLE_FRONT_ADMIN"), "index"=>"front", 
                    "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"time_active", "title"=>Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_TIMEACTIVE"), "index"=>"time_active", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>false],                
                ["name"=>"time_inactive", "title"=>Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_TIMEINACTIVE"), "index"=>"time_inactive", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>false],
                ["name"=>"id_tmp", "title"=>"ID", "index"=>"id_tmp", "hidefild"=>true, "width"=>80, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_tmp[]"
));
$items =  [
           ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id_tmp'=>'{++id_tmp++}']]), "title" => Text::_("EXP_TEMPLATING_TITLEMENU_EDIT")],               
           ["type" => 'link', "spriteClass" => 'menu-icon-copy', "href" =>Route::_([['run' => 'copy', 'id_tmp'=>['{++id_tmp++}']]]), "title" => Text::_("EXP_TEMPLATING_TITLEMENU_COPY")],
           ["type" => 'link', "spriteClass" => 'menu-icon-eye', 
                "actions"=>new JsExpression("function(target, e){"
                        . "var data = target.parents('tr').data('gridRow');"
                        . "window.open(cms.getURL([(data.front == 1 ? '/' : '/admin/'), {viewtmp:data.id_tmp, wid_view:1}]));"
                        . "}"),
                "title" => Text::_("EXP_TEMPLATING_TITLEMENU_VIEW")],
            
           
        ];
 if($this->access->roles("templating", "DELET_STYLE")){
     $items[] = ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" =>Route::_([['run' => 'delete', 'id_tmp'=>['{++id_tmp++}']]]), "title" => Text::_("EXP_TEMPLATING_TMP_FORM_JS_TREE_DELETE")];
 }
$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" =>$items]
]);


$grid->setPlugin("movesort", [
    "sorttable" =>false
]);
$grid->setPlugin("buttonfild", [
        "home" => [
            "spriteClass"=>["menu-icon-star", "menu-icon-star-empty"],
			"click"=>new JsExpression("function(e, type, row){				
				if(type == 'enable') return false;
                                var self = this;
                                $.get(cms.getURL([{run:'home', clear:'ajax', id_tmp:row.id_tmp}]), function(){
                                    self.mazeGrid('update');
                                }, 'json');							
				return true;
			}")
        ]
    ]);
ui\grid\MazeGrid::end();									 
?>		
