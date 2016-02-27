<?php
namespace admin\expansion\exp_constructorblock\filter\phpcode;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $phpcode;
    
    public function rules() {
      return [
          ['phpcode', 'required'],
          ['table', 'safe'],
          ['field', 'string']
      ];
    }
    

    
    public function buildQuery($query, $table, $id_name) {
        
        $callback = trim($this->phpcode);
        if (!empty($callback)) {
           
            try {
                $callback = create_function('$query', $callback);
                $resultFunc = $callback($query);
                
                if(!$resultFunc){
                    return false;
                }             
                
            } catch (\Exception $exc) {

            }
            
            
        }
    }

    
}
