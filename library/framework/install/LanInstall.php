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

class LanInstall extends BaseInstall {

    /**
     * @var string - базовый тип расширения
     */
    protected $type = "languages";
    protected $enabled = 1;
    protected $title;
    protected $reduce;
    protected $img;

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (InstallApp::find()->where(['type' => $this->type, 'name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
        }

        if ($this->front === null || !is_array($this->front)) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_FRONT");
        }


        if ($this->title == null) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG_INSTALL");
        }

        if ($this->reduce == null) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG_INSTALL");
        }

        if ($this->img == null) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_LANG_INSTALL");
        }

        return $this;
    }

    /**
     * Команда копирования дистрибутива
     * 
     * @return ExpInstall - self
     */
    public function actionCopy() {


        $pathInstall = $this->getPath();
        foreach (['admin', 'site', 'system'] as $front) {
            if (is_dir($pathInstall . DS . $front)) {
                $admin_lang = scandir($pathInstall . DS . $front);
                foreach ($admin_lang as $file) {
                    if (is_dir($pathInstall . DS . $front . DS . $file) || $file == "." || $file == "..")
                        continue;

                    if (!preg_match("#^[a-z]+-[A-Z]+(\.[\w]+){2,}\.ini$#", $file))
                        continue;

                    $file_name = explode(".", $file);
                    if (!isset($file_name[1]))
                        continue;

                    if ($file_name[1] == "exp") {
                        $path = PATH_ROOT;

                        if ($front == "admin")
                            $path .= DS . "admin";

                        $path .= DS . "expansion" . DS . "exp_" . $file_name[2];
                        if (!is_dir($path))
                            continue;



                        if (!is_dir($path . DS . "language"))
                            mkdir($path . DS . "language");

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . "language" . DS . $file);
                    }
                    elseif ($file_name[1] == "wid") {
                        $path = PATH_ROOT;

                        if ($front == "admin")
                            $path .= DS . "admin";

                        $path .= DS . "widgets" . DS . "wid_" . $file_name[2];
                        if (!is_dir($path))
                            continue;

                        if (!is_dir($path . DS . "language"))
                            mkdir($path . DS . "language");

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . "language" . DS . $file);
                    }
                    elseif ($file_name[1] == "tmp") {
                        $path = PATH_ROOT;

                        if ($front == "admin")
                            $path .= DS . "admin";

                        $path .= DS . "templates" . DS . $file_name[2];
                        if (!is_dir($path))
                            continue;

                        if (!is_dir($path . DS . "language"))
                            mkdir($path . DS . "language");

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . "language" . DS . $file);
                    }
                    elseif ($file_name[1] == "gad" && $front == "admin") {
                        $path = PATH_ROOT . DS . "admin" . DS . "gadgets" . DS . "gad_" . $file_name[2];

                        if (!is_dir($path))
                            continue;


                        if (!is_dir($path . DS . "language"))
                            mkdir($path . DS . "language");

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . "language" . DS . $file);
                    }
                    elseif ($file_name[1] == "plg") {
                        if (!isset($file_name[3]))
                            continue;

                        $path = PATH_ROOT . DS . "plugins" . DS . $file_name[2] . DS . $file_name[3];

                        if (!is_dir($path))
                            continue;


                        if (!is_dir($path . DS . "language"))
                            mkdir($path . DS . "language");

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . "language" . DS . $file);
                    }
                    elseif ($file_name[1] == "lib") {
                        if (!isset($file_name[3]))
                            continue;

                        $path = PATH_ROOT . DS . "language" . DS . $file_name[2];


                        if (!is_dir($path))
                            mkdir($path);

                        copy($pathInstall . DS . $front . DS . $file, $path . DS . $file);
                    }
                }
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
            InstallApp::deleteAll(['type' => 'languages', 'name' => $this->name]);
            foreach ($this->front as $front) {
                $app = new InstallApp();
                $app->type = "languages";
                $app->name = $this->name;
                $app->front_back = $front;

                if (!$app->save()) {
                    $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([name:' . $app->name . '])']);
                    throw new \Exception();
                }
            }

            $lang = new Languages();

            $lang->lang_code = $this->name;
            $lang->title = $this->title;
            $lang->reduce = $this->reduce;
            $lang->img = $this->img;
            $lang->enabled = $this->enabled;
            if (!$lang->save()) {
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'Languages::save([name:' . $app->name . '])']);
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
        return $this->path . DS . 'lan_' . $this->name;
    }

    public function text($const, $prop = []) {
        if ($this->defaultLang && $this->defaultLang !== null) {
            $path = $this->getPath();

            if ($this->defaultLang) {
                $group = explode("_", strtolower($const));
                if (isset($group[1]) && isset($group[2])) {
                    $path .= DS . "system" . DS . $class->defaultLang . ".lib." . $group[1] . "." . $group[2] . ".ini";
                    if (file_exists($path)) {
                        $langArr = parse_ini_file($path);
                        $const = array_key_exists($const, $langArr) ? $langArr[$const] : $const;
                    }
                }
            }
        }

        return Text::_($const, $prop);
    }

}
