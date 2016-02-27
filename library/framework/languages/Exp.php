<?php

namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Exp extends Type {

    public function getPath() {
        $path = $this->front ? PATH_ROOT : PATH_ADMINISTRATOR;
        $path .= DS."expansion".DS."exp_".$this->name.DS."language".DS.$this->langCode.".exp.".$this->name.".ini";
        
        return $path;
    }

    public function getLang() {
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db) {
                return LangApp::find()->from(["langapp"=>LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type'=>'expansion'])
                                ->where(['app.front_back' => $this->front, 'app.name' => $this->name, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
            }, null, 'fw_langautoload');
        }

        return $this->lang;
    }

}
