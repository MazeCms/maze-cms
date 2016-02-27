<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormatArray;
use ui\form\FormBuilder;
use ui\filter\FilterBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\table\InstallApp;
use maze\helpers\DataTime;
use maze\table\Users;

class Logs_Controller_Request extends Controller {

    public function actionDisplay() {
             
        $modelFilter = $this->form('FilterRequest');
        $model = $this->model('Logs');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            
            $log = RC::getLog();
            
            $modelFilter->queryBilder($log);

            return (new GridFormatArray([
                'id' => 'logs-request-grid',
                'model' => $log->load('request'),
                'colonum' => 'datetime',
                'colonumData' => [
                    'menu' => '$data->id',
                    'id',
                    'datetime',
                    'ip',
                    'route',
                    'category',
                    'statusText',
                    'statusCode',
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter, 'model'=>$model]);
    }
    
    public function actionEdit($id) {
        $logs = RC::getLog()->findById($id, 'request');
        
        if (!$logs) {
              throw new maze\exception\NotFoundHttpException(Text::_("EXP_LOGS_ACTION_EDIT_NOT_ID", ['id'=>$id]));
        }
        
        $user = Users::findOne($logs->user_id);
        
        $model = $this->model('Logs');
        
        if($user){
          $logs->user_id =  $user->username." [$logs->user_id]";
        }
        
        $appName = $model->getNameConf("expansion", $logs->category);
        if($appName){
            $logs->category = $appName." [$logs->category]";
        }
        
        if (!empty($logs->post)) {
            $logs->post = unserialize($logs->post);
        }
        if (!empty($logs->get)) {
            $logs->get = unserialize($logs->get);
        }
        if (!empty($logs->cookie)) {
            $logs->cookie = unserialize($logs->cookie);
        }
        if (!empty($logs->requestHeaders)) {
            $logs->requestHeaders = unserialize($logs->requestHeaders);
        }
        if (!empty($logs->responseHeaders)) {
            $logs->responseHeaders = unserialize($logs->responseHeaders);
            $head = [];
            foreach ($logs->responseHeaders as $val){
                $val = explode(":",$val);
                if(isset($val[0]) && isset($val[1])){
                   $head[$val[0]] =  $val[1];
                }
            }
            $logs->responseHeaders = $head;
        }
        if (!empty($logs->session)) {
            $logs->session = unserialize($logs->session);
        }
        if (!empty($logs->server)) {
            $logs->server = unserialize($logs->server);
        }

        return $this->renderPart("form", false, "form", [
            'logs' => $logs
        ]);
    }
    
    

}

?>