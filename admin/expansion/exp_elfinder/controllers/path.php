<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\base\JsExpression;
use maze\helpers\Json;
use exp\exp_elfinder\table\Dir;
use exp\exp_elfinder\table\Profile;
use exp\exp_elfinder\table\Attributes;
use maze\base\Model;

class Elfinder_Controller_Path extends Controller {
    
    public function accessFilter() {
        return [ 
            'display'=>["elfinder", 'VIEW_PATH'],
            'add edit sort' => ["elfinder", "EDIT_PATH"], 
            'delete'=>['elfinder','DELETE_PATH']
        ];
    }

    public function actionDisplay($profile_id) {

        $profile = Profile::findOne($profile_id);
        
        if(!$profile){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_ELFINDER_PROFILE_NOTID", ['id' => $profile_id]));
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = Dir::find()->where(['profile_id'=>$profile_id]);
            
            return (new GridFormat([
                'id' => 'elfinder-dir-grid',
                'model' => $model,
                'colonum' => 'sort',
                'colonumData' => [
                    'id' => '$data->path_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'path',
                    'alias',
                    'path_id',
                    'profile_id'
                ]
            ]))->renderJson();
        }
        return parent::display(['profile'=>$profile]);
    }

    public function actionAdd($profile_id) {

        $modelForm = $this->form('FormDir');
        $model = $this->model('Path');
        $modelForm->profile_id = $profile_id;
        
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));
            
            $attributes = [];
            
            $count = count($this->request->post('Attributes'));

            for($i=0; $i < $count; $i++){
                $attributes[] = new Attributes();
            }
            if($count == 0){
                $attributes = [new Attributes()];
            }
            
            Model::loadMultiple($attributes, $this->request->post(null, 'none'));
            
            if($this->request->isAjax() && $this->request->get('checkform') == 'elfinder-path-form') {

                return json_encode(['errors' =>FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if ($model->save($modelForm, $attributes)) {
                    $this->setMessage(Text::_("EXP_ELFINDER_DIR_SAVEOK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['profile_id'=>$modelForm->profile_id]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'profile_id' => $modelForm->profile_id, 'path_id'=>$modelForm->path_id]]);
                } else {
                    $this->setMessage(Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'), 'error');
                }
            }
        }else{
            $attributes = [new Attributes()];
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model,
                    'profile_id'=>$profile_id,
                    'attributes'=>$attributes
        ]);
    }

    public function actionEdit($profile_id, $path_id) {
        
        if (!($dir = Dir::findOne($path_id))) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_ELFINDER_DIR_NOTID", ['id' => $path_id]));
        }
        
        $modelForm = $this->form('FormDir');
        $model = $this->model('Path');
        $modelForm->profile_id = $dir->profile_id;
        $modelForm->path_id = $path_id;
        
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));
            
            $attributes = [];
            
            $count = count($this->request->post('Attributes'));

            for($i=0; $i < $count; $i++){
                $attributes[] = new Attributes();
            }
            if($count == 0){
                $attributes = [new Attributes()];
            }
            
            Model::loadMultiple($attributes, $this->request->post(null, 'none'));
            
            if($this->request->isAjax() && $this->request->get('checkform') == 'elfinder-path-form') {

                return json_encode(['errors' =>FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if ($model->save($modelForm, $attributes)) {
                    $this->setMessage(Text::_("EXP_ELFINDER_DIR_SAVEOK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['profile_id'=>$modelForm->profile_id]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'profile_id' => $modelForm->profile_id, 'path_id'=>$modelForm->path_id]]);
                } else {
                    $this->setMessage(Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'), 'error');
                }
            }
        }else{
            $modelForm->attributes = $dir->attributes;
            $attributes = $dir->attr;
            $modelForm->uploadallow = array_map(function($data) {
                return $data->mimetypes;
            }, $dir->uploadallow);
            if(empty($attributes)){
                $attributes = [new Attributes()];
            }
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model,
                    'profile_id'=>$profile_id,
                    'attributes'=>$attributes
        ]);
    }
    
    public function actionSort(array $sort){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
             throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach($sort as $s){
            if(isset($s['path_id']) && isset($s['sort'])){
                $dir = Dir::findOne($s['path_id']);
                $dir->sort = $s['sort'];
                $dir->save();
            }
           
        }
    }
    
    public function actionDelete(array $path_id, $profile_id){
        
        $dirs = Dir::findAll(['path_id'=>$path_id]);
        foreach($dirs as $dir){
            $dir->delete();
        }
        $this->setMessage(Text::_("EXP_ELFINDER_DIR_DELETEOK"), 'success');
        return $this->setRedirect([['profile_id'=>$profile_id]]);
    }

    
}

?>