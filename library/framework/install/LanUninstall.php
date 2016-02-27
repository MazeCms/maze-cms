<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\helpers\ArrayHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Plugin;
use maze\table\LangApp;

class LanUninstall extends BaseInstall {

    /**
     * @var string - базовый тип расширения
     */
    protected $type = "languages";

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (!InstallApp::find()->where(['type' => $this->type, 'name' => $this->name, 'front_back' => $this->front])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
        }

        if ($this->front === null || !is_int($this->front)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FRONT");
        }

        return $this;
    }

    /**
     * Команда удаления расширения из БД
     * 
     * @return ExpInstall - self
     */
    public function actionDel() {
        $transaction = RC::getDb()->beginTransaction();
        try {
            $langApp = InstallApp::find()->where(['type' => $this->type, 'name' => $this->name, 'front_back' => $this->front])->one();
            if (!$langApp) {
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
                throw new \Exception();
            }


            $apps = InstallApp::find()->where(['type' => ["expansion", "plugin", "template", "widget", "gadget", "library"]])->all();
            $pathRoot = $this->front ? PATH_ROOT : PATH_ADMINISTRATOR;
            $id_app = [];
            $path = [];
            $path_lib = [];

            foreach ($apps as $app) {
                $id_app[] = $app->id_app;
                if ($app->type == "expansion") {
                    $path[$app->id_app] = $pathRoot . DS . "expansion" . DS . "exp_" . $app->name . DS . "language" . DS . $this->name . ".exp." . $app->name . ".ini";
                } elseif ($app->type == "widget") {
                    $path[$app->id_app] = $pathRoot . DS . "widgets" . DS . "wid_" . $app->name . DS . "language" . DS . $this->name . ".wid." . $app->name . ".ini";
                } elseif ($app->type == "template") {
                    $path[$app->id_app] = $pathRoot . DS . "templates" . DS . $app->name . DS . "language" . DS . $this->name . ".tmp." . $app->name . ".ini";
                } elseif ($app->type == "gadget") {
                    $path[$app->id_app] = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $app->name . DS . "language" . DS . $this->name . ".gad." . $app->name . ".ini";
                } elseif ($app->type == "plugin") {
                    if (!($group = $this->getGroupPlugin($app->name)))
                        continue;

                    $path[$app->id_app] = PATH_ROOT . DS . "plugins" . DS . $group . DS . $app->name . DS . "language" . DS . $this->name . ".plg." . $group . "." . $app->name . ".ini";
                }
                elseif ($app->type == "library") {
                    $path_lib[$app->id_app] = PATH_ROOT . DS . "language" . DS . $app->name;
                } else {
                    continue;
                }
            }

            if (!empty($path)) {
                foreach ($path as $pt) {
                    FileHelper::remove($pt);
                }
            }

            if (!empty($path_lib) && !$this->front) {
                foreach ($path_lib as $ptl) {
                    $files = scandir($ptl);
                    if (!$files)
                        continue;
                    foreach ($files as $fl) {
                        if (substr_count($fl, $this->name) > 0) {
                            FileHelper::remove($ptl . DS . $fl);
                        }
                    }
                }
            }
            if ($lang = Languages::findOne(['lang_code' => $this->name])) {
                LangApp::deleteAll(['id_app' => $id_app, 'id_lang'=>$lang->id_lang]);
                $langApp->delete();

                if (!InstallApp::find()->where(['type' => $this->type, 'name' => $this->name])->exists()) {
                    Languages::deleteAll(['lang_code' => $this->name]);
                }
            }
            
            FileHelper::remove(RC::getAlias('@root/language/'.$app->name.($this->front ? '.site.' : '.admin.').'uninstall.php') );

            $transaction->commit();
        } catch (\Exception $e) {
            $this->addError('del', "LIB_FRAMEWORK_INSTALL_UNSCRIPTS", ['name' => 'LanUninstall::actionDel']);
            $transaction->rollBack();
        }

        return $this;
    }

    protected function getGroupPlugin($name) {
        static $group;

        if ($group == null) {
            $group = ArrayHelper::map(Plugin::find()->asArray()->all(), 'name', 'group_name');
        }

        return isset($group[$name]) ? $group[$name] : false;
    }

    public function text($const, $prop = []) {

        return Text::_($const, $prop);
    }

}
