<?php defined('_CHECK_') or die("Access denied");

use ui\form\FormBuilder;
use maze\helpers\DataTime;
use maze\table\Users;

class User_Controller_Recover extends Controller {

    
    public function accessFilter() {
        return [
            'display editPass' => function($contr) {
                return (int) RC::app()->getComponent('user')->config->getVar('recBloc');
            }
        ];
    }

    /**
     * 	Восстановление пароля логин или e-mail
     */
    public function actionDisplay() {
        $modelForm = $this->form('Recover');
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'user-recover') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($user = $this->model('User')->recover($modelForm->login)) {

                    $mail = RC::getMail()->compose('recovery', ['user' => $user])
                            ->setTo($user->email, $user->name)
                            ->setSubject(Text::_('EXP_USER_MAIL_THEM'))
                            ->send();
                    if ($mail) {
                        $this->setMessage(Text::_("EXP_USER_SEND_PASS"), 'success');
                    } else {
                        $this->setMessage(Text::_("EXP_USER_MAIL_FATAL"), 'error');
                    }
                } else {
                    $this->setMessage(Text::_("EXP_USER_MAIL_FATAL"), 'error');
                }
            }
        }
//        if (!$this->request->isAjax()) {
//            $this->setRedirect(['user']);
//        }
    }
    
    /**
     * 	Смена пароля по коду активации
     * @throws NotFoundHttpException - при отсутствии кода активации
     */
    public function actionEditPass($code) {
        $user = Users::findOne(['keyactiv' => $code]);
        if (!$user) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_USER_REC_NOCODE", ['code' => $code]));
        }

        $modelForm = $this->form('Editpass');
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'user-editpass') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                if ($this->model('User')->editPass($code, $modelForm->password)) {
                    $this->setMessage(Text::_("EXP_USER_REC_PASSOK"), 'success');
                    if ($modelForm->sendemail) {
                        $mail = RC::getMail()->compose('passnew', ['user' => $user, 'password'=>$modelForm->password])
                                ->setTo($user->email, $user->name)
                                ->setSubject(Text::_('EXP_USER_RECOVER_PASS'))
                                ->send();
                        if (!$mail) {
                             $this->setMessage(Text::_("EXP_USER_MAIL_FATAL"), 'error');
                        } 
                    }
                    $this->setRedirect(['/user']);
                } else {
                    $this->setMessage(Text::_("EXP_USER_REC_MAIL_FATAL"), 'error');
                }
            }
        }

        return $this->renderPart("recover", false, false,[
                    'modelForm' => $modelForm
        ]);
    }

   

}

?>