<?php


namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Wid extends Type {

    public function getPath() {
        $path = $this->app->front_back ? PATH_ROOT : PATH_ADMINISTRATOR;
        $path .= DS . "widgets" . DS . "wid_" . $this->name . DS . "language" . DS . $this->langCode . ".wid." . $this->name . ".ini";
        return $path;
    }

    public function getLang() {
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db) {
                return LangApp::find()->from(["langapp" => LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type' => 'widget'])
                                ->where(['app.name' => $this->name, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
            }, null, 'fw_langautoload');
        }
        
       
        return $this->lang;
    }

}
