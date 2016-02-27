<?php

namespace ui\help;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

class Tooltip extends Elements {

    /**
     * @var array - настройки jquery ui tooltip 
     * http://api.jqueryui.com/tooltip/
     * content - $( ".selector" ).tooltip({ content: "Awesome title!" });
     * disabled - $( ".selector" ).tooltip({ disabled: true });
     * hide - $( ".selector" ).tooltip({ hide: { effect: "explode", duration: 1000 } });
     * items - $( ".selector" ).tooltip({ items: "img[alt]" });
     * position - $( ".selector" ).tooltip({ position: { my: "left+15 center", at: "right center" } });
     * show - $( ".selector" ).tooltip({ show: { effect: "blind", duration: 800 } });
     * tooltipClass - $( ".selector" ).tooltip({ tooltipClass: "custom-tooltip-styling" });
     * track - $( ".selector" ).tooltip({ track: true }); - появляется и движется за мышью
     */
    public $settings = array();

    /**
     * @var array - настройки настройки тега цели
     */
    public $htmlOptions = array();
    /**
     * @var string - тег контента
     */
    public $teg = 'label';
    /**
     * @var string -  контент
     */
    public $content = '';
    /**
     * @var string - текст подсказки
     */
    public $help;
    
    public $icon = '<span aria-hidden="true" class="glyphicon glyphicon-question-sign"></span>';

    public function init() {
        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId();
        }
        
        $this->htmlOptions = array_merge([
            'class'=>'control-label'
        ], $this->htmlOptions);
        
        $this->htmlOptions['title'] = $this->help;
        
        $this->settings = array_merge([
            'tooltipClass'=>'form-tooltip',
            'show'=>['effect'=>'fadeIn', 'delay'=>150],
            'hide'=>['effect'=>'fadeOut', 'delay'=>150],
            'position'=>['my'=>'left bottom', 'at'=>'right+10 top+10']
        ],$this->settings);
        
    }

    public function run() {        
      
        if(!empty($this->help))
        {
           $this->content = $this->content .' '.$this->icon;
            \Document::instance()->setTextScritp('$( "#' . $this->htmlOptions['id'] . '" ).tooltip(' . Json::encode($this->settings) . ');',
                ['wrap'=>\Document::DOCREADY]);
        }
        return  Html::tag($this->teg, $this->content , $this->htmlOptions);
    }

}
