<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormatArray;
use ui\form\FormBuilder;
use ui\filter\FilterBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\table\InstallApp;
use maze\helpers\DataTime;
use maze\table\Users;

class Logs_Controller extends Controller {

    public function actionDisplay() {
        return parent::display();
    }
    
    public function actionFilesize() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
       $logs =  RC::getLog();
       $data = [
           ['name'=>Text::_('EXP_LOGS_DB_NAME'), 'y'=>$logs->getFileSize('db')/ 1024],
           ['name'=>Text::_('EXP_LOGS_EXT_NAME'), 'y'=>$logs->getFileSize('exp')/ 1024],
           ['name'=>Text::_('EXP_LOGS_ERROR_NAME'), 'y'=>$logs->getFileSize('error')/ 1024],
           ['name'=>Text::_('EXP_LOGS_CACHE_NAME'), 'y'=>$logs->getFileSize('cache')/ 1024],
           ['name'=>Text::_('EXP_LOGS_REQUEST_NAME'), 'y'=>$logs->getFileSize('request')/ 1024],
       ];
       return json_encode(['html'=>$data]);
        
    }
    
    public function actionRequestApp() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
       $logs =  RC::getLog()->load('request');
       
       $data = [];
       
       foreach($logs as $log){
           if(!isset($data[$log->category])){
               $data[$log->category] = [
                   'name'=>$log->category,
                   'data'=>[]
               ];
           }
           
          $data[$log->category]['data'][] = ['x'=>DataTime::format($log->datetime, 'c'), 'y'=>$log->statusCode]; 
           
       }
       return json_encode(['html'=>array_values($data)]);
        
    }
    
    public function actionUserExp(){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $logs =  RC::getLog()->load('exp');
        $user = [];
        $sum = count($logs);
        foreach($logs as $log){
            if(!isset($user[$log->user_id])){
                $user[$log->user_id] = 1;
            }else{
                $user[$log->user_id] += 1;
            }
        }
        
        $data = [];
        foreach($user as $id=>$val){
            $username = !is_numeric($id) ? 'Аноним' : (($use = Users::findOne($id)) ?  $use->username : 'Удален');
            $data[] = [$username, ($val/$sum*100)]; 
        }
        
        return json_encode(['html'=>array_values($data)]);
    }
    
    public function actionErrorPocent(){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $logs =  RC::getLog()->load('error');
        $error = [];
        $sum = count($logs);
        foreach($logs as $log){
            if(!isset($error[$log->code])){
                $error[$log->code] = 1;
            }else{
                $error[$log->code] += 1;
            }
        }
        
        $data = [];
        foreach($error as $id=>$val){
            $data[] = ['code: '.$id,($val/$sum*100)]; 
        }
        
        return json_encode(['html'=>array_values($data)]);
    }
    
    public function actionDbCount(){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $logs =  RC::getLog()->load('db');
        $dbSum = [];
        foreach($logs as $log){
            $date = $log->datetime;
            
            if(!isset($dbSum[$date])){
                $dbSum[$date] = ['x'=>DataTime::format($date, 'c'), 'y'=>1];
            }else{
                $dbSum[$date]['y'] += 1;
            }
        }
       
        
        return json_encode(['html'=>array_values($dbSum)]);
    }
    
    public function actionDelete($type){
       
        if(!RC::getLog()->clear($type)){
            $this->setMessage(Text::_('EXP_LOGS_CONTROLLER_CLEARLOG_OK', ['name'=>$type]), "success");
            RC::getLog()->add('exp',['component'=>'logs',
                        'category'=>__METHOD__,
                        'action'=>'delete',
                        'status'=>'success',
                        'message'=>Text::_('EXP_LOGS_CONTROLLER_CLEARLOG_OK', ['name'=>$type])]);
        }else{
            $this->setMessage(Text::_('EXP_LOGS_CONTROLLER_CLEARLOG_ERR', ['name'=>$type]), "error"); 
            RC::getLog()->add('exp',['component'=>'logs',
                        'category'=>__METHOD__,
                        'action'=>'delete',
                        'status'=>'danger',
                        'message'=>Text::_('EXP_LOGS_CONTROLLER_CLEARLOG_ERR', ['name'=>$type])]);
        }
        
        $this->setRedirect(['/admin/logs/'.$type]);
    }
    
}

?>