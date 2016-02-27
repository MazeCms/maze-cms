<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\Users;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;

class User_Controller extends Controller {

    public function accessFilter() {
        return [
            'display' => ["user", "VIEW_ADMIN"],
            'edit' => function($controller) {
        if ($this->_access->roles("user", "EDIT_USER")) {
            return true;
        } elseif ($this->request->get("id_user") && $this->_access->roles("user", "EDIT_SELF_USER") && $this->request->get("id_user") == $this->_access->getUid()) {
            return true;
        }
        return false;
    },
            'add publish unpublish pack' => ["user", "EDIT_USER"],
            'delete' => ["user", "DELET_USER"]
        ];
    }

    public function actionDisplay() {
        $modelFilter = $this->form('FilterUser');
        $modelUser = $this->model('User');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = maze\table\Users::find()->joinWith(['lang', 'role'])->from(['u' => maze\table\Users::tableName()]);

            $modelFilter->queryBilder($model);
            return (new GridFormat([
                'id' => 'user-grid',
                'model' => $model,
                'colonum' => 'lastvisitDate',
                'colonumData' => [
                    'id' => '$data->id_user',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'name' => function($data) {
                        if (!$data->avatar)
                            $data->avatar = '/library/image/custom/user.png';
                        return Html::imgThumb('@root' . $data->avatar, 50, 50) . ' ' . $data->name;
                    },
                    'username',
                    'role' => function($data) {
                        if (!($roles = $data->role)) {
                            return Text::_("EXP_USER_FILTER_NO");
                        }
                        return implode(', ', array_map(function($role) {
                                            return $role->name;
                                        }, $roles));
                    },
                    'status' => function($data) {
                        return $data->status ? 'ONLINE' : 'OFFLINE';
                    },
                    'bloc' => function($data) {
                        return $data->bloc ? 0 : 1;
                    },
                    'email',
                    'title' => function($data) {
                        $lang = $data->lang;
                        return $lang ? $lang->title : Text::_("EXP_USER_ALL");
                    },
                    'registerDate' => function($data) {
                        return DataTime::format($data->registerDate, false, '-');
                    },
                    'lastvisitDate' => function($data) {
                        return DataTime::format($data->lastvisitDate, false, '-');
                    },
                    'id_user'
                ]
                    ]))->renderJson();
        }
        return parent::display([
                    'modelFilter' => $modelFilter,
                    'modelUser' => $modelUser
        ]);
    }

    public function actionPublish(array $id_user) {
        if (empty($id_user))
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_USER_CONTROLLER_EMPTY_USER"));

        $this->model('User')->bloc($id_user, 0);
        if ($this->request->isAjax()) {
            return;
        }
        $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_PUBLIC"), 'success');
        $this->setRedirect('/admin/user');
    }

    public function actionUnpublish(array $id_user) {
        if (empty($id_user))
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_USER_CONTROLLER_EMPTY_USER"));
        $this->model('User')->bloc($id_user, 1);
        if ($this->request->isAjax()) {
            return;
        }
        $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_UNPUBLIC"), 'success');
        $this->setRedirect('/admin/user');
    }

    public function actionAdd() {
        $modelUser = $this->model('User');
        $modelForm = $this->form('User', ['scenario' => 'create']);
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'user-form') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($modelUser->saveUser($modelForm)) {
                    $this->sendMailPassword($modelForm);
                    $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_SAVE_OK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect('/admin/user');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_user' => $modelForm->id_user]]);
                } else {
                    $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_SAVE_ERROR"), "error");
                }
            }
        }
        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'modelUser' => $modelUser
        ]);
    }

    public function actionEdit($id_user) {
        $user = Users::findOne($id_user);
        if (!$user)
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_USER_CONTROLLER_EMPTY_USER"));

        $modelUser = $this->model('User');
        $modelForm = $this->form('User', ['scenario' => 'update']);
        $modelForm->id_user = $user->id_user;

        if ($this->request->isPost()) {
            if (!$this->_access->roles("user", "VIEW_ROLE")) {
                $modelForm->id_role = ArrayHelper::map($user->role, 'id_role', 'id_role');
            }
            if ($post = $this->request->post('User')) {
                if ($post['newpass'])
                    $modelForm->scenario = 'newpass';
            }
            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'user-form') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($modelUser->saveUser($modelForm)) {
                    $this->sendMailPassword($modelForm);
                    $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_SAVE_OK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect('/admin/user');
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_user' => $modelForm->id_user]]);
                } else {
                    $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_SAVE_ERROR"), "error");
                }
            }
        } else {
            $modelForm->attributes = $user->attributes;
            $modelForm->id_role = ArrayHelper::map($user->role, 'id_role', 'id_role');
        }
        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'modelUser' => $modelUser
        ]);
    }

    public function sendMailPassword($form) {

        if ($form->new_password && $form->send_email) {

            $mail = RC::getMail()->compose('passnew', [
                        'form' => $form
                    ])
                    ->setSubject(Text::_('EXP_USER_MAIL_SERVICETHEM'))
                    ->setTo($form->email, $form->name)
                    ->send();
            // событие отправки
            //RC::getPlugin("user")->triggerHandler("sendPassMail", array($user, $post, $body, $reg_flag));
            if ($mail) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_CLOSE"), 'info');
        $this->setRedirect('/admin/user');
    }

    /**
     * Рассылка писем для выбранных пользователей
     * @param array $id_user - id пользователей
     * @return type
     */
    public function actionSend(array $id_user) {

        $modelForm = $this->form('Mail');

        if ($this->request->isPost()) {
            $modelForm->load($this->request->post(null, ["mysql"]));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'mail-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $users = maze\table\Users::find()->where(['id_user' => $id_user])->all();
                foreach ($users as $user) {
                    $mail = RC::getMail()->compose('sends', ['messages' => $modelForm->mess, 'user' => $user])
                            ->setSubject($modelForm->theme)
                            ->setTo($user->email, $user->name)
                            ->send();
                    if (!$mail) {
                        $modelForm->addError('theme', Text::_('EXP_USER_CONTROLLER_MESS_MAILSEND_ERR'));
                    }
                }
            }

            if ($modelForm->hasErrors()) {
                $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_MAILSEND_ERR"), "error");
            } else {
                $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_MAILSEND_OK"), "success");
            }
            return $this->setRedirect(['/admin/user']);
        }


        return $this->renderPart("mail", false, "modal", [
                    'modelForm' => $modelForm
        ]);
    }

    public function actionPack(array $id_user) {

        $modelForm = $this->form('Pack');

        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            $modelForm->id_user = $id_user;
            if ($this->request->isAjax() && $this->request->get('checkform') == 'pack-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $this->model('User')->pack($modelForm);
            }
            $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_PACKNOHANDLER_OK"), "success");
            $this->setRedirect("/admin/user");
        }

        return $this->renderPart("pack", false, "modal", [
                    'modelForm' => $modelForm
        ]);
    }

    public function actionDelete(array $id_user) {

        if ($this->model('User')->delete($id_user)) {
            $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_DELETE_USER_OK"), "success");
        } else {
            $this->setMessage(Text::_("EXP_USER_CONTROLLER_MESS_DELETE_USER_ERR"), "error");
        }

        $this->setRedirect("/admin/user");
    }

}

?>