<?php  
	class db{
		 
		function __construct($dbuser, $dbpass, $dbname, $dbhost) {
			try {
			    $this->conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			    // set the PDO error mode to exception
			    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e){
			    echo "Connection failed: " . $e->getMessage();
			    die();
			}
		}

		function getRow($query, $values = array()){				   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8'); 
	   		$stmt = $this->conn->prepare($query);
	   		$stmt->execute($values); 
			$result  = $stmt->fetch(PDO::FETCH_OBJ);
			return $result; 
		}

		function getResults($query, $values = array()){			    		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		$stmt = $this->conn->prepare($query);
	   		$stmt->execute($values); 
			$result  = $stmt->fetchAll(PDO::FETCH_OBJ);
			return $result; 
		}

		function insert($tableName="",$values=array()){
			$fields			= "";
			$data_values	= "";
			$ctr			= 0;
			foreach($values as $key => $value)
			{
				if( $ctr == 0 )
				{ 
					$fields			.= "`$key`";
					$data_values	.= ":$key";
				}
				else
				{  
					$fields			.= ",`$key`";
					$data_values	.= ",:$key";	 
				}
				$ctr++;
			} 
			$sql = $this->conn->prepare("INSERT INTO `$tableName` ( $fields ) VALUES ($data_values)");
			$sql->execute($values);

			return $this->conn->lastInsertId(); 
		}

		function update($tableName, $id, $idValue, $values=array()){ 
			$fields			= "";
			$data_values	= "";
			$ctr			= 0;
			foreach($values as $key=> $value)
			{ 
				if( $ctr == 0 ){ 
					$fields	.= "`$key` = :$key"; 
				}
				else{  
					$fields	.= ",`$key` = :$key"; 	
				}
				$ctr++;
			}  
			$values[$id] = $idValue; 
			$sql = $this->conn->prepare("UPDATE `$tableName` SET $fields WHERE `$id`=:$id");
			$sql->execute($values); 
		}

		function delete($tableName="", $id="", $idValue){ 
			$value[$id] = $idValue;
			$sql = $this->conn->prepare("DELETE FROM $tableName WHERE `$id` = :$id"); 
			$sql->execute($value); 
		}
 
		function rawQuery(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute();  
		}
		
		function rawResults($query){				   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		$stmt = $this->conn->prepare($query); 
			$result  = $stmt->fetchAll(PDO::FETCH_OBJ);
			return $result; 
		}
	}
?>