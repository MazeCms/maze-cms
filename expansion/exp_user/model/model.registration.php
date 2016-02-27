<?php

defined('_CHECK_') or die("Access denied");

class User_Model_Registration extends Model {

    public function isCaptcha() {
        if ($this->_config->get("captcha") && $this->getParamObj()->getVar("reg_captcha")) {
            return true;
        }
        return false;
    }

    /*
     * Добавить пользователя
     */

    public function addUser($username, $password, $email, $name) {
        $bloc = $this->getParamObj()->getVar("reg_bloc");
        $data = date('Y-m-d H:i:s');
        $timeactiv = $bloc ? date('Y-m-d H:i:s', time() + 60 * $this->getParamObj()->getVar("bloctime")) : null;
        $key = $bloc ? md5($this->_ses->generateKey(20)) : null;
        $users = array(
            'id_user' => null,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'password' => md5($password),
            'registerDate' => $data,
            'lastvisitDate' => $data,
            'timeactiv' => $timeactiv,
            'keyactiv' => $key,
            'bloc' => $bloc ? 1 : 0
        );

        $table = $this->loadTable("user");

        $table->setData($users);

        if (!$table->save()) {
            return false;
        }
        $users["id_user"] = $table->getInsetId();
        $role = $this->_config->get("role_user");
        $roles = array();
        if (is_array($role)) {

            foreach ($role as $id_role) {
                if ($id_role == 0)
                    continue;
                $roles[] = array("id_role" => $id_role, "id_user" => $users["id_user"]);
            }

            if (!empty($roles)) {
                $this->_db->insertList("PREF__user_roles", $roles);
            }
        }

        return $users;
    }

    public function sendMail($theme, $name, $email, $body) {
        $mail = RC::getMail();
        $mail->setTheme($theme);
        $mail->setRecipient(array("name" => $name, "email" => $email));
        $mail->setType(true);
        $mail->setBody($body);
        if (!$mail->send()) {
            return false;
        }

        return true;
    }

    /*
     * 	проверяем пароль
     */

    public function valid_pass($password, $password_confir) {
        $error = false;

        if (!empty($password) && !empty($password_confir)) {
            $pattern = "#^[-0-9a-z_\.]+$#i";

            if (preg_match($pattern, $password)) {

                if ($password !== $password_confir) {
                    $error = Text::_("EXP_USER_REG_PASS_NOMATCH");
                } else {
                    return false;
                }
            } else {
                $error = Text::_("EXP_USER_REG_PASS_FORMAT");
            }
        } else {
            $error = Text::_("EXP_USER_REG_PASS_EMPTY");
        }

        return $error;
    }

    /*
     * Проверка e-mail
     */

    public function valid_mail($email, $id_user = false) {
        $error = false;
        if (!empty($email)) {
            $pattern = "#^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$#i";

            if (preg_match($pattern, $email)) {
                $query = "SELECT COUNT(*) FROM PREF__users WHERE email='$email'";

                if ($id_user)
                    $query .= " AND NOT id_user = '$id_user'";

                if ($this->_db->result($query)) {
                    $error = Text::_("EXP_USER_REG_EMAIL_BUSY");
                } else {
                    return false;
                }
            } else {
                $error = Text::_("EXP_USER_REG_EMAIL_FORMAT");
            }
        } else {
            $error = Text::_("EXP_USER_REG_EMAIL_EMPTY");
        }

        return $error;
    }

    /*
     * проверяем логин пользователя username
     */

    public function valid_username($username, $id_user = false) {
        $error = false;

        if (!empty($username)) {
            $pattern = "#^[-0-9a-z_\.]+$#i";

            if (preg_match($pattern, $username)) {
                $query = "SELECT COUNT(*) FROM PREF__users WHERE username='$username'";
                if ($id_user)
                    $query .= " AND NOT id_user = '$id_user'";
                if ($this->_db->result($query)) {
                    $error = Text::_("EXP_USER_REG_LOGO_BUSY");
                } else {
                    return false;
                }
            } else {
                $error = Text::_("EXP_USER_REG_LOGO_FORMAT");
            }
        } else {
            $error = Text::_("EXP_USER_REG_LOGO_EMPTY");
        }

        return $error;
    }

    /*
      АКТИВАЦИЯ ПОЛЬЗОВАТЕЛЯ
     */

    public function getActivCode($code) {
        $db = $this->_db;
        $query = "SELECT * FROM PREF__users WHERE keyactiv = '$code'";
        $result = $db->result_assoc($query);
        $table = $this->loadTable("user");

        if (!empty($result)) {
            $result["bloc"] = 0;
            $result["keyactiv"] = "";
            $timeactiv = date('Y-m-d H:i:s', time() + 60 * $this->getParamObj()->getVar("bloctime"));
            if ($result["registerDate"] < $timeactiv) {
                $table->setData($result);
                if ($table->save($result)) {
                    return $result;
                }
            }
        }
        return false;
    }

}

?>