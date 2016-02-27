<?php

namespace ui\lang;

use ui\select\Chosen;
use maze\table\Languages;
use Text;

class Langs extends Chosen {

    public $allLang = false;
    
    public function init() {
        parent::init();
        if($this->allLang){
            $this->items = Languages::getList();
            $this->items[0] = Text::_('LIB_USERINTERFACE_SELECT_ALLLANG'); 
            ksort($this->items);
        }else{
            $this->items = Languages::getList();
        }
       
    }

}
