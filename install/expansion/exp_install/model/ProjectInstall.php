<?php

namespace exp\exp_install\model;

use maze\base\Model;
use RC;
use maze\db\Connection;

class ProjectInstall extends Model {

    public $name; 
    

    public function rules(){
        return [
            [['name'], 'required', "message"=>"Поле ({attribute}) является обязательным"],
            ['name', 'match', 'pattern'=>'/^[a-zA-Z0-9-]{2,}$/',]
        ];
    }
    
    
    public function attributeLabels() {
        return[
            "name"=>"Профиль проетка"
        ];
    }

    

}
