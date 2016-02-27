<?php

defined('_CHECK_') or die("Access denied");

class User_Controller_Settings extends Controller {

    public function init() {

        if (!$this->_access->get()) {
            $this->setRedirect('/user');
        }
    }

    public function actionDisplay() {
        return '';
        //parent::display();
    }

    public function save() {
        $data = Request::getMetod("POST");
        $model = $this->loadModel("Settings");
        $input = Request::instance();

        $result = $model->save($data);

        if (isset($result["ok"])) {
            $this->setMessage(implode("<br>", $result), "success");
        } else {
            $this->setMessage(implode("<br>", $result), "error");
        }

        if ($input->isAjax()) {
            echo json_encode($result);
            return false;
        }

        $this->setRedirect('user/settings');
    }

}

?>