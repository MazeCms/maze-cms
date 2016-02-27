<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Template;


class TmpUninstall extends BaseInstall {

    /**
     * @var string - базовый тип расширения
     */
    protected $type = "template";

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
            InstallApp::deleteAll(['type' => 'template', 'name' => $this->name, 'front_back'=>$this->front]);
            Template::deleteAll(['name' => $this->name, 'front'=>$this->front]);
            $transaction->commit();
        } catch (\Exception $e) {
             $this->addError('del', "LIB_FRAMEWORK_INSTALL_UNSCRIPTS", ['name' => 'WidUninstall::actionDel']);
            $transaction->rollBack();
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
        return ($this->front ? PATH_ROOT : PATH_ADMINISTRATOR).DS.'templates'.DS.$this->name;
    }

    public function text($const, $prop = []) {

        return Text::_($const, $prop);
    }

}
