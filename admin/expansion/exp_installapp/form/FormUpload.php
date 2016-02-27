<?php

namespace exp\exp_installapp\form;

use maze\base\Model;
use RC;
use Text;

class FormUpload extends Model {


    public $type;
    
    public $file;
    
    public $path;
    
    public $url;
    
    public function rules() {
        return [
            ['type', "required"],            
            ['path', "required", 'on'=>'path'],
            ['url', "required", 'on'=>'url'],
            ['path', "validFile", 'on'=>'path'],
            ['file', 'file', 'types'=>['zip'], 'skipOnEmpty'=>false, 'on'=>'file'],
            ['path', 'file', 'types'=>['zip'],  'skipOnEmpty'=>false, 'on'=>'path'],
            ['url', 'file', 'types'=>['zip'], 'skipOnEmpty'=>false, 'on'=>'url']
        ];
    }
    
    public function validFile($attribute, $param){
        if(empty($this->$attribute) || !file_exists(RC::getAlias($this->$attribute))){
            $this->addError($attribute, Text::_('Данного файла {name} не существует', ['name'=>$this->$attribute]));
        }
    }
    
    public function attributeLabels() {
        return[
            "type" => Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_TYPE"),
            "file"=>Text::_("EXP_WIDGET_FORM_LABEL_TITLE")
        ];
    }

}
