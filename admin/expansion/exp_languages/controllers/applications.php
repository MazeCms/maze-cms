<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\Languages;
use maze\table\LangApp;

class Languages_Controller_Applications extends Controller {

    public function accessFilter() {
        return [
            'display'=>["languages", "VIEW_LANG_APP"],
            'add publish unpublish defaults appname close' => ["languages", "EDIT_LANG_APP"],
            'delete' => ["languages", "DELET_LANG_APP"]
        ];
    }
    
    public function actionDisplay() {
        $modelFilter = $this->form('FilterApp');
        $model = $this->model('Lang');
        
        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $modelGrind = LangApp::find()->joinWith(['lang', 'app'])->from(["lapp"=>LangApp::tableName()]);;
            $modelFilter->queryBilder($modelGrind);
            
            return (new GridFormat([
                'id' => 'applications-lang-grid',
                'model' => $modelGrind,
                'colonum' => 'id_lang_app',                
                'colonumData' => [
                    'id' => '$data->id_lang_app',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'front_back'=>function($data){
                        return $data->app->front_back ? Text::_("EXP_LANGUAGES_APP_TABLE_SITE") : Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN");
                    },        
                    'title'=>'$data->lang->title',        
                    'type'=>function($data) use ($model){
                        return $model->getTypeName($data->app->type);
                    },
                    'app_name'=>function($data) use ($model){
                        return $model->getNameConf($data->app->type, $data->app->name, $data->app->front_back).' ['.$data->app->name.']';
                    },
                    'defaults',
                    'enabled',
                    'lang_code'=>'$data->lang->lang_code',        
                    'id_lang_app'
                ]
           ]))->renderJson();
        }
        return parent::display(['modelFilter'=>$modelFilter, 'model'=>$model]);
    }

    public function actionPublish(array $id_lang_app) {
        
        $langsApp = LangApp::find()->where(['id_lang_app'=>$id_lang_app])->all();
        foreach($langsApp as $app){
            $app->enabled = 1;
            $app->save();
        }
        
        if (!$this->request->isAjax()){
            $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_PUBLISH", 'success');
            $this->setRedirect(['/admin/languages/applications']);
        }

    }

    public function actionUnpublish(array $id_lang_app) {
        
        $langsApp = LangApp::find()->where(['id_lang_app'=>$id_lang_app])->all();
        foreach($langsApp as $app){
            $app->enabled = 0;
            $app->save();
        }
        
        if (!$this->request->isAjax()){
            $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_UNPUBLISH", 'success');
            $this->setRedirect(['/admin/languages/applications']);
        }
    
    }

    public function actionDefaults($id_lang_app) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        if(!$this->model('Lang')->defaultsApp($id_lang_app)){
            $this->setMessage(Text::_("EXP_LANGUAGES_PACKS_TABLE_MESS_NO"), "error");
        }
    }

    public function actionAdd() {
        $model = $this->model('Lang');
        $modelForm = $this->form('FormApp');

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'languages-app-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if($model->addApp($modelForm)) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVECLOSE_OK"), "success");
                    
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVE_ERROR"), "error");
                }
                return $this->setRedirect(['/admin/languages/applications']);
            }
        }

        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm,
            'model' => $model
        ]);
    }

    public function actionAppname($type, $front) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $app = $this->model('Lang')->getAppName($type, $front);
        return Html::renderSelectOptions(null, $app);       
    }


    public function actionDelete(array $id_lang_app) {
        
        $langsApp = LangApp::find()->where(['id_lang_app'=>$id_lang_app])->all();
        $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_DELETE_OK", "success");
        foreach($langsApp as $app){
            if(!$app->delete()){
                $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_DELETE_ERROR_DEL", "error");
                break;
            }
        }

        $this->setRedirect(["/admin/languages/applications"]);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect(['/admin/languages/applications']);
    }
}

?>