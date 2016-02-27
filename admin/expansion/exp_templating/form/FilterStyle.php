<?php

namespace exp\exp_templating\form;
use maze\validators\DateValidator;

class FilterStyle extends \ui\filter\Model {
    
    public $front;
    
    public $tmpname;
    
    public $time_active;
    
    public $time_inactive;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['front', 'tmpname'], 'safe'];
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
        $query->andFilterWhere(['front'=>$this->front]);
        $query->andFilterWhere(['name'=>$this->tmpname]);
        $query->andFilterWhere(['>=', 'time_active', $this->time_active[0]]);
        $query->andFilterWhere(['<=', 'time_active', $this->time_active[1]]);
        $query->andFilterWhere(['>=', 'time_inactive', $this->time_inactive[0]]);
        $query->andFilterWhere(['<=', 'time_inactive', $this->time_inactive[1]]);
        
    }
    
    public function attributeLabels() {
        return[
            "front"=>\Text::_("EXP_TEMPLATING_STYLE_FILTER_FORNT_TITLE"),
            "tmpname"=>\Text::_("EXP_TEMPLATING_STYLE_FILTER_TMP_TITLE"),
            "time_active"=>\Text::_("EXP_TEMPLATING_STYLE_FILTER_DATA_START_TITLE"),
            "time_inactive"=>\Text::_("EXP_TEMPLATING_STYLE_FILTER_DATA_END_TITLE")
        ];
    }

}
