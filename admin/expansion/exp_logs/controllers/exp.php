<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormatArray;
use ui\form\FormBuilder;
use ui\filter\FilterBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\table\InstallApp;
use maze\helpers\DataTime;
use maze\table\Users;

class Logs_Controller_Exp extends Controller {

    public function actionDisplay() {

        $modelFilter = $this->form('FilterApp');
        $model = $this->model('Logs');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $log = RC::getLog();

            $modelFilter->queryBilder($log);

            return (new GridFormatArray([
                'id' => 'logs-grid',
                'model' => $log->load('exp'),
                'colonum' => 'datetime',
                'colonumData' => [
                    'menu' => '$data->id',
                    'id',
                    'datetime',
                    'ip',
                    'component',
                    'category',
                    'action',
                    'message',
                    'status'
                ]
                    ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter, 'model' => $model]);
    }

    public function actionEdit($id) {
        $logs = RC::getLog()->findById($id, 'exp');

        if (!$logs) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_LOGS_ACTION_EDIT_NOT_ID", ['id' => $id]));
        }

        $user = Users::findOne($logs->user_id);

        $model = $this->model('Logs');

        if ($user) {
            $logs->user_id = $user->username . " [$logs->user_id]";
        }
        if ($logs->component !== 'system') {
            $appName = $model->getNameConf("expansion", $logs->component);
            if ($appName) {
                $logs->component = $appName . " [$logs->component]";
            }
        }

        $logs->traces = implode('<br>', explode(',', $logs->traces));

        return $this->renderPart("form", false, "form", [
                    'logs' => $logs
        ]);
    }

}

?>