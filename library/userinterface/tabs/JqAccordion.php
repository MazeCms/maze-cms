<?php

namespace ui\tabs;

use ui\Elements;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

class JqAccordion extends Elements {

    public $options = [];
    public $settings = [];
    protected $_tabs;
    protected $count = 0;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
        $this->options = array_merge(['class'=>'admin-accordion-default'], $this->options);
       
        $this->settings = array_merge([
            'heightStyle'=>'content'
        ],$this->settings);
        
        echo Html::beginTag('div', $this->options);
    }
    
    public function beginTab($title, $options = [])
    {
        $this->count++;
        $this->_tabs[$this->count] = ['title'=>$title, 'options'=>$options];
        ob_start();
    }
    
    
    public function endTab()
    {        
        $content = ob_get_clean();
        $this->_tabs[$this->count]['content'] = $content;
    }

    public function run() {
     
        
        
	foreach($this->_tabs as $id=>$tab)
        {
            echo '<h3>'.$tab["title"].'</h3>';
            $tab['options']['id'] = $this->options['id'].'-'.$id;            
            echo Html::tag('div', $tab["content"], $tab['options']);
        }
		
                
        echo Html::endTag('div');
        \RC::app()->document->setTextScritp('$( "#' .
                $this->options['id'] . '" ).accordion('.Json::encode($this->settings).');',['wrap'=>\Document::DOCREADY]);
    }
       
    

}
