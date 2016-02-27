<?php

defined('_CHECK_') or die("Access denied");

class User_Controller_Registration extends Controller {

    public function init() {
        if ($this->_access->get()) {
            $this->setRedirect('/user');
        }
    }

    public function actionDisplay() {
        return '';
        //parent::display();
    }

    public function record() {
        $login = Request::getVar("login", "POST");
        $email = Request::getVar("email", "POST");
        $pass = Request::getVar("password", "POST");
        $verified = Request::getVar("verified", "POST");
        $captcha = Request::getVar("captcha", "POST");
        $model = $this->loadModel("Registration");
        $input = Request::instance();
        $error = array();
        $type = 'error';

        if ($model->isCaptcha()) {
            $keycode = $this->_ses->get("registration", "user");
            if (!$captcha || $keycode !== $captcha) {
                $error["regcaptcha"] = Text::_("EXP_USER_CAPTCHA");
            }
        }

        if (($errlogin = $model->valid_username($login))) {
            $error["reglogin"] = $errlogin;
        }

        if (($errmail = $model->valid_mail($email))) {
            $error["regemail"] = $errmail;
        }

        if (($errpass = $model->valid_pass($pass, $verified))) {
            $error["regpassword"] = $errpass;
        }

        if (empty($error)) {
            if (($user = $model->addUser($login, $pass, $email, $login))) {
                if ($user["bloc"]) {
                    $tpl = $this->loadTplMail("activeuser");
                } else {
                    $tpl = $this->loadTplMail("reguser");
                }

                $smatry = $tpl->getSmarty();
                $smatry->assign("user", $user);
                $smatry->assign("pass", $pass);

                $body = $tpl->loadTpl();

                if ($model->sendMail($tpl->theme, $login, $email, $body)) {
                    if ($user["bloc"]) {
                        $error["regok"] = Text::_("EXP_USER_REG_RECORD_OKAC", array($login, $this->_config->get("site_name"), $email));
                    } else {
                        $error["regok"] = Text::_("EXP_USER_REG_RECORD_OK", array($login, $this->_config->get("site_name")));
                    }
                    $type = 'success';
                } else {
                    $error["regmail"] = Text::_("EXP_USER_REG_ERREMAIL");
                }
            } else {
                $error["regerror"] = Text::_("EXP_USER_REG_RECORD_ERRADD");
            }
        }

        if ($input->isAjax()) {
            echo json_encode($error);
            return false;
        }
        $this->setMessage(implode("<br>", $error), $type);
        $this->setRedirect('user/registration');
    }

    /*
     * активация пользователя
     */

    public function activ() {
        $code = Request::getVar("code", "GET");
        $model = $this->loadModel("Registration");

        if (($user = $model->getActivCode($code))) {
            $this->setMessage(Text::_("EXP_USER_REG_RECORD_OK", array($user["username"], $this->_config->get("site_name"))), "success");
            $this->setRedirect('user');
        } else {
            Error::setError("EXP_USER_LOGIN_REQEST_ERR");
        }
    }

}

?>