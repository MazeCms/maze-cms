<?php

defined('_CHECK_') or die("Access denied");

class Contents_View_View extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        
        RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_VIEWS'];
 
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_VIEW_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>true,
            "HREF"=>[['run'=>'delete', 'expansion'=>$this->get('expansion')]] ,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#contents-view-grid', this.href)",
        ]);

    }

}

?>