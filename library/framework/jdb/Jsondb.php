<?php 
namespace maze\jdb;

class Jsondb{
        
        public 	$last_insert_id = false;
        
        public $statusCODE = 0;
        
	public	$statusMSG = Array();
        
	private $tableConfigs = array();

	
	public function __construct($path = false){		 
		$this->path = (!$path) ? $_SERVER['DOCUMENT_ROOT'].'/jdb/' : $path;		
	}
	
	/*
	 * Get status of last request.
	 * $flag - true or false.
	 * if true - return text message
	 * if fasle - return status code.
	 *
	 * Status codes:
	 * 	0 - All ok.
	 *	101 - Table already exists.
	 *	102 - Table doent exist. 
	 * 	103 - Unkonw property.
	 *	201 - Key already exist.
	 *	202 - Keys doesnt exsit. 
	 */
	public function status( $flag = false){
		return (!$flag)?$this->statusCODE:implode('; ', $this->statusMSG);	
	}
	
	/*
	 * Alter table. add or drop columns.
	 * $table - String. Table name
	 * $todo - String. 'add' or 'drop'
	 * 
	 * 	if $todo == add
	 * 
	 * 		$data  - array(item=>settings); Table keys. 
	 * 			[
	 * 				'item'=>['auto_increment'],
	 * 				'item2'=>['default'=>value],
	 * 				'item3',
	 *			]
	 *  if $todo == drop
	 * 
	 * 		$data  - String or Array; Table keys.
	 * 		'column1, column2, column3'
	 * 		Array('column1', 'column2', 'column3')
	 */
	public function alter($table, $todo, $keys){
		$this->statusCODE = 0;
		$this->statusMSG = array();
		
		if(!$this->exists($table)){			
			$this->statusMSG[] 	= 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE 	= 101;		
			return false;
		}
		$todo =strtolower($todo);
		if($todo == 'add'){
			$result = $this->addCol($table, $keys);
		} elseif($todo == 'drop') {
			$result = $this->dropCol($table, $keys);
		} else {
			$this->statusMSG[]	= 'Unknown propery "'.$todo.'";';	
			$this->statusCODE 	= 103;		
			return false;
		}		
		return $result;
	}
	

	public function truncate($table){
		
		$this->statusCODE = 0;
		$this->statusMSG = array();
			 
		if(!$this->exists($table)){			
			$this->statusMSG[] = 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE  = 102;			
			return false;
		}
		
		$result = file_put_contents($this->path.'/'.$table.'.json', '');
		
		$settings =  $this->getTableSettings($table);
		$settings['settings']['lastIncrement'] = 0;
		$this->saveTableSettings($table, $settings);
		
		return true;	
	}
	/*
	 * Select data from table;
	 * $select - String or Array; Keys to select
	 * 			'id,name,item';
	 * 			['id','name','item']'
	 * $table - String; table name;
	 * 
	 * $data - Array; where | order | limit
	 * 			[
	 * 				'where'=>Array(key=>value),
	 * 				'order'=>Array(key, how),
	 * 				'limit'=>array(min, count)
	 * 			]
	 */	
	public function select($select ,$table, $data = array()){
		
		$this->statusCODE = 0;
		$this->statusMSG = array();
		
		$select = $this->select2array($select);
			 
		if($this->checkArray($select, true)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', $select).'"';	
			$this->statusCODE  = 104;
			return false;
		}
		
		if(!$this->exists($table)){			
			$this->statusMSG[] = 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE  = 102;			
			return false;
		}
		$check = $this->checkTableKeys($select, $table);
		if(!$check){		
			$this->statusMSG[] = 'Try to select unexisting keys from table "'.$table.'";';
			$this->statusCODE = 202;			
			return false;
		}
		
		$where = ($data['where'])?$data['where']:false;
		$order = ($data['order'])?$data['order']:false;
		$limit = ($data['limit'])?$data['limit']:false;
		
		if($where){
			$check = $this->checkTableKeys(array_keys($where), $table);
			if(!$check){		
				$this->statusMSG[] = 'Try to select unexisting keys from table "'.$table.'";';
				$this->statusCODE = 202;			
				return false;
			}
		}
		
		if($order){
			$check = $this->checkTableKeys($order[0], $table);
			if(!$check){		
				$this->statusMSG[] = 'Try to order by unexisting keys from table "'.$table.'";';
				$this->statusCODE = 202;			
				return false;
			}
		}
		
		
		$result = $this->getJSON($table, true);
		$result = $this->getWhereArray($result, $where);	
				
		if($select != '*'){			
			$result = $this->getSelectArray($result, $select);
					
		}			
		
		

		$result = $this->getOrderArray($result, $order);		
		$result = $this->getLimitArray($result, $limit);		
		return $result;
	}
	

	
	/*
	 * Insert data to table
	 * $table - String; Table
	 * $data - Array; Data to insert
	 * 			[
	 * 				key=>value
	 * 			]
	 */
	public function insert($table, $data){
		$this->statusCODE = 0;
		$this->statusMSG = array();
		
		if(!$this->exists($table)){			
			$this->statusMSG[] = 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE  = 102;			
			return false;
		}
	
		if($this->checkArray(array_keys($data), true)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', array_keys($data)).'"';	
			$this->statusCODE  = 104;
			return false;
		}
		
		$check = $this->checkTableKeys(array_keys($data), $table);
		if(!$check){		
			$this->statusMSG[]	= 'Try to insert unexisting keys; Table "'.$table.'"';	
			$this->statusCODE 	= 202;		
			return false;
		}
		
		$settings = $this->getTableSettings($table);		
		$defaultValues = $settings['settings']['defaultValues'];
		
		foreach($settings['keys'] as $key){
			if(!isset($data[$key])){
				$value = ($defaultValues[$key])?$defaultValues[$key]:'';
				$data[$key] = $value;				
			}
		}
		
		$lastIncrement =  $settings['settings']['lastIncrement']+1;
		$increment =  $settings['settings']['auto_increment'];		
		$data[$increment] = $lastIncrement;		
		$result = $this->getJSON($table, true);
			
		$result[] = $data;
		$result = $this->setJSON($table, $result);		
		$settings['settings']['lastIncrement']++;		
		$resultConfig = $this->saveTableSettings($table, $settings);
		$this->last_insert_id  = $lastIncrement;
		
		return $result;
	}
	/*
	 * Update data
	 * $table - String; Table
	 * $data - Array; Data to updatet
	 * 			[
	 * 				key=>value
	 * 			]
	 * $where - Array;
	 * 			[
	 * 				key=>value
	 * 			]
	 */
	public function update($table, $data, $where){
		$this->statusCODE = 0;
		$this->statusMSG = array();
		
		
		
		if(!$this->exists($table)){			
			$this->statusMSG[] = 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE  = 102;			
			return false;
		}
		
		$check = $this->checkTableKeys(array_keys($data), $table);		
		if(!$check){			
			$this->statusMSG[] 	= 'Try to update unexisting keys; Table "'.$table.'"';	
			$this->statusCODE 	= 202;			
			return false;
		}
		
		if($where){
				 
			if($this->checkArray(array_keys($where), true)){
				$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', array_keys($where)).'"';	
				$this->statusCODE  = 104;
				return false;
			}
		
			$check = $this->checkTableKeys(array_keys($where), $table);		
			if(!$check){				
				$this->statusMSG[]	= 'Attribute "where" consist of unexisting keys for update table "'.$table.'"';	
				$this->statusCODE 	= 202;				
				return false;
			}
		}	
		$json = $this->getJSON($table);
		for($i=0; $i<count($json); $i++){				
				foreach($data as $key=>$value){					
					$check = $this->checkWhere($json[$i], $where);						
					if($check){
						$json[$i][$key] = $value;
					}				
				}		
			}	
		$result = $this->setJSON($table, $json);
		return $result;
	}
	
	/*
	 * Drop table
	 * $table - string. Table name
	 * 
	 */	
	public function drop($table){
	
		$result = false;

		if(!$this->exists($table)){			
			$this->statusMSG[] = 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE  = 102;			
			return false;
		}		
		$tableFile = $this->path.'/'.$table.'.json';
		$configFile = $this->path.'/'.$table.'.config.json';		
		if(file_exists($tableFile) and file_exists($configFile)){
			$result = unlink($tableFile);
			$result = unlink($configFile);			
		} 	
		return $result;	
	}
	
	/*
	 * Delete data
	 * $table - String; Table
	 * 
	 * $where - Array;
	 * 			[
	 * 				key=>value
	 * 			]
	 */
	public function delete($table, $where){
		
		$this->statusCODE = 0;
		$this->statusMSG = array();
		
		if(!$this->exists($table)){			
			$this->statusMSG[]	= 'Table: "'.$table.'" doesnt exists;';	
			$this->statusCODE 	= 102;				
			return false;
		}	
		
		if($this->checkArray(array_keys($where), true)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', array_keys($where)).'"';	
			$this->statusCODE  = 104;
			return false;
		}
			
		$check = $this->checkTableKeys(array_keys($where), $table);		
		if(!$check){				
			$this->statusMSG[]	= 'Attribute "where" consist of unexisting keys for select from table "'.$table.'"';	
			$this->statusCODE 	= 202;	
		}
		$data = $this->getJSON($table);
		$data = $this->getWhereArray($data, $where, true);
		$result = $this->setJSON($table, $data);
		return $result;
		
	}
	

	/*
	 * Create table.
	 * $table - table name
	 * $data  - array(item=>settings); Table keys. 
	 * 			[
	 * 				'item'=>['auto_increment'],
	 * 				'item2'=>['default'=>value],
	 * 				'item3',
	 *			]
	 * 
	 */
	public function create($table, $data){	
		$this->statusCODE = 0;	
		$this->statusMSG = array();
		
		if($this->exists($table)){			
			$this->statusMSG[]	= 'Table: "'.$table.'" already exists;';	
			$this->statusCODE 	= 101;				
			return false;
		}	
				
		$increment = false;
		$settings =  array();		
		$defaultValues = array();
		$tableKeys = array();	
		$auto_increment = false;
		if(!is_array($data)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.$data.'"';	
			$this->statusCODE  = 104;
			return false;
		}		
		foreach($data as $key=>$item){			
			if(is_array($item)){
				$tableKeys[] = $key;
				
				if(in_array('auto_increment',$item) and !$increment){
					$increment = true;
					$auto_increment = $key;
				}
				if($defaultValue = $item['default']){
					$defaultValues[$key] = $defaultValue;
				}
			} else {
				$tableKeys[] = $item;
			}
		}		
		
		if($this->checkArray($tableKeys, true)){
				$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', array_keys($tableKeys)).'"';	
				$this->statusCODE  = 104;
				return false;
		}
		$settings['keys'] = $tableKeys;
		$settings['create'] = date('Y-m-d h:i:s');
		$settings['settings']['auto_increment'] = $auto_increment;
		$settings['settings']['defaultValues'] = $defaultValues;
		$tableConfig = $table.'.config';					
		$result = $this->setJSON($tableConfig, Array($settings));		
		$tableFile = fopen($this->path.$table.'.json','w+');		
		fclose($tableFile);		
		return $result;
	}
	
	/*
	 * Check table for existing
	 * $table - String. Table name. 
	 */	
	public function exists($table){
		$path = $this->path.$table.'.json';	
		$errorConfig = file_exists($path);		
		$path = $this->path.$table.'.config.json';		
		$errorTable = file_exists($path);
		if(!$errorConfig or !$errorTable){		
			return false;
		} else {
			return true;
		}	
	}
	
	/*
	 * Get last insert id
	 */
	public function last_insert_id(){
		return $this->last_insert_id;
	}
	
	
	public function getTableSettings($table){		
		$hash = md5($this->path.$table);	
		if( $this->tableConfigs[$hash]){
			return $this->tableConfigs[$hash];
		}		
		$settings = $this->getJSON($table.'.config');
		$this->tableConfigs[$hash] = $settings[0];		
		return $settings[0];
		
		
	}
	
	private function addCol($table, $keys){		
		if(!is_array($keys)){
			$keys = explode(',',$keys);
		}
		
		
		 

		$settings = $this->getTableSettings($table);
		$increment = $settings['settings']['increment'];				
		$defaultValues = array();
		$addKeys = array();	
			
		foreach($keys as $key=>$item){			
			if(is_array($item)){
				$addKeys[]= trim($key);
				if($item['auto_increment'] and !$increment){
					$increment = true;
					$auto_increment = $key;
				}
				if($defaultValue = $item['default']){
					$defaultValues[$key] = $defaultValue;
				}
			} else {
				$addKeys[]= trim($item);
			}
		}	
		
		if($this->checkArray($addKeys, true)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', $addKeys).'"';	
			$this->statusCODE  = 104;
			return false;
		}
		
		
		$check = $this->checkTableKeys($addKeys, $table);
		
		if($check){		
			$this->statusMSG[]	= 'Addins keys are already exist; Table: "'.$table.'";';
			$this->statusCODE 	= 201;			
			return false;
		}	
		
		$settings['keys'] = array_merge($settings['keys'], $addKeys);
		
		if(!is_array($settings['settings']['defaultValues'])){
			$settings['settings']['defaultValues']= array();
		}
		$settings['settings']['defaultValues'] = array_merge($settings['settings']['defaultValues'], $defaultValues); 
		$this->saveTableSettings($table, $settings);		
		$data=$this->getJSON($table,true);
		
		for($i=0;$i<count($data);$i++){
			foreach($addKeys as $key){
				$data[$i][$key] = (!empty($defaultValues[$key]))?$defaultValues[$key]:'';
			}
		}
		$result = $this->setJSON($table, $data);
		return $result;
	}
	
	private function dropCol($table, $keys){
		if(!is_array($keys)){
			$keys = self::trimArray(explode(',',$keys));
			
		}
		
		
		if($this->checkArray($keys, true)){
			$this->statusMSG[] = 'Somthing wrong width parameters: "'.implode(',', $keys).'"';	
			$this->statusCODE  = 104;
			return false;
		}
		
		$check = $this->checkTableKeys($keys, $table);
		if(!$check){		
			$this->statusMSG[] 	= 'Try to drop an unexisting column from table "'.$table.'"';
			$this->statusCODE 	= 202;			
			return false;
		}		
		$settings = $this->getTableSettings($table);
		foreach($keys as $key){
			if($number = array_search($key, $settings['keys'])){
				unset($settings['keys'][$number]);
			}			
			if($settings['settings']['increment']==$key){
				$settings['settings']['increment'] = false;
			}
			#TODO: check default value
		}
		$data = $this->getJSON($table);
		foreach($data as $row){
			foreach($keys as $key){
				unset($row[$key]);
				$newData[] =$row;				
			}
		}
		$this->saveTableSettings($table, $settings);		
		$result = $this->setJSON($table, $newData);
		return $result;
	}
	
	private function select2array($select){
		
		if(!is_array($select)){
			$select = explode(',',$select);
		}
		for($i=0; $i<count($select); $i++){
			$select[$i] = trim($select[$i]);
			if($select[$i] == '*'){
				return '*';
			}
			if(empty($select[$i])){
				unset($select[$i]);
			}
		}
		return $select;
	}
	
	
	private function getJSON($file, $flag = false){
		$file = $this->path.$file.'.json';		
		if(file_exists($file) ){			
			$data = file_get_contents($file);			
			$json = json_decode($data, true);		
			if(!is_array($json) or count($json) == 0){				
				$json = (!$flag)?false:array();	
			}					
		} else {			
			$json = false;
		}
		return $json;
	}
	
	private function setJSON($file, $json){		
		$file = $this->path.$file.'.json';		
		if(!is_array($json)){			
			return false;
		}		
		$json = json_encode($json);		
		return (file_put_contents($file, $json))?true:false;			
	}
	
	
	private function getSelectArray($data, $select){		
		$selectData = array();		
		$i=0;	
		foreach($data as $row){			
			foreach($select as $key){		
				if(!empty($key)){										
					$selectData[$i][$key] = $row[$key];
				}							
			}						
			$i++;	
		}		
		return $selectData;		
	}
	
	private function getLimitArray($data, $limit){
		
		if(!$limit){
			return $data;
		}		
		if(is_array($limit)){
			$min = $limit[0];
			$max = $limit[1];	
		} else {
			$min = $limit;
		}
		if($max > count($data)){			
			$max = count($data);			
		}		
		
		
		if($max){			 
			$data = array_slice($data, $min, $max);				
		} else {			
			$data = array_slice($data, 0, $min);				
		}		
		return $data;
	}
	
	private function getWhereArray($data, $where, $reverse = false){	
				
		if(!$where){
			return $data;
		}		
		$whereData = array();
		$reverseArray = array();		
		$i=0;		
		foreach($data as $row){			
			$result = false;			
			foreach($where as $key=>$value){				
				$check = Jsondb::checkWhere($row, $where);	
				if($check){
					$result = true;
				}				
			}			
			if($result){
				$whereData[$i] = $row;
				$i++;
			} else {
				$reverseArray[] = $row;
			}
		}
		return (!$reverse)?$whereData:$reverseArray;		
	}
	
	private function getOrderArray($data, $order){
	
		if(!$order){
			return $data;
		}
		if(count($data) == 0){
			return $data;
		}
		$key = false;
		$how = false;
		
		if(is_array($order)){
			if(count($order) == 2){				
					
				$key = $order[0];
				$how = $order[1];
					
				if(empty($key) and empty($how)){
					return $data;
					}	
			} else {
				$how = $order[0];
			}		
		} else {
			$how = $order;
		}
		
		if(empty($key)){			
			if($how == 'rand()'){
				 shuffle($data);
				 return $data;
			} else {					
				return $data;			
			}			 
		} 
		
		foreach($data as $id=>$row){			
			$orderArray[$id] = $row[$key];
		}
			
		if($how == 'asc'){			
			asort($orderArray);
		} elseif($how == 'desc'){
			arsort($orderArray);
		}
		foreach($orderArray as $id=>$row){			
			$orderData[$id] = $data[$id];			
		}
		
		return $orderData;
	}
	
	static function checkWhere($row, $where){
		if(empty($where)){
			return true;
		}		
		
		$error = Array(true);
			
		foreach($where as $key=>$value){			
			if(!is_array($value)){				
				if($row[$key] != $value){
					$error[] = false;
				}			
			} else {		
				foreach($value as $val){
					$ea = false;
												
					if($row[$key] == $val){
							$ea = true;
							break;
					}						
				}
				
				$error[] = $ea;						
			}
			
		}
		
		
		
		if(in_array(false,$error)){
			$error = false;
		} else {
			$error = true;
		}
		

		return $error;
	}
	
	private function checkTableKeys($keys, $table){	
		if($keys == '*'){
			return true;
		}		
		$tableKeys = $this->getTableSettings($table);		
		$tableKeys = $tableKeys['keys'];
		if(!is_array($keys)){			
			$keys = array($keys);
		}		
		foreach($keys as $key){	
			if($key == '*'){
				return true;
			}		
			if($key == 'rand()'){
				return true;
			}
			if(!in_array($key, $tableKeys)){				
				return false;
			}
		}		
		return true;
	}
	
	private function saveTableSettings($table, $settings){		
		$this->tableConfigs[md5($this->path.$table)] = $settings;			
		$table = $table.'.config';						
		$result = $this->setJSON($table, array($settings));		
		return $result;
		
	}
	
	static function trimArray($array){
		$data = Array();
		
		foreach($array as $key=>$value){
			$key = trim($key);
			$value = trim($value);
			$data[$key]  = $value;
		}
		
		return $data;
	}
	
  	private function checkArray($array, $flag = false){
  		$empty = false;
  		if(!is_array($array)){
  			$array = Array($array);
  		}		
		foreach($array as $key=>$value){			
			$value= trim($value);			
			if( empty($value) ){
				unset($array[$key]);
				$empty = true;
			}
		}		
		if(!$flag){
			return array_values($array);
		} else {
			return $empty;
		}
	}
}
?>
