<?php

namespace ui\filter;


class Model extends \maze\base\Model {

    public $component;
    
    public $filter_id;
    
    public $code;
    
    public $visible;
    
    
     public function rules() {
        return [
            [['component', 'filter_id', 'code', 'visible'], 'safe']
        ];
    }

    public function queryBilder($query)
    {
        
    }
    
        
}
