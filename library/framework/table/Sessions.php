<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\table\Users;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Sessions extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%sessions}}';
    }
    
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id_user' => 'id_user'])->from(["u"=>Users::tableName()]);
    }
    
    public function scenarios() {
        return [
            'access' => ['id_sess', 'id_user', 'sid', 'ip', 'agent', 'time_start', 'time_last'],
        ];
    }
    
}
