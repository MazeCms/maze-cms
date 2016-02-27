<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\upload\UploadedFile;
use maze\upload\UploadedUrl;
use maze\upload\UploadedPath;
use maze\table\InstallApp;
use maze\helpers\FileHelper;

class Installapp_Controller_Install extends Controller {

    public function accessFilter() {
        return [
            'install dispaly' => ["installapp", "INSTALL_APP"]
        ];
    }

    public function actionDisplay() {
        return parent::display();
    }

    public function actionInstall() {

        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        $modelForm = $this->form('FormUpload');
        $model = $this->model('Install');
        $response = null;

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());
            $modelForm->scenario = $modelForm->type;
            $modelForm->load($this->request->post());

            if ($modelForm->type == 'file') {
                $file = $modelForm->file = UploadedFile::getInstance($modelForm, 'file');
            } elseif ($modelForm->type == 'url') {
                $file = $modelForm->url = UploadedUrl::getInstance($modelForm, 'url');
            } elseif ($modelForm->type == 'path') {
                $file = $modelForm->path = UploadedPath::getInstance($modelForm, 'path');
            }

            if ($modelForm->validate()) {
                $path = RC::getAlias('@root/temp/install');
                if (!is_dir($path)) {
                    FileHelper::createDirectory($path);
                }
                if ($file->saveAs($path . DS . $file->fileName)) {
                    $response = ['name' => $file->fileName];
                } else {
                    $modelForm->addError('type', Text::_('EXP_INSTALLAPP_INSTALL_ERROR_NODIR'));
                    $response = ['errors' => FormBuilder::validate($modelForm)];
                }
            } else {
                $response = ['errors' => FormBuilder::validate($modelForm)];
            }
        }

        if ($this->request->get('cmd')) {
            try {
                if ($this->request->get('cmd') == 'unzip') {
                    if ($type = $model->unzip($this->request->get('name'))) {
                        $type['mode'] = Installer::INSTALL;
                        $install = Installer::instance($type);
                        $response = ['step' => $install->getCommands(), 'params'=>['name'=>$install->name, 'type'=>$install->type, 'group'=>$install->group]];
                    } else {
                        $response = ['errors' => FormBuilder::validate($model)];
                    }
                } else {
                    $args = $this->request->get('params');
                    $args['mode'] = Installer::INSTALL;

                    $install = Installer::instance($args);
                    $app = $install->exec($this->request->get('cmd'));
                    if($app->hasErrors()){
                        $response = ['errors' => $app->getErrors()];
                    }else{
                        $response = ['result'=>1];
                    }
                    $this->_cache->fullClear();
                }
            } catch (Exception $ex) {
                $response = ['errors' => $ex->getMessage()];
            }
        }

        return json_encode(['html' => $response]);
    }


}

?>