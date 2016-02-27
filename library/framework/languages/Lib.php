<?php



namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Lib extends Type {
    
    public $group;

    public function getPath() {
        $path = PATH_ROOT.DS."language".DS.$this->group.DS.$this->langCode.".lib.".$this->group.".". $this->name . ".ini";
        return $path;
    }

    public function getLang() {
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db) {
                return LangApp::find()->from(["langapp"=>LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type'=>'library'])
                                ->where(['app.front_back' => $this->front, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
            }, null, 'fw_langautoload');
        }
        

        return $this->lang;
    }

}
