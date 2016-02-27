<?php

namespace ui\users;

use ui\select\Chosen;
use maze\table\Users;
use maze\helpers\ArrayHelper;

class UsersList extends Chosen {

    public $condition = [];

    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(Users::find()->asArray()->all(), 'id_user', 'username'); 
        
    }

}
