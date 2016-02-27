<?php

namespace maze\languages;

defined('_CHECK_') or die("Access denied");

use maze\table\Users;
use RC;

class Languages{

    public $class_local;
    public $langCode;
    public $front;
    protected $lang = [];
    public static $mapLang = [
        'exp' => 'maze\languages\Exp',
        'gad' => 'maze\languages\Gad',
        'lib' => 'maze\languages\Lib',
        'plg' => 'maze\languages\Plg',
        'tmp' => 'maze\languages\Tmp',
        'wid' => 'maze\languages\Wid'
    ];
    protected static $_instance;

 
    protected function __construct() {
        
     
        $config = RC::app()->config;

        if ($user = RC::app()->access->get()) {
         
            $result = RC::getDb()->cache(function($db) use ($user) {
                return Users::find()
                                ->innerJoinWith('lang')
                                ->andOnCondition([Users::tableName() . '.id_user' => $user["id_user"], 'lang.enabled' => 1])
                                ->andOnCondition(Users::tableName() . '.id_lang != 0')
                                ->one();
            }, null, 'fw_langautoload');
           
            
            if($result && $result->lang){
                $this->langCode = $result->lang->lang_code;
            }
            
           
            
        }
        
        if(!$this->langCode && isset($_COOKIE['lang']) && !empty($_COOKIE['lang'])){
            $this->langCode = $_COOKIE['lang'];
        }
        
        if(!$this->langCode && $config->autolang == 1 && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            
            $langCode = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            
            if(isset($langCode[0])){
                $this->langCode = $langCode[0];
            } 
            
        }
        $this->front = defined("SITE") ? 1 : 0;
    }

    public static function instance() {
   
        if (empty(static::$_instance)) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * 
     * @param array $key - массив с параметрами получения текущего экземпляра класса языка
     *              $key['type'] - тип объекта
     *              $key['name'] - название приложения
     *              $key['group'] - группа приложний
     * @return type
     */
    public function getLang($key) {

        if (isset($key['type']) && isset(static::$mapLang[$key['type']])) {
            $className = static::$mapLang[$key['type']];
        }

        if ($className == null)
            return false;
        $key['front'] = $this->front;
      
        $hash = md5(serialize($key));
        
        if (!isset($this->lang[$hash])) {

            $obj = RC::createObject(array_merge($key, [
                        'class' => $className,
                        'front' => $this->front,
                        'langCode' => $this->langCode
            ]));
            
            $this->lang[$hash] = $obj;
           
        }
       
        
        return $this->lang[$hash];
    }

    public function getText($text) {
         
        if (!is_string($text) || !(ctype_upper(str_replace("_", "", $text))))
            return $text;
        
        $alias = explode("_", strtolower($text));
        
        if (isset($alias[0]) && isset($alias[1])) {
            
            $key['type'] = $alias[0];
            if ($alias[0] == 'plg' || $alias[0] == 'lib') {
                $key['group'] = $alias[1];
                $key['name'] = $alias[2];
            } else{
                $key['name'] = $alias[1];
            }

            $lang = $this->getLang($key);
           
            if ($lang) {
                $text = $lang->getText($text);
            }
        }

        return $text;
    }
    
    public function getIdLang(){
        
        $obj = $this->getLang(['type'=>'lib', 'group'=>'framework', 'name'=>'application']);
         
        if($obj){
           return $obj->idLang;
        }        
    }


}

?>