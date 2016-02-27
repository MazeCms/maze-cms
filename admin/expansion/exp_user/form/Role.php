<?php

namespace exp\exp_user\form;

use maze\base\Model;
use maze\table\Privates;

class Role extends Model {

    public $id_role;
    public $name;
    public $description;
    public $private;

    public function rules() {
        return [
            [["name"], "required"],
            [['description', 'private'], 'safe'],
            ['name', 'validRole', 'params' => ["LOGIN_ADMIN", "VIEW_ADMIN", "EDIT_USER", "DELET_USER", "EDIT_ROLE", "DELET_ROLE", "VIEW_ROLE", "ROOT"]]
        ];
    }

    public function validRole($attribute, $params) {

        $selfRole = \RC::app()->access->getIdRole();
        $idRoot = \RC::app()->access->getIdAdminRole();
        $privRoot = Privates::find()->where(['exp_name' => 'system', 'name' => 'ROOT'])->one()->id_priv;
         
        if ($this->id_role) {
            if ($this->id_role == $idRoot && !in_array($this->id_role, $selfRole)) {
                $this->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR_USERROOT'));
            }
            if ($this->id_role == $idRoot && in_array($this->id_role, $selfRole)) {
                $this->requiredPrivate($attribute, $params);
            }
            if(in_array($privRoot, $this->private) && !in_array($this->id_role, $selfRole))
            {
                $this->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR_USERROOT'));
            }
        } elseif (!empty($this->private)) {
           
            if (in_array($privRoot, $this->private)) {
                $this->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR_USERROOT'));
            }
            else {
                $this->requiredPrivate($attribute, $params);
            }
        }
    }

    protected function requiredPrivate($attribute, $params) {
        $min_private = Privates::find()->where(['exp_name' => ["system", "user"]])->andWhere(['name' => $params])->all();
        $min_private = array_map(function($data) {
            return $data->id_priv;
        }, $min_private);
        if (!empty($this->private)) {
            $result_min = array_intersect($this->private, $min_private);

            if (count($result_min) !== count($min_private)) {
                $this->addError($attribute, \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR_PRIVATE',['private'=>implode(', ', $params)]));
            }
        } else {
            $this->addError($attribute,  \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR_PRIVATE',['private'=>implode(', ', $params)]));
        }
    }

    public function attributeLabels() {
        return[
            "name" => \Text::_("EXP_USER_ROLE_FORM_LABEL_NAME"),
            "description" => \Text::_("EXP_USER_ROLE_FORM_LABEL_DES")
        ];
    }

}
