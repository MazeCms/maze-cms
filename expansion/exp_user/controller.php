<?php

defined('_CHECK_') or die("Access denied");

use ui\form\FormBuilder;
use maze\helpers\DataTime;
use maze\table\Users;

use maze\db\Query;

class User_Controller extends Controller {

    public function actionDisplay() {

        return parent::display();
    }

    public function actionCaptcha() {
        var_dump(RC::app()->getComponent('user')->config->getVar('recMail'));
    }

    /*
     * 	Авторизация пользователя
     */

    public function actionLogin() {

        $modelForm = $this->form('Login');
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'user-login') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $this->_access->setLogin($modelForm->login, $modelForm->password, $modelForm->remember);
            } else {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($this->request->isAjax()) {
                return json_encode(['redirect' => true]);
            }

            $this->setMessage(implode("<br>", $mess), 'error');
            $this->setRedirect('/user');
        }
    }

    public function actionLogout() {


        $this->_access->logout();

        if ($this->request->isAjax())
            return false;

        return $this->setRedirect(['/admin/admin']);
    }

    public function actionLang($lang) {

//        $model = $this->loadModel("user");
//        if ($model->getLang($lang)) {
//            $expire = time() + 3600 * 24 * 100;
//            setcookie('lang', $lang, $expire, "/", $_SERVER['HTTP_HOST']);
//            $mess["redirect"] = true;
//            echo json_encode($mess);
//        }
    }

}

?>