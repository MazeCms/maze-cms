<?php

namespace admin\widgets\wid_breadcrumbs\ui;

use ui\Elements;
use maze\helpers\Html;

class Breadcrumbs extends Elements {

    public $options;
    public $items = [];

    public function init() {
        $this->items = array_filter($this->items, function($arr) {
            return isset($arr['visible']) ? $arr['visible'] : true;
        });
    }

    public function run() {

        if (empty($this->items))
            return '';
        $html = '<div id="breadcrumbs">';
        foreach ($this->items as $item) {
            if (!isset($item['label']))
                continue;
            $html .= '<div class="button-breadcrumbs">';
            $options = isset($item['options']) && is_array($item['options']) ?  $item['options'] : [];
            Html::addCssClass($options, 'label-breadcrumbs');
            if(isset($item['url']))
            {               
               $html .= Html::a(\Text::_($item['label']), $item['url'], $options); 
            }
            else
            {
                $html .= Html::tag('span', \Text::_($item['label']), $options);
            }
            
            $html .= '<span class="arrow-breadcrumbs"><span></span></span>';
            $html .= '</div>';
        }


        $html .= '</div>';
        
        return  $html;
    }

}
