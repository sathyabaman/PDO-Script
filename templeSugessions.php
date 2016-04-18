<?php

/**
 * Created by SathyaBaman on 25/03/2016.
 **/



		$name = $_POST['txt_name'];
		$title = $_POST['txt_title'];
		$description = $_POST['txt_description'];
		$imei = $_POST['imei'];
		$deviceName = $_POST['deviceName'];
		$manufacturer =$_POST['manufacturer'];
		$deviceAssignedName =$_POST['deviceAssignedName'];
		$temple_id = $_POST['temple_id'];
		
	




		$txt = "\n-------- ".date("F j, Y, g:i a")." ------------\nDevice Name : ".$deviceName." \nDevice Assigned Name : ".$deviceAssignedName." \nmanufacturer : ".$manufacturer." \nIMEI : ".$imei." \nTemple Id : ".$temple_id." \nUser Name : ".$name." \nTitle : ".$title." \nDescriprion: ". $description . " \n";
	
		$myfile = file_put_contents('sugessions.txt', $txt.PHP_EOL , FILE_APPEND);
	

		




