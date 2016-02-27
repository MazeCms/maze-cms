<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\table\Roles;
use maze\helpers\ArrayHelper;
use maze\base\DynamicModel;

class User_Controller_Role extends Controller {

    public function accessFilter() {
        return [
            'display' => ["user", "VIEW_ROLE"],
            'add edit' => ['user', 'EDIT_ROLE'],
            'delete' => ["user", "EDIT_ROLE"]
        ];
    }

    public function actionDisplay() {
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = Roles::find();

            return (new GridFormat([
                'id' => 'role-grid',
                'model' => $model,
                'colonum' => 'id_role',
                'colonumData' => [
                    'id' => '$data->id_role',
                    'menu' => function($data) {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'name',
                    'id_role',
                    'description',
                    'private' => function($data) {
                        return count($data->rolePrivate);
                    }
                ]
                    ]))->renderJson();
        }
        return parent::display();
    }

    public function actionEdit($id_role) {

        $modelForm = $this->form('Role');
        $modelUser = $this->model('User');
        $modelForm->id_role = $id_role;
        $role = Roles::findOne($id_role);
        if(!$role){
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему id({id}) нет роли", ['id'=>$id_role]));
        }
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'role-form') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($modelUser->saveRole($modelForm)) {
                    $this->setMessage(Text::_("EXP_USER_ROLE_CONTROLLER_SAVE_OK"), 'success');

                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect('/admin/user/role');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_role' => $modelForm->id_role]]);
                }
            }
            $this->setMessage(Text::_("EXP_USER_ROLE_CONTROLLER_SAVE_ERR"), "error");
        } else {            
            $modelForm->attributes = $role->attributes;
            $private = $role->rolePrivate;
            $modelForm->private = [];
            foreach ($private as $priv) {
                $modelForm->private[] = $priv->id_priv;
            }
        }
        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm
        ]);
    }

    public function actionAdd() {
        $modelForm = $this->form('Role');
        $modelUser = $this->model('User');
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'role-form') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($modelUser->saveRole($modelForm)) {
                    $this->setMessage(Text::_("EXP_USER_ROLE_CONTROLLER_SAVE_OK"), 'success');

                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect('/admin/user/role');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_role' => $modelForm->id_role]]);
                }
            }
            $this->setMessage(Text::_("EXP_USER_ROLE_CONTROLLER_SAVE_ERR"), "error");
        }
        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm
        ]);
    }

    public function actionDelete(array $id_role) {

        $roles = Roles::findAll(['id_role'=>$id_role]);
        $selfRole = \RC::app()->access->getIdRole();
        $idRoot = \RC::app()->access->getIdAdminRole();
        if(!$roles){
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему id({id}) нет роли", ['id'=>implode(", ",$id_role)]));
        }
        foreach ($roles as $role) {
            $model = new DynamicModel(['id_role' => $role->id_role]);
            $model->addRule('id_role', function($attribute, $params) use($selfRole, $model, $idRoot){
                  if(in_array($model->id_role, $selfRole)){
                      $model->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_DELETE_SELF_ERR'));
                  }
                  if($model->id_role == $idRoot){
                       $model->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_DELETE_ROOT_ERR'));
                  }
            })->validate();
                   
            if(!$model->hasErrors()){
                $role->delete();
                $this->setMessage(Text::_("EXP_USER_ROLE_CONTROLLER_DELETE_OK"),"success");
            }
            else
            {
                RC::getLog()->add('exp',['component'=>'user',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'message'=>$model->getFirstErrors()
                 ]);

            }
        }
        
        $this->setRedirect(["/admin/user/role"]);    
    }
    
    public function actionClose() {
        $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect('/admin/user/role');
    }

}

?>