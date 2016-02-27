<?php

namespace exp\exp_logs\model;

use RC;
use Text;
use maze\helpers\ArrayHelper;
use maze\table\InstallApp;
use maze\table\Plugin;

class Logs extends \maze\base\Model {

    public function getMenuLog() {
        $apps = InstallApp::find()
                ->joinWith(['plugin'])
                ->from(['ia' => InstallApp::tableName()])
                ->andWhere(['ia.type' => ["expansion", "library", "plugin", "template", "widget", "gadget"]])
                ->orderBy('ia.type,  ia.name')
                ->all();

        $menu = [];

        foreach ($apps as $app) {

            if (!isset($menu[$app->type])) {
                $menu[$app->type] = [
                    'id' => 'menu-logs-' . $app->type,
                    'title' => $this->getTypeName($app->type),
                    'path' => null,
                    'item' => []
                ];
            } else {
                if ($app->type == 'expansion') {
                    if (!isset($menu[$app->type]['item'][$app->name])) {
                        $menu[$app->type]['item'][$app->name] = [
                            'id' => 'menu-logs-' . $app->type.'-'.$app->name,
                            'title' => $this->getNameConf($app->type, $app->name),
                            'path' => null,
                            'item'=>[]
                        ];
                    }
                    
                     $menu[$app->type]['item'][$app->name]['item'][] = [
                            'id' => 'menu-logs-' . $app->type.'-'.$app->name.'-'.$app->front_back,
                            'title' => ($app->front_back ? 'Сайт' : 'Админ'),
                            'path' => ['/admin/logs', ['type'=>$app->type, 'name'=>$app->name, 'front'=>$app->front_back]],
                         ];
                }else{
                    if($app->type == 'library' || $app->type == 'gadget' ){
                       $menu[$app->type]['item'][$app->name] = [
                            'id' => 'menu-logs-' . $app->type.'-'.$app->name,
                            'title' => $this->getNameConf($app->type, $app->name, $app->front_back),
                            'path' => ['/admin/logs', ['type'=>$app->type, 'name'=>$app->name, 'front'=>$app->front_back]]
                        ]; 
                    }else{
                        if(!isset($menu[$app->type]['item'][$app->front_back])){
                            $menu[$app->type]['item'][$app->front_back] = [
                                'id' => 'menu-logs-' . $app->type.'-'.$app->front_back,
                                'title' => ($app->front_back ? 'Сайт' : 'Админ'),
                                'path' => null,
                                'item'=>[]
                            ];
                        }
                        $menu[$app->type]['item'][$app->front_back]['item'][] =[
                            'id' => 'menu-logs-' . $app->type.'-'.$app->name,
                            'title' => $this->getNameConf($app->type, $app->name, $app->front_back, ($app->plugin ? $app->plugin->group_name : false)),
                            'path' => ['/admin/logs', ['type'=>$app->type, 'name'=>$app->name, 'front'=>$app->front_back]]
                        ];
                    }
                    
                }
            }
        }
        
        return $menu;
    }

    public function getTypeName($type) {
        $result;
        switch ($type) {
            case "expansion":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_EXP");
                break;

            case "widget":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_WID");
                break;

            case "template":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_TMP");
                break;

            case "plugin":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_PLG");
                break;

            case "library":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_LIB");
                break;

            case "gadget":
                $result = Text::_("EXP_LOGS_APP_FILTER_TYPE_GAD");
                break;

            default:

                $result = $type;
        }
        return $result;
    }

    public function getNameConf($type, $name, $front = false, $plg_group = false) {
        $app_name;


        if ($type == "expansion" || $type == "widget" || $type == "gadget") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "template") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "plugin") {
            $info = RC::getConf(array("type" => "plugin", "name" => $name, "group" => $plg_group, "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "library") {
            $app_name = Text::_("EXP_LOGS_APP_FILTER_TYPE_LIB");
        } else {
            $app_name = $name;
        }
        return $app_name;
    }
    
    public function getGroupPlugin() {
        return ArrayHelper::map(Plugin::find()->asArray()->all(), 'name', 'group_name');
    }
    
    public function getListApp(){
        $exp = InstallApp::find()->where(['type'=>"expansion"])->innerJoinWith('expansion')->all();
        $items = [];
        foreach($exp as $e)
        {
            $info = \RC::getConf(array("type"=>"expansion", "name"=>$e->name));
            $items[$e->name] = $info->get("name") ? $info->get("name")." [$e->name]" : $e->name;
        }       
        return $items;
        
    }
 
    

}
