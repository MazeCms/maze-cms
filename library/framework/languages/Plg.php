<?php


namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Plg extends Type {

    public $group;

    public function getPath() {
        $path = PATH_ROOT.DS."plugins".DS.$this->group.DS.$this->name.DS."language".DS.$this->langCode . ".plg." . $this->group . "." . $this->name . ".ini";
        return $path;
    }

    public function getLang() {
        
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db) {
                return LangApp::find()->from(["langapp"=>LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type'=>'plugin'])
                                ->where(['app.name' => $this->name, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
                
            }, null, 'fw_langautoload');
        }

        return $this->lang;
    }

}
