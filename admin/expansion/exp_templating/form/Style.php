<?php

namespace exp\exp_templating\form;

use maze\base\Model;
use maze\table\Template;

class Style extends Model {

    /**
     * @var string - шаблон
     */
    public $name;

    /**
     * @var string - заголовок стиля
     */
    public $title;

    /**
     * @var bool - стиль по умолчанию
     */
    public $home;

    /**
     * @var bool - принадлежность части системы 
     */
    public $front;
   
        
    public $time_active;
    
    public $time_inactive;
     /**
     * @var array - дополнительные параметры пукта меню
     */
    public $param;
    
    public $id_menu;
    
    public $id_exp;

    public $id_tmp;
    
    public function rules() {
        return [
            [['name', 'title', 'front'], "required"],
            [['name','title'], 'string', 'max' => 255],
            [['front','home'],'boolean'],
            ['name', 'validNameTmp'],
            ['home', 'validHome', 'skipOnEmpty'=>false],
            ['name', 'exist', 'targetClass'=>'maze\table\InstallApp', 'targetAttribute'=>'name', 'filter'=>function($query){
                $query->andWhere(['type'=>'template', 'front_back'=>$this->front]);
            }],
            ['home', 'default', 'value'=>0],
            [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'],
            [['param', 'id_menu', 'id_tmp', 'id_exp'], 'safe']
        ];
    }
     
    public function validHome($attribute, $params){
        if(!$this->home && $this->id_tmp)
        {
           $tmp = Template::findOne($this->id_tmp);
           if($tmp && $tmp->home)
           {
               $this->addError($attribute, \Text::_('EXP_TEMPLATING_CONT_SAVE_MESS_NOHOME'));
           }           
        }
    }
    
    public function validNameTmp($attribute, $params){
        if($this->id_tmp)
        {
           $tmp = Template::findOne($this->id_tmp);
           if($tmp && $tmp->home && $tmp->front != $this->front)
           {
               $this->addError($attribute, \Text::_('EXP_TEMPLATING_CONT_SAVE_MESS_NO'));
           }           
        }
    }

    public function attributeLabels() {
        return[
            "name" => \Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_NAME"),
            "title"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_TITLE"),
            "home"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_TABLE_HOME"),
            "front"=>\Text::_("EXP_TEMPLATING_STYLE_TABLE_FRONT_ADMIN"),
            "time_active"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_TIMEACTIVE"),
            "time_inactive"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_FIELD_TIMEINACTIVE"),
            "id_menu"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_BIND_MENU"),
            "id_exp"=>\Text::_("EXP_TEMPLATING_STYLE_FORM_BIND_APP")
        ];
    }

}
