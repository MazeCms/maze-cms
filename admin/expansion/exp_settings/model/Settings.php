<?php

namespace exp\exp_settings\model;

use Text;
use RC;
use maze\helpers\ArrayHelper;
use maze\table\InstallApp;
use maze\table\Expansion;

class Settings extends \maze\base\Model {

    public function getPageNum(){
        return [
            '1' => '1',
            '3' => '3',
            '5' => '5',
            '10' => '10',
            '20' => '20',
            '30' => '30',
            '40' => '40',
            '50' => '50',
            '60' => '60',
            '70' => '70',
            '80' => '80',
            '90' => '90',
            '100' => '100'
        ];
    }
    
    public function getErrorReporting(){
        return [
            "default" => Text::_("LIB_USERINTERFACE_SELECT_TIMEZONE_DEFAULT"),
            "none" => Text::_("EXP_SETTINGS_SYSTEM_OPTIONERR_NONE"),
            "simple" => Text::_("EXP_SETTINGS_SYSTEM_OPTIONERR_SIMPLE"),
            "maximum" => Text::_("EXP_SETTINGS_SYSTEM_OPTIONERR_MAXIMUM"),
            "development" => Text::_("EXP_SETTINGS_SYSTEM_OPTIONERR_DEVELOPMENT"),
        ];
    }
    
    public function getTypeDB(){
        return [
            'mysql'=>'MySQL', 
            'pgsql'=>'PostgreSQL', 
            'sqlite'=>'sqlite 3', 
            'sqlsrv'=>'MSSQL', 
            'cubrid'=>'CUBRID', 
            'oci'=>"Oracle driver"
        ];
    }
    
    public function getTypelog(){
        return [
            'db'=>Text::_("EXP_SETTINGS_SYSTEM_LOGTYPE_DB"),
            'error'=>Text::_("EXP_SETTINGS_SYSTEM_LOGTYPE_ERROR"),
            'cache'=>Text::_("EXP_SETTINGS_SYSTEM_LOGTYPE_CAHCE"),
            'exp'=>Text::_("EXP_SETTINGS_SYSTEM_LOGTYPE_EXP"),
            'request'=>Text::_("EXP_SETTINGS_SYSTEM_LOGTYPE_REQUEST")
        ];
    }




    public function saveConf($form){
        if($form->validate()){
            $conf = RC::app()->config;
            $data = $form->attributes;
            $data['database'] = $conf->database;

            $cc = \CreateConfig::instance();
            $cc->getData($data);
            return $cc->saveFile(PATH_ROOT, "configuration.php");
             
        }
        return false;
        
    }
    
    public function pub($id, $enable){
       return  Expansion::updateAll(['enabled'=>$enable], ['id_exp'=>$id]);
    }
    
    public function refresh($id){
        return  Expansion::updateAll(['param'=>''], ['id_exp'=>$id]);
    }
    
    public function clearCache($id){
        $exp = Expansion::find()->where(['id_exp'=>$id])->all();
        foreach($exp as $e){
            if($cache =  RC::getCache('exp_'.$e->name)){
                $cache->clearTypeFull();
            }
        }
       
    }
}
