<?php

/**
 * Created by SathyaBaman on 11/11/15.
 **/

class temples{



	public function addTemple(){
		$db = new Db();

		$name 				= $_POST['name'];
		$address 			= $_POST['address'];
		$latitude 			= $_POST['latitude'];
		$longitude 			= $_POST['longitude'];
		$description 		= $_POST['description'];
		$imei 				= $_POST['imei'];
		$deviceAssignedName = $_POST['deviceAssignedName'];
		$deviceName 		= $_POST['deviceName'];
		$manufacturer 		= $_POST['manufacturer'];
		$temple_type 		= $_POST['templetype'];



		$txt = "\n-------- ".date("F j, Y, g:i a")." ------------\nDevice Name : ".$deviceName." \nDevice Assigned Name : ".$deviceAssignedName." \nmanufacturer : ".$manufacturer." \nIMEI : ".$imei." \nTemple name : ".$name." \nTemple Address : ".$address." \nLatitude : ".$latitude." \nLongitude: ". $longitude . " \nDescription: ". $description . " \n";
	
		$myfile = file_put_contents('SubmittedTemples.txt', $txt.PHP_EOL , FILE_APPEND);


		$results = $db->addNewTemple($name, $address, $latitude, $longitude, $description, $temple_type);
		

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


	public function addNewTemple($name, $address, $latitude, $longitude, $description, $temple_type){
		$this->connect();		

		$query ="INSERT INTO submitted_temples (id, name, type, description, latitude, longitude, address, image, status) VALUES ('','".$name."','".$temple_type."','".$description."','".$latitude."','".$longitude."','".$address."','', '1')";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute();
				return $result;
	}


}


$rpt = new temples();
$rpt->addTemple();
