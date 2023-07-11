<?php 
	//use Queries\{Insert, Select, Update, Delete};

	class db{
		 
		function db($dbuser, $dbpass, $dbname, $dbhost) {
			try {
			    $this->conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			    // set the PDO error mode to exception
			   	$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
				catch(PDOException $e){
			    echo "Connection failed: " . $e->getMessage(); 
			    die();
			}
		}
 
	    function getRow(?string $table = null, $values=array()) {
	   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$keyValue = ""; 
			$ctr  = 0;  
			foreach($values as $key => $value) {
				if( $ctr == 0 ) { 
					$keyValue .= $key.' = "'.$value.'"';
				}
				else {  
					$keyValue .= ' AND '.$key.'= "'.$value.'"';
				}
				$ctr++;
			}  
	   		$stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$keyValue.' LIMIT 1');
			$stmt->execute(); 
			$row  = $stmt->fetch(PDO::FETCH_OBJ);
 
			return $row; 
	    	
	    }
 		
 		function getResults(?string $table = null, $values=array()) {

 			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		
	   		$keyValue = ""; 
			$ctr  = 0;  
			foreach($values as $key => $value) {
				if( $ctr == 0 ) { 
					$keyValue .= $key.' = "'.$value.'"';
				}
				else {  
					$keyValue .= ' AND '.$key.' = "'.$value.'"';
				}
				$ctr++;
			} 

	   		$stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$keyValue);
			$stmt->execute(); 
			$result = $stmt->fetchAll(PDO::FETCH_OBJ); 

			return $result;
		}

		function query(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute(); 
			$result = $stmt->fetchAll(PDO::FETCH_OBJ); 

			return $result;
		}
		function queryRow(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute(); 
			//$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$result  = $stmt->fetch(PDO::FETCH_OBJ);
			return $result;
		}

		function queryLogin(?string $query, $username){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
	   		$stmt->execute(array('USERNAME' => $username));
			$stmt->execute(); 
			//$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$result  = $stmt->fetch(PDO::FETCH_OBJ);
			return $result;
		}

		function insert_all($tablename="",$values=array())
		{
			$fields			= "";
			$data_values	= "";
			$ctr			= 0;
			foreach($values as $key=> $value)
			{
				if( $ctr == 0 )
				{ 				
					if($value == "now")
					{
						$fields			.= "`$key`";
						$data_values	.= "NOW()";
					}else{
						$fields			.= "`$key`";
						$data_values	.= "'$value'";
					}
				}
				else
				{ 
					if($value == "now")
					{
						$fields			.= ",`$key`";
						$data_values	.= ", NOW()";
					}else{
						$fields			.= ",`$key`";
						$data_values	.= ",'$value'";	
					}
				}
				$ctr++;
			} 
			//echo "INSERT INTO `$tablename` ( $fields ) VALUES ($data_values)";
	   		$sql = $this->conn->prepare("INSERT INTO `$tablename` ( $fields ) VALUES ($data_values)");
			$sql->execute(); 
		}
		function getUserIP() {
		    // Get real visitor IP behind CloudFlare network
		    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		    }
		    $client  = @$_SERVER['HTTP_CLIENT_IP'];
		    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		    $remote  = $_SERVER['REMOTE_ADDR'];

		    if(filter_var($client, FILTER_VALIDATE_IP))
		    {
		        $ip = $client;
		    }
		    elseif(filter_var($forward, FILTER_VALIDATE_IP))
		    {
		        $ip = $forward;
		    }
		    else
		    {
		        $ip = $remote;
		    }

		    return $ip;
		}
		public function __sleep()
	    { 
	    }
	    
	    public function __wakeup()
	    { 
	    }

	}
?>