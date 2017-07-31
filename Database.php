<?php

class Database{
	public $conn;

	public function __construct(){
		return $this->connect();
	}	

	public function connect(){
		$this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if(!$this->conn){
			die("Connection failed: ". mysqli_connect_error());
			return null;
		}	

		return $this->conn;
	}

	public function disconnect(){
		if(mysqli_close($this->conn))
			return true;
		else
			return false;
	}

	public function checkIfTableExists($tblName){
		$res = mysqli_query($this->conn, "SHOW TABLES LIKE '$tblName'");

  		return mysqli_num_rows($res);
	}

	public static function createTable($tblName, $query){
		$db = new Database();
		if($db->checkIfTableExists($tblName, $db->conn)){
			Database::dropTable($tblName);
		}
		if(mysqli_query($db->conn, $query))
	        echo "$tblName table created successfully<br>";  
        else
            echo "Failed to create $tblName table<br>";
	}

	public static function dropTable($tblName){
		$db = new Database();
		mysqli_query($db->conn, 'SET foreign_key_checks = 0');

		if ($result = mysqli_query($db->conn, "SHOW TABLES")){
		    while($row = $result->fetch_array(MYSQLI_NUM)){
		    	if($tblName == $row[0]){
		    		if(mysqli_query($db->conn, 'DROP TABLE IF EXISTS '.$row[0])){
		    			mysqli_query($db->conn, 'SET foreign_key_checks = 1');
		    			echo $tblName." deleted successfully<br>";
		    			return true;
		    		}
		    	}
		    }
		}

		echo $tblName." not deleted successfully<br>";
		mysqli_query($migration->conn, 'SET foreign_key_checks = 1');
		return false;
	}
}


	
