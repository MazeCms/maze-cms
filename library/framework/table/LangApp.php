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
class LangApp extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {
        return '{{%lang_app}}';
    }
    
    public function rules() {
        return [
           [['id_lang', 'id_app', 'defaults', 'enabled'],  'number'] 
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
    
    public function getExpansion()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["exp"=>InstallApp::tableName()])
                ->andOnCondition(['exp.type'=>'expansion']);
    }
    
    public function getWidget()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["widget"=>InstallApp::tableName()])
                ->andOnCondition(['widget.type'=>'widget']);
    }
    
    public function getPlugin()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["plugin"=>InstallApp::tableName()])
                ->andOnCondition(['plugin.type'=>'plugin']);
    }
    public function getTemplate()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["template"=>InstallApp::tableName()])
                ->andOnCondition(['template.type'=>'template']);
    }
    
    public function getLibrary()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["library"=>InstallApp::tableName()])
                ->andOnCondition(['library.type'=>'library']);
    }
    
    public function getGadget()
    {
        return $this->hasOne(InstallApp::className(), ['id_app' => 'id_app'])
                ->from(["gadget"=>InstallApp::tableName()])
                ->andOnCondition(['gadget.type'=>'gadget','gadget.front_back'=>0]);
    }
  
    
}
