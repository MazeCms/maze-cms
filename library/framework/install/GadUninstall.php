<?php

namespace maze\install;

use Text;
use RC;
use maze\helpers\FileHelper;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\Gadgets;


class GadUninstall extends BaseInstall {

    
    /**
     * @var string - базовый тип расширения
     */
    protected $type = "gadget";
    

    /**
     * Команда инициализации установки
     * 
     * @return ExpInstall - self
     */
    public function actionInit() {

        if (!InstallApp::find()->where(['type' => $this->type, 'name' => $this->name])->exists()) {
            return $this->addError('init', "LIB_FRAMEWORK_INSTALL_UNISTALL_NAME", ['name' => $this->name]);
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
            InstallApp::deleteAll(['type' => 'gadget', 'name' => $this->name]);
            Gadgets::deleteAll(['name' => $this->name]);
            $transaction->commit();
        } catch (\Exception $e) {
             $this->addError('del', "LIB_FRAMEWORK_INSTALL_UNSCRIPTS", ['name' => 'GadUninstall::actionDel']);
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
        return RC::getAlias('@root/admin/gadgets/gad_'.$this->name);
    }
    
    public function text($const, $prop = []) {
    
        return Text::_($const, $prop);
    }

}
