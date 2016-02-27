<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\table\ContentTypeView;
use maze\table\FieldExp;
use maze\fields\FieldHelper;

class Contents_Controller_View extends Controller {

    public function accessFilter() {
        return [
            'catalog display addField editField edit publish unpublish fieldDelete delete sort' => ["contents", "EDIT_VIEW_CONTENTS"]
        ];
    }

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = ContentType::find()->where(['expansion' => 'contents']);
     
            return (new GridFormat([
                'id' => 'contents-view-grid',
                'model' => $model,
                'colonum' => 'bundle',
                'colonumData' => [
                    'id' => '$data->bundle',
                    'menu' => '"<span class=\"menu-icon-handle\"></span>"',
                    'bundle',
                    'title',
                    'description',
                    'countpreview'=>function($data){
                        return ContentTypeView::find()->where(['expansion' => 'contents', 'bundle'=>$data->bundle, 'mode'=>0])->count();
                    },
                    'countfull'=>function($data){
                        return ContentTypeView::find()->where(['expansion' => 'contents', 'bundle'=>$data->bundle, 'mode'=>1])->count();
                    }        
                ]
            ]))->renderJson();
        }
        return parent::display(['expansion'=>'contents']);
    }
    
    public function actionCatalog() {
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            
             $catalog = maze\table\FieldExp::find()
            ->from(['fe' => maze\table\FieldExp::tableName()])
            ->joinWith(['typeFields'])
            ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
            ->all();
            $dicHas = [];
            foreach ($catalog as $cat) {
                if (isset($cat->param['dictionary'])) {
                    // отбираем только уникальные словари
                    if (in_array($cat->param['dictionary'], $dicHas)){
                        continue;
                    }

                    $dicHas[] = $cat->param['dictionary'];
                }
            }

            $model = ContentType::find()->where(['expansion' => 'dictionary', 'bundle'=>$dicHas]);
     
            return (new GridFormat([
                'id' => 'contents-view-grid',
                'model' => $model,
                'colonum' => 'bundle',
                'colonumData' => [
                    'id' => '$data->bundle',
                    'menu' => '"<span class=\"menu-icon-handle\"></span>"',
                    'bundle',
                    'title',
                    'description',
                    'countpreview'=>function($data){
                        return ContentTypeView::find()->where(['expansion' => 'dictionary', 'bundle'=>$data->bundle, 'mode'=>0])->count();
                    },
                    'countfull'=>function($data){
                        return ContentTypeView::find()->where(['expansion' => 'dictionary', 'bundle'=>$data->bundle, 'mode'=>1])->count();
                    }
                ]
            ]))->renderJson();
        }
        
        return $this->renderPart("catalog", null, null, ['expansion'=>'dictionary']);
    }
    
    public function actionAddField($bundle, $expansion, $mode, $field_exp_id, $field_view = null) {
        
        $field = FieldHelper::find(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.field_exp_id'=>$field_exp_id, '{{%field_exp}}.active'=>1]);
        
        if(!$field){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_FIELD_NOTID", ['id' => $field_exp_id]));
        }
        
        $modelView = new ContentTypeView();
        $modelView->scenario = 'create';
        $modelView->expansion = $expansion;
        $modelView->bundle = $bundle;
        $modelView->entry_type = 'contents';
        $modelView->field_exp_id = $field_exp_id;
        $modelView->mode = $mode;
        
        $metaView = null;
        $fieldViewModel = null;
        $modelView->field_view = $field_view;
        if($field_view && $field->getIsView($field_view)){
            $metaView = $field->getConfigView($field_view);
            $fieldViewModel = $field->getView($field_view);
        }
        if ($this->request->isPost()) {
            $modelView->load($this->request->post(null, 'none'));
            if($fieldViewModel){
                   $fieldViewModel->load($this->request->post(null, 'none'));
            }
               
           if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-field-view-settings') {
               
               return json_encode(['errors' => FormBuilder::validate($modelView, $fieldViewModel)]);                
           }
           
           if($modelView->validate()){
               $error = false;
               if($fieldViewModel){
                   if(!$fieldViewModel->validate()){
                       $error = true;
                   }else{
                       $modelView->field_view_param = $fieldViewModel->attributes;
                   }
               }
               
               if(!$error){
                   if($modelView->save()){                       
                       $this->setMessage(Text::_("EXP_CONTENTS_ACTION_VIEW_ADDFIELD_OK", ['name'=>$field->title]), 'success');
                        if ($this->request->get('action') == 'saveClose') {
                            return $this->setRedirect([['run'=>'edit', 'expansion'=>$expansion, 'mode'=>$mode, 'bundle'=>$bundle]]);
                        }
                        return $this->setRedirect([['run'=>'editField', 'expansion'=>$expansion, 'mode'=>$mode, 'bundle'=>$bundle, 'view_name'=>$modelView->view_name]]);
                   }else{
                       $this->setMessage($modelView->getErrors() , "error");
                   }
                  
               }
           }
           
           
        }

        
        return $this->renderPart("form", false, "field", [
            'modelView'=>$modelView,
            'metaView'=>$metaView, 
            'fieldViewModel'=>$fieldViewModel, 
            'field'=>$field
        ]);
        
    }


    public function actionEditField($bundle, $expansion, $mode, $view_name, $field_view = null){        

        $modelView =  ContentTypeView::findOne($view_name);
        
        if(!$modelView){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_FIELD_NOTID", ['id' => $view_name]));
        }
        
        $field = FieldHelper::find(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.field_exp_id'=>$modelView->field_exp_id]);
         
        $metaView = null;
        $fieldViewModel = null;
        if($field_view !== null){
            $modelView->field_view = $field_view;
        }
        
        if($modelView->field_view && $field->getIsView($modelView->field_view)){
            $fieldViewModel = $field->getView($modelView->field_view);
            $fieldViewModel->attributes = $modelView->field_view_param;
            $metaView = $field->getConfigView($modelView->field_view);
            
        }
        
        if ($this->request->isPost()) {
            $modelView->load($this->request->post(null, 'none'));
            if($fieldViewModel){
                   $fieldViewModel->load($this->request->post(null, 'none'));
            }
               
           if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-field-view-settings') {
               
               return json_encode(['errors' => FormBuilder::validate($modelView, $fieldViewModel)]);                
           }
           
           if($modelView->validate()){
               $error = false;
               if($fieldViewModel){
                   if(!$fieldViewModel->validate()){
                       $error = true;
                   }else{
                       $modelView->field_view_param = $fieldViewModel->attributes;
                   }
               }
               
               if(!$error){
                   if($modelView->save()){
                       $this->setMessage(Text::_("EXP_CONTENTS_ACTION_VIEW_EDITFIELD_OK", ['name'=>$field->title]), 'success');
                        if ($this->request->get('action') == 'saveClose') {
                            return $this->setRedirect([['run'=>'edit', 'expansion'=>$expansion, 'mode'=>$mode, 'bundle'=>$bundle]]);
                        }
                        return $this->setRedirect([['run'=>'editField', 'expansion'=>$expansion, 'mode'=>$mode, 'bundle'=>$bundle, 'view_name'=>$modelView->view_name]]);
                   }else{
                       $this->setMessage($modelView->getErrors() , "error");
                   }
                  
               }
           }
        }

        return $this->renderPart("form", false, "field", [
            'modelView'=>$modelView,
            'metaView'=>$metaView, 
            'fieldViewModel'=>$fieldViewModel, 
            'field'=>$field
        ]);
    }
    /**
     * Сохранить настройки вида
     */
    public function actionEdit($bundle, $mode, $expansion) {
        
     if($this->request->isAjax()){
            $model = ContentTypeView::find()->from(['ctv'=>ContentTypeView::tableName()])
                    ->joinWith(['fieldExp', 'fieldExp.typeFields'], false)
                    ->where(['ctv.entry_type'=>'contents', 'ctv.expansion'=>$expansion, 'ctv.bundle'=>$bundle, 'mode'=>$mode]);
            
            return (new GridFormat([
                'id' => 'contents-view-type-grid',
                'model' => $model,
                'colonum' => 'ctv.group_name, ctv.sort',                
                'colonumData' => [
                    'id' => '$data->view_name',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'field_title'=>function($data){
                        return $data->fieldExp->title;
                    },
                    'field_type'=>function($data){
                        return $data->fieldExp->typeName;
                    },        
                    'enabled',
                    'group_name',
                    'show_label'=>function($data){
                        return $data->show_label ? 'ДА' : 'НЕТ';
                    },       
                    'field_view'=>function($data){
                        if($data->field_view){
                           return  FieldHelper::getInfoView($data->fieldExp->typeFields->type, $data->field_view)->get('name');
                        }
                        return '-';
                    },
                    'view_name'
                ]
            ]))->renderJson();
        }
        
        return $this->renderPart("type", false, "form", ['bundle'=>$bundle, 'mode'=>$mode, 'expansion'=>$expansion]);
    }
    
    
    public function actionPublish(array $view_name, $bundle, $expansion, $mode) {
        ContentTypeView::updateAll(['enabled'=>1], ['view_name'=>$view_name]);
        $this->setMessage("EXP_CONTENTS_VIEW_PUBLISH", 'success');
        if(!$this->request->isAjax()){
             $this->setRedirect(['/admin/contents/view', ['run'=>'edit', 'bundle'=>$bundle, 'expansion'=>$expansion, 'mode'=>$mode]]);
        }
    }

    public function actionUnpublish(array $view_name, $bundle, $expansion, $mode) {
        ContentTypeView::updateAll(['enabled'=>0], ['view_name'=>$view_name]);
        $this->setMessage("EXP_CONTENTS_VIEW_UNPUBLISH", 'success');
        if(!$this->request->isAjax()){            
             $this->setRedirect(['/admin/contents/view', ['run'=>'edit', 'bundle'=>$bundle, 'expansion'=>$expansion, 'mode'=>$mode]]);
        }
    }
    
    public function actionSort(array $sort){
         if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach ($sort as $view) {
            if(isset($view['view_name']) && isset($view['sort'])){
                if($obj = ContentTypeView::findOne(['view_name'=>$view['view_name']])){
                    $obj->sort = $view['sort'];  
                    $obj->save();
                }
            }
        }  
    }
    
    public function actionFieldDelete(array $view_name, $bundle, $expansion, $mode) {
        
        if(ContentTypeView::deleteAll(['view_name'=>$view_name])){
            $this->setMessage(Text::_("EXP_CONTENTS_ACTION_VIEW_DELETE_FIELD_OK"), 'success');
        }else{
            $this->setMessage('EXP_CONTENTS_ACTION_DELETE_ERR', "error");
        }
        
        $this->setRedirect(['/admin/contents/view', ['run'=>'edit', 'bundle'=>$bundle, 'expansion'=>$expansion, 'mode'=>$mode]]);
    }

    public function actionDelete(array $bundle, $expansion) {
        $viewModel = $this->model('ModelView');
        if(ContentTypeView::deleteAll(['expansion'=>$expansion, 'entry_type'=>'contents',  'bundle'=>$bundle])){
            $this->setMessage(Text::_("EXP_CONTENTS_VIEW_DELETE_OK"), 'success');
        }else{
            $this->setMessage($viewModel->getErrors() , "error");
        }
        if($expansion == 'dictionary'){
            $this->setRedirect(['/admin/contents/view', ['run'=>'catalog']]);
        }else{
            $this->setRedirect(['/admin/contents/view']);
        }
        
    }
    


}

?>