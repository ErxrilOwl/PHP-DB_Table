<?php 

class DB_Table extends Database{
	public $conn;
	public static $where;

	public function __construct(){
		$conn = parent::__construct();
	}

	public function addQuoteSymbol($array){
		
		foreach ($array as &$value) {
			$value = mysqli_real_escape_string($this->conn, $value);
			$value = "'".$value."'";
		}
		
		return $array;
	}

	public function checkIfTableExists($tblName){
		$res = mysqli_query($this->conn, "SHOW TABLES LIKE '$tblName'");

  		return mysqli_num_rows($res);
	}

	public function checkIfColumnExists($tblName, $columnName){
		$sql = "SHOW COLUMNS FROM $tblName LIKE '$columnName'";
		$result = mysqli_query($this->conn, $sql);
		if($result){
			return (mysqli_num_rows($result)) ? TRUE : FALSE;	
		}
		return FALSE;
	}

	public static function all($tblName, $type = 'object', $trash = false){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			$sql = "SELECT * FROM $tblName";
			if(!$trash){
				if($db_table->checkIfColumnExists($tblName, "deleted_at"))
					$sql .= " WHERE deleted_at IS NULL";
			}
			
			$result = mysqli_query($db_table->conn, $sql);

			$array = array();

			if(mysqli_num_rows($result) > 0){
				if($type == 'assoc'){
					while($row = mysqli_fetch_assoc($result)){
						array_push($array, $row);
					}	
				}
				else if($type == 'array'){
					while($row = mysqli_fetch_array($result)){
						array_push($array, $row);
					}	
				}
				else{
					while($row = mysqli_fetch_object($result)){
						array_push($array, $row);
					}		
				}
				return $array;
			}
			else
				return null;
		}
		return false;
	}

	public static function where($tblName, $where, $type = 'object'){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			if(count($where)){
				$keys = array_keys($where);
				$values = array_values($where);

				if(count($keys) != count($values)) return false;

				$values = $db_table->addQuoteSymbol($values);		

				$sql = "SELECT * FROM $tblName WHERE ";

				for($i = 0; $i < count($keys); $i++){
					$sql .= $keys[$i]." = ".$values[$i]." AND ";
				}

				$sql = rtrim($sql, " AND ");
				
				$result = mysqli_query($db_table->conn, $sql);

				$array = array();

				if(mysqli_num_rows($result) > 0){
					if($type == 'assoc'){
						while($row = mysqli_fetch_assoc($result)){
							array_push($array, $row);
						}	
					}
					else if($type == 'array'){
						while($row = mysqli_fetch_array($result)){
							array_push($array, $row);
						}	
					}
					else{
						while($row = mysqli_fetch_object($result)){
							array_push($array, $row);
						}		
					}
					return $array;
				}
				else
					return null;
			}
		}
	}

	public static function insert($tblName, $keyValue){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			if(count($keyValue)){
				$keys = array_keys($keyValue);
				$values = array_values($keyValue);

				if(count($keys) != count($values)) return false;

				$values = $db_table->addQuoteSymbol($values);

				$keys = implode(", ", $keys);
				$values = implode(", ", $values);

				$sql = "INSERT INTO $tblName ($keys) VALUES($values)";	
				
				$result = mysqli_query($db_table->conn, $sql);
				
				if($result){
					return mysqli_insert_id($db_table->conn);
				}
				
			}	
			else
				return false;
		}
		return false;
	}

	public static function update($tblName, $keyValue, $where){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			if(count($keyValue)){
				$keys = array_keys($keyValue);
				$values = array_values($keyValue);

				if(count($keys) != count($values)) return false;

				$values = $db_table->addQuoteSymbol($values);		

				$sql = "UPDATE $tblName SET ";

				for($i = 0; $i < count($keys); $i++){
					$sql .= $keys[$i]." = ".$values[$i]." ,";
				}

				$sql = rtrim($sql, ",");

				if(count($where)){
					$sql .= " WHERE ";
					$keys = array_keys($where);
					$values = array_values($where);

					if(count($keys) != count($values)) return false;

					$values = $db_table->addQuoteSymbol($values);						
					
					for($i = 0; $i < count($keys); $i++){
						$sql .= $keys[$i]." = ".$values[$i];
					}					
				}
				
				$result = mysqli_query($db_table->conn, $sql);
				if($result){
					return true;
				}
			}	
		}
		else
			return false;
	}

	public static function delete($tblName, $where, $softDelete = false){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			if(count($where)){
				$keys = array_keys($where);
				$values = array_values($where);

				if(count($keys) != count($values)) return false;

				$values = $db_table->addQuoteSymbol($values);		
				
				if($softDelete){
					if($db_table->checkIfColumnExists($tblName, "deleted_at")){
						$sql = "UPDATE $tblName SET deleted_at = NOW() WHERE ";
						
						for($i = 0; $i < count($keys); $i++){
							$sql .= $keys[$i]." = ".$values[$i]." AND ";
						}

						$sql = rtrim($sql, " AND ");

						$result = mysqli_query($db_table->conn, $sql);

						if($result)
							return true;
						else
							return false;
					}
					return false;
				}
				
				$sql = "DELETE FROM $tblName WHERE ";

				for($i = 0; $i < count($keys); $i++){
					$sql .= $keys[$i]." = ".$values[$i]." AND ";
				}

				$sql = rtrim($sql, " AND ");

				$result = mysqli_query($db_table->conn, $sql);
				if($result){
					return true;
				}	
			}	
		}
		else
			return false;
	}

	public static function reset($tblName){
		$db_table = new DB_Table();

		if($db_table->checkIfTableExists($tblName)){
			$sql = "SET FOREIGN_KEY_CHECKS = 0"; 
			$result1 = mysqli_query($db_table->conn, $sql);
			$sql = "TRUNCATE $tblName"; 
			$result2 = mysqli_query($db_table->conn, $sql);
			$sql = "SET FOREIGN_KEY_CHECKS = 1"; 
			$result3 = mysqli_query($db_table->conn, $sql);
					
			if($result1 && $result2 && $result3){
				return true;
			}
			else{
				return false;
			}
		}		
		return false;
	}

	public static function SelectStatement($sql, $type = 'object'){
		$db_table = new DB_Table();

		$result = mysqli_query($db_table->conn, $sql);

		$array = array();

		if(mysqli_num_rows($result) > 0){
			if($type == 'assoc'){
				while($row = mysqli_fetch_assoc($result)){
					array_push($array, $row);
				}	
			}
			else if($type == 'array'){
				while($row = mysqli_fetch_array($result)){
					array_push($array, $row);
				}	
			}
			else{
				while($row = mysqli_fetch_object($result)){
					array_push($array, $row);
				}		
			}
			return $array;
		}
		else
			return null;		
	}
}