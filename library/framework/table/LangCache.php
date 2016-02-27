<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\table\InstallApp;
use maze\table\Languages;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class LangCache extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {
        return '{{%lang_cache}}';
    }
    
    public function rules() {
        return [
           ['constant', 'match', 'pattern'=>'/^[A-Z_]+$/'],
           [['id_app', 'id_lang', 'constant', 'value', 'path'],  'safe'] 
        ];
    }
    
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id_lang' => 'id_lang'])
                ->from(["lang"=>Languages::tableName()]);
    }
    
    public function getApp()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["app"=>InstallApp::tableName()]);
           
    }
    
    public function attributeLabels() {
        return[
            "constant" => Text::_("Константа"),
            "id_app" => Text::_("Расширение"),
            "id_lang" => Text::_("Язык"),
            "value" => Text::_("Значение")
        ];
    }
    
}
