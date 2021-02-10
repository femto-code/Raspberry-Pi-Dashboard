<?php
error_reporting(E_ALL);
ini_set ('display_errors', 'On');
// Authorization
session_start();
if(!isset($_SESSION["rpidbauth"])){
  $output = array('auth' => 'false');
	echo json_encode($output);
  exit();
}
require "Config.php";
$config = new Config;
$config->load("../user-settings.php");
// Uptime
$uptime = shell_exec("cat /proc/uptime");
$uptime = explode(" ", $uptime);
$uptime = $uptime[0];
$y = floor($uptime / 60 / 60 / 24 / 365);
$d = floor($uptime / 60 / 60 / 24) % 365;
$h = floor(($uptime / 3600) % 24);
$m = floor(($uptime / 60) % 60);
$s = $uptime % 60;
$uptime_string = '';
if ($y > 0) {
	$yw = $y > 1 ? ' years ' : ' year ';
	$uptime_string .= $y . $yw;
}
if ($d > 0) {
	$dw = $d > 1 ? ' days ' : ' day ';
	$uptime_string .= $d . $dw;
}
if ($h > 0) {
	$hw = $h > 1 ? ' hours ' : ' hour ';
	$uptime_string .= $h . $hw;
}
if ($m > 0) {
	$mw = $m > 1 ? ' mins ' : ' min ';
	$uptime_string .= $m . $mw;
}
if ($s > 0) {
	$sw = $s > 1 ? ' secs ' : ' sec ';
	$uptime_string .= $s . $sw;
}
// CPU temperature
exec("cat /sys/class/thermal/thermal_zone0/temp",$cputemp);
$cputemp = $cputemp[0] / 1000;
// CPU frequency
exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq",$cpufreq);
$cpufreq = $cpufreq[0] / 1000;
// load of processor
$getLoad = sys_getloadavg();
// time
$timed=date("H:i:s");
// RAM
$free = shell_exec('free -m'); // output in megabytes (-m)
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$mem = explode(" ", $free_arr[1]);
$mem = array_filter($mem);
$mem = array_merge($mem);
$free_version=trim(shell_exec("free --version")); // required trim(), to remove trailing whitespace
//echo "<pre>".$free_version."</pre>";
if ($free_version == "free from procps-ng 3.3.9"){ // old free version Linux 8
	$memtotal = $mem[1];
	$memused = $mem[2];
	$memfree = $mem[3];
	$membuffer = $mem[5];
	$memcached = $mem[6];
	$mavail = $memfree + $membuffer + $memcached;
	$munavail = $memused - $membuffer - $memcached;
	$memperc = round(($munavail / $memtotal)*100);
	// Swap
	$swap = explode(" ", $free_arr[3]);
	$swap=array_filter($swap, function($value) { return $value !== ''; });
	$swap = array_merge($swap);
	$swaptotal = $swap[1];
	$swapused = $swap[2];
	$swapfree = $swap[3];
	$swapperc = round(($swapused/$swaptotal)*100);
}else{ // new free version Linux 9 + 10
	$memtotal = $mem[1];
	$memused = $mem[2];
	$memfree = $mem[3];
	$membuffcache = $mem[5];
	//$memcached = $mem[6];
	$mavail = $mem[6];
	//$mavail = $memfree + $membuffer + $memcached;
	$munavail = $memtotal - $mavail;
	$memperc = round(($munavail / $memtotal)*100);
	// Swap
	$swap = explode(" ", $free_arr[2]);
	$swap=array_filter($swap, function($value) { return $value !== ''; });
	$swap = array_merge($swap);
	$swaptotal = $swap[1];
	$swapused = $swap[2];
	$swapfree = $swap[3];
	$swapperc = round(($swapused/$swaptotal)*100);
}
//
if(isset($_GET["statemail"])){
	
	$name_tag = array();
	$name_tag[0] = "Sunday";
	$name_tag[1] = "Monday";
	$name_tag[2] = "Tuesday";
	$name_tag[3] = "Wednesday";
	$name_tag[4] = "Thursday";
	$name_tag[5] = "Friday";
	$name_tag[6] = "Saturday";
	$num_tag = date("w");
	$tag = $name_tag[$num_tag];
	$jahr = date("Y");
	$n = date("d");
	$monat = date("m");
	$time = date("H:i");

	$msg = "\n\n :: Request sent on $tag, $n.$monat.$jahr - $time ::\n\n";
	$msg .= "Uptime: ".$uptime_string."\n\n";
	$msg .= "CPU Temperature: ".$cputemp." Grad Celsius\n\n";
	$msg .= "CPU Frequency: ".$cpufreq." MHz\n\n";
	$msg .= "CPU Loads: ".$getLoad[0].",".$getLoad[1].",".$getLoad[2]."\n\n";
	$msg .= "RAM: ".$memperc."%\n\n";
	$msg .= "RAM unused: ".$mavail." MB\n\n";
	$msg .= "RAM used: ".$munavail." MB\n\n";
	$msg .= "SWAP: ".$swapperc."%\n\n";
	$msg .= "SWAP overall: ".$swaptotal." MB\n\n";
	$msg .= "SWAP used: ".$swapused." MB\n\n";

	$command='python /var/www/html/'.$config->get("general.folder").'/statemail.py "Status Mail of RPi" "'.$msg.'"';
	//echo $command;
	$output=shell_exec($command);
	echo "<pre>$output</pre><br>";
	
	$a1 = "\n<div class=\"testbox\"	style=\"border: 1px dotted green; position: absolute; margin:0px; padding:10px; z-index:5; background-color:#ffffcc; color:#000000;\"> \n";
	$a2 = " \n </div> \n";
	$ausgabe = $a1 . "Status mail was sent!<br><button onclick='location.replace(\"../index.php\");'>Zurück</button>" . $a2;
	echo $ausgabe;

}else{
	$output = array('auth' => 'true', 'timest' => $timed, 'uptime' => $uptime_string, 'cputemp' => $cputemp, 'cpufreq' => $cpufreq, 'load' => $getLoad, 'memperc' => $memperc, 'memavail' => $mavail, 'memunavail' => $munavail, 'swapperc' => $swapperc, 'swaptotal' => $swaptotal, 'swapused' => $swapused);
	echo json_encode($output);
}

//$DURATION_start=microtime(true);
//$DURATION_end=microtime(true);
//$DURATION = $DURATION_end - $DURATION_start;
//echo "<p><font size='0'>Site generated in ".round($DURATION,3)." seconds</font></p>\n";
?>
