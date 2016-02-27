<?php

use maze\helpers\Html;

$this->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
$this->addScript(RC::app()->getExpUrl("/js/install.js"));
ui\assets\AssetUpload::register();

$this->setLangTextScritp([
    'EXP_INSTALLAPP_INSTALL_ALERT_TITLE', 
    'EXP_INSTALLAPP_INSTALL_ALERT_ERROR_TYPE',
    'EXP_INSTALLAPP_INSTALL_STEPLOAD',
    'EXP_INSTALLAPP_INSTALL_OK',
    'EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA',
    'EXP_INSTALLAPP_INSTALL_UNZIP',
    'EXP_INSTALLAPP_INSTALL_FORM_ALERT_TITLE',
    'EXP_INSTALLAPP_CANCEL'
    ]);
$this->setTextScritp("				
    jQuery(document).ready(function(){	
       var installObj = new Install().init();
    })
");
?>
<div class="wrap-form">
<?= Html::beginForm([['run'=>'install']], 'post', ['class'=>'form', 'id'=>'form-install']); ?>

    <div class="form-group" id="content-file">
        <?php
        echo ui\checkbox\Toggle::element(["name" => "FormUpload[type]", "value" => "file", "id" => 'select-form', "settings" => [ "option" => [
                    'file' => '<span aria-hidden="true" class="glyphicon glyphicon-download-alt"></span> ' . Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADINPC"),
                    'url' => '<span aria-hidden="true" class="glyphicon glyphicon-cloud-download"></span> ' . Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADINURL"),
                    'path' => '<span aria-hidden="true" class="glyphicon glyphicon-hdd"></span> ' . Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADINSERV")
                    
        ]]]);
        ?>
    </div>

    <div class="form-group type-load" id="load-file">
        <div id="load-bloc-drop" class="load-bloc-drop">               
            <h3><?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADFILE_DRAG"); ?></h3>                 
            <div id="btn-add-file" class="btn btn-primary">
                <span aria-hidden="true" class="glyphicon glyphicon-download-alt"></span>
                <input id="hide-upload" type="file" name="FormUpload[file]" />
                <?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADFILE_BTN"); ?>
            </div>  
        </div>
    </div>

    <div class="form-group type-load hide-type" id="load-url">
        <label><?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADURL"); ?></label>
        <div class="input-group">
            <span class="input-group-addon"><span aria-hidden="true" class="glyphicon glyphicon-cloud-download"></span></span>
            <input type="text" class="form-control" name="FormUpload[url]"  placeholder="<?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADURL_PLACEHOLDER"); ?>">
        </div>
    </div>

    <div class="form-group type-load hide-type" id="load-path">
        <label><?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADPATH"); ?></label>
        <div class="input-group">
            <span class="input-group-addon"><span aria-hidden="true" class="glyphicon glyphicon-hdd"></span></span>
            <input type="text" class="form-control" name="FormUpload[path]"  placeholder="<?php echo Text::_("EXP_INSTALLAPP_INSTALL_FORM_LOADPATH_PLACEHOLDER"); ?>">
        </div>
    </div>
<?= Html::endForm(); ?> 

</div>
