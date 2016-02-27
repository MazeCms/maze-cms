<?php

defined('_CHECK_') or die("Access denied");

use maze\table\FieldExp;
use maze\helpers\Json;
use maze\fields\FieldHelper;
use maze\table\ContentType;

class Contents_View_View extends View {

    public function registry() {
        

        $modelView = $this->get('modelView');
        $field = $this->get('field');
        $type = ContentType::findOne(['expansion' => $modelView->expansion, 'bundle' => $modelView->bundle]);

        RC::app()->breadcrumbs = ['label' => 'EXP_CONTENTS_VIEWS', 'url' => ['/admin/contents/view']];
        if($modelView->expansion == 'dictionary'){
            RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_CATALOG', 'url'=>['/admin/contents/view', ['run'=>'catalog']]];
        }else{
            RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_NAME', 'url'=>['/admin/contents/view']];
        }
        RC::app()->breadcrumbs = ['label' => $type->title, 'url'=>[['run'=>'edit', 'bundle'=>$modelView->bundle, 'mode'=>$modelView->mode, 'expansion'=>$modelView->expansion]]];
        RC::app()->breadcrumbs = ['label' => $field->title];
        
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#contents-field-view-settings')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#contents-field-view-settings')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#contents-field-view-settings', {action:'saveClose'})"
                ]
            ]
        ]);
        
        $url = [['run'=>'edit', 'bundle'=>$modelView->bundle, 'mode'=>$modelView->mode, 'expansion'=>$modelView->expansion]];
        
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>$url,
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
    }

}

?>