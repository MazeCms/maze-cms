<?php

namespace exp\exp_logs\form;
use maze\validators\DateValidator;
use Text;

class FilterApp extends \ui\filter\Model {
    
    public $expapp;
    
    public $user_id;
    
    public $category;
    
    public $action;
    
    public $message;

    public $ip;
    
    public $datetime;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['expapp', 'user_id', 'category', 'action', 'ip'], 'safe'];
       $rules[] = [['datetime'], 'validDate'];
       return $rules;
    }
    
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute))
        {
           
            $date = new DateValidator(['format'=>'Y-m-d H:i:s']);
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
        $query->andFilterWhere(['user_id'=>$this->user_id]);
        $query->andFilterWhere(['component'=>$this->expapp]);
        $query->andFilterWhere(['ip'=>$this->ip]);
        $query->andFilterWhere(['like', 'category', $this->category]);
        $query->andFilterWhere(['like', 'action', $this->action]);        
        $query->andFilterWhere(['like', 'message', $this->message]);
        if(isset($this->datetime[0])){
            $query->andFilterWhere(['>=', 'datetime', $this->datetime[0]]);
        }
        if(isset($this->datetime[1])){
            $query->andFilterWhere(['<=', 'datetime', $this->datetime[1]]);
        }
        

    }
    public function attributeLabels() {
        return[
            "expapp" => Text::_("EXP_LOGS_COMPONENT_LABEL"),
            "user_id"=>Text::_("EXP_LOGS_USER_LABEL"),
            "category"=>Text::_("EXP_LOGS_CATEGORY_LABEL"),
            "action"=>Text::_("EXP_LOGS_ACTION_LABEL"),
            "datetime"=>Text::_("EXP_LOGS_DATETIME_LABEL"),
            "ip"=>Text::_("EXP_LOGS_IP_LABE"),
            "message"=>Text::_("EXP_LOGS_MESSAGES_LABEL"),
        ];
    }

}
