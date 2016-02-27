<?php

namespace ui\type;

use ui\select\Chosen;
use maze\helpers\ArrayHelper;
use Text;
use RC;

class MimeList extends Chosen {


    public function init() {
        parent::init();
        $path = RC::getAlias('@maze/helpers/mimeTypes.php');
        $types = include_once $path;
        
        $this->items = array_combine($types, array_keys($types));
        
        
        
        
    }

}
