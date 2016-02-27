<?php 
namespace maze\jdb;

/*
 *	ERROR CODES 
 *	update+
 *	delete
 *	alert
 *
 *	fdb request constructor
 *
 *	minimalizee class ( insert, update, select )
 *
 * quotes nad spaces 
 *	
 */
  
function Jsonsql($string, $path = false){

	return Jsonsql::request($string, $path);	
}

class Jsonsql{
	
	static 	$error = false,
			$JSON_DB_OBJS = Array(),
			$obj = false,
			$prevObj = false;
	
	static function replaceQuotes($item){
		
		$quotes = array('/^\s*/','/\s*$/','/^"/','/^`/','/^\'/','/`$/','/\'$/','/"$/');		
		if(!is_array($item)){			
			return preg_replace($quotes,'', $item);
		}		
		$return = array();		
		foreach($item as $key=>$string){	
			$key = preg_replace($quotes,'', $key);
			$return[$key] = preg_replace($quotes,'', $string);
		}		
		return $return;
	}
	
	static function request($string, $path){	
		self::$prevObj = self::$obj;
		
		$classHash = md5($path);
		if(!self::$JSON_DB_OBJS[$classHash]){
			self::$JSON_DB_OBJS[$classHash] = new Jsondb($path);			 
		}
		self::$obj = self::$JSON_DB_OBJS[$classHash];
		
		$result =false;		
		$string = trim($string);
		if( !preg_match('/^\s*(truncate|table|last_insert_id|status|select|insert|update|delete|drop|create|alter)/i', $string, $out) ){				
			self::$error = 'wrong request: '.$string;			
			return false;
		}				
		$item = strtolower($out[0]);		
		if($item == 'select'){					
			$result = Jsonsql::selectSql($string);
		} elseif($item == 'insert') {
			$result = Jsonsql::insertSql($string);
		} elseif($item == 'drop') {
			$result = Jsonsql::dropSql($string);
		} elseif($item == 'delete'){
			$result = Jsonsql::deleteSql($string);
		} elseif($item == 'update'){
			$result = Jsonsql::updateSql($string);
		} elseif($item == 'create'){
			$result = Jsonsql::createSql($string);
		} elseif($item == 'status'){
			$result = Jsonsql::statusSql($string);
		}	elseif($item == 'alter'){
			$result = Jsonsql::alterSql($string);
		}	elseif($item == 'truncate'){
			$result = Jsonsql::truncateSql($string);
		}	elseif($item == 'table'){
			$result = Jsonsql::tableExistSql($string);
		} 	elseif($item == 'last_insert_id'){
			$result = Jsonsql::LiiSql($string);
		}

		return $result;
	}	
	
	static function truncateSql($string){
		
		if( !preg_match('/truncate\s*table\s*(.+)/i', $string, $out) ){				
			self::$error = 'wrong request: '.$string;				
			return false;
		}
		
		$table = Jsonsql::replaceQuotes($out[1]);
		
		
		return self::$obj->truncate($table);
	}
	
	static function tableExistSql($string){
		
		if( !preg_match('/table\s*exists\s*(.+)/i', $string, $out) ){				
			self::$error = 'wrong request: '.$string;				
			return false;
		}
		$table = Jsonsql::replaceQuotes($out[1]);
		
		return self::$Obj->exists($table);
		
	}
	
	static function LiiSql($string){
		
		return self::$prevObj->last_insert_id();
	}
	
	static function statusSql($string){
		
		$flag = true;
		if(preg_match('/code/', $string, $out)){
			$flag = false;
		}
		
		return self::$prevObj->status($flag);
	}

	static function createSql($string){
		$result = false;			
		if( !preg_match('/create\stable\s([^\s]+)\s\((.+)\)/i', $string, $out) ){				
			self::$error = 'wrong request: '.$string;				
			return false;
		}
		
		$table = Jsonsql::replaceQuotes($out[1]);

		$temp = explode(',', $out[2]);
		$increment = false;	
		$defaultValues = array();
		
		foreach($temp as $item){
			$default = false;
			$inc = false;
			
			if(preg_match('/(.+)\s*auto_increment/', $item , $out) and !$increment){
				$inc = true;				
				$increment = trim($out[1]);
				$item = str_replace('auto_increment', '', $item);
				
			}
			
			if(preg_match('/(.+)\s*(default(.+))/', $item , $out)){
				$default = Jsonsql::replaceQuotes(trim($out[3]));
				$defaultValues[trim($out[1])] = $default;
				$item = str_replace($out[2], '', $item);
			}
			
			$item = trim($item);
			
			if($inc){
				$keys[$item] = Array('auto_increment');
			} elseif($default){
				$keys[$item] = Array('default'=>$default);
			} else {			
				$keys[] =  trim($item);
			}			
		}
	
		
	
		return self::$obj->create($table, $keys);
	}
	
	static function updateSql($string){
		
		$result = false;

		
		if( !preg_match('/update\s([^\s]+)\sset\s(.+)\s*(where\s*(.+))/i', $string, $out) ){
			
			self::$error = 'wrong request: '.$string;
			
			return false;
		}
		
		$table = Jsonsql::replaceQuotes($out[1]);

		$updateData = explode(',',$out[2]);
		
		for($i=0; $i<count($updateData); $i++){
			
			$temp = explode('=', $updateData[$i]);
			
			$key = Jsonsql::replaceQuotes(trim($temp[0]));
			$value = Jsonsql::replaceQuotes(trim($temp[1]));
			
			if(!empty($key) and !empty($value)){
				$data[$key ] = $value;
			}
			
		}
		
		$where = Jsonsql::getWhere($out[4]);
		
		return self::$obj->update($table, $data, $where);
	}
	
	static function deleteSql($string){
		
		$result = false;

		
		if( !preg_match('/delete\sfrom\s([^\s]+)\s\((.+)\)/i', $string, $out) ){
			
			self::$error = 'wrong request: '.$string;
			
			return false;
		}
		
		$fileFromDrop = $out[1];
		
		$keys = explode(',', $out[2]);
		
		$file = self::$path.'/'.$fileFromDrop.'.json';
		
		$data = array();

	}
	
	static function alterSql($string){
		$result = false;
	
		if( !preg_match('/alter\s*table\s*([^\s]+)\s*([^\s]+)\s*(.+)/i', $string, $out) ){
			
			self::$error = 'wrong request: '.$string;
			
			return false;
		}
		
		$todo = $out[2];
		$data = $out[3];
		$table = Jsonsql::replaceQuotes($out[1]);
		if($todo == 'add'){
			$temp = explode(',', $data);
			$data = array();
			foreach($temp as $item){
				$default = false;
				$inc = false;
				
				if(preg_match('/(.+)\s*auto_increment/', $item , $out) and !$increment){
					$inc = true;				
					$increment = trim($out[1]);
					$item = str_replace('auto_increment', '', $item);
					
				}
				
				if(preg_match('/(.+)\s*(default(.+))/', $item , $out)){
					$default = Jsonsql::replaceQuotes(trim($out[3]));
					$defaultValues[trim($out[1])] = $default;
					$item = str_replace($out[2], '', $item);
				}
				
				$item = Jsonsql::replaceQuotes(trim($item));
				
				if($inc){
					$data[$item] = Array('auto_increment');
				} elseif($default){
					$data[$item] = Array('default'=>$default);
				} else {			
					$data[] =  $item;
				}			
			}
		} else {
			$data = explode(',',str_replace('drop','',$data));
			$data = Jsonsql::replaceQuotes($data);
		}
		
		
		return self::$obj->alter($table, $todo, $data);	
	}
	
	
	static function dropSql($string){
		
		$result = false;

		
		if( !preg_match('/drop\stable\s(.+)/i', $string, $out) ){
			
			self::$error = 'wrong request: '.$string;
			
			return false;
		}
		
		$table = Jsonsql::replaceQuotes($out[1]);
		
		
		
		return self::$obj->drop($table);

	}
	
	static function insertSql($string){
			
		$result = false;
		
					
		if( !preg_match('/insert\sinto\s(.+)\s\((.+)\)\svalues\s\((.+)\)/i', $string, $out) ){
			
			self::$error = 'wrong request: '.$string;
			
			return false;
		}
		
		$table = Jsonsql::replaceQuotes($out[1]);		
		$keys = Jsonsql::str2array($out[2]);		
		$values = Jsonsql::str2array($out[3]);
			
		
		
			
		if(count($keys) != count($values)){			
			self::$error = 'wrong request: '.$string;			
			return false;
		}
		
		$data = array_combine($keys, $values);
		
		return self::$obj->insert($table, $data);		
	
	}
	
	static function selectSql($string){
		
		$result = false;			
		if( !preg_match('/select\s(.+)\sfrom\s([^\s]+)\s*(where\s*(.+\s*(?=order)|.+\s*(?=limit)|.+\s*))?\s*(order\s*by\s*([^\s]+)?\s*(asc|desc|rand\(\)))?\s*(limit\s*([0-9,\s]+))?\s*/i', $string, $out) ){		
		
			self::$error = 'wrong request: '.$string;			
			return false;
		}		
		$select =  Jsonsql::str2array($out[1]);				
		$table = Jsonsql::replaceQuotes($out[2]);		
		$limit = Jsonsql::getLimit($out[9]);
		$where = Jsonsql::getWhere($out[4]);		
		$order = Jsonsql::getOrder($out[6], $out[7]);	

		$rules = array(
			'limit'=>$limit,
			'where'=>$where,
			'order'=>$order
		);	
		
		
		return self::$obj->select($select, $table, $rules);
	
	}

	static function str2array($select){	
		$select = trim($select);
		if($select !='*'){
			$select = explode(',', $select);			
			foreach($select as $row){
				if(!empty($row)){
					$data[] = Jsonsql::replaceQuotes($row);
				}
			}		
		} else return $select;
				
		return $data;
	}
	
	static function getLimit($key){
		
		if(empty($key)){
			return false;
		}
		$limit = array();
		
		$temp = explode(',',$key);
		
		$limit[] =  trim($temp[0]);
		if(!empty($temp[1])){
			$limit[] = trim($temp[1]);
		}
		
		return $limit;
		
	}
	
	static function getOrder($key, $how){
		
		if(empty($key) and empty($how)){
			return false;
		} elseif($how == 'rand()') {
			return array($how);
		}
	
		$key = Jsonsql::replaceQuotes($key);
		
		//$allowArray = array('asc','desc','rand()');
		
		$how = strtolower($how);
		
		//$resultHow = in_array($key, $allowArray);
		
		return array($key,$how);
	
	}
	
	static function getWhere($key){
		
		if(empty($key)){
			return false;
		}
		
		$where = array();
		$inArray = array();
		
		preg_match_all('/([^\s]+)\sin\(([^\)]+)\)/i', $key, $out);
		
	
		
		for($i=0; $i<count($out[0]); $i++){
			$key = str_replace($out[0][$i],'', $key);
			
			$inArray[$out[1][$i]] =  explode(',', $out[2][$i]);
			
		}
		
		$key = trim($key);
		if(empty($key)){
			
				$where = $inArray;
				
				foreach($where as $key=>$value){
					$key = Jsonsql::replaceQuotes($key);						
					$value = Jsonsql::replaceQuotes($value);					
					$data[$key] = $value;
				}
			
				return $data;
		}
		
		$key = str_replace(Array("\\'",'\\"','\\`'), '#BQ#' , $key);
	
		preg_match_all('/([^=]+)\s*=\s*(((\'|"|`)([^(\'|"|`)]+)(\'|"|`))|(\d+))\s*(and)?/i', $key, $temp);
		
		
		
		
		$temp = array_combine($temp[1], $temp[2]);
		
		
		
		foreach($temp as $key=>$value){
				$key = Jsonsql::replaceQuotes($key);
				$value = Jsonsql::replaceQuotes($value);			
				$value = str_replace('#BQ#',"\'", $value);				
				if(!empty($key) and !empty($value)){
					if(!empty($where[$key])){
						if(is_array($where[$key])){
							$where[$key][] = $value;
						} else {
							
							$where[$key] = array(	$where[$key] );
							$where[$key][] = $value;
						}
						
					} else {
						$where[$key] = $value;
					}
				}
			
		}

		
		$where = array_merge($inArray, $where);
		
		foreach($where as $key=>$value){
			$key = Jsonsql::replaceQuotes($key);
				
			$value = Jsonsql::replaceQuotes($value);
			
			$data[$key] = $value;
		}
	
		return $data;
	}
}
?>
