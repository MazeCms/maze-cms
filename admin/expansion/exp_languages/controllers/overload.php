<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\Languages;
use maze\table\InstallApp;
use maze\table\LangCache;
use maze\table\LangOverload;

class Languages_Controller_Overload extends Controller {

    public function init(){
        if($this->_rout->run == 'search'){
            $this->enableCsrfValidation = false;
        }       
    }
    public function accessFilter() {
        return [
            'display'=>["languages", "VIEW_LANG_OVERLOAD"],
            'add edit index' => ["languages", "VIEW_LANG_OVERLOAD"],
            'delete' => ["languages", "DELET_LANG_OVERLOAD"]
        ];
    }

    public function actionDisplay() {
        $modelFilter = $this->form('FilterOverload');
        $model = $this->model('Lang');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $modelGrind = LangOverload::find()->joinWith(['lang'])->from(["lo" => LangOverload::tableName()]);
            ;
            $modelFilter->queryBilder($modelGrind);

            return (new GridFormat([
                'id' => 'overload-languages-grid',
                'model' => $modelGrind,
                'colonum' => 'id',
                'colonumData' => [
                    'id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'constant',
                    'value',
                    'front' => function($data) {
                        return $data->front ? Text::_("EXP_LANGUAGES_APP_TABLE_SITE") : Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN");
                    },
                    'title' => '$data->lang->title'
                ]
                    ]))->renderJson();
        }
        return parent::display(['modelFilter' => $modelFilter, 'model' => $model]);
    }

    public function actionIndex() {
        $this->model('Lang')->indexOverload();
        $this->setRedirect(["/admin/languages/overload"]);
    }

    public function actionSearch() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $model = $this->model('Lang');
        $modelGrind = LangCache::find()->joinWith(['lang', 'app'])->from(["lca" => LangCache::tableName()]);
        $modelGrind->andFilterWhere(['like','lca.constant', $this->request->post('search')]);
        $modelGrind->orFilterWhere(['like','lca.value', $this->request->post('search')]);
        return (new GridFormat([
            'id' => 'applications-ovreloadsearch-grid',
            'model' => $modelGrind,
            'colonum' => 'id',
            'colonumData' => [
                'constant',
                'value',
                'front'=>'$data->app->front_back',
                'id_lang'=>'$data->lang->id_lang',
                'front_back' => function($data) {
                    return $data->app->front_back ? Text::_("EXP_LANGUAGES_APP_TABLE_SITE") : Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN");
                },
                'title' => '$data->lang->title',
                'type' => function($data) use ($model) {
                    return $model->getTypeName($data->app->type);
                },
                'app_name' => function($data) use ($model) {
                    return $model->getNameConf($data->app->type, $data->app->name, $data->app->front_back) . ' [' . $data->app->name . ']';
                }
            ]
        ]))->renderJson();
    }

    public function actionAdd() {

        $model = $this->model('Lang');
        $modelForm = $this->form('FormOverload', ['scenario' => 'create']);

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'languages-overload-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($model->saveOverload($modelForm)) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_OPEN"), "success");
                    if ($this->request->get('action') !== 'saveClose') {
                        return $this->setRedirect([['run' => 'edit', 'id' => $modelForm->id]]);
                    }
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_ERR_FATAL"), "error");
                }

                return $this->setRedirect(["/admin/languages/overload"]);
            }
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model
        ]);
    }

    public function actionEdit($id) {
        $model = $this->model('Lang');
        $modelForm = $this->form('FormOverload', ['scenario' => 'edit']);
        $modelForm->id = $id;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'languages-overload-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($model->saveOverload($modelForm)) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_OK_OPEN"), "success");
                    if ($this->request->get('action') !== 'saveClose') {
                        return $this->setRedirect([['run' => 'edit', 'id' => $modelForm->id]]);
                    }
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_SAVE_ERR_FATAL"), "error");
                }

                return $this->setRedirect(["/admin/languages/overload"]);
            }
        }else{
            $langOverload = LangOverload::findOne($id);
            $modelForm->attributes =  $langOverload->attributes;
            $modelForm->constant = $langOverload->constant;
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model
        ]);
    }

    public function actionDelete(array $id) {
        if($this->model('Lang')->deleteOverload($id)){
            $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_DELET_OK"), 'success');
        }else{
           $this->setMessage(Text::_("EXP_LANGUAGES_OVERLOAD_DELET_ERR"), 'error');
        }
        $this->setRedirect(['/admin/languages/overload']);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect(['/admin/languages/overload']);
    }

}

?>