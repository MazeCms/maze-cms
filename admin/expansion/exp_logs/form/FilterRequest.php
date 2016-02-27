<?php

namespace exp\exp_logs\form;
use maze\validators\DateValidator;
use Text;

class FilterRequest extends \ui\filter\Model {
    
    
    public $user_id;
    
    public $category;
    
    public $route;
    
    public $action;
    
    public $controller;

    public $statusCode;
    
    public $statusText;

    public $ip;
    
    public $datetime;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['user_id', 'category', 'route', 'controller', 'action', 'statusCode', 'statusText', 'ip'], 'safe'];     
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
        $query->andFilterWhere(['route'=>$this->route]);
        $query->andFilterWhere(['action'=>$this->action]);
        $query->andFilterWhere(['controller'=>$this->controller]);
        $query->andFilterWhere(['statusCode'=>$this->statusCode]);
        $query->andFilterWhere(['statusText'=>$this->statusText]);
        $query->andFilterWhere(['like', 'category', $this->category]);   

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
            "datetime"=>Text::_("EXP_LOGS_DATETIME_LABEL"),
            "ip"=>Text::_("EXP_LOGS_IP_LABE"),
            "statusCode"=>Text::_("EXP_LOGS_REQUEST_STATUSCODE_LABEL"),
            "statusText"=>Text::_("EXP_LOGS_REQUEST_STATUSTEXT_LABEL"),
            "controller"=>Text::_("EXP_LOGS_REQUEST_CONTROLLER_LABEL"),
            "action"=>Text::_("EXP_LOGS_REQUEST_ACTION_LABEL"),
            "route"=>Text::_("EXP_LOGS_REQUEST_ROUTE_LABEL")
        ];
    }

}
