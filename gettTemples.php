<?php

/**
 * Created by SathyaBaman on 11/11/15.
 **/

class temples{



	public function getalltemples(){
		$db = new Db();


		$from = $_POST['from'];
		$temp = $_POST['radius'];
		$radius = explode(" ", $temp);
		$type = $_POST['type'];
		$count = $_POST['count'];
		$from_lat_lng = $_POST['from_lat_lng'];
		$imei = $_POST['imei'];
		$deviceName = $_POST['deviceName'];
		$manufacturer =$_POST['manufacturer'];
		$deviceAssignedName =$_POST['deviceAssignedName'];
		
		
		$lat = "6.924832";
		$lng = "79.855990";

		if($from == "My GPS Location"){
				$temp_lat_lng = explode(',', $from_lat_lng);
				$lat = $temp_lat_lng[0];
				$lng = $temp_lat_lng[1];
		}else{
				switch ($from) {
					case "Chankanai":
							$lat = "9.748266"; $lng = "79.970265";
							break;
					case "Jaffna Town":
							$lat = "9.664740"; $lng = "80.020788";
							break;
					case "Karainagar":
							$lat = "9.745053"; $lng = "79.881946";
							break;
					case "Karaveddy":
							$lat = "9.800168"; $lng = "80.200202";
							break;
					case "Kilinochchi":
							$lat = "9.390484"; $lng = "80.406473";
							break;
					case "Kopay":
							$lat = "9.705922"; $lng = "80.065380";
							break;
					case "Maruthankerney":
							$lat = "9.622185"; $lng = "80.396826";
							break;
					case "Nallur":
							$lat = "9.673756"; $lng = "80.033183";
							break;
					case "Point Pedro":
							$lat = "9.824650"; $lng = "80.236677";
							break;
					case "Sandilipay":
							$lat = "9.742129"; $lng = "79.986157";
							break;
					case "Skanthapuram":
							$lat = "9.341890"; $lng = "80.302674";
							break;
					case "Tellippalai":
							$lat = "9.785668"; $lng = "80.035347";
							break;
					case "Uduvil":
							$lat = "9.732496"; $lng = "80.008623";
							break;
					default:
							$lat = "6.924832"; $lng = "79.855990";
							break;
				}

		}



		$txt = "\n-------- ".date("F j, Y, g:i a")." ------------\nDevice Name : ".$deviceName." \nDevice Assigned Name : ".$deviceAssignedName." \nmanufacturer : ".$manufacturer." \nIMEI : ".$imei." \nFrom : ".$from." \nRadius : ".$radius[0]." \nType : ". $type . " \nLimit : ".$count." \nFrom Latlng : ".$from_lat_lng. " \nLatitude : ". $lat. " \nLongitude : ".$lng ."";
	
		$myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND);
	

		$results = $db->gettemples($lat, $lng, $radius[0], $type, $count);
		$size =  sizeof($results);
		
			

	if($size == 0) {
		echo '[{"id":"1","name":"No Data to Display","description":"","latitude":"","longitude":"","image":"noimage.png","status":"1"}]';
		} else{
		echo json_encode($results);
		}

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


	public function gettemples($latitude, $longitude, $distance, $templetype, $limitno){
		$this->connect();
		// $query = "SELECT * FROM temples";
		
		$query = "SELECT *, ( 6371 * acos( cos( radians(".$latitude.") ) * cos( radians( latitude ) ) * 
			cos( radians( longitude ) - radians(".$longitude.") ) + sin( radians(".$latitude.") ) * 
			sin( radians( latitude ) ) ) ) AS distance FROM temples HAVING
			distance < ".$distance." ORDER BY RAND() LIMIT 0 , ".$limitno."";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute();
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $results;
	}

}


$rpt = new temples();
$rpt->getalltemples();
