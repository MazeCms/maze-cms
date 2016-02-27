<?php

namespace lib\fields\images\widget\imgbox;

use RC;
use Text;
use maze\helpers\Html;
use maze\base\JsExpression;

class Widget extends \maze\fields\BaseWidget {



    public function run() {
      
        $types = $this->field->settings->types;
        $types = preg_split("/,[\s]+|,/s", $types);
        $data = array_map(function($val){
           return $val->path_image;
        }, $this->data);
        return $this->render('index', [
                    'widget' => $this,
                    'types'=>$types,
                    'data'=>$data,
                    'form' => $this->form
        ]);
    }

  

}
