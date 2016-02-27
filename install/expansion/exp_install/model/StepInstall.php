<?php

namespace exp\exp_install\model;

use RC;
use Text;
use maze\base\Model;
use maze\db\Connection;
use maze\install\WizardInstall;

class StepInstall extends Model {

    public $dbModel;
    public $accountModel;
    public $langModel;
    public $wizardInstall;

    public function getLang() {
        $path = RC::getAlias('@root/profiles/language');
        $paths = scandir($path);
        $result = [];
        libxml_use_internal_errors(true);
        foreach ($paths as $p) {
            if ($p !== '.' && $p !== ".." && file_exists($path . DS . $p . DS . "meta.options.xml")) {
                $xml = simplexml_load_file($path . DS . $p . DS . "meta.options.xml", null, LIBXML_NOCDATA);
                $xml = (array) $xml;
                $result[$xml['reduce']] = $xml;
            }
        }

        return $result;
    }

    public function getProfiles() {
        $path = RC::getAlias('@root/profiles/project');
        $paths = scandir($path);
        $result = [];
        foreach ($paths as $p) {
            if ($p !== '.' && $p !== ".." && ($conf = $this->getProfile($p))) {
                $result[$p] = $conf->getConfig();
            }
        }
        return $result;
    }

    public function getProfile($name) {
        if (!isset($this->wizardInstall[$name])) {
            $this->wizardInstall[$name] = new WizardInstall(['project' => $name]);
            if (!$this->wizardInstall[$name]->getConfig()) {
                $this->wizardInstall[$name] = null;
            }
        }

        return $this->wizardInstall[$name];
    }

    public function createCommandStep($commad, $wizardInstall) {
        $result = false;
        if (in_array($commad, ['createTable', 'installDB', 'createConfigPhp']) && $this->hasMethod($commad)) {
            $result = $this->$commad($wizardInstall);
        } else {
            if ($wizardInstall && $wizardInstall->allModel) {
                $i = 1;
                 
                foreach ($wizardInstall->allModel as $m) {
                    if($m->formName() == $commad){
                        $result = $m->action($this->getDB());
                       
                        $result['step'] = 3 + $i;
                        $result['curentStep'] = $commad;
                        $result['nextStep'] = $wizardInstall->getNextStep($commad);
                        break;
                    }
                    $i++;
                }
            }
        }
        return $result;
    }

    public function copyLang($lang) {
        $pathInstall = RC::getAlias('@root/profiles/language/' . $lang);
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
    }

    /**
     * Создаем таблицы ьазы данных
     * 
     */
    public function createTable($wizardInstall) {
        $db = $this->getDB();
        $response = [];
        $tablePath = RC::getAlias('@root/profiles/install/tables.php');

        if (file_exists($tablePath)) {
            $tables = include_once $tablePath;
            if (empty($tables)) {
                $this->addError('dbModel', 'Запросы для БД отсуствуют');
            }
            if (!$this->hasErrors()) {
                try {
                    $transaction = $db->beginTransaction();
                    $data = $db->createCommand("SHOW TABLES")->queryAll();
                    if($this->dbModel->clear && $data){
                        //удаляем внешние ключи
                        foreach ($data as $t) {
                            $t = array_values($t)[0];

                            $table = $db->createCommand("SHOW CREATE TABLE " . $t)->queryOne();

                            if (preg_match_all("/CONSTRAINT\s*`([^`]+)`\s*FOREIGN\s*KEY/isU", $table['Create Table'], $foreignKeys, PREG_SET_ORDER)) {
                                foreach ($foreignKeys as $fk) {
                                    if (isset($fk[1])) {
                                        $db->createCommand()->dropForeignKey($fk[1], $t)->execute();
                                    }
                                }
                            }
                        }
                        //удаляем таблицы
                        foreach ($data as $t) {
                            $t = array_values($t)[0];
                            $db->createCommand()->dropTable($t)->execute();
                        }
                    }
                
                    foreach ($tables as $table => $data) {
                        try {
                          
                            if (isset($data['columns']) && !empty($data['columns'])) {
                                
                                $options = isset($data['options']) && !empty($data['options']) ? $data['options'] : 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
                                $db->createCommand()->createTable('{{%' . $table . '}}', $data['columns'], $options)->execute();
                            }
                        } catch (\Exception $e) {
                          
                            $q = $db->createCommand()->createTable('{{%' . $table . '}}', $data['columns'], $options)->getRawSql();
                            throw new \Exception(Text::_('Ошибка выполнения запроса {query} (error:{mess})', ['query' => $q, 'mess' => $e->getMessage()]));
                        }
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $this->addError('dbModel', $e->getMessage());
                    $transaction->rollBack();
                }
            }
        } else {
            $this->addError('dbModel', 'Отсутвует файл установки таблиц');
        }
        $result = ['message' => 'Системные таблицы успешно созданы', 'step' => 1, 'curentStep' => 'createTable', 'nextStep' => 'installDB', 'resultCode' => 1];
        if ($this->hasErrors()) {
           $result['message'] = $this->getFirstError('dbModel'); 
            $result['resultCode'] = 0;
        }
        return $result;
    }

    public function installDB($wizardInstall) {
        $db = $this->getDB();
        $tablePath = RC::getAlias('@root/profiles/install/database.php');
        if (file_exists($tablePath)) {
            $tables = include_once $tablePath;

            if (empty($tables)) {
                $this->addError('dbModel', 'Запросы для БД отсуствуют');
            }
            if (!$this->hasErrors()) {
                try {
                    $transaction = $db->beginTransaction();
                    foreach ($tables as $table => $data) {
                        if (is_callable($data)) {
                            call_user_func($data, $db, $table, $this);
                        } elseif (is_array($data)) {
                            foreach ($data as $d) {
                                try {
                                    $db->createCommand()->insert('{{%' . $table . '}}', $d)->execute();
                                } catch (\Exception $e) {
                                    $q = $db->createCommand()->insert('{{%' . $table . '}}', $d)->getRawSql();
                                    throw new \Exception(Text::_('Ошибка выполнения запроса {query} (error:{mess})', ['query' => $q, 'mess' => $e->getMessage()]));
                                }
                            }
                        }
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $this->addError('dbModel', $e->getMessage());
                    $transaction->rollBack();
                }
            }
        } else {
            $this->addError('dbModel', 'Отсутвует файл установки данных');
        }

        $result = ['message' => 'Данные базы данных установлены успешно', 'step' => 2, 'curentStep' => 'installDB', 'nextStep' => 'createConfigPhp', 'resultCode' => 1];
        if ($this->hasErrors()) {
            $result['message'] = $this->getFirstError('dbModel'); 
            $result['resultCode'] = 0;
        }
        return $result;
    }

    public function getDB() {
        return new Connection([
            "dsn" => $this->dbModel->type . ":host=" . $this->dbModel->host . ";dbname=" . $this->dbModel->dbname,
            "username" => $this->dbModel->user,
            "password" => $this->dbModel->password,
            "charset" => $this->dbModel->encoding,
            "tablePrefix" => $this->dbModel->prefix . "_",
            "queryCacheDuration" => (int) 15
        ]);
    }

    public function createConfigPhp($wizardInstall) {
        $path = RC::getAlias('@root/profiles/semple.configuration.php');
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            $prop = [];
            foreach ($this->dbModel->attributes as $name => $val) {
                $prop['{' . $name . '}'] = $val;
            }
            $contents = strtr($contents, $prop);
            $prop = [];
            foreach ($this->accountModel->attributes as $name => $val) {
                $prop['{' . $name . '}'] = $val;
            }
            $contents = strtr($contents, $prop);
            if (!file_put_contents(RC::getAlias('@root/configuration.php'), $contents)) {
                $this->addError('accountModel', 'Ошибка записи файла конфигурации ' . RC::getAlias('@root/configuration.php'));
            }
        } else {
            $this->addError('accountModel', 'Отсутвует файл установки ' . $path);
        }

        $result = ['message' => 'Файл конфигурации успешно создан', 'step' => 3, 'curentStep' => 'createConfigPhp', 'nextStep' => $wizardInstall->firstStep(), 'resultCode' => 1];

        if ($this->hasErrors()) {
            $result['message'] = $this->getFirstError('accountModel'); 
            $result['resultCode'] = 0;
        }
        return $result;
    }

}
