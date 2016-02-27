<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\base\JsExpression;
use maze\helpers\Json;
use maze\helpers\FileHelper;
use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\table\ContentType;

class Dictionary_Controller extends Controller {

    public function accessFilter() {
        return [     
            'add edit' => ["dictionary", "EDIT_DICTIONARY"], 
            'delete'=>['dictionary','DELETE_DICTIONARY']
        ];
    }

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = ContentType::find()->where(['expansion' => 'dictionary']);
     
            return (new GridFormat([
                'id' => 'dictionary-grid',
                'model' => $model,
                'colonum' => 'bundle',
                'colonumData' => [
                    'id' => '$data->bundle',
                    'menu' => '"<span class=\"menu-icon-handle\"></span>"',
                    'bundle',
                    'title',
                    'description'
                ]
            ]))->renderJson();
        }
        return parent::display();
    }

    public function actionAdd() {
        $model = $this->model('ModelType')->createType();    
        
        if ($this->request->isPost()) {
            $model->loadAll($this->request->post());            
            if ($this->request->isAjax() && $this->request->get('checkform') == 'dictionary-form') {
                return json_encode(['errors' => FormBuilder::validate($model->type, $model->param)]);
            }
            if ($model->valid()) {
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_DICTIONARY_TYPE_ACTIONS_SAVE", ['name'=>$model->type->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/dictionary']);
                    }elseif($this->request->get('action') ==  'saveAddField'){
                        return $this->setRedirect(['/admin/dictionary/field', ['run'=>'field', 'bundle'=>$model->type->bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'bundle' => $model->type->bundle]]);
                } else {
                    $this->setMessage($model->getErrors() , "error");
                }
            }
        }
        return $this->renderPart("form", false, "form", ['modelForm' => $model->type, 'modelParam'=>$model->param]);
        
    }

    public function actionEdit($bundle) {
        
       $model = $this->model('ModelType');

        if (!$model->getTypeByBundle($bundle)) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_DICTIONARY_TYPE_ACTIONS_NOID", ['code'=>$bundle]));
        }

        if ($this->request->isPost()) {
            $model->loadAll($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'dictionary-form') {
                return json_encode(['errors' => FormBuilder::validate($model->type, $model->param)]);
            }
              
            if ($model->valid()) {                
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_DICTIONARY_TYPE_ACTIONS_SAVE", ['name'=>$model->type->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/dictionary']);
                    }
                    elseif($this->request->get('action') ==  'saveAddField'){
                        return $this->setRedirect(['/admin/dictionary/field', ['run'=>'field', 'bundle'=>$bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'bundle' => $model->type->bundle]]);
                } else {
                    $this->setMessage($model->getErrors() , "error");
                }
            }
        } 

        return $this->renderPart("form", false, "form", ['modelForm' => $model->type, 'modelParam'=>$model->param]);
    }

   

    public function actionDelete(array $bundle) {
         $error = null;
        $model = $this->model('ModelType');
        foreach($bundle as $b){            
            if(!$model->delete($b)){
               $error = $model->getErrors();
               break;
            }
        }
        
        if(!$error){
            $this->setMessage(Text::_("EXP_DICTIONARY_TYPE_ACTION_DELETE_OK", ["name"=>implode(", ",$bundle)]), 'success');
        }else{
            $this->setMessage($model->getErrors() , "error");
        }
        $this->setRedirect(['/admin/dictionary']);
       
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_DICTIONARY_ACTIONS_CLOSE"), 'info');
        $this->setRedirect(['/admin/dictionary']);
    }
}

?>