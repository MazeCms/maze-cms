<?php

namespace exp\exp_elfinder\form;

use Text;
use maze\base\Model;
use maze\helpers\ArrayHelper;

class FormDir extends Model {
    
    public $profile_id;
    
    public $path_id;

    public $path;
    
    public $alias;
    
    public $uploadMaxSize;
    
    public $acceptedName;
    
    public $uploadallow;
    
    public function rules() {
        return [
            [["path", "alias", "uploadMaxSize", "acceptedName", "uploadallow", "profile_id"], "required"],
            ['profile_id', 'exist', 'targetClass'=>'exp\exp_elfinder\table\Profile', 'targetAttribute'=>'profile_id'],
            ['uploadMaxSize', 'string', 'max'=>11],
            ['acceptedName', 'string', 'max'=>100],
            [['uploadallow'], 'safe']
        ];
    }

   

    public function attributeLabels() {
        return[
            "profile_id" => Text::_("EXP_ELFINDER_PROFILE"),
            "path" => Text::_("EXP_ELFINDER_DIR_PATH"),
            "alias" => Text::_("EXP_ELFINDER_DIR_ALIAS"),
            "uploadMaxSize" => Text::_("EXP_ELFINDER_DIR_UPLOADMAXSIZE"),
            "acceptedName" => Text::_("EXP_ELFINDER_PROFILE_VALIDNAME"),
            "uploadallow" => Text::_("EXP_ELFINDER_DIR_UPLOADALLOW")
        ];
    }

}
        