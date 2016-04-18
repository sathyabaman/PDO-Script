<?php

/**
 * Created by SathyaBaman on 11/11/15.
 **/

class temples{



	public function getsingletemple(){
		$db = new Db();


		$id = $_POST['temple_id'];
		$results = $db->gettempleDetails($id);
		echo json_encode($results);
		
	
	}

}


class Db {
	public $connection;

	private $servername = "";
	private $username   = "";
	private $password   = "";
	private $database   = "";


	public function connect(){
		try {
			$this->connection = new PDO("mysql:host={$this->servername};dbname={$this->database}", $this->username, $this->password);
			// set the PDO error mode to exception
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			//$this->logger->writeLog('MySQL Connection Success');
			return $this->connection;
		} catch(PDOException $e) {
			$this->logger->writeLog('MySQL Connection failed');
			return false;
		}
	}


	public function gettempleDetails($temple_id){
		$this->connect();		
		$query = "SELECT * FROM temples WHERE id=".$temple_id."";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute();
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $results;
	}


}


$rpt = new temples();
$rpt->getsingletemple();
