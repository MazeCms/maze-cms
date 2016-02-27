<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\helpers\FileHelper;

class Settings_Controller extends Controller {

    public function accessFilter() {
        return [
            'display' =>function(){
                if($this->request->isPost()){
                  return $this->_access->roles("settings", "EDIT_SYS_SERVER");
                }
                return true;
            },
            'clearcache'=>["settings", "CLEAR_CACHE"]       
        ];
    }
    
    public function actionDisplay() {

        $model = $this->model('Settings');        
        $modelForm = $this->form('FormSystem');
        
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post(null, 'none'));
           
            if ($this->request->isAjax() && $this->request->get('checkform') == 'settings-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if($model->saveConf($modelForm)){
                    $this->setMessage('EXP_SETTINGS_CONTROLLER_SAVE_OK', 'success');
                }else{
                    $this->setMessage('EXP_SETTINGS_CONTROLLER_SAVE_ERR_CONF', 'error');
                }
                  
                $this->setRedirect(['/admin/settings']); 
            }
            
        }else{
           $prop = $this->_config->getAllProp(); 
           $modelForm->attributes = $prop;
           
        }
     
       return parent::display(['modelForm'=>$modelForm, 'model'=>$model]);
    }

    public function actionClearcache() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        $this->_cache->fullClear();
        $this->setMessage('EXP_SETTINGS_CONTROLLER_CACEH_TEXTOK', 'success');
       
    }
    public function actionClearthumb() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
         FileHelper::remove(RC::getAlias('@root/images/thumb'));
         $this->setMessage('EXP_SETTINGS_CONTROLLER_CACEH_TEXTOK', 'success');
    }
    public function actionClearassets() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        FileHelper::remove(RC::getAlias('@root/assets'));
        $this->setMessage('EXP_SETTINGS_CONTROLLER_CACEH_TEXTOK', 'success');
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_SETTINGS_EXPANSION_MESS_CLOSE"), "warning");
        $this->setRedirect(["/admin/settings/"]);
    }

}

?>