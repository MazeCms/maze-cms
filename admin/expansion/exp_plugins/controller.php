<?php defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;

class Plugins_Controller extends Controller {

    public function accessFilter() {
        return [
            'publish unpublish edit sort' => ["plugins", "EDIT_PLUGIN"]
        ];
    }
    public function actionDisplay() {

        $modelFilter = $this->form('FilterPlugin');
        $model = $this->model('Plugins');
         
        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
       
            $modelGrid = maze\table\Plugin::find()->joinWith([
                        'role',
                        'installApp'
                    ])->from(['p' => maze\table\Plugin::tableName()]);
            
            $modelFilter->queryBilder($modelGrid);
            
            return (new GridFormat([
                'id' => 'plugins-grid',
                'model' => $modelGrid,
                'colonum' => 'p.group_name, p.ordering',
                'colonumData' => [
                    'id' => '$data->id_plg',
                    'ordering' => function() {
                        return "<span class=\"sort-icon-handle\"></span>";
                    },
                    'name',
                    'title'=>function($data){
                        $front = $data->installApp->front_back;
                        $conf = RC::getConf(["type"=>"plugin", "group"=>$data->group_name, "name"=>$data->name, "front"=>$front]);
                        return Html::a($conf->get('name'), ['/admin/plugins', ['run'=>'edit', 'id_plg'=>$data->id_plg, 'front'=>$front]]);
                    },        
                    'enabled',
                    'front'=>function($data){
                        return $data->installApp->front_back ? Text::_("EXP_PLUGINS_SITE") : Text::_("EXP_PLUGINS_ADMIN");
                    },
                    'front_back'=>'$data->installApp->front_back',
                    'group_name', 
                    'role' => function($data) {
                        if (!($roles = $data->role)) {
                            return Text::_("EXP_PLUGINS_ALL");
                        }
                        return implode(', ', array_map(function($role) {return $role->name;}, $roles));
                    },
                    'id_plg'
                ]
            ]))->renderJson();
        }

        return parent::display(['modelFilter'=>$modelFilter, 'model'=>$model]);
    }

    public function actionPublish(array $id_plg) {

        $this->model('Plugins')->enable($id_plg, 1);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_PLUGINS_CONTROLLER_MESS_PUBLIC_OK", 'success');
            $this->setRedirect(['/admin/plugins']);
        }
       
    }

    public function actionUnpublish(array $id_plg) {       

        $this->model('Plugins')->enable($id_plg, 0);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_PLUGINS_CONTROLLER_MESS_UNPUBLIC_OK", 'success');
            $this->setRedirect(['/admin/plugins']);
        }
        
    }

    public function actionSort(array $sort) {
       
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
             throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        foreach($sort as $plg){
            $plugin = maze\table\Plugin::findOne($plg['id_plg']);
            if($plugin){
                $plugin->ordering = $plg['ordering'];
                $plugin->save();
            }
        }
    }

    public function actionEdit($id_plg) {
       
        $modelForm = $this->form('FormPlugin');
        $model = $this->model('Plugins');
        
        $modelTable = maze\table\Plugin::find()->joinWith([
                        'role',
                        'installApp'
                    ])->where(['id_plg'=>$id_plg])->one();
        if(!$modelTable){
             throw new maze\exception\NotFoundHttpException(Text::_("EXP_PLUGINS_CONTROLLER_MESS_SAVE_ERROR"));
        }
        
        $modelForm->id_plg = $id_plg;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'plugin-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
           
                if ($model->savePlugin($modelForm)) {
                    $this->setMessage(Text::_("EXP_PLUGINS_CONTROLLER_MESS_SAVE_OK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/plugins']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_plg' => $modelForm->id_plg]]);
                } else {
                    $this->setMessage(Text::_('EXP_PLUGINS_CONTROLLER_MESS_SAVE_ERROR'), 'error');
                }
            }
        }
        else{
            $modelForm->attributes = $modelTable->attributes;
            $modelForm->id_role = array_map(function($arr){return $arr->id_role; }, $modelTable->role);
        }
        
        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm,
            'model'=>$model,
            'modelTable'=>$modelTable
        ]);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_PLUGINS_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect(['/admin/plugins']);
    }

}

?>