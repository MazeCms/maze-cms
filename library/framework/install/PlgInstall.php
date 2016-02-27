<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Privates;
use maze\table\RolePrivate;
use maze\table\LangApp;
use maze\table\Plugin;

class PlgInstall extends BaseInstall {

    /**
     * @var string - базовый тип расширения
     */
    protected $type = "plugin";

    /**
     * @var string - группа событий плагина
     */
    protected $group;
    
    /**
     * @var int - активность плагина 
     */
    protected $enabled = 1;

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (InstallApp::find()->where(['name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
        }

        if ($this->front === null) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FRONT");
        }

        if ($this->group === null || !is_string($this->group)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_GROUP");
        }


        if (!file_exists($this->getPath() . DS . "plg." . $this->group . "." . $this->name . ".php")) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FILEINPUT", ['name' => $this->getPath() . DS . "plg." . $this->group . "." . $this->name . ".php"]);
        }

        // проверка имени класса
        include($this->getPath().DS. "plg." . $this->group . "." . $this->name . ".php");
        $class_name = ucfirst($this->name) . "_Plugin_" . ucfirst($this->group);
        if (!class_exists($class_name)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_PLUGIN");
        }
        // проверка родитеского класса
        if (get_parent_class($class_name) !== "Plugin") {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_PLUGIN_PARENT", ['name'=>$this->name]);
        }

        if ($this->lang !== null && is_array($this->lang)) {
            $count = Languages::find()->where(['lang_code' => $this->lang])->count();
            if ($count != count($this->lang)) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => $this->name]);
            }
        }


        if ($this->defaultLang) {
            if (!Languages::find()->where(['lang_code' => $this->defaultLang])->exists()) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => $this->defaultLang]);
            }
        }


        $conf = RC::getConf(["type" => "install", "path" => $this->getPath()]);
        if ($conf) {
            $meta_data = array(
                $conf->get("name"),
                $conf->get("description"),
                $conf->get("copyright"),
                $conf->get("license"),
                $conf->get("version"),
                $conf->get("author"),
                $conf->get("email"),
                $conf->get("created"),
                $conf->get("siteauthor")
            );

            if (in_array(null, $meta_data)) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_METADATA");
            }
        } else {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_META");
        }

        return $this;
    }

    /**
     * Команда копирования дистрибутива
     * 
     * @return ExpInstall - self
     */
    public function actionCopy() {

        $path = RC::getAlias('@root/plugins/'.$this->group.'/'.$this->name);
        if (is_dir($path)) {
            return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_DIR", ['name' => $path]);
        }

        FileHelper::copy($this->getPath(), $path);

        if (!is_dir($path)) {
            return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_COPY", ['name' => $path]);
        }

        return $this;
    }

    /**
     * Команда добавления расширения в БД
     * 
     * @return ExpInstall - self
     */
    public function actionAdd() {
        $transaction = RC::getDb()->beginTransaction();
        try {
            InstallApp::deleteAll(['type' => 'widget', 'name' => $this->name]);
            $app = new InstallApp();
            $app->type = "plugin";
            $app->name = $this->name;
            $app->front_back = $this->front;

            if (!$app->save()) {
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([name:' . $app->name . '])']);
                throw new \Exception();
            }


            if (isset($app) && $this->lang && is_array($this->lang)) {

                $lang = Languages::find()->where(['lang_code' => $this->lang])->all();

                if ($this->defaultLang) {
                    $defaulLang = Languages::find()->where(['lang_code' => $this->defaultLang])->one();
                }

                foreach ($lang as $l) {
                    $la = new LangApp();
                    $la->id_lang = $l->id_lang;
                    $la->id_app = $app->id_app;
                    $la->enabled = 1;
                    if (isset($defaulLang)) {
                        $la->defaults = $l->id_lang == $defaulLang->id_lang ? 1 : 0;
                    } else {
                        $la->defaults = 0;
                    }
                    if (!$la->save()) {
                        $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'LangApp::save(id_lang:' . $l->id_lang . ')']);
                        throw new \Exception();
                    }
                }
            }
            $ordering = Plugin::find()->where(['group_name'=>$this->group])->max('ordering');

            $plg = new Plugin();
            
            $plg->name = $this->name;
            $plg->group_name = $this->group;
            $plg->ordering = $ordering+1;
            $plg->enabled = $this->enabled;
            if(!$plg->save()){
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'Plugin::save(name:' . $this->name. ')']);
                throw new \Exception();
            }
            
            $transaction->commit();
        } catch (\Exception $e) {
            if(!$this->hasErrors()){
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([front_back:0, name:' . $app->name . '])']);
            }
            $transaction->rollBack();
        }

        return $this;
    }

    
    /**
     * Команда удаления файлов установки
     * 
     * @return ExpInstall - self
     */
    public function actionRemove() {

        $path = $this->getPath();

        FileHelper::remove($path);
        if (is_dir($path)) {
            $this->addError('remove', "LIB_FRAMEWORK_INSTALL_ACTIONREMOVE_NODIR", ['name' => $path]);
        }

        return $this;
    }

    public function getPath() {
        return $this->path . DS . 'plg_'.$this->group.'_'. $this->name;
    }

    public function text($const, $prop = []) {
        if ($this->defaultLang && $this->defaultLang !== null) {
            $path = $this->getPath();

            if ($this->defaultLang) {
                $path .= DS . "language" . DS . $this->defaultLang . ".plg." . $this->group . "." . $this->name . ".ini";
                if (file_exists($path)) {
                    $langArr = parse_ini_file($path);
                    $const = array_key_exists($const, $langArr) ? $langArr[$const] : $const;
                }
            }
        }

        return Text::_($const, $prop);
    }

}
