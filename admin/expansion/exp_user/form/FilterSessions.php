<?php

namespace exp\exp_user\form;
use maze\validators\DateValidator;

class FilterSessions extends \ui\filter\Model {
    
    public $username;
    
    public $id_user;
    
    public $time_start;
    
    public $time_last;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['id_user', 'username'], 'safe'];
       $rules[] = [['time_start', 'time_last'], 'validDate'];
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
       if($this->id_user !== null){
           if(in_array(1, $this->id_user))
           {
               $query->andFilterWhere(['>', 's.id_user', 0]);
           }
           if(in_array(0, $this->id_user))
           {
              $query->andFilterWhere(['or', ['s.id_user'=>0], ['s.id_user'=>null]]); 
           }
       }

        $query->andFilterWhere(['like', 'u.username', $this->username]);
        $query->andFilterWhere(['>=', 's.time_start', $this->time_start[0]]);
        $query->andFilterWhere(['<=', 's.time_start', $this->time_start[1]]);
        $query->andFilterWhere(['>=', 's.time_last', $this->time_last[0]]);
        $query->andFilterWhere(['<=', 's.time_last', $this->time_last[1]]);
        
    }
    
    public function attributeLabels() {
        return[
            "id_user"=>\Text::_("EXP_USER_FILTER_ROLE_TITLE"),
            "username"=>\Text::_("EXP_USER_TABLE_HEAD_LOGIN"),
            "time_start"=>\Text::_("EXP_USER_SESSIONS_TABLE_STARTSES"),
            "time_last"=>\Text::_("EXP_USER_TABLE_HEAD_DATELAST")
        ];
    }

}
