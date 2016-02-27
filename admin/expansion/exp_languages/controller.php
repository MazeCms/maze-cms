<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\Languages;

class Languages_Controller extends Controller {

    public function accessFilter() {
        return [
            'add edit publish unpublish' => ["languages", "EDIT_LANG"],
            'delete' => ["languages", "DELET_LANG"]
        ];
    }

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            return (new GridFormat([
                'id' => 'languages-grid',
                'model' => 'maze\table\Languages',
                'colonum' => 'id_lang',
                'colonumData' => [
                    'id' => '$data->id_lang',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'lang_code',
                    'img' => function($data) {
                        return Html::img('/' . trim($this->getParams("img_path"), '/') . '/' . $data->img);
                    },
                    'title',
                    'reduce',
                    'enabled',
                    'id_lang'
                ]
                    ]))->renderJson();
        }

        return parent::display();
    }

    public function actionPublish(array $id_lang) {

        $lang = Languages::find()->where(['id_lang' => $id_lang])->all();
        foreach ($lang as $l) {
            $l->enabled = 1;
            $l->save();
        }

        if (!$this->request->isAjax()) {
            $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_PUBLISH", 'success');
            $this->setRedirect(['/admin/languages']);
        }
    }

    public function actionUnpublish(array $id_lang) {


        $lang = Languages::find()->where(['id_lang' => $id_lang])->all();
        foreach ($lang as $l) {
            $l->enabled = 0;
            $l->save();
        }

        if (!$this->request->isAjax()) {
            $this->setMessage("EXP_LANGUAGES_CONTROLLER_MESS_UNPUBLISH", 'success');
            $this->setRedirect(['/admin/languages']);
        }
    }

    public function actionAdd() {

        $model = $this->model('Lang');
        $modelForm = new Languages();

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'languages-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if($modelForm->save()) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVE_OK"), "success");
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/languages']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_lang' => $modelForm->id_lang]]);
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVE_ERROR"), "error");
                }
            }
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model
        ]);
    }

    public function actionEdit($id_lang) {
        $model = $this->model('Lang');
        $modelForm =  Languages::findOne($id_lang);
        if(!$modelForm){
            throw new Exception(Text::_("EXP_LANGUAGES_PACKS_TABLE_MESS_NO"));  
        }
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'languages-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if($modelForm->save()) {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVE_OK"), "success");
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/languages']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_lang' => $modelForm->id_lang]]);
                } else {
                    $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_SAVE_ERROR"), "error");
                }
            }
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model
        ]);
    }

    public function actionDelete(array $id_lang) {


        $langs = Languages::find()->joinWith(['app'])->from(['l'=>Languages::tableName()])->where(['l.id_lang'=>$id_lang])->all();
        
        $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_DELETE_OK"),"success");
        
        foreach($langs as $lang){
            if($lang->app){
                $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_DELETE_ERROR_ACTIVE", ['name'=>$lang->title]),"error");
                break;
            }else{
               if(!$lang->delete()){
                   $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_DELETE_ERROR_DEL"),"error");
                   break;
               }
            }
        }   
        
        $this->setRedirect(["/admin/languages/"]);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_LANGUAGES_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect(['/admin/languages']);
    }

}

?>