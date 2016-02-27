<?php

defined('_CHECK_') or die("Access denied");
use maze\table\FieldExp;
use maze\helpers\Json;
use maze\fields\FieldHelper;
use maze\table\ContentType;

class Contents_View_View extends View {

    public function registry() {

        $bundle = $this->get('bundle');
        $mode = $this->get('mode');
        $expansion = $this->get('expansion');
        $type = ContentType::findOne(['expansion'=>$expansion, 'bundle'=>$bundle]);
        
        RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_VIEWS', 'url'=>['/admin/contents/view']];
        if($expansion == 'dictionary'){
            RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_CATALOG', 'url'=>['/admin/contents/view', ['run'=>'catalog']]];
        }else{
            RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_NAME', 'url'=>['/admin/contents/view']];
        }
        RC::app()->breadcrumbs = ['label'=>$type->title];
        $toolbar = RC::app()->toolbar;
        
        $fields = FieldExp::find()
                ->from(['fe'=>FieldExp::tableName()])
                ->joinWith('typeFields')
                ->where(['fe.expansion' => $expansion, 'fe.bundle'=>$bundle, 'fe.active'=>1])->orderBy('fe.sort')->all();
        
        $items = [];
        foreach($fields as $k=>$field){
           $items[] = [
                    "class" => 'ContextMenu',
                    "TITLE" => $field->title,
                    "SORT" => $k,
                    "HREF"=>[['run'=>'addField', 'expansion'=> $field->expansion, 'bundle'=> $field->bundle, 'field_exp_id'=>$field->field_exp_id, 'mode'=>$mode,]],
                    "ACTION" => "return this;"
                ];
        }
     
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_VIEW_FIELD_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 20,
            "VISIBLE" => true,
            "SORTGROUP" => 8,
            "SRC" => "/library/jquery/toolbarsite/images/icon-plus.png",
            "MENU"=>$items
        ]);
        
        $url = $expansion == 'dictionary' ? ['/admin/contents/view', ['run'=>'catalog']] : ['/admin/contents/view'];
        
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
        
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_VIEW_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>true,
            "HREF"=>[['run'=>'fieldDelete', 'bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#contents-view-type-grid', this.href)",
        ]);
        
         $toolbar->addGroup('contentsedit', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish', 'bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-view-type-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish',  'bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-view-type-grid', this.href)"
                ]
            ]
        ]);
        


    }

}

?>