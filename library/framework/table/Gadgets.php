<?php

namespace maze\table;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Gadgets extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%gadgets}}';
    }
   
    public function rules() {
        return [
            [["name","id_des"], "required", 'on'=>'add'],
            ["name", "exist", "targetClass"=>'maze\table\InstallApp','targetAttribute'=>'name','filter'=>['type'=>'gadget'], 'on'=>'add'],
            ["id_des", "exist", "targetClass"=>'exp\exp_admin\table\Desktop','targetAttribute'=>'id_des', 'on'=>'add'],
            [['name','colonum', 'id_des', 'ordering'], 'safe', 'on'=>'add']
        ];
    }
    
    public function beforeSave($insert)
    {
       $this->param = serialize($this->param); 
      
       return true;
    }
    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }
    }

}
