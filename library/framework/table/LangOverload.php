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
class LangOverload extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {
        return '{{%lang_overload}}';
    }
    
    public function rules() {
        return [
           ['constant', 'match', 'pattern'=>'/^[A-Z_]+$/'],
           [['id_lang', 'constant', 'value', 'front'],  'safe'] 
        ];
    }
    
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id_lang' => 'id_lang'])
                ->from(["lang"=>Languages::tableName()]);
    }
    
    
    public function attributeLabels() {
        return[
            "constant" => Text::_("Константа"),
            "front" => Text::_("Принадлежность"),
            "id_lang" => Text::_("Язык"),
            "value" => Text::_("Значение")
        ];
    }
    
}
