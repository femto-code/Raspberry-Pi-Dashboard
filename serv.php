<?php
error_reporting (E_ALL);
ini_set ('display_errors', 'On');
if(isset($_GET["checkShutdown"])){
  system("date --date @$(head -1 /run/systemd/shutdown/scheduled |cut -c6-15)");
  exit;
}else if(isset($_GET["cancelShutdown"])){
  system('sudo /sbin/shutdown -c');
  exit;
}
$pass = $_REQUEST["p"];
$time = $_REQUEST["time"];
if (strpos($time, ':') == false) {
  $time="+".$time;
}
if($pass != "root"){
	echo "wrongCredentials";
}else{
	if($_REQUEST["a"]=="1"){
		echo "true_";
		system('sudo /sbin/shutdown -h '.$time);
	}else if($_REQUEST["a"]=="2"){
		echo "true_";
		system("sudo /sbin/shutdown -r ".$time);
	}else{
		echo "false";
  }
  system("date --date @$(head -1 /run/systemd/shutdown/scheduled |cut -c6-15)");
}
?>
