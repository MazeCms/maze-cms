<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormatArray;
use ui\form\FormBuilder;
use ui\filter\FilterBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\table\InstallApp;
use maze\helpers\DataTime;
use maze\table\Users;

class Logs_Controller_Error extends Controller {

    public function actionDisplay() {
             
        $modelFilter = $this->form('FilterError');
        $model = $this->model('Logs');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            
            $log = RC::getLog();
            
            $modelFilter->queryBilder($log);

            return (new GridFormatArray([
                'id' => 'logs-error-grid',
                'model' => $log->load('error'),
                'colonum' => 'datetime',
                'colonumData' => [
                    'menu' => '$data->id',
                    'id',
                    'datetime',
                    'ip',
                    'code',
                    'category',
                    'fileline'=>function($data){
                        return $data->file." ($data->line)";
                    },
                    'message',
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter, 'model'=>$model]);
    }
    
    public function actionEdit($id) {
        $logs = RC::getLog()->findById($id, 'error');
        
        if (!$logs) {
              throw new maze\exception\NotFoundHttpException(Text::_("EXP_LOGS_ACTION_EDIT_NOT_ID", ['id'=>$id]));
        }
        
        $user = Users::findOne($logs->user_id);
        
        $model = $this->model('Logs');
        
        if($user){
          $logs->user_id =  $user->username." [$logs->user_id]";
        }

        return $this->renderPart("form", false, "form", [
            'logs' => $logs
        ]);
    }

}

?>