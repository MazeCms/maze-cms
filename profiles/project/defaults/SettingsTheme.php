<?php

namespace root\profiles\project\defaults;

use RC;
use maze\db\Connection;
use maze\helpers\FileHelper;

class SettingsTheme extends \maze\base\Model {

    public $thema; 
    

    public function rules(){
        return [
            ['thema', 'required', "message"=>"Поле ({attribute}) является обязательным"],
        ];
    }
    
    public function action($db) {
        
        sleep(2);
        FileHelper::remove(RC::getAlias("@root/templates/defaults"));
        FileHelper::copy(RC::getAlias("@root/profiles/project/defaults/thema/defaults"), RC::getAlias("@root/templates/defaults"));
        return ['message' => 'Установка темы', 'resultCode' => 1];
    }
    
    public function attributeLabels() {
        return[
            "thema"=>"Тема оформления"
        ];
    }

    

}
