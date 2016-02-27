<?php

namespace exp\exp_install\model;

use maze\base\Model;
use RC;
use maze\db\Connection;

class LangInstall extends Model {

    public $lang_code;
    
    public $title;
    
    public $reduce;
    
    public $img;
    
    public $enabled;      
    
    public function rules(){
        return [
            [['lang_code', 'title', 'reduce', 'img', 'enabled'], 'required', "message"=>"Поле ({attribute}) является обязательным"],
            [['lang_code'], 'match', 'pattern'=>'/^[a-zA-Z0-9-]{2,}$/', "message"=>"Поле ({attribute}) содержит недопустимые символы"],
            ['enabled', 'boolean']
        ];
    }
    
 
    
    public function attributeLabels() {
        return[
            "lang_code"=>"Код языка",
            "title" => "Название языка",
            "reduce" => "Сокращенный код языка",
            "img" => "Флаг страны происхождения языка",
            "enabled"=>"Активность"
        ];
    }

    

}
