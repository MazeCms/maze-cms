<?php

namespace exp\exp_languages\form;

use maze\base\Model;
use maze\table\InstallApp;
use maze\table\Languages;
use maze\table\Plugin;
use maze\helpers\ArrayHelper;

class FormApp extends Model {

    public $id_lang_app;
    
    public $id_lang;
    
    public $id_app;
    
    public $front;
    
    public $type;
    
    public $defaults;
    
    public $enabled;

    public function rules() {
        return [
            [['id_lang', 'id_app', 'front', 'type'], "required"],
            [['enabled', 'defaults'], 'default', 'value' => 0],
            [['enabled', 'defaults', 'front'], 'boolean'],
            [['id_lang', 'id_app', 'id_lang_app'], 'number'],
            ['id_lang', 'unique', 'targetClass' => 'maze\table\LangApp', 'targetAttribute' => 'id_lang', 'filter' => function($query) {
                    $query->andWhere(['id_app' => $this->id_app]);
                }, 'message' => 'EXP_LANGUAGES_APP_FORM_ALER_ERROR_ISLANGAPP'],
            ['id_lang', 'validLang']
        ];
    }

    public function validLang($attribute, $params) {
        $app = InstallApp::find()->where(['id_app' => $this->id_app, 'type' => $this->type, 'front_back' => $this->front])->one();
        $lang = Languages::find()->where(['id_lang' => $this->id_lang])->one();
        if ($app && $lang) {
            $path = $app->front_back ? PATH_ROOT : PATH_ADMINISTRATOR;

            switch ($app->type) {
                case "expansion" :
                    $path = $path . DS . "expansion" . DS . "exp_" . $app->name . DS . "language" . DS . $lang->lang_code . ".exp." . $app->name;
                    break;

                case "widget" :
                    $path = $path . DS . "widgets" . DS . "wid_" . $app->name . DS . "language" . DS . $lang->lang_code . ".wid." . $app->name;
                    break;

                case "template" :
                    $path = $path . DS . "templates" . DS . $app->name . DS . "language" . DS . $lang->lang_code . ".tmp." . $app->name;
                    break;

                case "plugin" :
                    $plg_group = ArrayHelper::map(Plugin::find()->asArray()->all(), 'name', 'group_name');

                    $path = PATH_ROOT . DS . "plugins" . DS . $plg_group[$app->name] . DS . $app->name . DS . "language" . DS . $lang->lang_code . ".plg."
                            . $plg_group[$app->name] . "." . $app->name;
                    break;

                case "library" :
                    $path = PATH_ROOT . DS . "language";
                    break;

                case "gadget" :
                    $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $app->name . DS . "language" . DS . $lang->lang_code . ".gad." . $app->name;
                    break;
            }

            if ($app->type !== "library") {
               
                if (!file_exists($path . ".ini")) {
                    $this->addError('id_lang', \Text::_('EXP_LANGUAGES_APP_FORM_ALER_ERROR_NOLOCAL'));
                }
            } else {
               
                $files = scandir($path . DS . "framework");
                $count = 0;
                foreach ($files as $file) {
                    if ($file == "." || $file == "..")
                        continue;
                
                    if (mb_stripos($file, $lang->lang_code) !== false) {
                        $count++;
                    }
                }
               
                if ($count <= 0) {
                    $this->addError('id_lang', \Text::_('EXP_LANGUAGES_APP_FORM_ALER_ERROR_NOLOCAL'));
                }
            }
        }
    }

    public function attributeLabels() {
        return[
            "id_lang" => \Text::_("EXP_LANGUAGES_APP"),
            "type" => \Text::_("EXP_LANGUAGES_APP_TABLE_TYPE"),
            "enabled" => \Text::_("EXP_LANGUAGES_TABLE_ENABLED"),
            "id_app" => \Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_EXP"),
            "front" => \Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"),
            "defaults" => \Text::_("EXP_LANGUAGES_APP_TABLE_DEFAULT")
        ];
    }

}
        