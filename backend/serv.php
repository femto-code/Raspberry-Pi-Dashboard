<?php
session_start();
error_reporting (E_ALL);
ini_set ('display_errors', 'On');

// Change password here as MD5 encryption
$correctPassword = "63a9f0ea7bb98050796b649e85481845";

if(isset($_GET["logout"])){
  session_destroy();
  exit();
}
if(isset($_POST["check"])){
  $dif=time() - $_SESSION["rpidbauth"];
  if($dif > 60 * 4){
    echo "invalid";
  }else{
    echo "valid";
  }
  exit();
}
if(isset($_POST["login"])){
  if(isset($_POST["pw"])){
    $pw=md5($_POST["pw"]);
    if($pw==$correctPassword){
      echo "correctCredentials";
      $_SESSION["rpidbauth"]=time();
    }else{
      echo "wrongCredentials";
    }
  }
  exit();
}
if(isset($_GET["checkShutdown"])){
  system("date --date @$(head -1 /run/systemd/shutdown/scheduled |cut -c6-15)");
  exit();
}else if(isset($_GET["cancelShutdown"])){
  system('sudo /sbin/shutdown -c');
  exit();
}
$pass = $_REQUEST["p"];
$time = $_REQUEST["time"];
if (strpos($time, ':') == false) {
  $time="+".$time;
}
if( ($pass != $correctPassword) && (time()-$_SESSION["rpidbauth"] > 5 * 60) ){
	echo "wrongCredentials";
}else{
  if($pass==$correctPassword){
    $_SESSION["rpidbauth"]=time();
  }
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
