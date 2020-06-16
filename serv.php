<?php
error_reporting (E_ALL);
ini_set ('display_errors', 'On');
$pass = $_REQUEST["p"];
$time = $_REQUEST["time"]
if($pass != "root"){
	echo "wrongCredentials";
}else{
	if($_REQUEST["a"]=="1"){
		echo "true";
		echo system('sudo /sbin/shutdown -h +'.$time);
	}else if($_REQUEST["a"]=="2"){
		echo "true";
		echo system("sudo /sbin/shutdown -r +".$time);
	}else{
		echo "false";
	}
}
?>
