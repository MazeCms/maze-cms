<?php

namespace exp\exp_installapp\model;

use maze\helpers\ArrayHelper;
use maze\table\InstallApp;
use maze\table\Plugin;
use Text;
use RC;


class Install extends \maze\base\Model {

    public $unzip;
    
    public $app;
    
    public function getTypeApp($type) {
        $result;
        switch ($type) {
            case "expansion":
                $result = Text::_("EXP_INSTALLAPP_TYPE_EXP");
                break;

            case "widget":
                $result = Text::_("EXP_INSTALLAPP_TYPE_WID");
                break;

            case "template":
                $result = Text::_("EXP_INSTALLAPP_TYPE_TMP");
                break;

            case "plugin":
                $result = Text::_("EXP_INSTALLAPP_TYPE_PLG");
                break;

            case "languages":
                $result = Text::_("EXP_INSTALLAPP_TYPE_LANG");
                break;

            case "library":
                $result = Text::_("EXP_INSTALLAPP_TYPE_LIB");
                break;

            case "gadget":
                $result = Text::_("EXP_INSTALLAPP_TYPE_GAD");
                break;

            default:

                $result = $type;
        }
        return $result;
    }

    public function getAppConfig($type, $name, $front = false) {
        $info = false;
        if ($type == "expansion") {
            $info = RC::getConf(array("type" => $type, "name" => $name));
        } elseif ($type == "widget" || $type == "gadget") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));

        } elseif ($type == "template") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));

        } elseif ($type == "plugin") {
            $plg_group = $this->getGroupPlugin();
            $info = RC::getConf(array("type" => "plugin", "name" => $name, "group" => $plg_group[$name], "front" => $front));

        }
        return $info;
    }

    public function getGroupPlugin() {
        return ArrayHelper::map(Plugin::find()->asArray()->all(), 'name', 'group_name');
    }
    
    public function isDelete($name, $type, $front) {

        if ($type == "expansion") {
            $path = PATH_ADMINISTRATOR . DS . "expansion" . DS . "exp_" . $name;
        } elseif ($type == "widget") {
            $path = $front ? PATH_ROOT : PATH_ADMINISTRATOR;
            $path .= DS . "widgets" . DS . "wid_" . $name;
        } elseif ($type == "template") {
            $path = $front ? PATH_ROOT : PATH_ADMINISTRATOR;
            $path .= DS . "templates" . DS . $name;
        } elseif ($type == "gadget") {
            $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $name;
        } elseif ($type == "plugin") {
            $group = $this->getGroupPlugin();

            if (!isset($group[$name]))
                return false;

            $path = PATH_ROOT . DS . "plugins" . DS . $group[$name] . DS . $name;
        }
        elseif ($type == "library") {
            $path = PATH_ROOT . DS . "library" . DS . $name;
        } elseif ($type == "languages") {
            $path = PATH_ROOT . DS . "language";
        } else {
            return false;
        }

        if ($type == "languages") {
            $pref_front = $front ? "site" : "admin";
            $path = $path . DS . $name . "." . $pref_front . ".Uninstall.php";
        } else {
            $path = $path . DS . "Uninstall.php";
        }


        if (!file_exists($path))
            return false;

        return true;
    }

    public function unzip($file_name) {
        $path = RC::getAlias('@root/temp/install/'.$file_name);

        if (!extension_loaded('zip')){
            $this->addError('unzip', 'Ошибка отсутвует расширения PHP "zip"');
            return false;
        }

        if (!file_exists($path)){
            $this->addError('unzip', Text::_('Отсутвует пакет установки {name}', ['name'=>$path]));
            return false;
        }

        $zip = new \ZipArchive;
        if ($zip->open($path) === TRUE) {
            $zip->extractTo(RC::getAlias('@root/temp/install/'));
            $zip->close();
        } else {
            $this->addError('unzip', Text::_('Ошибка открытия пакета установки {name}', ['name'=>$path]));
            return false;
        }
        
        if($pack = $this->getTypeName($file_name)){
           return $pack;
            
        }else{
            $this->addError('unzip', Text::_('Ошибка определения типа установочного пакета {name}', ['name'=>$file_name]));
            return false; 
        }
        
        
    }
    
    public function getTypeName($file_name) {

        if (!preg_match("#^([a-z]+)_([^_]+)(_(.+))?\.zip$#", $file_name, $type)) {
            return false;
        }
        switch ($type[1]) {
            case "exp":
                $result = "expansion";
                break;

            case "wid":
                $result = "widget";
                break;

            case "tmp":
                $result = "template";
                break;

            case "plg":
                $result = "plugin";
                break;

            case "lan":
                $result = "languages";
                break;

            case "lib":
                $result = "library";
                break;

            case "gad":
                $result = "gadget";
                break;

            default:

               return false;
        }
        if(isset($type[4])){
            return ['type'=>$result, 'name'=>$type[4], 'group'=>$type[2]];
        }
        return ['type'=>$result, 'name'=>$type[2]];
    } 
    
    public function getAppByID($id){
        $app = InstallApp::findOne($id);
        if($app){
          $result = ['name'=>$app->name, 'type'=>$app->type, 'front'=>$app->front_back];
          if($app->type == 'plugin'){
              $group = $this->getGroupPlugin();
              $result['group'] = $group[$app->name];
          }
          return $result;
          
        }{
            $this->addError('app', Text::_('Ошибка, расширения с ID{id} несуществует', ['id'=>$id]));
            return false;
        }
        return true;
    }

}
