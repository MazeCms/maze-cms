<?php
use maze\base\JsExpression;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use ui\grid\MazeGrid;
use maze\helpers\Json;

$this->addScript(RC::app()->getExpUrl("/js/uninstall.js"));
$message = [
    'title_uninstall'=>Text::_("EXP_INSTALLAPP_UNINSTALL_ALERT_TITLE"),
    'title_error'=>Text::_("EXP_INSTALLAPP_UNINSTALL_ALERTERR_TITLE"),
    'error_server'=>Text::_("EXP_INSTALLAPP_UNINSTALL_ERRSERVER"),
    'error_uninstall'=>Text::_("EXP_INSTALLAPP_UNINSTALL_ERRDATA"),
    'cancel'=>Text::_("EXP_INSTALLAPP_CANCEL"),
    'uninstall_ok'=>Text::_("EXP_INSTALLAPP_UNINSTALL_OK"),
    'close'=>Text::_("LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON"),
    'ok'=>Text::_("LIB_USERINTERFACE_TOOLBAR_PACK_SEND"),
    'title'=>Text::_("LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE"),
    'text'=>Text::_("LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT"),
    'start_text'=>'Секундочку.... дай подумать'
];
$this->setTextScritp("var UN = new Uninstall(".Json::encode(['message'=>$message]).");");
$this->setTextScritp("    
    $('#installapp-grid').bind('sortrowupdate.mazegrid', function(e, obj){
    var options = $(this).mazeGrid('getoptionsall');
    var \$self = $(this);
    var page = Number(options.page); 
    var rowNum = Number(options.rowNum);
    var count = page == 1 ? 0 : rowNum*page;
    var param =	$.map(obj.group, function(val, i){return {id_app:val.id_app, ordering:(i+1)+count}})       
    $.get(cms.getURL([{run:'sort', clear:'ajax'}]), {sort:param}, function(data){},'json');
})					
", ['wrap'=>\Document::DOCREADY]);       
?>

<?php
$filter = FilterBuilder::begin([
            'model' => $modelFilter,
            'onFilter' => 'function(form){$("#installapp-grid").mazeGrid("update", $(form).serializeObject(),true); return false;}',
            'onReset' => 'function(form){$(form).find("select > option").removeAttr("selected"); $(form).find("select").trigger("liszt:updated")}'
        ]);
?>
<div class="row">        
    <div class="col-md-6">
        <?= $filter->field('front_back')->element('ui\select\Chosen', ['items' => [Text::_('EXP_INSTALLAPP_FILTER_FRONT_ADMIN'), Text::_('EXP_INSTALLAPP_FILTER_FRONT_SITE')],
            'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?= $filter->field('type')->element('ui\type\AppList', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
    </div>
    <div class="col-md-6">
        <?= $filter->field('name'); ?>
        <?= $filter->beginField('install_data'); ?>
        <?= Html::activeLabel($modelFilter, 'install_data', ['class' => 'control-label']); ?>
        <div class="input-group">
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'install_data[0]', 'options' => ['class' => 'form-control']]); ?>
            <span class="input-group-addon" id="basic-addon1">-</span>
            <?= ui\date\Datepicker::element(['model' => $modelFilter, 'attribute' => 'install_data[1]', 'options' => ['class' => 'form-control']]); ?>
        </div>
        <?= $filter->endField(); ?>
    </div>
</div>

<?php FilterBuilder::end(); ?>

<?php

$grid = MazeGrid::begin([
            'settings' => ['id' => 'installapp-grid'],
            'colModel' => [
                ["name"=>"ordering", "index"=>"ge.ordering, ia.ordering", "title"=>"Сортировка", "align"=>"center", "width"=>20, "sorttable"=>true, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_DES")],
                ["name"=>"deletes", "title"=>"Удалить", "index"=>"deletes", "hidefild"=>true, "width"=>40, "align"=>"center"],
                ["name"=>"title", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_NAME"), "hidefild"=>true, "width"=>250,  "align"=>"left"],
                ["name"=>"name", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_SYSNAME"), "index"=>"ia.name", "hidefild"=>true, "width"=>80, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_TOOLTIP"), "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"front", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_FRONT"), "index"=>"ia.front_back", "hidefild"=>true, "width"=>80, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_TOOLTIP"), "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"type_name", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_TYPE"), "index"=>"ia.type", "hidefild"=>true, "width"=>80, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_TOOLTIP"), "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"version", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_VER"), "hidefild"=>true, "width"=>80, "align"=>"center"],
                ["name"=>"install_data", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_DATE"), "index"=>"ia.install_data", "hidefild"=>true, "width"=>200, "help"=>Text::_("LIB_USERINTERFACE_TOOLBAR_SORT_TOOLTIP"), "align"=>"center", "sorttable"=>true, "grouping"=>true],
                ["name"=>"author", "title"=>Text::_("EXP_INSTALLAPP_TABLE_HEAD_AUTOR"), "hidefild"=>true, "width"=>250, "align"=>"center"],
                ["name"=>"id_app", "title"=>"ID", "index"=>"id_app", "hidefild"=>true, "width"=>50,  "align"=>"center", "sorttable"=>true, "grouping"=>false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_app[]"
));


$grid->setPlugin("movesort", [
    "sorttable" => new JsExpression("function(options){return options.sortfild == 'ge.ordering, ia.ordering' && options.sortorder == 'asc' ? true : false}"),
    "sortgroup"=>["type", "front_back", "group_name"],
    "handle"=>".sort-icon-handle"
   
]);

$grid->setPlugin("buttonfild", [
    "deletes" => [
        "spriteClass" => ["menu-icon-trash", "menu-icon-lock"],
        "click" => new JsExpression("function (e, type, row){
            if(row.deletes == 0) return false;
            UN.delete(row.id_app);
            return false;
    }")
    ]
]);

$grid->setPlugin("tooltip_content", [
    "filds" => [
        "title" => [
            Text::_("EXP_INSTALLAPP_HELPAPP_DES")=>"description",
            Text::_("EXP_INSTALLAPP_HELPAPP_LIC")=>"license",
            Text::_("EXP_INSTALLAPP_HELPAPP_DATE")=>"created",
            Text::_("EXP_INSTALLAPP_HELPAPP_COPY")=>"copyright",
            "e-mail"=>"email",
            Text::_("EXP_INSTALLAPP_SITE")=>"siteauthor"
        ]
    ]
]);

ui\grid\MazeGrid::end();
?>