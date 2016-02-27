<?php

namespace wid\wid_adminlogo;

use maze\helpers\ArrayHelper;


class Recover extends \maze\base\Model {

    public $login;
      
    public function rules() {
        return [
            [["login"], "required"],
        ];
    }
    public function attributeLabels() {
        return[
            "login" => \Text::_("WID_ADMINLOGO_NAME_LOGO")
        ];
    }
   

}
