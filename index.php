<?php
session_start();
error_reporting (E_ALL);
ini_set ('display_errors', 'On');
$auth=(isset($_SESSION["rpidbauth"])) ? true : false;

require "backend/Config.php";
$config = new Config;
$config->load("user-settings.php");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="apple-touch-icon" sizes="57x57" href="rpidb_ico/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="rpidb_ico/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="rpidb_ico/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="rpidb_ico/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="rpidb_ico/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="rpidb_ico/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="rpidb_ico/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="rpidb_ico/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="rpidb_ico/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="rpidb_ico/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="rpidb_ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="rpidb_ico/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="rpidb_ico/favicon-16x16.png">
<link rel="manifest" href="rpidb_ico/rpidb_manifest.webmanifest">
<meta name="msapplication-TileColor" content="#0099ff">
<meta name="msapplication-TileImage" content="rpidb_ico/ms-icon-144x144.png">
<meta name="theme-color" content="#0099ff">

<link rel="stylesheet" href="css/bootstrap-4.6.0.min.css">
<link rel="stylesheet" href="css/bootstrap-icons.css">
<link rel="stylesheet" href="css/darkmode.css" id="dmcss" type="text/css" disabled>
<link rel="stylesheet" href="css/mdtoast.min.css">

<title>RPi Dashboard</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Rubik:wght@320&display=swap');
body{
  font-family: 'Rubik', sans-serif;
}
.hidden{
  display: none;
}
@media screen and (max-width: 530px) {
  #notf {
    display: block;
  }
  #dot{
    display:none;
  }
}
.preload-screen {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(img/load.gif) center no-repeat #fff;
}
.doughnut-chart-container {
  height: 360px;
  width: 360px;
  float: left;
}
</style>

<?php
if($auth){
  $upt=new DateTime(shell_exec('uptime -s'));
  $uptstr = $upt->format('d.m.Y H:i:s');
  // Disk space
  $df = disk_free_space("/");
  $df = $df / 1000;//KB
  $df = $df / 1000;//MB
  $df = $df / 1000;//GB

  $ds = disk_total_space("/");
  $ds = $ds / 1000;//KB
  $ds = $ds / 1000;//MB
  $ds = $ds / 1000;//GB

  $df_rund = round($df, 2);
  $ds_rund = round($ds, 2);

  $p = $df / $ds * 100;
  //

  $spannung=substr(exec("vcgencmd measure_volts core"),5);
  if(strpos($spannung,"failed")!==false) $spannung=$spannung."<div class='alert alert-danger' role='alert'>Reading of core voltage failed. Please run<br><kbd>sudo usermod -aG video www-data</kbd><br>in a terminal to solve this problem.</div>";
}

?>

</head>
<body onload="preload()" style="background-color: #eee">
<noscript style="z-index: 99999!important; position: absolute; top: 0; width: 98%; padding: 3%;"><div class="alert alert-danger" role="alert">JavaScript is disabled in your browser. This site <b>requires</b> JS in order to work properly - please activate!</div></noscript>
<div class="preload-screen"></div>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark shadow-sm">
  <img class="rounded float-left" src="img/rpi_logo.png" alt="Responsive image" height="30px" width="auto">&nbsp;<!--50px height navbar default-->
  <a class="navbar-brand" href="#">Raspberry Pi Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
				<a class="nav-link" href="backend/sys_infos.php?statemail" onclick="return confirm('Send status mail?')">Status Mail</a>
      </li>
    </ul>
		<p style="color: white;line-height:15px;margin-bottom:0px"><b>Hostname:</b> <?php system("hostname");?> &#183; <b>Internal IP:</b> <?php echo $_SERVER["SERVER_ADDR"];?><br>
		<b>Access from:</b> <?php echo $_SERVER["REMOTE_ADDR"];?> &#183; <b>Port:</b> <?php echo $_SERVER['SERVER_PORT']; ?></p>
	</div>
</nav>

<div style="margin-top:70px" class="container">
	<div class="row<?php if(!$auth){ echo " hidden"; } ?>">
	  <div class="col-sm-9">
			<div class="card shadow-sm">
	      <div class="card-header border-primary text-primary"><i class="bi bi-info-circle"></i>&nbsp;Overview</div>
			  <div class="card-body">
					<h5 id="sys1" class="card-title"><span id="overallstate"></span></h5>
          <p id="sys11" class="card-text"></p>
					<p id="sys2" class="card-text"></p>
					<hr>
					<p><i class="bi bi-clock-history"></i><!--<img src="img/time-icon.png">-->&nbsp;Uptime: <b><span id="uptime"></span></b><?php if($auth){ ?>&nbsp;(started <?=$uptstr;?>)<?php } ?></p>
		      <table style="width:100%"><tbody><tr><td style="width:10%"><button type="button" id="pctl" onclick="y=100; this.innerHTML=togglep(true);" class="btn btn-secondary btn-sm"><i class="bi bi-pause"></i></button></td><td style="width:90%">
		      <div class="progress" style="margin-top: 1px; height: 2px;"><div class="progress-bar py" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></td></tr></tbody></table>
					<p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
			  </div>
			</div>	
	  </div>
	  <div class="col-sm-3 pt-1 pt-md-0">
			<div class="card shadow-sm">
        <div class="card-header border-primary text-primary"><i class="bi bi-command"></i>&nbsp;System</div>
			  <div class="card-body">
					<button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-outline-primary"><i class="bi bi-power"></i>&nbsp;Power</button>
			  </div>
			</div>	
	  </div>
	</div>
	<div class="row pt-3<?php if(!$auth){ echo " hidden"; } ?>">
	  <div class="col-sm-5">
			<div class="card text-center border-info shadow-sm">
			  <div class="card-body">
					<h5 class="card-title"><i class="bi bi-cpu"></i>&nbsp;<span id="cput"></span></h5>
					<p class="card-text"><canvas id="myChart"></canvas>1 min: <b><span id="m1"></span></b> &#183; 5 min: <b><span id="m5"></span></b> &#183; 15 min: <b><span id="m15"></span></b><br>CPU clock: <b><span id="frequency"></span> MHz</b></p>
					<p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
			  </div>
			</div>
	  </div>
	  <div class="col-sm-3 pt-1 pt-md-0">
			<div class="card text-center border-danger shadow-sm">
			  <div class="card-body">
				<h5 id="tempstate" class="card-title"></h5>
				<div id="indicatorContainer"></div><!--CPU-Indicator-->
				<p class="card-text"><b><span style="font-size: 20px" id="temperature"></span> °C</b></p>
				<p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
			  </div>
			</div>
	  </div>
	  <div class="col-sm-4 pt-1 pt-md-0">
			<div class="card text-center border-warning shadow-sm">
			  <div class="card-body">
					<h5 class="card-title"><i class="bi bi-hdd-network"></i>&nbsp;<span id="ramt"></span></h5>
					<div class="progress">
					  <div class="progress-bar bg-success" id="ram1" role="progressbar" style="" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"></div>
					  <div class="progress-bar bg-danger" id="ram2" role="progressbar" style="" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<p class="card-text">Free: <b><span id="memfree"></span> MB</b> &#183; Used: <b><span id="memused"></span> MB</b><br>Total: <b><span id="memtotal"></span> MB</b></p>
					<p class="card-text"><span id="swapsys"></span></p>
					<p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
			  </div>
			</div>
	  </div>
	</div>
  <hr id="ldiv" class="my-4<?php if(!$auth){ echo " hidden"; } ?>"><!-- Static infos, that won't be updated -->
  <?php
  if($auth){
  ?>
  <div class="row pt-3">
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center border-info">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-hdd-rack"></i>&nbsp;Hardware</h5>
          <?php print "<pre>"; echo shell_exec("lsusb"); print "</pre>"; ?>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center border-info">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-globe"></i>&nbsp;Web Server</h5>
          <p class="card-text" id="webinfo">Software: <b><?php echo $_SERVER["SERVER_SOFTWARE"];?></b><br>Address: <b><?php echo $_SERVER["SERVER_ADDR"];?></b><br>PHP version: <b><?php echo phpversion();?></b><br>User: <b><?php system("whoami"); ?></b><br>Protocol: <b><?php echo $_SERVER["SERVER_PROTOCOL"]; ?></b></p>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
	</div>
	<div class="row pt-3">
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-body">
					<h5 class="card-title"><i class="bi bi-hdd"></i>&nbsp;SD Card</h5>
					<p class="card-text"><canvas height="100" class="doughnut-chart-container" id="space"></canvas>Total: <b><?php echo $ds_rund;?> GB</b> &#183; Free: <b><?php echo $df_rund;?> GB</b> &#183; Used: <b><?php echo round($ds-$df,2);?> GB</b></p>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-body">
					<h5 class="card-title"><i class="bi bi-lightning"></i>&nbsp;Voltage</h5>
					<p style="font-size: 20px" class="card-text text-muted"><?php echo $spannung; ?></p>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	</div>
	<div class="row pt-3">
	  <div class="col-sm-6 pt-1 pt-md-0">
		<div class="card text-center border-info">
		  <div class="card-header">Kernel</div>
		  <div class="card-body">
				<p class="card-text" id="kernel"><?php echo php_uname(); ?></p>
				<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
		  </div>
		</div>
	  </div>
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-header">Model</div>
			  <div class="card-body">
					<samp><?php echo exec("cat /sys/firmware/devicetree/base/model");?></samp>
					<samp><?php $ot=shell_exec("vcgencmd version");if(strpos($ot,"failed")!==false){echo "<div class='alert alert-danger' role='alert'>Execution of system command failed. Please run<br><kbd>sudo usermod -aG video www-data</kbd><br>in a terminal to solve this problem.</div>";}else{echo $ot;}?></samp>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	</div>
	<div class="row pt-3">
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-header">Partitions / Storage</div>
			  <div class="card-body">
					<?php print "<pre style='text-align: left!important;'>"; echo shell_exec("df -h"); print "</pre>"; ?>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-header">Operating System</div>
			  <div class="card-body">
					<?php print "<pre>"; echo shell_exec("cat /etc/os-release"); print "</pre>"; ?>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	</div>
	<div class="row pt-3">
	  <div class="col-sm-6 pt-1 pt-md-0">
		<div class="card text-center border-info">
		  <div class="card-header">Hostnamectl</div>
		  <div class="card-body">
				<?php print "<pre style='text-align: left!important;'>"; echo shell_exec("hostnamectl"); print "</pre>"; ?>
				<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
		  </div>
		</div>
	  </div>
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-header">Processor</div>
			  <div class="card-body">
					<?php print "<pre>"; echo shell_exec("lscpu"); print "</pre>"; ?>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	</div>

<?php
}else{
?>
<div style="text-align:center" id="lock_section"><i style="width: 100px;height:100px;color:#aaa" class="bi bi-shield-lock"></i><br>You are not authorized!</div>

<?php

}
?>
</div>

<!-- Shutdown/Reboot options modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i class="bi bi-power"></i>&nbsp;Shutdown / Reboot RPi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
				<!---->
        <div id="currentState"></div>
				<form id="pwrform" onkeydown="return event.key != 'Enter';">
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="pwrOptions" id="inlineRadio1" value="1">
					  <label class="form-check-label" for="inlineRadio1">Shutdown</label>
					</div>
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="pwrOptions" id="inlineRadio2" value="2" checked>
					  <label class="form-check-label" for="inlineRadio2">Reboot</label>
					</div>
					<hr>
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-item nav-link active" id="nav-home-presets" data-toggle="tab" href="#nav-presets" role="tab" aria-controls="nav-presets" aria-selected="true">Presets</a>
              <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Delay</a>
              <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Exact</a>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent" style="padding:10px">
            <div class="tab-pane fade show active" id="nav-presets" role="tabpanel" aria-labelledby="nav-home-presets">
              <select class="custom-select my-1 mr-sm-2" id="time1" onchange="tselect=1">
                <option selected value="1">now (= 1 min)</option>
                <option value="5">in 5 min</option>
                <option value="15">in 15 min</option>
                <option value="30">in 30 min</option>
              </select>
            </div>
            <div class="tab-pane fade show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
              <label for="customRange2">Delay by <b><span id="rinp">?</span></b> min</label>
              <input type="range" id="time2" class="custom-range" min="1" max="60" id="customRange2" onchange="document.getElementById('rinp').innerHTML=this.value;tselect=2">
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
              <input id="time3" class="form-control" type="time" placeholder="Select exact time" onchange="tselect=3">
            </div>
          </div>
          <hr>
					<div id="pwrauth" class="form-group">
					  <label for="inputPassword2" class="sr-only">Password</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" id="myPsw2"><i class="bi bi-key"></i></div>
              </div>
              <input type="password" class="form-control" id="inputPassword2" placeholder="Password" aria-label="Password" aria-describedby="myPsw2">
              <div class="invalid-feedback">Invalid password!</div>
            </div>
          </div>
          <div id="pwrCheck" class='alert alert-info' role='alert'><i class='bi bi-chevron-double-right'></i>&nbsp;Checking authorization ...</div>
          <div id="pwrCheck2" class="hidden alert alert-success" role="alert"><i class="bi bi-check2-circle"></i>&nbsp;Authenticated</div>
				</form>
      </div>
      <div class="modal-footer">
				<button id="confbtn" class="btn btn-primary" onclick="authorize();">Confirm identity</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Restart modal -->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="restartModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="restartModalTitle">System is being restarted...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Please be patient...<br>
				<div class="progress">
				  <div class="progress-bar p1" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary b2" onclick="location.reload()" id="secbutton" disabled>Refresh dashboard</button>
      </div>
    </div>
  </div>
</div>
<!-- About/Help Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-gear"></i>&nbsp;Options & About</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="custom-control custom-switch">
          <input type="checkbox" onchange="toggleDarkMode()" class="custom-control-input" id="dm">
          <label class="custom-control-label" for="dm">Dark Mode</label>
        </div>
        <div class="custom-control custom-switch">
          <input type="checkbox" onchange="toggleAutoDarkMode()" class="custom-control-input" id="dmauto">
          <label class="custom-control-label" for="dmauto">According to system settings (overrides first option)</label>
        </div>
        <hr>
        <h4 class="mb-0">Threshold values</h4>
        <small class="text-muted">Throwing a warning</small>
        <form>
          <div class="form-row">
            <div class="col">
              <input type="number" class="form-control" placeholder="default: 60" aria-describedby="critCpuTempHelp" min="20" max="80">
              <small id="critCpuTempHelp" class="form-text text-muted">CPU Temperature (°C) - default: 60°C</small>
            </div>
            <div class="col">
              <input type="number" class="form-control" placeholder="default: 80" aria-describedby="critRamSizeHelp" min="0" max="100">
              <small id="critRamSizeHelp" class="form-text text-muted">RAM Load (%) - default: 80%</small>
            </div>
          </div>
          <div class="form-row">
            <div class="col-6">
              <input type="number" class="form-control" placeholder="default: 2" aria-describedby="critCpuLoadHelp" min="1" max="4">
              <small id="critCpuLoadHelp" class="form-text text-muted">CPU workload (last min) - default: 2</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputRefreshRate" class="col-sm-6 col-form-label">Refresh rate (sec)</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" placeholder="default: 15" id="inputRefreshRate" aria-describedby="dbRefreshHelp" min="5" max="600">
            </div>
            <small id="dbRefreshHelp" class="col form-text text-muted">Refresh interval of live data update section (recommended: 10 - 60 sec) - Pay attention: Do not set too low. - default: 15</small>
          </div>
          <button type="button" class="btn btn-outline-success">Apply</button>
          <button type="button" class="btn btn-outline-secondary">Discard changes</button>
          <button type="button" class="btn btn-outline-primary">Defaults</button>
        </form>
        <hr />
				<div id="accordion">
				  <div class="card">
            <div class="card-header" id="headingOne">
              <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">About this version of RPi Dashboard</button>
              </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
              <div class="card-body">
              <h3><font class='text-success'>&#10003;</font> Version 0.7.1</h3>
              <ul><li>fixed password check always failing</li><li>improved footer layout</li><li>reworked shutdown / power event handling (less problems detecting which shutdown event is scheduled)</li><li>added dark theme option to apply to current system settings</li><li>bug fixes</li><li><a href='https://github.com/femto-code/Rasberry-Pi-Dashboard/releases'>Stay updated here</a></li><li><i><a href="CHANGELOG.md">All changes</a></i></li></ul>
              <small>changes since RPi Dashboard v0.7 (Feb 2021)</small>
              </div>
            </div>
				  </div>
				  <div class="card">
            <div class="card-header" id="headingTwo">
              <h5 class="mb-0">
              <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Customization</button>
              </h5>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
              <div class="card-body">Under <code>/var/www/html/Raspberry-Pi-Dashboard/custom/</code> there is <kbd>custom.js</kbd>. Within this JS file you can customize a few things of functions and appearance of RPi Dashboard. See the notes inside the file for instructions.</div>
            </div>
				  </div>
				  <div class="card">
            <div class="card-header" id="headingThree">
              <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">What does 'CPU Load' mean?</button>
              </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
              <div class="card-body"><i>Explanation of CPU Loads in Linux at <a href="http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages">http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages</a> (recommended article)</i><br>Critical value about 3~4, when all kernel cores are busy.</div>
            </div>
				  </div>
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel"><i class="bi bi-shield-lock"></i>&nbsp;Authentication</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class='alert alert-info' role='alert'>Please enter password to access dashboard!</div>
        <form onkeydown="return event.key != 'Enter';">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="myPsw">Password</span>
            </div>
            <input type="password" class="form-control" placeholder="" aria-label="Password" aria-describedby="myPsw" id="lpwd">
            <div class="invalid-feedback">Invalid password!</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="loginToServer()" id="lbtn">Login</button>
      </div>
    </div>
  </div>
</div>

<!--End Modal(s)-->

<!-- Footer -->
<footer style="line-height: 40px; background-color: #f5f5f5; margin-top: 10px;">
	<div class="container text-center">
		RPi Dashboard v0.7.1 <font class="text-muted">(Feb 2021)</font> <span id="dot">&middot;</span> <font id="notf" class="text-success">See the <a href="https://github.com/femto-code/Rasberry-Pi-Dashboard/releases">Github releases</a> for updates!</font><br />
    <button class="btn btn-secondary mb-2" onclick="$('#exampleModal').modal('show');"><i class="bi bi-gear"></i>&nbsp;Options</button>
		<hr style="margin-top: 0; margin-bottom: 0;">
		femto-code&nbsp;<a href="https://github.com/femto-code"><i class="bi bi-github"></i></a> &middot; <font class="text-muted">2018 - 2021</font>
	</div>
</footer>
<!-- End Footer -->

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/popper-1.16.1.min.js"></script>
<script src="js/bootstrap-4.6.0.min.js"></script>
<script src="js/Chart-2.9.3.min.js"></script>
<script src="js/mdtoast.min.js"></script>
<script src="js/radialIndicator-2.0.0.min.js"></script>

<script>
warn_cpu_temp = <?=$config->get("thresholds.warn_cpu_temp")?>;
warn_ram_space = <?=$config->get("thresholds.warn_ram_space")?>;
upd_time_interval = <?=$config->get("thresholds.upd_time_interval")?>;
warn_loads_size = <?=$config->get("thresholds.warn_loads_size")?>;
console.log("Custom user options: warncputemp="+warn_cpu_temp+" | warn_ram_space="+warn_ram_space+" | upd_time_interval="+upd_time_interval+" | warn_loads_size="+warn_loads_size);
</script>

<script src="js/main.js"></script>

<script>
var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["1 min", "5 min", "15 min"],
    datasets: [{
      label: "Loads",
      backgroundColor: 'rgb(255, 99, 132)',
      borderColor: 'rgb(255, 99, 132)',
      data: [0,0,0],
    }]
  },
   options: {
		animation: {
			easing: 'easeInExpo'
		},
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true
        }
      }],    
    }
  }
});
<?php
if($auth){
?>
var ctx2 = document.getElementById('space').getContext('2d');
var chart2 = new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: ["Free", "Used"],
    datasets: [{
      label: "Disk usage",
      backgroundColor: ['rgb(132, 244, 71)','rgb(255, 99, 132)'],
      data: [<?=round($df,2)?>,<?=round($ds-$df,2)?>],
    }]
  },
  options: {}
});
<?php
}
?>
</script>

</body>
</html>
