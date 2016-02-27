<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\helpers\FileHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use maze\table\DictionaryTerm;
use maze\db\Query;

class Dictionary_Controller_Term extends Controller {

    public function accessFilter() {
        return [     
            'add edit sort publish unpublish' => ["dictionary", "EDIT_TERM"], 
            'delete'=>['dictionary','DELETE_TERM']
        ];
    }

    public function actionDisplay() {
        
        $modelFilter = $this->form('FilterTerm');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = DictionaryTerm::find()->joinWith(['fields', 'type', 'route', 'lang', 'accessRole.role'])
                    ->from(['dt' => DictionaryTerm::tableName()]);
            $model->where(['dt.expansion'=>'dictionary'])->groupBy('dt.term_id');
            
            $modelFilter->queryBilder($model);
            
            return (new GridFormat([
                'id' => 'dictionary-term-grid',
                'model' => $model,
                'colonum' => 'dt.bundle, dt.sort',
                'colonumData' => [
                    'id' => '$data->term_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'typename'=>function($data){
                        return $data->type->title;
                    },
                    'enabled',
                    'bundle',
                    'alias'=>function($data){
                        return $data->route->alias;
                    },
                    'roletitle'=>function($data){
                        $result =  array_map(function($val){
                            return $val->role->name;
                        }, $data->accessRole);
                        return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                    },   
                    'langtitle'=>function($data){
                        $lang = $data->lang ;
                        return $lang ? $lang->title : Text::_('LIB_USERINTERFACE_FIELD_ALL');
                    },
                    'title'=>function($data){
                        $fields = $data->fields;
                        $result = null;
                        foreach($fields as $fl){
                            if($fl->field_name == 'title'){
                               $field = (new Query())->from("{{%field_title_title}}")
                                       ->where(['entry_id'=>$data->term_id, 'field_exp_id'=>$fl->field_exp_id])
                                       ->one();
                                $result = $field['title_value'];
                                break;
                            }
                        }
                        
                        return $result;
                    },
                    'term_id'
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter'=>$modelFilter]);
    }
    
    public function actionTerm($bundle){
        
        $modelFilter = $this->form('FilterTerm');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = DictionaryTerm::find()
                    ->joinWith(['fields', 'type', 'route', 'lang', 'accessRole.role'])
                    ->from(['dt' => DictionaryTerm::tableName()])
                    ->where(['dt.expansion'=>'dictionary', 'dt.bundle'=>$bundle])
                    ->groupBy('dt.term_id');
            
            $modelFilter->queryBilder($model);
            
            return (new GridFormat([
                'id' => 'dictionary-term-grid-tree',
                'model' => $model,
                'mode' => 'tree',
                'link' => 'parent',               
                'colonum' => 'dt.sort',
                'colonumData' => [
                    'id' => '$data->term_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'typename'=>function($data){
                        return $data->type->title;
                    },
                    'enabled',
                    'bundle',
                    'parent',        
                    'typetree' => '"term"',
                    'alias'=>function($data){
                        return $data->route->alias;
                    },
                    'roletitle'=>function($data){
                        $result =  array_map(function($val){
                            return $val->role->name;
                        }, $data->accessRole);
                        return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                    }, 
                    'langtitle'=>function($data){
                        $lang = $data->lang ;
                        return $lang ? $lang->title : Text::_('LIB_USERINTERFACE_FIELD_ALL');
                    },
                    'title'=>function($data){
                        $fields = $data->fields;
                        $result = null;
                        foreach($fields as $fl){
                            if($fl->field_name == 'title'){
                               $field = (new Query())->from("{{%field_title_title}}")
                                       ->where(['entry_id'=>$data->term_id, 'field_exp_id'=>$fl->field_exp_id])
                                       ->one();
                                $result = $field['title_value'];
                                break;
                            }
                        }
                        
                        return $result;
                    },
                    'term_id'
                ]
            ]))->renderJson();
        }
        
        return $this->renderPart("tree", false, false, ['bundle'=>$bundle, 'modelFilter'=>$modelFilter]);
        
    }

    public function actionAdd($bundle) {

        $model = $this->model('ModelTerm', ['bundle' => $bundle]);

        if(!$model->type){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_DICTIONARY_TYPE_NOTID", ['code'=>$bundle]));
        }
        
        $fields = $model->getFields();


        if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'dictionary-term-form') {
                return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_DICTIONARY_TERM_ACTION_ADD", ['name' =>$model->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['run'=>'term', 'bundle'=>$bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'term_id' =>$model->id]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                }
            }
        } else {
            foreach ($fields as $field) {
                $field->addData();
            }
        }

        return $this->renderPart("form", false, "form", ['bundle' => $bundle, 'model' => $model]);
    }

    public function actionEdit($term_id, $return = null) {
       $model = $this->model('ModelTerm');
       
       if(!$model->find($term_id)){
           throw new maze\exception\NotFoundHttpException(Text::_("EXP_DICTIONARY_TERM_NOTID", ['id'=>$term_id]));
       }
       
       if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'dictionary-term-form') {
                return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_DICTIONARY_TERM_UPDATE", ['name' =>$model->title]), 'success');
                    if($return){
                        return $this->setRedirect($return);
                    }
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['run'=>'term', 'bundle'=>$model->bundle]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'term_id' =>$model->id]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                }
            }
        } 
       
       return $this->renderPart("form", false, "form", ['bundle' => $model->bundle, 'model' => $model]);
    }

    public function actionSort(array $sort, $parent) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach ($sort as $term) {
            if(isset($term['term_id']) && isset($term['sort'])){
                if($obj = DictionaryTerm::findOne(['term_id'=>$term['term_id'], 'expansion'=>'dictionary'])){
                    $obj->sort = $term['sort'];
                    $obj->parent = $parent;
                    $obj->save();
                }
            }

        }  
    }

    public function actionPublish(array $term_id, $bundle = null) {
        $this->model('ModelTerm')->enabled($term_id, 1);
        $this->setMessage("EXP_DICTIONARY_TERM_PUBLISH", 'success');
        if(!$this->request->isAjax()){
            if($bundle){
                $this->setRedirect(['/admin/dictionary/term', ['run'=>'term', 'bundle'=>$bundle]]);
            }else{
                $this->setRedirect(['/admin/dictionary/term']);
            }
            
        }
    }

    public function actionUnpublish(array $term_id, $bundle = null) {
        $this->model('ModelTerm')->enabled($term_id, 0);
        $this->setMessage("EXP_DICTIONARY_TERM_UNPUBLISH", 'success');
        if(!$this->request->isAjax()){            
            if($bundle){
                $this->setRedirect(['/admin/dictionary/term', ['run'=>'term', 'bundle'=>$bundle]]);
            }else{
                $this->setRedirect(['/admin/dictionary/term']);
            }
        }
    }
    

    public function actionDelete(array $term_id, $bundle = null) {
        
        $error = false;
        foreach($term_id as $id){
            
            $model = $this->model('ModelTerm', ['id'=>$id]);
            if(!$model->deleteAll()){
                $this->setMessage($model->getFirstErrors(), 'error');
                $error = true;
                break;
            }
        }
        
        if(!$error){
            $this->setMessage("EXP_DICTIONARY_TERM_DELETE_OK", 'success');
        }
        
        if($bundle){
            $this->setRedirect(['/admin/dictionary/term', ['run'=>'term', 'bundle'=>$bundle]]);
        }else{
            $this->setRedirect(['/admin/dictionary/term']);
        }
    }

    public function actionClose($bundle = null) {
        $this->setMessage(Text::_("EXP_DICTIONARY_ACTIONS_CLOSE"), 'info');
        if($bundle){
            $this->setRedirect([['run'=>'term', 'bundle'=>$bundle]]);
        }else{
            $this->setRedirect(['/admin/dictionary/term']);
        }
        
    }
}

?>