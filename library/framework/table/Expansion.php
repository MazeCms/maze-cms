<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\InstallApp;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Expansion extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%expansion}}';
    }

    public function rules() {
        return [
            [['time_cache'], "required"],
            [['enabled', 'enable_cache'], 'boolean'],
            ['time_cache', 'number', 'min'=>10],
            ['id_tmp', 'number'],
            ['param', 'safe']
        ];
    }
    
    public function getInstallApp() {
        return $this->hasOne(InstallApp::className(), ['name' => 'name'])
                        ->from(["ia" => InstallApp::tableName()])
                        ->andOnCondition(['ia.type'=>'expansion']);
    }

    public function beforeSave($insert) {

        if (!empty($this->param)) {
            $this->param = serialize($this->param);
        }

        return true;
    }

    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }

    }
}
