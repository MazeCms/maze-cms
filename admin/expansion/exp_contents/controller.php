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
use maze\table\Contents;
use maze\db\Query;
use maze\table\Roles;


class Contents_Controller extends Controller {

    /**
     * Разрешения для дествий
     * 
     * @return boolean
     */
    public function accessFilter() {
        return [     
            'add edit sort publish unpublish home unhome' =>function($controller){
                if($this->_rout->run == 'edit' && !RC::app()->access->roles("contents", "EDIT_CONTENTS")){
                    return RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS', null,['contents_id'=>$this->request->get('contents_id')]);
                }
                return RC::app()->access->roles("contents", "EDIT_CONTENTS")  || RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS') ? true :  false;
            } , 
            'delete'=>function($controller){    
                return RC::app()->access->roles("contents", "DELETE_CONTENTS") || RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS') ? true :  false;

            }
        ];
    }
    

    public function actionDisplay() {
       
        
        $modelFilter = $this->form('FilterContent');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            
            $model = Contents::find()->joinWith(['fields', 'type', 'route', 'lang', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }])
                    ->from(['c' => Contents::tableName()])
                    ->where(['c.expansion'=>'contents'])->groupBy('c.contents_id');
            
            if(!RC::app()->access->roles("contents", "EDIT_CONTENTS") && RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS')){
                $model->andWhere(['c.id_user'=>RC::app()->access->getUid()]);
            }
            
            $modelFilter->queryBilder($model);
            
            return (new GridFormat([
                'id' => 'contents-grid',
                'model' => $model,
                'colonum' => 'c.bundle, c.sort',
                'colonumData' => [
                    'id' => '$data->contents_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'typename'=>function($data){
                        return $data->type->title;
                    },
                    'enabled',
                    'home',
                    'bundle',
                    'isDelete'=>function($data){
                        if(!RC::app()->access->roles("contents", "DELETE_CONTENTS")){                           
                           return RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS', null, ['id_user'=>$data->id_user]);
                        }
                        return true;
                    },
                    'roletitle'=>function($data){
                        $result =  array_map(function($val){
                            return $val->role->name;
                        }, $data->accessRole);
                        return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                    },     
                    'alias'=>function($data){
                        return $data->route->alias;
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
                                       ->where(['entry_id'=>$data->contents_id, 'field_exp_id'=>$fl->field_exp_id])
                                       ->one();
                                $result = $field['title_value'];
                                break;
                            }
                        }
                        
                        return $result;
                    },
                    'contents_id'
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter'=>$modelFilter]);
    }
    
    public function actionModal(){
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
  
        $modelFilter = $this->form('FilterContent');
        
        $modelFilter->load($this->request->post());
        $model = Contents::find()->joinWith(['fields', 'type', 'route', 'lang', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }])
                ->from(['c' => Contents::tableName()])
                ->where(['c.expansion'=>'contents'])->groupBy('c.contents_id');

        if(!RC::app()->access->roles("contents", "EDIT_CONTENTS") && RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS')){
            $model->andWhere(['c.id_user'=>RC::app()->access->getUid()]);
        }
        $modelFilter->queryBilder($model);

        return (new GridFormat([
            'id' => 'contents-modal-grid',
            'model' => $model,
            'colonum' => 'c.bundle, c.sort',
            'colonumData' => [
                'id' => '$data->contents_id',
                'menu' => function() {
                    return "<span class=\"menu-icon-handle\"></span>";
                },
                'typename'=>function($data){
                    return $data->type->title;
                },
                'enabled',
                'home',
                'bundle',
                'isDelete'=>function($data){
                    if(!RC::app()->access->roles("contents", "DELETE_CONTENTS")){                           
                       return RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS', null, ['id_user'=>$data->id_user]);
                    }
                    return true;
                },
                'roletitle'=>function($data){
                    $result =  array_map(function($val){
                        return $val->role->name;
                    }, $data->accessRole);
                    return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                },     
                'alias'=>function($data){
                    return $data->route->alias;
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
                                   ->where(['entry_id'=>$data->contents_id, 'field_exp_id'=>$fl->field_exp_id])
                                   ->one();
                            $result = $field['title_value'];
                            break;
                        }
                    }

                    return $result;
                },
                'contents_id'
            ]
        ]))->renderJson();
    }

    public function actionAdd($bundle, $term_id = null, $bundleCat = null, $return=null) {

        $model = $this->model('ModelContent', ['bundle' => $bundle]);

    
        if(!$model->type){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_TYPE_NOTID", ['code'=>$bundle]));
        }
        
        $fields = $model->getFields();


        if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-form') {
                return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_CONTENTS_ACTION_ADD", ['name' =>$model->title]), 'success');
                     RC::getLog()->add('exp',['component'=>'contents',
                        'category'=>__METHOD__,
                        'action'=>'add', 
                        'message'=>Text::_("EXP_CONTENTS_ACTION_ADD", ['name' =>$model->title])]);
                    if($return){
                        return $this->setRedirect($return);
                    }  
                    if ($this->request->get('action') == 'saveClose') {
                        if($term_id && $bundleCat){
                            return $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundleCat, 'term_id'=>$term_id]]);
                        }
                        return $this->setRedirect(['/admin/contents']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'contents_id' =>$model->id, 'term_id'=>$term_id]]);
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

    public function actionEdit($contents_id, $term_id = null, $bundleCat = null, $return=null) {
       $model = $this->model('ModelContent');
       
       if(!$model->find($contents_id)){
           throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONT_NOTID", ['id'=>$contents_id]));
       }
     
       if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'contents-form') {
                return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->save()) {
                    $this->setMessage(Text::_("EXP_CONTENTS_CONT_UPDATE", ['name' =>$model->title]), 'success');
                     RC::getLog()->add('exp',['component'=>'contents',
                        'category'=>__METHOD__,
                        'action'=>'edit', 
                        'message'=>Text::_("EXP_CONTENTS_CONT_UPDATE", ['name' =>$model->title])]);

                    if($return){
                        return $this->setRedirect($return);
                    } 
                    if ($this->request->get('action') == 'saveClose') {
                        if($term_id && $bundleCat){
                            return $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundleCat, 'term_id'=>$term_id]]);
                        }
                        return $this->setRedirect(['/admin/contents']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'contents_id' =>$model->id]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                }
            }
        } 
       
       return $this->renderPart("form", false, "form", ['bundle' => $model->bundle, 'model' => $model]);
    }

    public function actionSort(array $sort) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach ($sort as $cont) {
            if(isset($cont['contents_id']) && isset($cont['sort'])){
                if($obj = Contents::findOne($cont['contents_id'])){
                    $obj->sort = $cont['sort'];
                    $obj->save();
                }
            }

        }  
    }

    public function actionPublish(array $contents_id, $bundle = null, $term_id = null) {
        $this->model('ModelContent')->enabled($contents_id, 1);
        $this->setMessage("EXP_CONTENTS_CONT_PUBLISH", 'success');
        if(!$this->request->isAjax()){
            if($bundle && $term_id){
                $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundle, 'term_id'=>$term_id]]);
            }else{
                $this->setRedirect(['/admin/contents']);
            }
            
        }
    }

    public function actionUnpublish(array $contents_id, $bundle = null, $term_id = null) {
        $this->model('ModelContent')->enabled($contents_id, 0);
        $this->setMessage("EXP_CONTENTS_CONT_UNPUBLISH", 'success');
        if(!$this->request->isAjax()){            
           if($bundle && $term_id){
                $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundle, 'term_id'=>$term_id]]);
            }else{
                $this->setRedirect(['/admin/contents']);
            }
        }
    }
    
    public function actionHome(array $contents_id, $bundle = null, $term_id = null){
        $this->model('ModelContent')->home($contents_id, 1);
        $this->setMessage("EXP_CONTENTS_CONT_HOME", 'success');
        if(!$this->request->isAjax()){            
           if($bundle && $term_id){
                $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundle, 'term_id'=>$term_id]]);
            }else{
                $this->setRedirect(['/admin/contents']);
            }
        } 
    }
    
    public function actionUnhome(array $contents_id, $bundle = null, $term_id = null){
        $this->model('ModelContent')->home($contents_id, 0);
        $this->setMessage("EXP_CONTENTS_CONT_UNHOME", 'success');
        if(!$this->request->isAjax()){            
            if($bundle && $term_id){
                $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundle, 'term_id'=>$term_id]]);
            }else{
                $this->setRedirect(['/admin/contents']);
            }
        } 
    }

    public function actionDelete(array $contents_id, $bundle = null, $term_id = null) {
        
        $error = false;
        foreach($contents_id as $id){
            $model = $this->model('ModelContent', ['id'=>$id]);            
            if(!$model->delete()){
                $this->setMessage($model->getFirstErrors(), 'error');
                $error = true;
                break;
            }
        }
        
        if(!$error){
            $this->setMessage("EXP_CONTENTS_ACTION_DELETE_OK", 'success');
        }
        
        if($bundle && $term_id){
            $this->setRedirect(['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$bundle, 'term_id'=>$term_id]]);
        }else{
            $this->setRedirect(['/admin/contents']);
        }
    }

    public function actionClose($bundle = null) {
        $this->setMessage(Text::_("EXP_CONTENTS_ACTIONS_CLOSE"), 'info');
        $this->setRedirect(['/admin/contents']);
    }
    
}

?>