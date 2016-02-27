<?php

namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Gad extends Type {

    public function getPath() {
        $path = PATH_ADMINISTRATOR.DS . "gadgets" . DS . "gad_" . $this->name . DS . "language" . DS . $this->langCode . ".gad." . $this->name . ".ini";
        return $path;
    }

    public function getLang() {
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db){
                return LangApp::find()->from(["langapp"=>LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type'=>'gadget'])
                                ->where(['app.front_back' => 0, 'app.name' => $this->name, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
            }, null, 'fw_langautoload');
        }

        return $this->lang;
    }

}
