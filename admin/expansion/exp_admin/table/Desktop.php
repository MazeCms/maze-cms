<?php

namespace exp\exp_admin\table;
use exp\exp_admin\table\Gadgets;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Desktop extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%desktop}}';
    } 
    public function beforeSave($insert)
    {
       $this->param = serialize($this->param); 
       if($this->ordering == null)
       {
           $this->ordering = static::find()->max('ordering') + 1;
       }
       
       
       return true;
    }
     public function afterFind()
    {
         if(!empty($this->param))
         {
             $this->param = unserialize($this->param);
         }
    }   
    
    public function rules() {
        return [
            [['title','description','defaults', 'ordering', 'param'], 'safe']

        ];
    }
    
    public function getGadgets()
    {
        return $this->hasMany(Gadgets::className(), ['id_des' => 'id_des'])
                ->from(["gad"=>Gadgets::tableName()])
                ->orderBy('gad.ordering');
    }
    
    
}
