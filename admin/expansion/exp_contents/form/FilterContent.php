<?php

namespace exp\exp_contents\form;

use maze\validators\DateValidator;
use Text;

class FilterContent extends \ui\filter\Model {
    
    public $home;
    
    public $id_lang;
    
    public $id_role;
    
    public $enabled;
    
    public $time_active;
    
    public $time_inactive;
    
    public $bundle;
    
    public $alias;


    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['home', 'id_lang', 'id_role', 'enabled', 'bundle', 'alias'], 'safe'];
       $rules[] = [['time_active', 'time_inactive'], 'validDate'];
       return $rules;
    }
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute))
        {           
            $date = new DateValidator();
            $attr = $this->$attribute;
            
             
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
        
        $query->andFilterWhere(['c.id_lang'=>$this->id_lang]);
        
        $query->andFilterWhere(['c.home'=>$this->home]);
        
        $query->andFilterWhere(['c.enabled'=>$this->enabled]);
        
        $query->andFilterWhere(['c.bundle'=>$this->bundle]);
        
        $query->andFilterWhere(['like', 'route.alias', $this->alias]);
        $query->andFilterWhere(['r.id_role'=>$this->id_role]);
        if(isset($this->time_active[0])){
            $query->andFilterWhere(['>=', 'c.time_active', $this->time_active[0]]);
        }
        if(isset($this->time_active[1])){
            $query->andFilterWhere(['<=', 'c.time_active', $this->time_active[1]]);
        }
        if(isset($this->time_inactive[0])){
           $query->andFilterWhere(['>=', 'c.time_inactive', $this->time_inactive[0]]);
        }
        if(isset($this->time_inactive[1])){
            $query->andFilterWhere(['<=', 'c.time_inactive', $this->time_inactive[1]]);
        }
       
        
    }
    
    public function attributeLabels() {
        return[
            "bundle"=>Text::_("EXP_CONTENTS_TYPE"),
            "alias"=>Text::_("EXP_CONTENTS_ALIAS"),
            "home"=>Text::_("EXP_CONTENTS_HOMEIN"),
            "id_lang"=>Text::_("EXP_CONTENTS_LABEL_LANG"),
            "id_role"=>Text::_("EXP_CONTENTS_ACCESS_ROLE"),
            "enabled"=>Text::_("EXP_CONTENTS_FILTER_PUBLISH"),
            "time_active"=>Text::_("EXP_CONTENTS_FILTER_DATEACTINE"),
            "time_inactive"=>Text::_("EXP_CONTENTS_FILTER_DATEINACTINE")
        ];
    }

}
