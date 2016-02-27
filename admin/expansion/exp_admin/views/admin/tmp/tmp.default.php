<?php
$this->setLangTextScritp([
    'LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE',
    'LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT',
    'LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON',
    'LIB_USERINTERFACE_TOOLBAR_PACK_SEND',
    'EXP_ADMIN_FORM_ADDDESCKTOP_CHECK_NAME',
    'EXP_ADMIN_ERROR_SERVER',
    'EXP_ADMIN_FORM_ADDDESKTOP_ALERT',
    'EXP_ADMIN_FORM_EDITDESKTOP_ALERT',
    'EXP_ADMIN_SAVE',
    'EXP_ADMIN_FORM_SETTINGSGADGET_ALERT',
    'EXP_ADMIN_TITLE',
    'EXP_ADMIN_ADDGADGET'
]);

$this->addStylesheet(RC::app()->getExpUrl('css/style.css'));
$this->addScript(RC::app()->getExpUrl('js/admin-desktop.js'));
?>
<?php if($this->access->roles("admin", "ADD_DESKTOP")):?>
<script>
    $(document).ready(function(){
       adminBar.sortGadgets('.desktop-gadgets td')
    })
</script>
<?php endif;?>
<?php if($desktop):?>
<div class="filter-wrapp filter-wrapp-view-contents">
    <ul class="filter-form-tabs-link">
        <?php foreach ($desktop as $des): ?>        
        <li<?= $des->defaults == 1 ? ' class="active"' : ''; ?>><a href="<?= Route::_(['/admin', ['run'=>'defaultsDesktop', 'id'=>$des->id_des]]); ?>"><?= $des->title; ?></a></li>        
        <?php endforeach; ?>
    </ul>
    <div class="wrap-eidit-desktop">
    <?php if($this->access->roles("admin", "ADD_DESKTOP")):?>
    <a class="btn btn-default edit-desktop" href="<?=Route::_([['run'=>'editDesktopFrom', 'id_des'=>$id_des]])?>" role="button" onclick="return adminBar.desktopForm(this);"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span></a>
    <?php endif;?>
    <div class="desktop-gadgets-wrap">    
    <table class="desktop-gadgets">
            <tbody>
                <tr>
                <?php for ($i = 0; $i < $marking['colonum']; $i++): ?>                    
                    <td style="width:<?= $marking['width'][$i]; ?>%;" class="colonum-gadget">
                            <?php if (isset($gadgets[$i])): ?>
                                <?php foreach ($gadgets[$i] as $gad): ?>
                                    <div class="panel panel-default gadget-box" data-id_gad="<?=$gad->id_gad?>">
                                        <div class="panel-heading">
                                            <?=$gad->title;?> 
                                            <div class="button-edit-gadget">
                                                <?php if($this->access->roles("admin", "DELETE_GADGET")):?>
                                                <a href="<?=Route::_([['run'=>'deleteGadget', 'id_gad'=>$gad->id_gad]])?>" onclick="return adminBar.deleteGadget(this, '.gadget-box');"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></a>
                                                <?php endif;?>
                                                <?php if($this->access->roles("admin", "SETTINGS_GADGET")):?>
                                                <a href="<?=Route::_([['run'=>'settingsGadget', 'id_gad'=>$gad->id_gad]])?>" onclick="return adminBar.editGadget(this);"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span></a>
                                                <?php endif;?>
                                            </div>                                            
                                        </div>
                                        <div class="panel-body"><?= $model->getContentGadget($gad); ?></div>
                                    </div>
                                <?php endforeach; ?>  
                            <?php endif; ?>
                        </td>                    
                <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>
<?php endif;?>

