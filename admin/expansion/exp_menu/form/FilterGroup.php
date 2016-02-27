<?php

namespace exp\exp_menu\form;
use maze\validators\DateValidator;

class FilterGroup extends \ui\filter\Model {
    
    public $enabled;
    
    public $home;
    
    public $id_tmp;
    
    public $name;

    public $id_exp;
    
    public $id_lang;
    
    public $id_role;
    
    public $typeLink;
    
    public $time_active;
    
    public $time_inactive;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['enabled', 'home', 'id_tmp', 'id_exp', 'id_lang', 'id_role', 'typeLink', 'name'], 'safe'];
       $rules[] = [['time_active', 'time_inactive'], 'validDate'];
       return $rules;
    }
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute))
        {
           
            $date = new DateValidator();
            $attr = $this->$attribute ;
            foreach($attr as $key=>$val)
            {
                if(!$date->validate($val))
                {
                    $attr[$key] = null;
                }
            }
            $this->offsetSet($attribute, $attr);
        }
    }

    public function queryBilder($query)
    {
       
        $query->andFilterWhere(['m.home'=>$this->home]);
        $query->andFilterWhere(['m.enabled'=>$this->enabled]);
        $query->andFilterWhere(['m.id_tmp'=>$this->id_tmp]);
        $query->andFilterWhere(['m.id_exp'=>$this->id_exp]);
        $query->andFilterWhere(['m.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['m.typeLink'=>$this->typeLink]);
        $query->andFilterWhere(['r.id_role'=>$this->id_role]);
        $query->andFilterWhere(['like', 'm.name', $this->name]);
        if(isset($this->time_active[0])){
             $query->andFilterWhere(['>=', 'm.time_active', $this->time_active[0]]);
        }
        if(isset($this->time_active[1])){
             $query->andFilterWhere(['<=', 'm.time_active', $this->time_active[1]]);
        }
        if(isset($this->time_inactive[0])){
             $query->andFilterWhere(['>=', 'm.time_inactive', $this->time_inactive[0]]);
        }
        if(isset($this->time_inactive[1])){
             $query->andFilterWhere(['<=', 'm.time_inactive', $this->time_inactive[1]]);
        }
            
    }
    public function attributeLabels() {
        return[
            "enabled" => \Text::_("EXP_MENU_FILTER_PUBLISH_TITLE"),
            "home" => \Text::_("EXP_MENU_FILTER_ITEM_TITLE_ELEM_HOME"),
            "id_tmp"=>\Text::_("EXP_MENU_FILTER_ITEM_TMP_TITLE"),
            "id_exp"=>\Text::_("EXP_MENU_FILTER_ITEM_EXP_TITLE"),
            "typeLink"=>\Text::_("EXP_MENU_FILTER_ITEM_TYPELINK_TITLE"),
            "id_lang"=>\Text::_("EXP_MENU_FILTER_ITEM_LANG_TITLE"),
            "id_role"=>\Text::_("EXP_MENU_FILTER_ROLES_TITLE"),
            "time_active"=>\Text::_("EXP_MENU_FILTER_ITEM_DATA_START_TITLE"),
            "time_inactive"=>\Text::_("EXP_MENU_FILTER_ITEM_DATA_END_TITLE"),
            "name"=>\Text::_("EXP_MENU_TABLE_NAME_TITLE")
        ];
    }

}
