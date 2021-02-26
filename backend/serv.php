<?php
session_start();
error_reporting (E_ALL);
ini_set ('display_errors', 'On');

require "Config.php";
$config = new Config;
$config->load("../user-settings.php");
$correctPassword = $config->get("general.pass");

if(isset($_GET["logout"])){
  session_destroy();
  exit();
}
if(isset($_POST["check"])){
  if(!isset($_SESSION["rpidbauth"])){
    echo "invalid";
    exit();
  }
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
function getShutdownEventsInfo(){
  // system("date --date @$(head -1 /run/systemd/shutdown/scheduled |cut -c6-15)");
  // old command which is not very comfortable in order to retrieve which event is scheduled (poweroff vs reboot)
  // For now use old one for compatibility reasons which I dont know so far
  $return=array();
  $output2=shell_exec("busctl get-property org.freedesktop.login1 /org/freedesktop/login1 org.freedesktop.login1.Manager ScheduledShutdown");
  if($output2==""){
    $output=shell_exec("date --date @$(head -1 /run/systemd/shutdown/scheduled |cut -c6-15)");
    $return["date"]=$output;
    $return["act"]="unknown";
  }else{
    $strings=explode(" ", $output2);
    // The output specifies the shutdown time as microseconds since the Unix epoch -> divide by 1000
    //echo gettype($strings[2]);
    $return["date"]=round(floatval($strings[2]) / 1000); // Unix milliseconds
    $return["act"]=explode("\"", $strings[1])[1];
  }
  return $return;
}
if(isset($_GET["checkShutdown"])){
  if(!isset($_SESSION["rpidbauth"])){
    $return=array();
    $return["act"]="unauthorized";
    echo json_encode($return);
  }else{
    echo json_encode(getShutdownEventsInfo());
  }
  exit();
}else if(isset($_GET["cancelShutdown"])){
  if(!isset($_SESSION["rpidbauth"])){
    echo "unauthorized";
  }else{
    system('sudo /sbin/shutdown -c');
  }  
  exit();
}
if(isset($_REQUEST["p"])){
  $pass = md5($_REQUEST["p"]);
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
  		echo "false_";
    }
    echo json_encode(getShutdownEventsInfo());
  }
}
if(isset($_REQUEST["updateSettings"])){
  $allowed=array_keys($config->get("thresholds"));
  $edit=array('thresholds' => array (), 'general' => array ());
  foreach ($_POST as $key => $val) {
    if(in_array($key, $allowed)){
      $edit["thresholds"]+=array($key => $val);
    }
  }
  $edit["general"]=$config->get("general");
  echo $config->save($edit);
  //echo json_encode($edit);
}
?>
