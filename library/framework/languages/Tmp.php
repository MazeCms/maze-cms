<?php



namespace maze\languages;

use maze\languages\Type;
use maze\table\LangApp;

class Tmp extends Type {

    public function getPath() {
        $path = $this->app->front_back ? PATH_ROOT : PATH_ADMINISTRATOR;
        $path .= DS . "templates" . DS .$this->name . DS . "language" . DS . $this->langCode . ".tmp." . $this->name . ".ini";
        return $path;
    }

    public function getLang() {
        if ($this->lang == null) {
            $this->lang = \RC::getDb()->cache(function($db) {
                return LangApp::find()->from(["langapp"=>LangApp::tableName()])
                                ->innerJoinWith(['app', 'lang'])
                                ->andOnCondition(['app.type'=>'template'])
                                ->where(['app.name' => $this->name, 'langapp.enabled' => 1])
                                ->andWhere(['lang.enabled' => 1])
                                ->all();
            }, null, 'fw_langautoload');
        }

        return $this->lang;
    }

}
