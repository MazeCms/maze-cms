<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Privates;
use maze\table\RolePrivate;
use maze\table\Expansion;
use maze\table\LangApp;

class ExpInstall extends BaseInstall {

    /**
     * @var int - активность расширения 1 или 0 
     */
    protected $enabled = 1;
    
    /**
     * @var string - группа в которую входит расширение
     */
    protected $group = "system";
    
    /**
     * @var string - базовый тип расширения
     */
    protected $type = "expansion";
    
    /**
     * @var int - время жизни кеша по умолчанию
     */
    protected $timeCache = 1200;
    
    /**
     * @var int - активность кеширование 1 или 0
     */
    protected $enableCache = 1;
    
    /**
     * @var array - массив файлов с SQL скриптами 
     */
    protected $sql;
    
    /**
     * @var array - массив разрешений для расширения
     */
    protected $private;

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (InstallApp::find()->where(['name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
        }

        if (empty($this->front) || !is_array($this->front)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FRONT");
        }

        if (in_array("0", $this->front) && empty($this->group)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_GROUP");
        }

        // наличее точки входа
        if (in_array("1", $this->front)) {
            if (!file_exists($this->getPath() . DS . "site" . DS . $this->name . ".php")) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FILEINPUT", ['name' => $this->getPath() . DS . "site" . DS . $this->name . ".php"]);
            }
        }

        if (in_array("0", $this->front)) {
            if (!file_exists($this->getPath() . DS . "admin" . DS . $this->name . ".php")) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FILEINPUT", ['name' => 'admin']);
            }
        }

        if (isset($this->lang["admin"])) {
            $count = Languages::find()->where(['lang_code' => $this->lang["admin"]])->count();
            if ($count != count($this->lang["admin"])) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => 'lang::admin']);
            }
        }

        if (isset($this->lang["site"])) {
            $count = Languages::find()->where(['lang_code' => $this->lang["site"]])->count();
            if ($count != count($this->lang["site"])) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => 'lang::site']);
            }
        }

        if (isset($this->defaultLang["admin"])) {

            if (!Languages::find()->where(['lang_code' => $this->defaultLang["admin"]])->exists()) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => 'defaultLang::admin']);
            }
        }

        if (isset($this->defaultLang["site"])) {
            if (!Languages::find()->where(['lang_code' => $this->defaultLang["site"]])->exists()) {
                return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG", ['name' => 'defaultLang::site']);
            }
        }

        $conf = RC::getConf(["type" => "install", "path" => $this->getPath() . DS . 'admin']);
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
            // если тип прилодения расширение то и у него есть админ часть проверить меню
            if (is_array($this->front) && in_array("0", $this->front)) {
                if ($conf->getMenu() == null) {
                    return $this->addError('init', "LIB_FRAMEWORK_INSTALL_METADATA_MENU");
                }
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

        if (is_dir($this->getPath() . DS . "site")) {

            $path_site = PATH_ROOT . DS . "expansion" . DS . "exp_" . $this->name;

            if (is_dir($path_site)) {
                return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_DIR", ['name' => $path_site]);
            }
            FileHelper::copy($this->getPath() . DS . "site", $path_site, ['fileMode'=>0777, 'dirMode'=>0777]);

            if (!is_dir($path_site)) {
                return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_COPY", ['name' => $path_site]);
            }
        }

        if (is_dir($this->getPath() . DS . "admin")) {

            $path_admin = PATH_ROOT . DS . "admin" . DS . "expansion" . DS . "exp_" . $this->name;

            if (is_dir($path_admin)) {
                return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_DIR", ['name' => $path_admin]);
            }

            FileHelper::copy($this->getPath() . DS . "admin", $path_admin, ['fileMode'=>0777, 'dirMode'=>0777]);

            if (!is_dir($path_admin)) {
                return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_COPY", ['name' => $path_admin]);
            }
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

            InstallApp::deleteAll(['type' => 'expansion', 'name' => $this->name]);

            if ($this->front && in_array("0", $this->front)) {
                $ordering = InstallApp::find()->where(['type' => 'expansion', 'front_back' => 0, 'group_name' => $this->group])->max('ordering');
                $appAdmin = new InstallApp();
                $appAdmin->type = "expansion";
                $appAdmin->name = $this->name;
                $appAdmin->group_name = $this->group;
                $appAdmin->front_back = 0;
                $appAdmin->ordering = $ordering + 1;
                if (!$appAdmin->save()) {
                    $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([front_back:0, name:' . $app->name . '])']);
                    throw new \Exception();
                }
            }

            if ($this->front && in_array("1", $this->front)) {

                $appSite = new InstallApp();
                $appSite->type = "expansion";
                $appSite->name = $this->name;
                $appSite->front_back = 1;
                if (!$appSite->save()) {
                    $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([front_back:1, name:' . $app->name . '])']);
                    throw new \Exception();
                }
            }
            Privates::deleteAll(['exp_name' => $this->name]);

            if ($this->front && in_array("0", $this->front)) {
                $priv = new Privates();
                $priv->exp_name = $this->name;
                $priv->name = "VIEW_ADMIN";
                $priv->title = "LIB_FRAMEWORK_EXPANSION_VIEW_ADMIN";
                $priv->description = "LIB_FRAMEWORK_EXPANSION_VIEW_ADMIN_DES";
                if (!$priv->save()) {
                    $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'Privates::save([exp_name:' . $this->name . ', name:VIEW_ADMIN])']);
                    throw new \Exception();
                }
            }

            if ($this->private && is_array($this->private)) {
                foreach ($this->private as $pr) {
                    if (!isset($priv["name"]) || !isset($priv["title"]))
                        continue;
                    $priv = new Privates();
                    $priv->exp_name = $this->name;
                    $priv->name = $pr["name"];
                    $priv->title = $pr["title"];
                    $priv->description = isset($pr["description"]) ? $pr["description"] : "";
                    if (!$priv->save()) {
                        $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'Privates::save([exp_name:' . $this->name . ', name:' . $pr["name"] . '])']);
                        throw new \Exception();
                    }
                }
            }

            $id_role = RC::app()->access->getIdAdminRole();
            $private = Privates::findAll(['exp_name' => $this->name]);
            foreach ($private as $pr) {
                $prRole = new RolePrivate();
                $prRole->id_role = $id_role;
                $prRole->id_priv = $pr->id_priv;
                if (!$prRole->save()) {
                    $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'RolePrivate::save(id_priv:' . $pr->id_priv . ')']);
                    throw new \Exception();
                }
            }

            Expansion::deleteAll(['name' => $this->name]);
            $exp = new Expansion();
            $exp->name = $this->name;
            $exp->enabled = $this->enabled;
            $exp->enable_cache = $this->enableCache;
            $exp->time_cache = $this->timeCache;

            if (!$exp->save()) {
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'Expansion::save(name:' . $this->name . ')']);
                throw new \Exception();
            }

            if (isset($appAdmin) && $this->lang && isset($this->lang["admin"])) {

                $lang = Languages::find()->where(['lang_code' => $this->lang["admin"]])->all();

                if ($this->defaultLang && isset($this->defaultLang["admin"])) {
                    $defaulLang = Languages::find()->where(['lang_code' => $this->defaultLang["admin"]])->one();
                }

                foreach ($lang as $l) {
                    $la = new LangApp();
                    $la->id_lang = $l->id_lang;
                    $la->id_app = $appAdmin->id_app;
                    $la->enabled = 1;
                    if (isset($defaulLang)) {
                        $la->defaults = $l->id_lang == $defaulLang->id_lang ? 1 : 0;
                    } else {
                        $la->defaults = 0;
                    }
                    if (!$la->save()) {
                        $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'LangApp::save(id_lang:' . $l->id_lang . ', admin)']);
                        throw new \Exception();
                    }
                }
            }

            if (isset($appSite) && $this->lang && isset($this->lang["site"])) {

                $lang = Languages::find()->where(['lang_code' => $this->lang["site"]])->all();

                if ($this->defaultLang && isset($this->defaultLang["site"])) {
                    $defaulLang = Languages::find()->where(['lang_code' => $this->defaultLang["site"]])->one();
                }

                foreach ($lang as $l) {
                    $la = new LangApp();
                    $la->id_lang = $l->id_lang;
                    $la->id_app = $appSite->id_app;
                    $la->enabled = 1;
                    if (isset($defaulLang)) {
                        $la->defaults = $l->id_lang == $defaulLang->id_lang ? 1 : 0;
                    } else {
                        $la->defaults = 0;
                    }
                    if (!$la->save()) {
                        $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'LangApp::save(id_lang:' . $l->id_lang . ',site)']);
                        throw new \Exception();
                    }
                }
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
     * Команда добавления SQL скриптов в БД
     * должна существовать директория ~admin/sql
     * 
     * @return ExpInstall - self
     */
    public function actionSql() {
        if ($this->sql && is_array($this->sql) && !empty($this->sql)) {
            $dir = $this->getPath() . DS ."admin".DS. "sql";

            $paser = \SQLParser::instance();

            foreach ($this->sql as $file) {
                if (!file_exists($dir . DS . $file)) {
                    $this->addError('sql', "LIB_FRAMEWORK_INSTALL_SQL_NOTFILE", ['name' => ($dir . DS . $file)]);
                    break;
                }
                $query_parse = $paser->loadParser($dir . DS . $file);
                
                $query = [];
                if (!(empty($query_parse)) && is_array($query_parse)) {
                    $query = array_merge($query_parse, $query);
                }
            }

            if (empty($query)) {
                return $this->addError('sql', "LIB_FRAMEWORK_INSTALL_BD_PARSER");
            }
            
            $transaction = RC::getDb()->beginTransaction();
            $db = RC::getDb();
            
            try {
                foreach ($query as $qu) {
                    try {                        
                        $db->createCommand($qu)->execute();
                        
                    } catch (\Exception $e) {
                        $this->addError('sql', "LIB_FRAMEWORK_INSTALL_BD_QUERY",['query'=>$qu]);
                        throw new \Exception();
                    }
                }
               
                $transaction->commit();
            } catch (\Exception $e) {                
                $transaction->rollBack();
            }
        } else {
            $this->addError('sql', "LIB_FRAMEWORK_INSTALL_BD_NOTFILE");
        }
        return $this;
    }
    /**
     * Команда удаления файлов установки
     * 
     * @return ExpInstall - self
     */
    public function actionRemove(){
        
        $path = $this->getPath();
        
        FileHelper::remove($path);
        if(is_dir($path)){
            $this->addError('sql', "LIB_FRAMEWORK_INSTALL_ACTIONREMOVE_NODIR", ['name'=>$path]);
        }
        
        return $this;
        
    }
    
    public function getPath(){
        return $this->path.DS.'exp_'.$this->name;
    }
    
    public function text($const, $prop = []) {
        if ($this->defaultLang && $this->defaultLang !== null) {
            $path = $this->getPath();
        
            if (isset($this->defaultLang["admin"])) {
                $path .= DS . "admin" . DS . "language" . DS . $this->defaultLang["admin"] . ".exp." . $this->name . ".ini";
                if (file_exists($path)) {
                    $langArr = parse_ini_file($path);
                    $const = array_key_exists($const, $langArr) ? $langArr[$const] : $const;
                }
            }
        }

        return Text::_($const, $prop);
    }

}
