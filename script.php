<?php

/**
 * Created by SathyaBaman on 11/11/15.
 * Tanzaniya Automated IVR Report Generation Script.This script will 
 * be used to generate daily reports for tanzaniya IVR and log the 
 * results in to a file called Automated_Reports.log. This logged data will be 
 * used for generating monthly reports and further data analysis.
 * Copyright (C) 2015  Kanasalingam SathyaBaman
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 **/

class Reports{

	public $logger;
	public $db;
	public $time1;
	public $time2;
	// public $time1 = "2015-11-05 00.00.00";
	// public $time2 = "2015-11-05 23.59.59";

	public function __construct(){
		$this->logger            = new Logger();

		$this->logger->log_path  = '/var/www/html/';
		$this->logger->file_name = 'Automated_Reports.log';
		$this->db = new Db($this->logger);

		$time1=strftime('%F')." 00.00.00";
		$time2=strftime('%F')." 23.59.59";
	}


	public function generate_all_reports(){
		//$this->logger->writeLog("--------------------------------***********--------------------------------");
		$this->get_transfer_status();
		$this->get_successful_subscription_through_ivr();
		$this->get_packages_purchaged_through_ivr();
		$this->get_TPS_busy_hour_transaction();
		//$this->logger->writeLog("--------------------------------***********--------------------------------");
	}

	public function get_transfer_status(){

		//$this->logger->writeLog("About to Generate call transfer status reports");
		$results = $this->db->redirection_request_to_Call_Center($this->time1, $this->time2);
		// print_r('<pre>');
		// var_dump($results);
		// print_r('</pre>');
		if ($results) {
			for($k=0; $k<4; $k++){
			$this->logger->writeLog("call transfer, {$results[$k]["Transfer_Status"]}, {$results[$k]["Count"]} ");
			}
		}
		//$this->logger->writeLog("Generation of call transfer status reports done! :)");
	}

	public function get_successful_subscription_through_ivr(){

		//$this->logger->writeLog("About to Generate successfully subscription through ivr report");
		$results = $this->db->successful_subscription_through_ivr($this->time1, $this->time2);
		if ($results) {
			for($k=0; $k<2; $k++){
			$this->logger->writeLog("successfully subscription, {$results[$k]["Status"]}, {$results[$k]["Count"]} ");
			}
		}
		//$this->logger->writeLog("Generation successfully subscription through ivr report done! :)");
	}

	public function get_packages_purchaged_through_ivr(){
		//$this->logger->writeLog("About to Generate packages purchaged through ivr report");
		$results = $this->db->packages_purchaged_through_ivr($this->time1, $this->time2);
		if ($results) {
			for($k=0; $k<2; $k++){
			$this->logger->writeLog("packages purchaged, {$results[$k]["Status"]}, {$results[$k]["Count"]} ");
			}
		}
		//$this->logger->writeLog("Generation packages purchaged through ivr report done! :)");
	}

	public function get_TPS_busy_hour_transaction(){
		
		$t1=strftime('%F')." 12.00.00";
		$t2=strftime('%F')." 22.00.00";

		//$this->logger->writeLog("About to Generate TPS busy hour transaction report");
		$results = $this->db->TPS_busy_hour_transaction($t1, $t2);
		if ($results) {
			for($k=0; $k<2; $k++){
			$this->logger->writeLog("TPS busy, {$results[$k]["Status"]}, {$results[$k]["Count"]} ");
			}
		}
		//$this->logger->writeLog("Generation TPS busy hour transaction report done! :)");
	}
}


class Db {
	public $connection;
	public $logger;

	private $servername = "localhost";
	private $username   = "root";
	private $password   = "sathyabaman";
	private $database   = "ivrdatabase";

	public function __construct($logger){
		$this->logger = $logger;
	}

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

	public function redirection_request_to_Call_Center($time1, $time2) {
		$this->connect();
		$query = "SELECT TransferStatus as Transfer_Status,  
					count(ID) as Count FROM  calldetails 
					WHERE  (IVRStartTime BETWEEN ? AND ?) 
					AND CallTransfer LIKE '163%'  
					GROUP BY TransferStatus";
					$stmt = $this->connection->prepare($query);
					$result = $stmt->execute(array($time1, $time2));
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					return $results;
	}

	public function successful_subscription_through_ivr($time1, $time2){
		$this->connect();
		$query = "SELECT 
				        t.Txn_Status as Status, 
				        count(*) as Count 
				FROM 
				        transaction as t
				LEFT OUTER JOIN 
				        calldetails as c
				ON 
				        t.ID = c.ID
				WHERE 
				        (IVRStartTime BETWEEN ? AND ?) 
				GROUP BY 
				        t.Txn_Status;";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute(array($time1, $time2));
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $results;
	}

	public function packages_purchaged_through_ivr($time1, $time2){
		$this->connect();
		$query = "SELECT 
				        t.Txn_Status as Status, 
				        count(*) as Count 
				FROM 
				        transaction as t
				LEFT OUTER JOIN 
				        calldetails as c
				ON 
				        t.ID = c.ID
				WHERE 
				        (IVRStartTime BETWEEN ? AND ?) 
				GROUP BY 
				        t.Txn_Status;";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute(array($time1, $time2));
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $results;
	}

	public function TPS_busy_hour_transaction($time1, $time2)	{
		$this->connect();
		$query = "SELECT 
				        t.Txn_Status as Status, 
				        count(*) as Count 
				FROM 
				        transaction as t
				LEFT OUTER JOIN 
				        calldetails as c
				ON 
				        t.ID = c.ID
				WHERE 
				        (IVRStartTime BETWEEN ? AND ?) 
				GROUP BY 
				        t.Txn_Status;";
				$stmt = $this->connection->prepare($query);
				$result = $stmt->execute(array($time1, $time2));
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $results;
	}
}

class Logger {
	public $log_path;
	public $file_name;
	/**
	 * @param String $message
	 * @tutorial file_put_contents($file, $person, FILE_APPEND | LOCK_EX);
	 */
	public function writeLog($message) {
		$date     = date('Y-m-d H:i:s');
		$log_file = $this->file_name;
		$message  = sprintf('%s , %s , %s', getmypid(), $date, is_array($message)?json_encode($message):$message);
		file_put_contents($log_file, "{$message}\n", FILE_APPEND | LOCK_EX);
	}
}

$rpt = new Reports();
$rpt->generate_all_reports();

