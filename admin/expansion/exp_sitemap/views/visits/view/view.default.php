<?php

defined('_CHECK_') or die("Access denied");
use admin\expansion\exp_sitemap\table\Sitemap;

class Sitemap_View_Visits extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        RC::app()->breadcrumbs = ['label' => "EXP_SITEMAP_VISITS"];
        
        $items = [];
        $listMap = $this->get('listMap');
        foreach($listMap as $id=>$m){
            $items[]= [
                    "class" => 'ContextMenu',
                    "HREF"=>[['run'=>'delete', 'sitemap_id'=>$id, 'type'=>'xml']],
                    "TITLE" => $m.' - XML',
                ];
            $items[]= [
                    "class" => 'ContextMenu',
                    "HREF"=>[['run'=>'delete', 'sitemap_id'=>$id, 'type'=>'html']],
                    "TITLE" => $m.' - HTML',
                ];
        }
   
                
        $toolbar->addGroup('block', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>true,            
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "MENU" =>$items
        ]);
        
    }

}

?>