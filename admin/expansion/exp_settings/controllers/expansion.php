<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\grid\GridFormat;
use maze\helpers\DataTime;


class Settings_Controller_Expansion extends Controller {

    public function accessFilter() {
        return [
            'display close'=>["settings", "VIEW_EXP"],
            'publish unpublish clear refresh edit'=>["settings", "EDIT_EXP"]
        ];
    }
    
    public function actionDisplay() {
       
      if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $modelGrid = maze\table\Expansion::find()->joinWith(['installApp'])->where(['ia.front_back'=>0])->from(['e'=>maze\table\Expansion::tableName()]);
            return (new GridFormat([
                'id' => 'settings-expansion-grid',
                'model' => $modelGrid,
                'colonum' => 'e.id_exp',
                'colonumData' => [
                    'id' => '$data->id_exp',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'name',
                    'title' => function($data) {
                       $info = RC::getConf(array("type" => "expansion", "name" => $data->name));
                       return $info->get('name');
                    },
                    'enabled',
                    'description'=>function($data){
                       $info = RC::getConf(array("type" => "expansion", "name" => $data->name));
                       return $info->get('description'); 
                    },
                    'id_exp'
                ]
            ]))->renderJson();
        }

        return parent::display();
    }
    
    public function actionPublish(array $id_exp){
        $this->model('Settings')->pub($id_exp, 1);
        if(!$this->request->isAjax()){
            $this->setMessage(Text::_("EXP_SETTINGS_ACTION_PUBLISH"), "success");
            $this->setRedirect(['/admin/settings/expansion']);
        }
    }

    public function actionUnpublish(array $id_exp){
        
        $this->model('Settings')->pub($id_exp, 0);
        if(!$this->request->isAjax()){
            $this->setMessage(Text::_("EXP_SETTINGS_ACTION_UNPUBLISH"), "success");
            $this->setRedirect(['/admin/settings/expansion']);
        }
    }
    
    public function actionClear(array $id_exp){
        $this->model('Settings')->clearCache($id_exp);
        $this->setMessage('EXP_SETTINGS_CONTROLLER_CACEH_TEXTOK', 'success');
        $this->setRedirect(['/admin/settings/expansion']);
    }
    
    public function actionRefresh(array $id_exp){
        $this->model('Settings')->refresh($id_exp);
        $this->setMessage(Text::_("EXP_SETTINGS_ACTION_REFRESH"), "success");
        $this->setRedirect(['/admin/settings/expansion']);
    }
    
    public function actionEdit($id_exp){
        
        $exp = maze\table\Expansion::findOne($id_exp);
        
        if(!$exp){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_SETTINGS_CONTROLLER_NO_ID"));
        }
        
        $modelForm =  $this->form('FormExp');
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'expansion-app-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $exp->attributes = $modelForm->attributes;
                if($exp->save()) {
                    $this->setMessage(Text::_("EXP_SETTINGS_EXPANSION_SAVE_OK"), "success");
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/settings/expansion']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_exp' => $exp->id_exp]]);
                } else {
                    $this->setMessage(Text::_("EXP_SETTINGS_EXPANSION_SAVE_ERR"), "error");
                }
            }
        }else{
            $modelForm->attributes = $exp->attributes;
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'exp'=>$exp
        ]);
    }
 
    public function actionClose() {
        $this->setMessage(Text::_("EXP_SETTINGS_EXPANSION_MESS_CLOSE"), "success");
        $this->setRedirect(["/admin/settings/expansion/"]);
    }

}

?>