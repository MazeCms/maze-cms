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

class LibInstall extends BaseInstall {

    /**
     * @var string - базовый тип расширения
     */
    protected $type = "library";

    /**
     * @var array - массив файлов с SQL скриптами 
     */
    protected $sql;

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (InstallApp::find()->where(['name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_ISNAME", ['name' => $this->name]);
        }

        return $this;
    }

    /**
     * Команда копирования дистрибутива
     * 
     * @return ExpInstall - self
     */
    public function actionCopy() {

        $path = PATH_ROOT . DS . "library" . DS . $this->name;


        if (is_dir($path)) {
            return $this->addError('copy', "LIB_FRAMEWORK_INSTALL_DIR", ['name' => $path]);
        }

        if (is_dir($this->getPath() . DS . "language")) {
            if (!is_dir(PATH_ROOT . DS . "language" . DS . $this->name))
                mkdir(PATH_ROOT . DS . "language" . DS . $this->name);
            FileHelper::move($this->getPath() . DS . "language", PATH_ROOT . DS . "language" . DS . $this->name);
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
            InstallApp::deleteAll(['type' => 'library', 'name' => $this->name]);
            $app = new InstallApp();
            $app->type = "library";
            $app->name = $this->name;
            $app->front_back = 1;

            if (!$app->save()) {
                $this->addError('add', "LIB_FRAMEWORK_INSTALL_ADD_SAVE", ['name' => 'InstallApp::save([name:' . $app->name . '])']);
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
     * Команда добавления SQL скриптов в БД
     * должна существовать директория ~admin/sql
     * 
     * @return ExpInstall - self
     */
    public function actionSql() {
        if ($this->sql && is_array($this->sql) && !empty($this->sql)) {
            $dir = $this->getPath() . DS . "sql";

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
                        $this->addError('sql', "LIB_FRAMEWORK_INSTALL_BD_QUERY", ['query' => $qu]);
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
    public function actionRemove() {

        $path = $this->getPath();

        FileHelper::remove($path);
        if (is_dir($path)) {
            $this->addError('remove', "LIB_FRAMEWORK_INSTALL_ACTIONREMOVE_NODIR", ['name' => $path]);
        }

        return $this;
    }

    public function getPath() {
        return $this->path . DS . 'lib_' . $this->name;
    }

    public function text($const, $prop = []) {
        if ($this->defaultLang && $this->defaultLang !== null) {
            $path = $this->getPath();

            if ($this->defaultLang) {
                $group = explode("_", $const);
                if (isset($group[2])) {
                    $path .= DS . "language" . DS . $this->defaultLang . ".lib." . $this->name . "." . strtolower($group[2]) . ".ini";
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
