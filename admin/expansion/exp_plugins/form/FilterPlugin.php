<?php

namespace exp\exp_plugins\form;
use maze\validators\DateValidator;

class FilterPlugin extends \ui\filter\Model {
    
    public $front_back;
    
    public $enabled;
    
    public $id_role;
    
    public $group_name;      
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['front_back', 'enabled', 'id_role', 'group_name'], 'safe'];
       return $rules;
    }
   
    public function queryBilder($query)
    {
        
        $query->andFilterWhere(['ia.front_back'=>$this->front_back]);
        $query->andFilterWhere(['p.enabled'=>$this->enabled]);
        $query->andFilterWhere(['p.group_name'=>$this->group_name]);
        $query->andFilterWhere(['r.id_role'=>$this->id_role]);

        
    }
    
    public function attributeLabels() {
        return[
            "front_back"=>\Text::_("EXP_PLUGINS_FILTER_FRONT_TITLE"),
            "enabled"=>\Text::_("EXP_PLUGINS_FILTER_ENABLE_TITLE"),
            "id_role"=>\Text::_("EXP_PLUGINS_FILTER_ROLE_TITLE"),            
            "group_name"=>\Text::_("EXP_PLUGINS_FILTER_GROUP_TITLE")
        ];
    }

}
