<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\InstallApp;

class Installapp_Controller extends Controller {

    public function accessFilter() {
        return [
            'uninstall sort' => ["installapp", "DELET_APP"]
        ];
    }

    public function actionDisplay() {

        $modelFilter = $this->form('FilterApp');
        $model = $this->model('Install');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $modelGrind = InstallApp::find()->joinWith(['groupExp'])->from(['ia' => InstallApp::tableName()]);
            $modelFilter->queryBilder($modelGrind);

            return (new GridFormat([
                'id' => 'installapp-grid',
                'model' => $modelGrind,
                'colonum' => 'ia.id_app',
                'colonumData' => [
                    'id' => '$data->id_app',
                    'ordering' => function() {
                        return "<span class=\"sort-icon-handle\"></span>";
                    },
                    'front' => function($data) {
                        return $data->front_back ? Text::_("EXP_INSTALLAPP_SITE") : Text::_("EXP_INSTALLAPP_ADMIN");
                    },
                    'title' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('name') : $data->name;
                    },
                    'type_name' => function($data) use ($model) {
                        return $model->getTypeApp($data->type);
                    },
                    'install_data' => function($data) {
                        return DataTime::format($data->install_data);
                    },
                    'deletes' => function($data) use ($model) {
                        return $model->isDelete($data->name, $data->type, $data->front_back) ? 1 : 0;
                    },
                    'front_back',
                    'author' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('author') : '';
                    },
                    'description' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('description') : '';
                    },
                    'version' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('version') : '';
                    },
                    'copyright' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('copyright') : '';
                    },
                    'created' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('created') : '';
                    },
                    'email' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('email') : '';
                    },
                    'siteauthor' => function($data) use ($model) {
                        $conf = $model->getAppConfig($data->type, $data->name, $data->front_back);
                        return $conf ? $conf->get('siteauthor') : '';
                    },
                    'name',
                    'id_app',
                    'type',
                    'group_name'
                ]
                    ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter]);
    }

    public function actionSort(array $sort) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
           throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        foreach ($sort as $obj) {
            if (isset($obj['id_app']) && isset($obj['ordering'])) {
                if ($exp = InstallApp::findOne($obj['id_app'])) {
                    $exp->ordering = $obj['ordering'];
                    if (!$exp->save()) {
                        
                    }
                }
            }
        }
    }

    public function actionUninstall($cmd) {

        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        try {
            if ($cmd == "command") {
                $model = $this->model('Install');

                if ($params = $model->getAppByID($this->request->get('id_app'))) {
                    $params['mode'] = Installer::UNINSTALL;
                    $install = Installer::instance($params);
                    $response = ['step' => $install->getCommands(), 'params' => $params];
                } else {
                    $response = ['errors' => FormBuilder::validate($model)];
                }
            } else {
                
                    $args = $this->request->get('params');
                    $args['mode'] = Installer::UNINSTALL;

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

        return json_encode(['html' => $response]);
    }

}

?>