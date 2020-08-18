<?php
session_start();
error_reporting (E_ALL);
ini_set ('display_errors', 'On');
$auth=(isset($_SESSION["rpidbauth"])) ? true : false;
?>
<!doctype html>
<html>
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
<link rel="manifest" href="rpidb_ico/rpidb_manifest.json">
<meta name="msapplication-TileColor" content="#0099ff">
<meta name="msapplication-TileImage" content="rpidb_ico/ms-icon-144x144.png">
<meta name="theme-color" content="#0099ff">

<link rel="stylesheet" href="css/bootstrap-4.5.0.min.css">
<link rel="stylesheet" href="css/darkmode.css" id="dmcss" type="text/css" disabled>
<link rel="stylesheet" href="css/mdtoast.min.css">

<link rel="stylesheet" href="custom/custom.css"><!-- Custom Styles -->

<title>RPi Dashboard</title>

<script>
function preload(){
	// Update of all data
	updatedb();
	if(window.location.search == "?live=disabled"){
		console.info("Live Update was disabled through site parameters.");
		document.getElementById("pctl").innerHTML='<i data-feather="play"></i>';
	}
  setTimeout(function(){ $(".preload-screen").fadeOut("slow"); }, 500);
  checkShutdown();
}
</script>

<style>
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

<script src="custom/custom.js"></script>

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
	      <div class="card-header border-primary text-primary"><i data-feather="align-justify"></i>&nbsp;Overview</div>
			  <div class="card-body">
					<h5 id="sys1" class="card-title"><span id="overallstate"></span></h5>
          <p id="sys11" class="card-text"></p>
					<p id="sys2" class="card-text"></p>
					<hr>
					<p><i data-feather="clock"></i><!--<img src="img/time-icon.png">-->&nbsp;Uptime: <b><span id="uptime"></span></b><?php if($auth){ ?>&nbsp;(started <?=$uptstr;?>)<?php } ?></p>
		      <table style="width:100%"><tbody><tr><td style="width:10%"><button type="button" id="pctl" onclick="y=100; this.innerHTML=togglep(true);feather.replace();" class="btn btn-secondary btn-sm"><i data-feather="pause"></i></button></td><td style="width:90%">
		      <div class="progress" style="margin-top: 1px; height: 2px;"><div class="progress-bar py" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></td></tr></tbody></table>
					<p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
			  </div>
			</div>	
	  </div>
	  <div class="col-sm-3 pt-1 pt-md-0">
			<div class="card shadow-sm">
        <div class="card-header border-primary text-primary"><i data-feather="command"></i>&nbsp;System</div>
			  <div class="card-body">
					<button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-outline-primary"><i data-feather="power"></i>&nbsp;Power</button>
			  </div>
			</div>	
	  </div>
	</div>
	<div class="row pt-3<?php if(!$auth){ echo " hidden"; } ?>">
	  <div class="col-sm-5">
			<div class="card text-center border-info shadow-sm">
			  <div class="card-body">
					<h5 class="card-title"><i data-feather="activity"></i>&nbsp;<span id="cput"></span></h5>
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
					<h5 class="card-title"><i data-feather="cpu"></i>&nbsp;<span id="ramt"></span></h5>
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
          <h5 class="card-title"><i data-feather="hard-drive"></i>&nbsp;Hardware</h5>
          <?php print "<pre>"; echo shell_exec("lsusb"); print "</pre>"; ?>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center border-info">
        <div class="card-body">
          <h5 class="card-title"><i data-feather="globe"></i>&nbsp;Web Server</h5>
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
					<h5 class="card-title"><i data-feather="database"></i>&nbsp;SD Card</h5>
					<p class="card-text"><canvas height="100" class="doughnut-chart-container" id="space"></canvas>Total: <b><?php echo $ds_rund;?> GB</b> &#183; Free: <b><?php echo $df_rund;?> GB</b> &#183; Used: <b><?php echo round($ds-$df,2);?> GB</b></p>
					<p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
			  </div>
			</div>
	  </div>
	  <div class="col-sm-6 pt-1 pt-md-0">
			<div class="card text-center border-info">
			  <div class="card-body">
					<h5 class="card-title"><i data-feather="zap"></i>&nbsp;Voltage</h5>
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
<div style="text-align:center" id="lock_section"><i style="width: 100px;height:100px;color:#aaa" data-feather="lock"></i><br>You are not authorized!</div>

<?php

}
?>
</div>

<!-- Shutdown/Reboot options modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i data-feather="power"></i>&nbsp;Shutdown / Reboot RPi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
				<!---->
        <div id="currentState"></div>
				<form action="javascript:void(0);">
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
					<div class="form-group">
					  <label for="inputPassword2" class="sr-only">Password</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i data-feather="key"></i></div>
              </div>
              <input type="password" class="form-control" id="inputPassword2" placeholder="Password">
            </div>
          </div>
				  <div id="rmd"></div>
				</form>
      </div>
      <div class="modal-footer">
				<button class="btn btn-primary" onclick="authorize(document.getElementById('inputPassword2').value);">Confirm identity</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Restart modal -->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter2Title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">System is being restarted...</h5>
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
        <h5 class="modal-title" id="exampleModalLabel">Help & Docs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="custom-control custom-switch">
          <input type="checkbox" onchange="toggleDarkMode()" class="custom-control-input" id="dm">
          <label class="custom-control-label" for="dm">Dark Mode</label>
        </div>
        <hr>
				<div id="accordion">
				  <div class="card">
            <div class="card-header" id="headingOne">
              <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">About this version of RPi Dashboard</button>
              </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
              <div class="card-body">
              <h3><font class='text-success'>&#10003;</font> Version 0.4</h3>
              <ul><li>New authorization/login modal to secure dashboard</li><li><a href='https://github.com/femto-code/Rasberry-Pi-Dashboard/releases'>Stay updated here</a></li><li><i><a href="CHANGELOG.md">All changes</a></i></li></ul>
              <small>RPi Dashboard v0.4 (Aug 2020)</small>
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
        <h5 class="modal-title" id="staticBackdropLabel">Authentication</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class='alert alert-info' role='alert'>Please login to access server information!</div>
        <form onkeydown="return event.key != 'Enter';">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="myPsw">Password</span>
            </div>
            <input type="password" class="form-control" placeholder="" aria-label="Password" aria-describedby="myPsw" id="lpwd">
            <div class="invalid-feedback">Wrong password!</div>
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
		RPi Dashboard v0.4 <font class="text-muted">(Aug 2020)</font> <span id="dot">&middot;</span> <font id="notf" class="text-success">See the <a href="https://github.com/femto-code/Rasberry-Pi-Dashboard/releases">Github releases</a> for updates!</font>
		<hr style="margin-top: 0; margin-bottom: 0;">
		femto-code (<a href="javascript:send_supportmail()">Support</a>) &middot; <button class="btn btn-secondary" onclick="$('#exampleModal').modal('show');">?</button><br><font class="text-muted">&copy; 2018 - 2020</font>
	</div>
</footer>
<!-- End Footer -->

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/popper-1.16.0.min.js"></script>
<script src="js/bootstrap-4.5.0.min.js"></script>


<script>
tselect=1;
function authorize(pass) {
  console.log(pass);
  var e = document.getElementById("time"+tselect);
  if( (tselect==1) || (tselect==2) ){
    
    if(tselect==1){
      var time = parseInt(e.options[e.selectedIndex].value);
    }else{
      var time = parseInt(e.value);
    }
    
    if( (!Number.isInteger(time)) || (time < 1) ){
		  alert("Invalid time input!");
		  return false;
	  }
  }else if(tselect==3){
    var time = e.value;
  }
	
  console.log(time);
	
	var act=document.querySelector('input[name="pwrOptions"]:checked').value;
  if (pass.length == 0) { 
    document.getElementById("rmd").innerHTML = "<font class='text-danger'>Please enter a valid password!</font>";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        if(this.responseText.indexOf("true") > -1){
          document.getElementById("rmd").innerHTML = "<font class='text-success'>Authorization completed!</font>";
          var res=this.responseText.split("_");
          outputShutdown(res[1],act);
          $("#exampleModalCenter").modal("hide");
        }else if(this.responseText=="wrongCredentials"){
          document.getElementById("rmd").innerHTML = "<font class='text-danger'>Authorization failed!</font>";
        }else{
          document.getElementById("rmd").innerHTML = "<font class='text-danger'>Error!</font>";
        }
      }
    };
    xmlhttp.open("GET", "backend/serv.php?p=" + pass+"&a="+act+"&time="+time, true);
    xmlhttp.send();
  }
}
function checkShutdown() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if(this.responseText==""){
        document.getElementById("sys2").innerHTML="";
        shutdownCurrent=false;
        return;
      }
      shutdownCurrent=true;
      outputShutdown(this.responseText,"unknown");
    }
  };
  xmlhttp.open("GET", "backend/serv.php?checkShutdown", true);
  xmlhttp.send();
}

function cancelShutdown() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      if(this.responseText==""){
        console.log("Cancel response is empty");
        checkShutdown();
        return;
      }
    }
  };
  xmlhttp.open("GET", "backend/serv.php?cancelShutdown", true);
  xmlhttp.send();
}

var dobj={Mon: "Monday", Tue: "Tuesday", Wed: "Wednesday", Thu: "Thursday", Fri: "Friday", Sat: "Saturday", Sun: "Sunday"};
function outputShutdown(data,act) {
  var toParse=data.split(" CEST ")[0];
  var day=data.substring(0,3);
  var s = data.replace(day,dobj[day]);
  s=s.split(" ")[0]+" "+s.split(" ")[1]+" "+s.split(" ")[2]+", "+s.split(" ")[5]+" "+s.split(" ")[3];
  scheduled=Date.parse(s);
  d = new Date(scheduled);
  var restd = Math.floor((d.getTime() - Date.now()) / (1000 * 60 * 60 * 24));
  var resth = Math.floor((d.getTime() - Date.now()) / (1000 * 60 * 60)) % 24;
  var restm = Math.floor((d.getTime() - Date.now()) / (1000 * 60)) % 60;
  var str="";
  if(restd>0){
    str+=restd + " d ";
  }
  if(resth > 0){
    str+=resth + " h ";
  }
  if(restm>0){
    str += restm + " m";
  }
  console.log(str);
  var action = (act=="1") ? "shutdown" : "reboot";
  if(act=="unknown"){
    action="shutdown/reboot";
  }
  var c =toParse.split(" ");
  document.getElementById("sys2").innerHTML='<div class="alert alert-warning" role="alert"><button class="btn btn-sm btn-outline-danger" onclick="cancelShutdown()" style="float:right">Cancel</button>Planned to '+action+' at <kbd>'+c[3]+'</kbd> on <kbd>'+c[0]+', '+c[1]+' '+c[2]+'</kbd><br>Remaining time: <kbd>'+str+'</kbd><br></div>';
}

function shutdown(){
	clearInterval(updinterval);
	$('#exampleModalCenter').modal('hide');
	$('#exampleModalCenter2').modal('show');
	$('#sys1').html('System is being restarted...');
	$('#sys2').html('<div class="progress" style="height: 1px;"><div class="progress-bar p2" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div><br><button type="button" class="btn btn-primary b2" onclick="location.reload()" id="secbutton" disabled>Refresh dashboard</button>');

	i=5;
	ival=setInterval(function(){
	//console.log(i);
		if(i<=100){
			$('.p1').width(i+'%');
			$('.p1').html(i+'%');
			$('.p2').width(i+'%');
			i=i+5;
		}else{
			clearInterval(ival);
			//console.log("Interval cleared");
			$( ".b2" ).prop( "disabled", false );
			$(".b2"). css("background-color","green");
		}
	}, 3000);
}
function send_supportmail(){
	esubject="Support-Mail";
	// ebody is created in update function
	ebody = encodeURIComponent(ebody);
	estring="mailto:gitarrenflo@gmx.de?subject="+esubject+"&body="+ebody;
	location.href=estring;
}
</script>

<script src="js/Chart-2.9.3.min.js"></script>
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
Chart.defaults.global.legend.display = false;

function addData(chart, label, data) {
	chart.data.labels.push(label);
	chart.data.datasets.forEach((dataset) => {
		dataset.data.push(data);
	});
	chart.update();
}

function removeData(chart) {
  chart.data.labels.pop();
  chart.data.datasets.forEach((dataset) => {
    dataset.data.pop();
  });
  chart.update();
}

function updatedb(){
	$('.p3').width('100%');
	console.log("Live : Updating...");
	$.ajax({
		type: "GET",
		dataType: "json",
		cache: false,
		url: "backend/sys_infos.php",
		error : function(jqXHR, textStatus, errorThrown){
			console.error("Ajax error");
			console.error(jqXHR + " | " + textStatus + " | " + errorThrown);
		},
		success: function(result) {
      document.getElementById("sys11").innerHTML="";
      if(result.auth=="false"){
        if(timer==true){
          clearInterval(updinterval);
          timer=false;
          console.log("Timer gestoppt");
          $('#overallstate').html('<font class="text-muted"><i data-feather="loader"></i>&nbsp;Waiting for authentication ...</font>');
          feather.replace();
        }
        $('#staticBackdrop').modal('show');
        $("footer").addClass("fixed-bottom");
        return;
      }
      if(!timer) togglep(false);
			ebody = 'Loads: ' + result.load + '\r\n' + 'Timestamp: ' + result.timest + '\r\n' + 'Uptime: ' + result.uptime + '\r\n' + 'CPU Temperature: ' + result.cputemp + '\r\n' + 'CPU Frequency: ' + result.cpufreq + '\r\n' + 'RAM total: ' + (result.memavail + result.memunavail) + '\r\n' + 'RAM used: ' + result.memunavail + '\r\n' + 'RAM free: ' + result.memavail + '\r\n' + 'RAM perc: ' + result.memperc + '\r\n' + 'SWAP perc: ' + result.swapperc + '\r\n' + 'SWAP total: ' + result.swaptotal + '\r\n' + 'SWAP used: ' + result.swapused;
			warn=0;
			var x = document.getElementsByName("lastupdated");
			var i;
			for (i = 0; i < x.length; i++) {
				//if (x[i].type == "checkbox") {
				//	x[i].checked = true;
				//}
				x[i].innerHTML=result.timest;
				//console.log(x.length);
			}
			// Uptime
			document.getElementById("uptime").innerHTML=result.uptime;
			// CPU Temperature
			document.getElementById("temperature").innerHTML=result.cputemp;
			radialObj.animate(parseInt(result.cputemp));
			//console.log(parseInt(result.cputemp));
			if ( parseInt(result.cputemp) < warn_cpu_temp){
				document.getElementById("tempstate").innerHTML="<i data-feather='thermometer'></i>&nbsp;Temperature <font class='text-success'>(OK)</font>";
			}else{
        document.getElementById("tempstate").innerHTML="<i data-feather='thermometer'></i>&nbsp;Temperature <font class='text-warning'>(WARNING)</font>";
        addWarning("CPU Temperature","thermometer");
				warn++;
			}
			// CPU Frequency
			document.getElementById("frequency").innerHTML=result.cpufreq;
			// CPU Loads
			var str=result.load+'';
			var array=str.split(",");
			document.getElementById("m1").innerHTML=array[0];
			document.getElementById("m5").innerHTML=array[1];
			document.getElementById("m15").innerHTML=array[2];
			removeData(chart);
			removeData(chart);
			removeData(chart);
			addData(chart, "1 min", array[0]);
			addData(chart, "5 min", array[1]);
			addData(chart, "15 min", array[2]);
			if (array[0] >= warn_loads_size){
        document.getElementById("cput").innerHTML="CPU <font class='text-warning'>(WARNING)</font>";
        addWarning("CPU Loads","activity");
        warn++;
			}else{
				document.getElementById("cput").innerHTML="CPU <font class='text-success'>(OK)</font>";
			}
			// RAM
			document.getElementById("memused").innerHTML=result.memunavail;
			document.getElementById("memfree").innerHTML=result.memavail;
			document.getElementById("memtotal").innerHTML=parseInt(result.memunavail) + parseInt(result.memavail);
			document.getElementById("ram1").setAttribute("aria-valuenow", (100-result.memperc));
			document.getElementById("ram1").style.width = (100-result.memperc) + "%";
			document.getElementById("ram1").innerHTML = (100-result.memperc) + " %";
			document.getElementById("ram2").setAttribute("aria-valuenow", result.memperc);
			document.getElementById("ram2").style.width = result.memperc + "%";
			document.getElementById("ram2").innerHTML = result.memperc + " %";
			if (result.memperc >= warn_ram_space){
        document.getElementById("ramt").innerHTML='Memory <font class="text-warning">(WARNING)</font>';
        addWarning("Memory","cpu");
				warn++;
			}else{
				document.getElementById("ramt").innerHTML='Memory <font class="text-success">(OK)</font>';
			}
			// Swap
			document.getElementById("swapsys").innerHTML="Swap: <b>"+result.swapperc+"</b> % ("+result.swapused+" MB of "+result.swaptotal+" MB)";
			// Overall
			if (warn > 0){
        var s = (warn>1) ? "s" : "";
				document.getElementById("overallstate").innerHTML="<font class='text-danger'><i data-feather='alert-circle'></i>&nbsp;"+warn+" problem"+s+" occured</font>";
				warnuser(warn);
			}else{
				document.getElementById("overallstate").innerHTML="<font class='text-success'><i data-feather='check-circle'></i>&nbsp;System runs normally</font>";
			}
			feather.replace()
		}
	});
}
// Live-Static-change
timer=false;
y=0;
function togglep(force){
	console.log("Togglep() - Timer: "+timer);
	if(timer == false){
		if(force){ y=100; }
		updinterval=setInterval(function(){
			if(y<100){
				$('.py').width(y+'%');
				y+=10;
			}else{
				$('.py').width(y+'%');
				updatedb();
				y=0;
			}
		}, (((upd_time_interval*1000)-1.5)/10));
		timer=true;
		console.log("Timer started");
		$('#overallstate').html('<font class="text-info"><i data-feather="chevrons-right"></i>&nbsp;Will be updated ...</font>');
    feather.replace();
		return '<i data-feather="pause"></i>';
	}else{
		clearInterval(updinterval);
		timer=false;
		console.log("Timer gestoppt");
		$('#overallstate').html('<font class="text-muted"><i data-feather="clock"></i>&nbsp;Live Update disabled</font>');
    feather.replace();
		return '<i data-feather="play"></i>';
	}
}
$('#staticBackdrop').on('hidden.bs.modal', function (e) {
  updatedb();
  $("footer").removeClass("fixed-bottom");
  $("#lpwd").prop("disabled","");
  $("#lbtn").prop("disabled","");
  $("#lpwd").val("").removeClass("is-valid is-invalid");
  $("#lbtn").html("Login").addClass("btn-primary").removeClass("btn-success");
});
function loginToServer(){
  var value=$("#lpwd").val();
  if(value.length==0){ return; }
  $("#lpwd").prop("disabled","true");
  $("#lbtn").prop("disabled","true");
  $("#lbtn").html("Checking...");
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      if(this.responseText=="correctCredentials"){
        $("#lpwd").addClass("is-valid").removeClass("is-invalid");
        $("#lbtn").html("Updating now...").addClass("btn-success").removeClass("btn-primary");
        setTimeout(() => {
          $(".row").removeClass("hidden");
          $("#ldiv").removeClass("hidden");
          $("#lock_section").html("<i style='width: 100px;height:100px;color:#aaa' data-feather='unlock'></i><br><font class='text-success'>You are authorized!<br><a href='javascript:location.reload()'>Reload</a> the page to load the full page content.</font>")
          $('#staticBackdrop').modal('hide');
        }, 1000);
      }else{
        $("#lpwd").prop("disabled","");
        $("#lpwd").addClass("is-invalid");
        $("#lbtn").html("Try again");
        $("#lbtn").prop("disabled","");
      }
    }
  };
  xmlhttp.open("POST", "backend/serv.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send("login=true&pw="+value);
}
$("#lpwd").keyup(function (event) {
  if (event.keyCode === 13) {
    event.preventDefault();
    loginToServer();
  }
});
$("#inputPassword2").keyup(function (event) {
  if (event.keyCode === 13) {
    event.preventDefault();
    authorize(document.getElementById('inputPassword2').value);
  }
});
</script>

<script src="js/radialIndicator-2.0.0.min.js"></script>
<script>
$('#indicatorContainer').radialIndicator({
	barColor: {
    0: '#dfffbf',
    45: '#00ff00',
    60: '#ff4000',
    70: '#cc0000',
		100: '#000'
  },
  initValue: 0,
  minValue: 0,
  maxValue: 100,
  format: '##°C'
});
var radialObj = $('#indicatorContainer').data('radialIndicator');
//now you can use instance to call different method on the radial progress.
//like
//radialObj.animate(25);


function warnuser(c) {
  var str=(c>1) ? "are" : "is";
  var str2=(c>1) ? "s" : "";
  mdtoast('<i data-feather="alert-circle"></i>&nbsp;There '+str+'&nbsp;<b>'+c+'</b>&nbsp;problem'+str2+', please check!', { type: 'error'});
  feather.replace();
}
function addWarning(problem, icon){
  document.getElementById("sys11").innerHTML+='<div class="bg-danger card text-white text-center shadow m-1" style="max-width: 18rem;"><h5 style="margin-top: .75rem;" class="card-title"><i data-feather="'+icon+'"></i>&nbsp;'+problem+'</h5></div>';
  feather.replace();
}

$('#exampleModalCenter').on('shown.bs.modal', function (e) {
  checkShutdown();
  if(shutdownCurrent){
    document.getElementById("currentState").innerHTML='<div class="alert alert-danger" role="alert"><i data-feather="alert-circle"></i>&nbsp;Existing shutdown will be overwritten.&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="cancelShutdown();$(\'#exampleModalCenter\').modal(\'hide\');">Remove</button></div>';
  }else{
    document.getElementById("currentState").innerHTML='<div class="alert alert-success" role="alert"><i data-feather="check-circle"></i>&nbsp;Currently there is no other power event planned.</div>';
  }
  feather.replace();
});

function toggleDarkMode() {
  var state = $("#dm").prop("checked");
  if(state){
    $("#dmcss").prop("disabled", false);
  }else{
    $("#dmcss").prop("disabled", true);
  }
  localStorage.setItem("darkmode", state);
}
$("#dmcss").prop("disabled", (localStorage.getItem("darkmode") == 'false' || localStorage.getItem("darkmode") == null));
if( localStorage.getItem("darkmode") == 'false' || localStorage.getItem("darkmode") == null ){
  $("#webinfo").removeClass("text-muted");
  $("#kernel").removeClass("text-muted");
}else{
  $("#webinfo").addClass("text-muted");
  $("#kernel").addClass("text-muted");
}
$("#dm").prop("checked", (localStorage.getItem("darkmode") == 'true'));
</script>
<script src="js/feather.min.js"></script>
<script>feather.replace()</script>
<script src="js/mdtoast.min.js"></script>

</body>
</html>
