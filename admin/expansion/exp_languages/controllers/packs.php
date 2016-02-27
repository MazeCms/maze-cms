<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\LangCache;

class Languages_Controller_Packs extends Controller {

    public function accessFilter() {
        return [
            'display'=>["languages", "VIEW_LANG_PACKS"],
            'add edit index' => ["languages", "EDIT_LANG_PACKS"],
            'delete' => ["languages", "DELET_LANG_PACKS"]
        ];
    }
    public function actionDisplay() {
        
        $modelFilter = $this->form('FilterPack');
        $model = $this->model('Lang');
        
        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $modelGrind = LangCache::find()->joinWith(['lang', 'app'])->from(["lca"=>LangCache::tableName()]);;
            $modelFilter->queryBilder($modelGrind);
            
            return (new GridFormat([
                'id' => 'applications-pack-grid',
                'model' => $modelGrind,
                'colonum' => 'id',                
                'colonumData' => [
                    'id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'constant',
                    'value',        
                    'front_back'=>function($data){
                        return $data->app->front_back ? Text::_("EXP_LANGUAGES_APP_TABLE_SITE") : Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN");
                    },        
                    'title'=>'$data->lang->title',        
                    'type'=>function($data) use ($model){
                        return $model->getTypeName($data->app->type);
                    },
                    'app_name'=>function($data) use ($model){
                        return $model->getNameConf($data->app->type, $data->app->name, $data->app->front_back).' ['.$data->app->name.']';
                    }
                ]
           ]))->renderJson();
        }
        return parent::display(['modelFilter'=>$modelFilter, 'model'=>$model]);
    }
    
    public function actionIndex(){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        $model = $this->model('Lang');
        
        if ($this->request->isPost() && $this->request->post('id_app')) {
            return ['html'=>$model->indexPack($this->request->post('id_app'))];
        }
        
        $apps = InstallApp::find()->all();
        $result = [];
        foreach($apps as $app){
          $result[$app->id_app] = $model->getNameConf($app->type, $app->name, $app->front_back);  
        }
        
        return json_encode(['html'=>$result]);
    }
    
    public function actionAdd() {

        $model = $this->model('Lang');
        $modelForm = $this->form('FormPack', ['scenario'=>'create']);
        
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'applications-pack-grid') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if($model->addPack($modelForm)) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_OPEN"), "success");
                    if ($this->request->get('action') !== 'saveClose') {
                       return $this->setRedirect([['run' => 'edit', 'id' => $modelForm->id]]);
                    }
                    
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_CLOSE"), "error");
                }
                
                return $this->setRedirect(['/admin/languages/packs']);
            }
        }

        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm, 
            'model'=>$model
        ]);
    }
    
    public function actionEdit($id) {

        $model = $this->model('Lang');
        $modelForm = $this->form('FormPack', ['scenario'=>'edit']);
     
        $modelForm->id = $id;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'applications-pack-grid') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if($model->addPack($modelForm)) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_OPEN"), "success");
                    if ($this->request->get('action') !== 'saveClose') {
                       return $this->setRedirect([['run' => 'edit', 'id' => $modelForm->id]]);
                    }
                    
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_CLOSE"), "error");
                }
                
                return $this->setRedirect(['/admin/languages/packs']);
            }
        }else{
            $langCache = LangCache::findOne($id);
            $modelForm->attributes =  $langCache->attributes;
            $modelForm->constant = $langCache->constant;
        }
        
        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm, 
            'model'=>$model
        ]);
    }

    public function actionDelete(array $id) {

        if($this->model('Lang')->deletePack($id)){
            $this->setMessage(Text::_("EXP_LANGUAGES_PACKS_DELETE_OK"),  "success");
        }else{
            $this->setMessage(Text::_("EXP_LANGUAGES_PACKS_DELETE_ERR"),  "error");
        }
        
        $this->setRedirect(['/admin/languages/packs']);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect(['/admin/languages/packs']);
    }

}

?>