<?php

namespace exp\exp_languages\model;

use maze\helpers\ArrayHelper;
use maze\table\Plugin;
use maze\table\InstallApp;
use maze\table\LangApp;
use maze\table\Languages;
use maze\table\LangCache;
use maze\table\LangOverload;
use Text;
use RC;

class Lang extends \maze\base\Model {

    protected $langID = [];

    public function getIcon($path) {
        $path = trim($path, "/");

        $pathAb = RC::getAlias('@root/' . $path);

        if (!is_dir($pathAb))
            return false;

        $files = scandir($pathAb);
        $items = [];
        $options = [];
        foreach ($files as $file) {
            if ($file == "." || $file == "..")
                continue;

            $items[$file] = $file;
            $options[$file] = ['data-image' => '/' . $path . '/' . $file];
        }

        return ['items' => $items, 'options' => $options];
    }

    public function getTypeName($type) {
        $result = $type;
        $types = $this->getTypeApp();
        if (isset($types[$type])) {
            $result = $types[$type];
        }
        return $result;
    }

    public function getTypeApp() {
        return [
            "expansion" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_EXP"),
            "widget" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_WID"),
            "template" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_TMP"),
            "plugin" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_PLG"),
            "library" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_LIB"),
            "gadget" => Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_GAD")
        ];
    }

    public function getAppName($type, $front) {

        return ArrayHelper::map(InstallApp::find()->where(['front_back' => $front, 'type' => $type])->asArray()->all(), 'id_app', function($data) {
                    return $this->getNameConf($data['type'], $data['name'], $data['front_back']) . ' [' . $data['name'] . ']';
                });
    }

    public function getAllApp() {
        $apps = InstallApp::find()->where(['type' => ["expansion", "widget", "template", "plugin", "library", "gadget"]])->all();
        $result = [];
        $types = $this->getTypeApp();

        foreach ($apps as $app) {
            $type = isset($types[$app->type]) ? $types[$app->type] : $app->type;
            $result[$type][$app->id_app] = $this->getNameConf($app->type, $app->name, $app->front_back) . ' [' . $app->name . ']';
        }
        return $result;
    }

    public function getNameConf($type, $name, $front = false) {
        $app_name;
        if ($type == "expansion") {
            $info = RC::getConf(array("type" => $type, "name" => $name));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "widget" || $type == "gadget") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "template") {
            $info = RC::getConf(array("type" => $type, "name" => $name, "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "plugin") {
            $plg_group = $this->getGroupPlugin();

            $info = RC::getConf(array("type" => "plugin", "name" => $name, "group" => $plg_group[$name], "front" => $front));
            $app_name = $info->get("name") ? $info->get("name") : $name;
            unset($info);
        } elseif ($type == "library") {
            $app_name = Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_LIB");
        } else {
            $app_name = $name;
        }
        return $app_name;
    }

    public function getGroupPlugin() {
        return ArrayHelper::map(Plugin::find()->asArray()->all(), 'name', 'group_name');
    }

    public function addApp($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                throw new \Exception();
            }
            $langApp = new LangApp();

            $langApp->attributes = $form->attributes;

            if ($langApp->defaults) {
                LangApp::updateAll(['defaults' => 0], ['id_app' => $langApp->id_app]);
            }
            if (!$langApp->save()) {
                $form->addError('id_lang', \Text::_('EXP_LANGUAGES_CONTROLLER_MESS_SAVE_ERROR'));
                throw new \Exception();
            }

            $form->id_lang_app = $langApp->id_lang_app;
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function defaultsApp($id) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            $langApp = LangApp::find()->where(['id_lang_app' => $id])->one();
            if ($langApp) {
                LangApp::updateAll(['defaults' => 0], ['id_app' => $langApp->id_app]);
                $langApp->defaults = 1;
                if (!$langApp->save()) {
                    throw new \Exception();
                }
            } else {
                throw new \Exception();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function getLocalFile($type, $name, $front) {
        $path = $front ? PATH_ROOT : PATH_ADMINISTRATOR;

        switch ($type) {
            case "expansion" :
                $path = $path . DS . "expansion" . DS . "exp_" . $name . DS . "language";
                break;

            case "widget" :
                $path = $path . DS . "widgets" . DS . "wid_" . $name . DS . "language";
                break;

            case "template" :
                $path = $path . DS . "templates" . DS . $name . DS . "language";
                break;

            case "plugin" :
                $plg_group = $this->getGroupPlugin();

                $path = PATH_ROOT . DS . "plugins" . DS . $plg_group[$name] . DS . $name . DS . "language";
                break;

            case "library" :
                $path = PATH_ROOT . DS . "language";
                break;

            case "gadget" :
                $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $name . DS . "language";
                break;

            default:
                return false;
                break;
        }
        if (!is_dir($path))
            return false;

        $files = scandir($path);
        $result = [];

        foreach ($files as $file) {
            if ($type == "library" && is_dir($path . DS . $file)) {
                $files_d = scandir($path . DS . $file);
                foreach ($files_d as $file_d) {
                    if ($file_d == "." || $file_d == ".." || substr_count($file_d, ".ini") <= 0)
                        continue;
                    $result[] = $path . DS . $file . DS . $file_d;
                }
            }

            if ($file == "." || $file == ".." || substr_count($file, ".ini") <= 0)
                continue;

            $result[] = $path . DS . $file;
        }

        return $result;
    }

    public function getLangCode($langCode) {
        $id = false;
        if (!isset($this->langID[$langCode])) {
            $id = Languages::find()->where(['lang_code' => $langCode])->one();
        } else {
            $id = $this->langID[$langCode];
        }
        return $id;
    }

    public function getLangID($id) {
        return Languages::findOne($id);
    }

    public function indexPack($id_app) {
        $app = InstallApp::findOne($id_app);
        if (!$app)
            return false;
        LangCache::deleteAll(['id_app' => $id_app]);
        if ($result = $this->getLocalFile($app->type, $app->name, $app->front_back)) {
            foreach ($result as $file) {
                if (preg_match("/^([a-z]{2,}-[A-Z]{2,}).+/", pathinfo($file, PATHINFO_FILENAME), $code)) {
                    if ($lang = $this->getLangCode($code[1])) {
                        if ($contents = parse_ini_file($file)) {
                            foreach ($contents as $const => $val) {

                                $langCache = new LangCache();
                                $langCache->id_app = $app->id_app;
                                $langCache->id_lang = $lang->id_lang;
                                $langCache->constant = $const;
                                $langCache->value = $val;
                                $langCache->path = str_replace(PATH_ROOT, '@root', $file);
                                if ($langCache->validate()) {
                                    $langCache->save();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            return false;
        }
        return true;
    }

    public function indexOverload() {
        $path = RC::getAlias('@root/language/overload');
        $files = scandir($path);

        foreach ($files as $file) {
            if ($file == "." || $file == ".." || substr_count($file, ".ini") <= 0)
                continue;
            if (file_exists($path . DS . $file)) {
                if ($contents = parse_ini_file($path . DS . $file)) {
                    $fileInfo = $this->getInfoOverload($path . DS . $file);
                    $front = $fileInfo[2] == 'site' ? 1 : 0;
                    $lang = $this->getLangCode($fileInfo[1]);
                    if(!$lang){
                        continue;
                    }
                    LangOverload::deleteAll(['id_lang' => $lang->id_lang, 'front'=>$front]);
                    foreach ($contents as $const => $val) {

                        $langOverload = new LangOverload();
                        $langOverload->id_lang = $lang->id_lang;
                        $langOverload->constant = $const;
                        $langOverload->value = $val;
                        $langOverload->front = $front;
                        if ($langOverload->validate()) {
                            $langOverload->save();
                        }
                    }
                }
            }
        }
    }

    public function getInfoOverload($file) {
        if (preg_match("/^([a-z]{2,}-[A-Z]{2,})\.overload\.([a-z]+)/", pathinfo($file, PATHINFO_FILENAME), $info)) {
            return $info;
        }
        return false;
    }

    public function getPathLang($type, $name, $front, $id_lang) {
        $path = $front ? PATH_ROOT : PATH_ADMINISTRATOR;

        $lang = $this->getLangID($id_lang);

        switch ($type) {
            case "expansion" :
                $path = $path . DS . "expansion" . DS . "exp_" . $name . DS . "language" . DS . $lang->lang_code . ".exp."
                        . $name . ".ini";
                break;

            case "widget" :
                $path = $path . DS . "widgets" . DS . "wid_" . $name . DS . "language" . DS . $lang->lang_code . ".wid."
                        . $name . ".ini";
                break;

            case "template" :
                $path = $path . DS . "templates" . DS . $name . DS . "language" . DS . $lang->lang_code . ".tmp."
                        . $name . ".ini";
                break;

            case "plugin" :
                $plg_group = $this->getGroupPlugin();

                $path = PATH_ROOT . DS . "plugins" . DS . $plg_group[$name] . DS . $name . DS . "language" . DS . $lang->lang_code . ".plg."
                        . $plg_group[$name] . "." . $name . ".ini";
                break;

            case "library" :
                $path = PATH_ROOT . DS . "language";
                break;

            case "gadget" :
                $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $name . DS . "language" . DS . $lang->lang_code . ".gad.". $name . ".ini";
                break;

            default:
                return false;
                break;
        }

        return $path;
    }

    public function addPack($form) {

        if (!$form->validate())
            return false;

        if ($form->id) {
            $lang = LangCache::findOne($form->id);
            $lang->value = $form->value;
            $path = $lang->path;
        } else {
            $lang = new LangCache();
            $app = InstallApp::findOne($form->id_app);
            $path = $this->getPathLang($form->type, $app->name, $form->front, $form->id_lang);
            if ($form->type == 'library') {
                if (preg_match("/^[A-Z]{3}_([A-Z]+)_([A-Z]+).+$/", $form->constant, $code)) {
                    $group = mb_strtolower($code[1]);
                    $name = mb_strtolower($code[2]);
                    $langLib = $this->getLangID($form->id_lang);
                    $path .= DS . $group . DS . $langLib->lang_code . ".lib." . $group . "." . $name . ".ini";
                } else {
                    return false;
                }
            }
            $lang->id_app = $form->id_app;
            $lang->id_lang = $form->id_lang;
            $lang->constant = $form->constant;
            $lang->value = $form->value;
            $lang->path = str_replace(PATH_ROOT, '@root', $path);
        }
        $content = [];
        if (file_exists(RC::getAlias($path))) {
            $content = parse_ini_file(RC::getAlias($path));
        }

        $content[$lang->constant] = $lang->value;

        $this->saveFileLoc($content, $path);
        $lang->save();
        $form->id = $lang->id;
        return true;
    }

    public function saveFileLoc($contents, $path) {
        $result = [];
        foreach ($contents as $const => $val) {
            $result[] = $const . " = " . "\"" . str_replace(["\\r\\n", "\\n\\r", "\\n"], " ", $val) . "\"";
        }
        $result = implode("\n", $result);

        file_put_contents(RC::getAlias($path), $result);
    }

    public function deletePack($id) {
        $langs = LangCache::findAll(['id' => $id]);
        if ($langs) {
            foreach ($langs as $lang) {
                if (file_exists(RC::getAlias($lang->path))) {
                    $content = parse_ini_file(RC::getAlias($lang->path));
                    if (isset($content[$lang->constant])) {
                        unset($content[$lang->constant]);
                        $this->saveFileLoc($content, $lang->path);
                        $lang->delete();
                    }
                }
            }
            return true;
        }
        return false;
    }
    
     public function saveOverload($form) {

        if (!$form->validate())
            return false;

        if ($form->id) {
            $lang = LangOverload::findOne($form->id);
            $lang->value = $form->value;
        } else {
            $lang = new LangOverload();            
            $lang->id_lang = $form->id_lang;
            $lang->constant = $form->constant;
            $lang->value = $form->value;
            $lang->front = $form->front;

        }
        $langID = $this->getLangID($lang->id_lang);
        $fileName = $langID->lang_code.'.overload.'.($lang->front ? 'site' : 'admin').'.ini';
        $path = RC::getAlias('@root/language/overload/'.$fileName);
        $content = [];
        if (file_exists(RC::getAlias($path))) {
            $content = parse_ini_file(RC::getAlias($path));
        }

        $content[$lang->constant] = $lang->value;

        $this->saveFileLoc($content, $path);
        $lang->save();
        $form->id = $lang->id;
        return true;
    }
    
    public function deleteOverload($id) {
        $langs = LangOverload::findAll(['id' => $id]);
        if ($langs) {
            foreach ($langs as $lang) {
                $langID = $this->getLangID($lang->id_lang);
                $fileName = $langID->lang_code.'.overload.'.($lang->front ? 'site' : 'admin').'.ini';
                $path = RC::getAlias('@root/language/overload/'.$fileName);
        
                if (file_exists(RC::getAlias($path))) {
                    $content = parse_ini_file(RC::getAlias($path));
                    if (isset($content[$lang->constant])) {
                        unset($content[$lang->constant]);
                        $this->saveFileLoc($content, $path);
                        $lang->delete();
                    }
                }
            }
            return true;
        }
        return false;
    }

}
