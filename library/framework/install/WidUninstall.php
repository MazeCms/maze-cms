<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Privates;
use maze\table\Widgets;
use maze\table\LangApp;

class WidUninstall extends BaseInstall {

    
    /**
     * @var string - базовый тип расширения
     */
    protected $type = "widget";
    
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

        if (!InstallApp::find()->where(['type' => $this->type, 'name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_UNISTALL_NAME", ['name' => $this->name]);
        }

        if ($this->front === null) {
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
            InstallApp::deleteAll(['type' => 'widget', 'name' => $this->name, 'front_back'=>$this->front]);
            Widgets::deleteAll(['name' => $this->name]);
            $transaction->commit();
        } catch (\Exception $e) {
             $this->addError('del', "LIB_FRAMEWORK_INSTALL_UNSCRIPTS", ['name' => 'WidUninstall::actionDel']);
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
            $dir = $this->getPath() .DS. "sql";

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
            $this->addError('remove', "LIB_FRAMEWORK_INSTALL_ACTIONREMOVE_NODIR", ['name'=>$path]);
        }
        
        return $this;
        
    }
    
    public function getPath(){
        return ($this->front ? PATH_ROOT : PATH_ADMINISTRATOR).DS.'widgets'.DS.'wid_'.$this->name;
    }
    
    public function text($const, $prop = []) {
        return Text::_($const, $prop);
    }

}
