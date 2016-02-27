<?php

namespace admin\expansion\exp_contents\access;

use maze\access\Rule;
use RC;
use maze\table\Contents;

class CreateUserRule extends Rule{
    
    public $name = 'isUserCreate';
    
    public $expName = 'contents';


    public function execute($id_user, $permission, $params)
    {
        if(isset($params['id_user'])){
            return  $params['id_user'] == $id_user;
        }elseif (isset($params['contents_id'])) {
           $cont = Contents::findOne($params['contents_id']);
           if($cont){
               return $cont->id_user == $id_user;
           }
        }
        
        return false;
    }
}
