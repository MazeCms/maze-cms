<?php

namespace exp\exp_logs\form;
use maze\validators\DateValidator;
use Text;

class FilterDb extends \ui\filter\Model {
    
    
    public $user_id;
    
    public $category;
    
    public $time;
    
    public $query;

    public $ip;
    
    public $datetime;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['user_id', 'category','time', 'query', 'ip'], 'safe'];     
       $rules[] = [['datetime'], 'validDate'];
       return $rules;
    }
    
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute)){           
            $date = new DateValidator(['format'=>'Y-m-d H:i:s']);
            $attr = $this->$attribute ;
            foreach($attr as $key=>$val){
                if(!$date->validate($val)){
                    $attr[$key] = null;
                }
            }
            $this->offsetSet($attribute, $attr);
        }
    }
   

    public function queryBilder($query)
    {
        $query->andFilterWhere(['user_id'=>$this->user_id]);
        $query->andFilterWhere(['ip'=>$this->ip]);
        $query->andFilterWhere(['like', 'query', $this->query]);
        $query->andFilterWhere(['like', 'category', $this->category]);   

        if(isset($this->time[0])){
            $query->andFilterWhere(['>=', 'time',$this->time[0]]);
        }
        if(isset($this->time[1])){
            $query->andFilterWhere(['<=', 'time', $this->time[1]]);
        }

        if(isset($this->datetime[0])){
            $query->andFilterWhere(['>=', 'datetime', $this->datetime[0]]);
        }
        if(isset($this->datetime[1])){
            $query->andFilterWhere(['<=', 'datetime', $this->datetime[1]]);
        }
        

    }
    public function attributeLabels() {
        return[            
            "user_id"=>Text::_("EXP_LOGS_USER_LABEL"),
            "category"=>Text::_("EXP_LOGS_CATEGORY_LABEL"),
            "time"=>Text::_("EXP_LOGS_DB_TIME_LABEL"),
            "datetime"=>Text::_("EXP_LOGS_DATETIME_LABEL"),
            "ip"=>Text::_("EXP_LOGS_IP_LABE"),
            "query"=>Text::_("EXP_LOGS_DB_QUERY_LABEL")
        ];
    }

}
