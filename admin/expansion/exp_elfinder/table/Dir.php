<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace exp\exp_elfinder\table;

use RC;
use Text;
use maze\db\ActiveRecord;
use exp\exp_elfinder\table\Uploadallow;
use exp\exp_elfinder\table\Attributes;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Dir extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%elfinder_dir}}';
    }
    
    public function rules() {
        return [
            [["profile_id", "path", "alias", "uploadMaxSize", "acceptedName"], "required"],
            ['alias', 'string', 'max'=>100],
            [['uploadMaxSize', 'acceptedName'], 'string', 'max'=>200]
        ];
    }
    
    public function getUploadallow(){
        return $this->hasMany(Uploadallow::className(), ['path_id' => 'path_id']);
    }
    
    public function getAttr(){
        return $this->hasMany(Attributes::className(), ['path_id' => 'path_id']);
    }
    
}
