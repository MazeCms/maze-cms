<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use ui\filter\FilterBuilder;


$this->addStylesheet(RC::app()->getExpUrl("css/style.css"));	
$this->addScript(RC::app()->getExpUrl("js/user-table.js"));
?>
<?php
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#user-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('status')->element('ui\select\Chosen', ['items' => [Text::_("EXP_USER_FILTER_STATUS_LABLE_OFFLINE"), Text::_("EXP_USER_FILTER_STATUS_LABLE_ONLINE")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('bloc')->element('ui\select\Chosen', ['items' => [Text::_("EXP_USER_FILTER_ENABLE_LABEL_ACTIVE"), Text::_("EXP_USER_FILTER_ENABLE_LABEL_INACTIVE")],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
    <?= $filter->field('id_role', ['visible' => true])->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);?>
        <?= $filter->beginField('lastvisitDate'); ?>
        <?= Html::activeLabel($modelFilter, 'lastvisitDate', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'lastvisitDate[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'lastvisitDate[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
        <?= $filter->beginField('registerDate'); ?>
        <?= Html::activeLabel($modelFilter, 'registerDate', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'registerDate[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'registerDate[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>
<?php FilterBuilder::end(); ?>

<?php
$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'user-grid'],
            'model' => 'maze\table\Users',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("user", "EDIT_USER")],                
                ["name"=>"bloc", "title"=>Text::_("EXP_USER_TABLE_HEAD_ACTIVE"), "index"=>"u.bloc", 
                    "hidefild"=>true, "width"=>20, "align"=>"center", "sorttable"=>true, 'visible' => $this->access->roles("user", "EDIT_USER")],
                ["name" => "menu", "width" => 20, "title" => "Действия", 'visible' => $this->access->roles("user", "EDIT_USER")],
                ["name"=>"name", "title"=>Text::_("EXP_USER_TABLE_HEAD_NAME"), "index"=>"u.name",
                    "hidefild"=>true, "width"=>250, "align"=>"left", "sorttable"=>true, "grouping"=>false],
                ["name"=>"username", "title"=>Text::_("EXP_USER_TABLE_HEAD_LOGIN"), "index"=>"u.username", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>false],
                ["name"=>"role", "title"=>Text::_("EXP_USER_TABLE_HEAD_ROLE"), "index"=>"r.name", 
                    "hidefild"=>true, "width"=>150, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"status", "title"=>Text::_("EXP_USER_TABLE_HEAD_STATUS"), "index"=>"u.status", 
                    "hidefild"=>true, "width"=>100, "align"=>"center", "sorttable"=>true, "grouping"=>true],                
                ["name"=>"email", "title"=>"e-mail", "index"=>"u.email", "hidefild"=>true, "width"=>170, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false],
                ["name"=>"title", "title"=>Text::_("EXP_USER_TABLE_HEAD_LANG"), "index"=>"lang.title", 
                    "hidefild"=>true, "width"=>80, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"registerDate", "title"=>Text::_("EXP_USER_TABLE_HEAD_DATEREG"), "index"=>"u.registerDate", 
                    "hidefild"=>true, "width"=>220, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"lastvisitDate", "title"=>Text::_("EXP_USER_TABLE_HEAD_DATELAST"), "index"=>"u.lastvisitDate", 
                    "hidefild"=>true, "width"=>220, "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"id_user", "title"=>"ID", "index"=>"u.id_user", "hidefild"=>true, "width"=>80, 
                    "align"=>"center", "sorttable"=>true, "grouping"=>false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_user[]"
));


$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
           ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" =>Route::_([['run' => 'edit', 'id_user'=>'{++id_user++}']]), "title" => Text::_("EXP_USER_TITLEMENU_EDIT")],               
           ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" =>Route::_([['run' => 'delete', 'id_user'=>['{++id_user++}']]]), "title" => Text::_("EXP_USER_TITLEMENU_DEL")],
        ]]
]);

$grid->setPlugin("movesort", [
    "sorttable" =>false
]);
$grid->setPlugin("buttonfild", [
        "bloc" => [
            "spriteClass" => ["menu-icon-unlock", "menu-icon-lock"],
            "click" => new JsExpression("function (e, type, row){
                var selfEl = this;
                $.get(cms.getURL([{run:(type == 'enable' ? 'unpublish' : 'publish'), id_user:[row.id_user], clear:'ajax'}]), 
                function(){}, 'json');
		return true;
	}")
        ]
    ]);
ui\grid\MazeGrid::end();

?>

