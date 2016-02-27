<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\Template;

class Templating_Controller extends Controller {

    public function accessFilter() {
        return [
            'add edit home copy' => ["templating", "EDIT_STYLE"],
            'delete' => ["templating", "DELET_STYLE"]
        ];
    }

    public function actionDisplay() {
        $modelFilter = $this->form('FilterStyle');
        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = Template::find();
            $modelFilter->queryBilder($model);
            return (new GridFormat([
                'id' => 'style-grid',
                'model' => $model,
                'colonum' => 'front',
                'colonumData' => [
                    'id' => '$data->id_tmp',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'name',
                    'title',
                    'username',
                    'home',
                    'front',       
                    'front_name' => function($data) {
                        return $data->front ? Text::_("EXP_TEMPLATING_STYLE_TABLE_FRONT") : Text::_("EXP_TEMPLATING_STYLE_TABLE_ADMIN");
                    },
                    'time_active' => function($data) {
                        return DataTime::format($data->time_active, false, '-');
                    },
                    'time_inactive' => function($data) {
                        return DataTime::format($data->time_inactive, false, '-');
                    },
                    'id_tmp'
                ]
                    ]))->renderJson();
        }
        return parent::display([
                    'modelFilter' => $modelFilter
        ]);
    }

    public function actionAdd() {
        $modelForm = $this->form('Style');
        $modelForm->name = $this->request->get('name');
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'templating-style-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                
                if ($this->model('Templating')->saveStyle($modelForm)) {
                    $this->setMessage(Text::_("EXP_TEMPLATING_CONT_SAVE_MESS_YES"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect('/admin/templating');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_tmp' => $modelForm->id_tmp]]);
                } else {
                    $this->setMessage(Text::_('EXP_TEMPLATING_CONT_ERRSAVE_MESS_YES'), 'error');
                }
            }
        } 

        return $this->renderPart("form", false, "form", ['modelForm' => $modelForm]);
    }

    public function actionEdit($id_tmp) {
        $modelForm = $this->form('Style');
        $modelForm->id_tmp = $id_tmp;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());
             if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                $modelForm->title = $modelForm->title . " - (copy)";
                $modelForm->id_tmp = null;
            }
            if ($this->request->isAjax() && $this->request->get('checkform') == 'templating-style-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                
                if ($this->model('Templating')->saveStyle($modelForm)) {
                    if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                        $this->setMessage(Text::_("EXP_TEMPLATING_CONT_SAVECOPY_MESS_YES"), 'success');
                    }
                    else{
                       $this->setMessage(Text::_("EXP_TEMPLATING_CONT_SAVE_MESS_YES"), 'success');  
                    }
                   
                    if ($this->request->get('action') == 'saveClose' || $this->request->get('action') == 'saveCopy') {
                        return $this->setRedirect('/admin/templating');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_tmp' => $modelForm->id_tmp]]);
                } else {
                    $this->setMessage(Text::_('EXP_TEMPLATING_CONT_ERRSAVE_MESS_YES'), 'error');
                }
            }
        }
        else
        {           
           $tmp = Template::find()->where(['id_tmp'=>$id_tmp])->one();
           if(!$tmp){
               throw new maze\exception\NotFoundHttpException(Text::_("EXP_TEMPLATING_TMP_FORM_MESSAGE_NOTMP"));
           }
           $modelForm->attributes = $tmp->attributes;          
           $modelForm->id_exp = array_map(function($data){return $data->id_exp;}, $tmp->expansion);
           $modelForm->id_menu = array_map(function($data){return $data->id_menu;}, $tmp->menu);
           
        }
        if($this->request->get('name')){
            $modelForm->name = $this->request->get('name');
        }
        

        return $this->renderPart("form", false, "form", ['modelForm' => $modelForm]);
    }
    
    public function actionHome($id_tmp) {
       if (!$this->request->isAjax()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        return $this->model('Templating')->home($id_tmp);
    }

    public function actionCopy(array $id_tmp){
        
        $tmp = Template::findAll(['id_tmp'=>$id_tmp]);
        foreach($tmp as $t){
            $newTmp = new Template();
            $newTmp->attributes = $t->attributes;
            $newTmp->home = 0;
            $newTmp->title = $newTmp->title . " - (copy)";
            $newTmp->save();
        }
        $this->setMessage(Text::_("EXP_TEMPLATING_CONT_SAVECOPY_MESS_YES"), 'success');
        $this->setRedirect('/admin/templating');
    }
    
    public function actionDelete(array $id_tmp) {

        if($this->model('Templating')->delete($id_tmp)){
            $this->setMessage(Text::_("EXP_TEMPLATING_CONT_DELETE_MESS_YES"), "success");
        }else{
            $this->setMessage(Text::_('EXP_TEMPLATING_CONT_DELETE_MESS_NO'), 'error');
        }
        
        $this->setRedirect('/admin/templating');
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_TEMPLATING_STYLE_FORM_CLOSE_MESS_YES"), 'info');
        $this->setRedirect('/admin/templating');
    }

}

?>