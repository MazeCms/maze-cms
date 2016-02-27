<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use maze\table\FieldExp;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\fields\FieldHelper;

class Contents_Controller_Field extends Controller {

    public function accessFilter() {
        return [
            'add edit publish unpublish sort display field' => ["contents", "EDIT_FIELD_CONTENTS"],
            'delete' => ["contents", "DELETE_FIELD_CONTENTS"]
        ];
    }

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = ContentType::find()->from(['ct'=>ContentType::tableName()])
                    ->joinWith('field')->where(['ct.expansion' => 'contents'])->groupBy('bundle');
     
            return (new GridFormat([
                'id' => 'contents-field-type-grid',
                'model' => $model,
                'colonum' => 'ct.bundle',                
                'colonumData' => [
                    'bundle',
                    'title'=>function($data){
                        return Html::a($data->title, ['/admin/contents/field', ['run'=>'field', 'bundle'=>$data->bundle]]);
                    },
                    'description',
                    'countfield'=>function($data){
                        return count($data->field);
                    }
                ]
                    ]))->renderJson();
        }
        return parent::display();
    }
    
    public function actionField($bundle){
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = FieldExp::find()->from(['fe'=>FieldExp::tableName()])
                    ->joinWith('typeFields')->where(['fe.expansion' => 'contents', 'fe.bundle'=>$bundle]);
     
            return (new GridFormat([
                'id' => 'contents-field-grid',
                'model' => $model,
                'colonum' => 'fe.sort',
                'colonumData' => [
                    'id'=>'$data->field_exp_id',
                    'menu' => '"<span class=\"menu-icon-handle\"></span>"',
                    'field_name',
                    'title',
                    'active',
                    'type'=>function($data){
                        return $data->typeName;
                    },
                    'widget'=>function($data){
                        return $data->widgetName;
                    },
                    'field_exp_id',
                     'locked'       
                ]
            ]))->renderJson();
        }
        return $this->renderPart("type", null, null, ['bundle'=>$bundle]);
    }

    public function actionAdd($name, $bundle, $widget_name = null) {
       $field = FieldHelper::createField($name, [
           'expansion'=>'contents', 
           'bundle'=>$bundle,
           'widget_name'=>$widget_name
       ]);

       if ($this->request->isPost()) {
           $field->loadAll($this->request->post());
           
           if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-form-field') {
               
               if($field->widget){
                   return json_encode(['errors' => FormBuilder::validate($field, $field->settings, $field->widget)]);
               }else{
                   return json_encode(['errors' => FormBuilder::validate($field, $field->settings)]);
               }
                
           }
           $models = [$field, $field->settings];
           if($field->widget){
               $models[] = $field->widget;
           }
         
           if(maze\base\Model::validateMultiple($models)){
               if($field->save()){
                  $this->setMessage(Text::_("EXP_CONTENTS_FIELD_ACTION_ADD_OK", ['name'=>$field->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['run'=>'field', 'bundle'=>$field->bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'bundle' =>$field->bundle, 'field_exp_id'=> $field->field_exp_id]]); 
               }
               else {
                    $this->setMessage($field->getErrors() , "error");
               }
           }
       }
       
       return $this->renderPart("form", null, "form", ['field'=>$field, 'name'=>$name]);
        
    }

    public function actionEdit($field_exp_id, $widget_name = null) {
        
       if(!($field = FieldHelper::findByID($field_exp_id))){
           throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_FIELD_NOTID", ['id' => $field_exp_id]));
       }
       
       if($field->locked){
           throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_FIELD_LOCK", ['name'=> $field->type, 'id' => $field_exp_id]));
       }
       if($widget_name !== null){
          $field->widget_name = $widget_name;
       }
       if ($this->request->isPost()) {
           
           $field->loadAll($this->request->post());
           
           if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-form-field') {
               
               if($field->widget){
                   return json_encode(['errors' => FormBuilder::validate($field, $field->settings, $field->widget)]);
               }else{
                   return json_encode(['errors' => FormBuilder::validate($field, $field->settings)]);
               }
                
           }
           
           $models = [$field, $field->settings];
           if($field->widget){
               $models[] = $field->widget;
           }  
      
           if(maze\base\Model::validateMultiple($models)){
               if($field->save()){
                  $this->setMessage(Text::_("EXP_CONTENTS_FIELD_UPDATE", ['name'=>$field->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['run'=>'field', 'bundle'=>$field->bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'bundle' =>$field->bundle, 'field_exp_id'=> $field->field_exp_id]]); 
               }
               else {
                    $this->setMessage($field->getErrors() , "error");
               }
           }
       }
       
       return $this->renderPart("form", null, "form", ['field'=>$field, 'name'=>$field->type]);  
    }


    public function actionPublish(array $field_exp_id, $bundle) {
        FieldExp::updateAll(['active'=>1], ['field_exp_id'=>$field_exp_id, 'locked'=>0]);
        RC::getCache("exp_contents")->clearTypeFull();
        RC::getCache("fw_fields")->clearTypeFull();
        $this->setMessage("EXP_CONTENTS_FIELD_PUBLISH", 'success');
        if(!$this->request->isAjax()){
            $this->setRedirect(['/admin/contents/field', ['run'=>'field', 'bundle'=>$bundle]]);
        }
    }

    public function actionUnpublish(array $field_exp_id, $bundle) {
        FieldExp::updateAll(['active'=>0], ['field_exp_id'=>$field_exp_id, 'locked'=>0]);
        RC::getCache("exp_contents")->clearTypeFull();
        RC::getCache("fw_fields")->clearTypeFull();
        $this->setMessage("EXP_CONTENTS_FIELD_UNPUBLISH", 'success');
        if(!$this->request->isAjax()){            
            $this->setRedirect(['/admin/contents/field', ['run'=>'field', 'bundle'=>$bundle]]);
        }
    }
    
    public function actionDelete(array $field_exp_id, $bundle) {
        
        $result = false;
        
        foreach($field_exp_id as $id){
            if(($field = FieldHelper::findByID($id))){
                if($field->findData()){
                    $this->setMessage(Text::_("EXP_CONTENTS_FIELD_DELETE_ENTRY", ['name' => $field->title]), "error");
                    $result = true;
                    break;
                }elseif($field->locked){
                    $this->setMessage(Text::_("EXP_CONTENTS_FIELD_DELETE_LOCKED", ['name' => $field->title]), "error");
                    $result = true;
                    break;
                }else{
                    if(!$field->delete()){
                        $this->setMessage($field->getFirstErrors(), "error");
                        $result = true;
                        break;
                    }
                }
               
            }else{
                $this->setMessage(Text::_("EXP_CONTENTS_FIELD_NOTID", ['id' => $field_exp_id]), "error");
                $result = true;
                break;
            }
        }
        RC::getCache("exp_contents")->clearTypeFull();
        if(!$result){
            $this->setMessage(Text::_("EXP_CONTENTS_FIELD_DELETE_OK"), 'success');
        }
        
        $this->setRedirect(['/admin/contents/field', ['run'=>'field', 'bundle'=>$bundle]]);
    }
    
    public function actionSort(array $sort){
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach ($sort as $cont) {
            if(isset($cont['field_exp_id']) && isset($cont['sort'])){
                if($obj = FieldExp::findOne(['field_exp_id'=>$cont['field_exp_id']])){
                    $obj->sort = $cont['sort'];  
                    $obj->save();
                }
            }
        }
        RC::getCache("exp_contents")->clearTypeFull();
        RC::getCache("fw_fields")->clearTypeFull();
    }
    
    public function actionClose($bundle = null) {
        $this->setMessage(Text::_("EXP_CONTENTS_ACTIONS_CLOSE"), 'info');
        $this->setRedirect(['/admin/contents/field', ['run'=>'field', 'bundle'=>$bundle]]);
    }


}

?>