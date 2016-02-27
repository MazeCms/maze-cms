<?php

namespace exp\exp_logs\form;
use maze\validators\DateValidator;
use Text;

class FilterCache extends \ui\filter\Model {
    
    
    public $user_id;
    
    public $category;
    
    public $type;
    
    public $group;

    public $ip;
    
    public $datetime;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['user_id', 'category', 'type','group', 'ip'], 'safe'];
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
        $query->andFilterWhere(['type'=>$this->type]);
        $query->andFilterWhere(['ip'=>$this->ip]);
        $query->andFilterWhere(['group'=>$this->group]);
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
            "type"=>Text::_("EXP_LOGS_DB_TYPE_LABEL"),
            "datetime"=>Text::_("EXP_LOGS_DATETIME_LABEL"),
            "ip"=>Text::_("EXP_LOGS_IP_LABE"),
            "group"=>Text::_("EXP_LOGS_DB_GROUP_LABEL"),
        ];
    }

}
