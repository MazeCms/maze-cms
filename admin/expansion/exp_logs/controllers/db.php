<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormatArray;
use ui\form\FormBuilder;
use ui\filter\FilterBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\table\InstallApp;
use maze\helpers\DataTime;
use maze\table\Users;

class Logs_Controller_Db extends Controller {

    public function actionDisplay() {
             
        $modelFilter = $this->form('FilterDb');
        $model = $this->model('Logs');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            
            $log = RC::getLog();
            
            $modelFilter->queryBilder($log);

            return (new GridFormatArray([
                'id' => 'logs-db-grid',
                'model' => $log->load('db'),
                'colonum' => 'datetime',
                'colonumData' => [
                    'menu'=>'$data->id',
                    'id',
                    'datetime',
                    'ip',
                    'query'=>function($data){
                        return $data->query.'<br><em class="traces-mark">'.implode('<br>',explode(',', $data->traces)).'</em>';
                    },
                    'category',
                    'time'=>function($data){
                        return $data->time;
                    }
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter, 'model'=>$model]);
    }
    
    public function actionEdit($id) {
        $logs = RC::getLog()->findById($id, 'db');
        
        if (!$logs) {
              throw new maze\exception\NotFoundHttpException(Text::_("EXP_LOGS_ACTION_EDIT_NOT_ID", ['id'=>$id]));
        }
        
        $user = Users::findOne($logs->user_id);
        
        $model = $this->model('Logs');
        
        if($user){
          $logs->user_id =  $user->username." [$logs->user_id]";
        }
        
        $logs->query = SyntaxColor::instance()->parse($logs->query);
        
        $logs->traces = implode('<br>', explode(',', $logs->traces));
        return $this->renderPart("form", false, "form", [
            'logs' => $logs
        ]);
    }
    
    

}

?>