<?php
session_start();
error_reporting (E_ALL);
ini_set ('display_errors', 'On');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require "backend/Config.php";
$config = new Config;
$config->load("local.config", "defaults.php");

$loginWithPassword = $config->get("general.loginWithPassword");

if($loginWithPassword) {
    $auth = (isset($_SESSION["rpidbauth"])) ? true : false;
}
else {
    $auth = true;
    $_SESSION["rpidbauth"]=time();
}

if(!isset($_SESSION["setup"])){
  if( ($config->get("general.initialsetup")=="0") || ($config->get("general.initialsetup")=="") ){
    header("Location: setup.php");
  }
}

$path=$_SERVER['SCRIPT_FILENAME'];
$fol=substr($path, 0, -9);

$passVal = ($config->get("general.pass")!=='63a9f0ea7bb98050796b649e85481845') ? "***notdefault***" : '';

$string = trim(preg_replace('/\s\s+/', '', shell_exec("hostname")));
?>
<!doctype html>
<html lang="en">
<head>
<script src="js/color-modes.js"></script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="apple-touch-icon" sizes="180x180" href="rpidb_ico/apple-touch-icon.png?v=PYAg5Ko93z">
<link rel="icon" type="image/png" sizes="32x32" href="rpidb_ico/favicon-32x32.png?v=PYAg5Ko93z">
<link rel="icon" type="image/png" sizes="16x16" href="rpidb_ico/favicon-16x16.png?v=PYAg5Ko93z">
<link rel="manifest" href="rpidb_ico/site.webmanifest?v=PYAg5Ko93z">
<link rel="mask-icon" href="rpidb_ico/safari-pinned-tab.svg?v=PYAg5Ko93z" color="#b91d47">
<link rel="shortcut icon" href="rpidb_ico/favicon.ico?v=PYAg5Ko93z">
<meta name="apple-mobile-web-app-title" content="Raspberry Pi Dashboard">
<meta name="application-name" content="Raspberry Pi Dashboard">
<meta name="msapplication-TileColor" content="#b91d47">
<meta name="msapplication-TileImage" content="rpidb_ico/mstile-144x144.png?v=PYAg5Ko93z">
<meta name="msapplication-config" content="rpidb_ico/browserconfig.xml?v=PYAg5Ko93z">
<meta name="theme-color" content="#b91d47">

<link rel="stylesheet" href="css/bootstrap-5.3.2.min.css">
<link rel="stylesheet" href="css/bootstrap-icons-1.11.1.css">
<link rel="stylesheet" href="css/mdtoast.min.css?v=2.0.2">

<title><?php system("hostname");?> - Loading...</title>

<style>
/* rubik-300 - latin */
@font-face {
  font-family: 'Rubik';
  font-style: normal;
  font-weight: 300;
  src: url('fonts/rubik-v12-latin-300.eot'); /* IE9 Compat Modes */
  src: local(''),
    url('fonts/rubik-v12-latin-300.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
    url('fonts/rubik-v12-latin-300.woff2') format('woff2'), /* Super Modern Browsers */
    url('fonts/rubik-v12-latin-300.woff') format('woff'), /* Modern Browsers */
    url('fonts/rubik-v12-latin-300.ttf') format('truetype'), /* Safari, Android, iOS */
    url('fonts/rubik-v12-latin-300.svg#Rubik') format('svg'); /* Legacy iOS */
}
body, .mdtoast{
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
<style>
.bd-placeholder-img {
  font-size: 1.125rem;
  text-anchor: middle;
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
}

@media (min-width: 768px) {
  .bd-placeholder-img-lg {
    font-size: 3.5rem;
  }
}

.b-example-divider {
  width: 100%;
  height: 3rem;
  background-color: rgba(0, 0, 0, .1);
  border: solid rgba(0, 0, 0, .15);
  border-width: 1px 0;
  box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
}

.b-example-vr {
  flex-shrink: 0;
  width: 1.5rem;
  height: 100vh;
}

.bi {
  /* vertical-align: -.125em; */
  fill: currentColor;
}

.nav-scroller {
  position: relative;
  z-index: 2;
  height: 2.75rem;
  overflow-y: hidden;
}

.nav-scroller .nav {
  display: flex;
  flex-wrap: nowrap;
  padding-bottom: 1rem;
  margin-top: -1px;
  overflow-x: auto;
  text-align: center;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
}

.btn-bd-primary {
  --bd-violet-bg: #712cf9;
  --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

  --bs-btn-font-weight: 600;
  --bs-btn-color: var(--bs-white);
  --bs-btn-bg: var(--bd-violet-bg);
  --bs-btn-border-color: var(--bd-violet-bg);
  --bs-btn-hover-color: var(--bs-white);
  --bs-btn-hover-bg: #6528e0;
  --bs-btn-hover-border-color: #6528e0;
  --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
  --bs-btn-active-color: var(--bs-btn-hover-color);
  --bs-btn-active-bg: #5a23c8;
  --bs-btn-active-border-color: #5a23c8;
}

.bd-mode-toggle {
  z-index: 1500;
}

.bd-mode-toggle .dropdown-menu .active .bi {
  display: block !important;
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

  $permissionerr=false;
  $spannung=substr(exec("vcgencmd measure_volts core"),5);
  if( (strpos($spannung,"failed")!==false) || (strlen($spannung)<2) ){
    $spannung=$spannung."<div class='alert alert-danger' role='alert'>Reading of core voltage failed. Please run<br><kbd>sudo usermod -aG video www-data</kbd><br>in a terminal to solve this problem.&nbsp;<a href='https://github.com/femto-code/Raspberry-Pi-Dashboard#core-voltage-or-other-hardware-info-output-is-not-shown-optional' target='blank'><i class='bi bi-question-circle'></i>&nbsp;Help</a></div>";
    $permissionerr=true;
  }
}
?>

</head>
<body onload="preload()">
<noscript style="z-index: 99999!important; position: absolute; top: 0; width: 98%; padding: 3%;"><div class="alert alert-danger" role="alert">Raspberry Pi Dashboard Web Application <b>requires</b> JavaScript to be enabled in order to work properly - enable it to continue!</div></noscript>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check2" viewBox="0 0 16 16">
    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"></path>
  </symbol>
  <symbol id="circle-half" viewBox="0 0 16 16">
    <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"></path>
  </symbol>
  <symbol id="moon-stars-fill" viewBox="0 0 16 16">
    <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"></path>
    <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"></path>
  </symbol>
  <symbol id="sun-fill" viewBox="0 0 16 16">
    <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>
  </symbol>
</svg>
<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
  <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
    <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
    <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text" style="">
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#sun-fill"></use></svg>
        Light
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
        Dark
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#circle-half"></use></svg>
        Auto
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
      </button>
    </li>
  </ul>
</div>

<div class="preload-screen"></div>

<div class="container">
  <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
    <div class="col-md-3 mb-2 mb-md-0">
      <a href="./" class="d-inline-flex link-body-emphasis text-decoration-none" style="line-height: 32px;">
        <img src="img/official_logo.svg" width="30" height="30" class="d-inline-block align-top me-1" alt="RPi Logo">
        Raspberry Pi Dashboard
      </a>
    </div>

    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
      <!-- <li><a href="#" class="nav-link px-2 link-secondary">Home</a></li>
      <li><a href="#" class="nav-link px-2">Features</a></li>
      <li><a href="#" class="nav-link px-2">Pricing</a></li>
      <li><a href="#" class="nav-link px-2">FAQs</a></li>
      <li><a href="#" class="nav-link px-2">About</a></li> -->
      <p style="line-height:15px;margin-bottom:0px"><b>Hostname:</b> <?php system("hostname");?> &#183; <b>Internal IP:</b> <?php echo $_SERVER["SERVER_ADDR"];?><br>
    <b>Access from:</b> <?php echo $_SERVER["REMOTE_ADDR"];?> &#183; <b>Port:</b> <?php echo $_SERVER['SERVER_PORT']; ?></p>
    </ul>

    <div class="col-md-3 text-end">
      <button class="btn btn-outline-secondary mb-2" style="margin-bottom: 1px!important;" onclick="$('#exampleModal').modal('show');"><i class="bi bi-gear"></i>&nbsp;Options</button>
    </div>
  </header>
</div>

<div class="container">
  <div class="row<?php if(!$auth){ echo " hidden"; } ?>">
    <div class="col-sm-8 pt-1 pt-md-0">
      <div class="card shadow-sm">
        <div class="card-header border-primary text-primary"><i class="bi bi-info-circle"></i>&nbsp;Overview</div>
        <div class="card-body">
          <h5 id="sys1" class="card-title"><span id="overallstate"></span></h5>
          <p id="sys11" class="card-text"></p>
          <?php
          if(isset($_SESSION["setup"])){
          ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert"><i class="bi bi-info-circle"></i>&nbsp;Setup finished! RPi Dashboard is ready.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
          <?php
          unset($_SESSION["setup"]);
          }
          ?>

          <p id="sys2" class="card-text"></p>
          <hr>
          <p><i class="bi bi-clock-history"></i><!--<img src="img/time-icon.png">-->&nbsp;Uptime: <b><span id="uptime"></span></b><?php if($auth){ ?>&nbsp;(started <?=$uptstr;?>)<?php } ?></p>
          <table style="width:100%"><tbody><tr><td style="width:10%"><button type="button" id="pctl" onclick="y=100; this.innerHTML=togglep(true);" class="btn btn-secondary btn-sm"><i class="bi bi-pause"></i></button></td><td style="width:90%">
          <div class="progress" style="margin-top: 1px; height: 2px;"><div class="progress-bar py" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></td></tr></tbody></table>
          <p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-4 pt-1 pt-md-0">
      <div class="card shadow-sm">
        <div class="card-header border-primary text-primary"><i class="bi bi-command"></i>&nbsp;System</div>
        <div class="card-body">
          <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" class="btn btn-outline-primary mt-1"><i class="bi bi-power"></i>&nbsp;Power</button>&nbsp;
          <?php 
            if($loginWithPassword) {
          ?>
          <button type="button" onclick="logout()" class="btn btn-outline-warning mt-1"><i class="bi bi-arrow-right-square"></i>&nbsp;Logout</button>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row pt-3<?php if(!$auth){ echo " hidden"; } ?>">
    <div class="col-12 col-sm-6 col-md-5 pt-1 pt-md-0">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cpu"></i>&nbsp;<span id="cput"></span></h5>
          <p class="card-text"><canvas id="myChart"></canvas>1 min: <b><span id="m1"></span></b> &#183; 5 min: <b><span id="m5"></span></b> &#183; 15 min: <b><span id="m15"></span></b><br>CPU clock: <b><span id="frequency"></span> MHz</b></p>
          <p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 pt-1 pt-md-0">
      <div class="card text-center shadow-sm">
        <div class="card-body">
        <h5 id="tempstate" class="card-title"></h5>
        <div id="indicatorContainer"></div><!--CPU-Indicator-->
        <p class="card-text"><b><span style="font-size: 20px" id="temperature"></span></b></p>
        <p class="card-text"><small class="text-muted">Updated <span name="lastupdated">now</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 pt-1 pt-md-0">
      <div class="card text-center shadow-sm">
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
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-hdd-rack"></i>&nbsp;Hardware</h5>
          <?php print "<pre>"; echo shell_exec("lsusb"); print "</pre>"; ?>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
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
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-hdd"></i>&nbsp;Disk Space</h5>
          <p class="card-text"><canvas height="100" class="doughnut-chart-container" id="space"></canvas>Total: <b><?php echo $ds_rund;?> GB</b> &#183; Free: <b><?php echo $df_rund;?> GB</b> &#183; Used: <b><?php echo round($ds-$df,2);?> GB</b></p>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
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
    <div class="card text-center">
      <div class="card-header">Kernel</div>
      <div class="card-body">
        <p class="card-text" id="kernel"><?php echo php_uname(); ?></p>
        <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
      </div>
    </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
        <div class="card-header">Model</div>
        <div class="card-body">
          <samp><?php echo exec("cat /sys/firmware/devicetree/base/model");?></samp>
          <?php $ot=shell_exec("vcgencmd version");if($permissionerr){echo "<div class='alert alert-danger' role='alert'>Execution of system command failed. Please run<br><kbd>sudo usermod -aG video www-data</kbd><br>in a terminal to solve this problem.&nbsp;<a href='https://github.com/femto-code/Raspberry-Pi-Dashboard#core-voltage-or-other-hardware-info-output-is-not-shown-optional' target='blank'><i class='bi bi-question-circle'></i>&nbsp;Help</a></div>";}else{echo '<samp>'.$ot.'</samp>';}?>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
  </div>
  <div class="row pt-3">
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
        <div class="card-header">Partitions / Storage</div>
        <div class="card-body">
          <?php print "<pre style='text-align: left!important;'>"; echo shell_exec("df -h | grep -v tmp"); print "</pre>"; ?>
          <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
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
    <div class="card text-center">
      <div class="card-header">Hostnamectl</div>
      <div class="card-body">
        <?php print "<pre style='text-align: left!important;'>"; echo shell_exec("hostnamectl"); print "</pre>"; ?>
        <p class="card-text"><small class="text-muted">Updated <span><?php echo date("H:i:s");?> (at page load)</span></small></p>
      </div>
    </div>
    </div>
    <div class="col-sm-6 pt-1 pt-md-0">
      <div class="card text-center">
        <div class="card-header">Processor</div>
        <div class="card-body">
          <?php print "<pre style='height: 250px;'>"; echo shell_exec("lscpu"); print "</pre>"; ?>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
              <button type="button" class="nav-item nav-link active" id="nav-home-presets" data-bs-toggle="tab" data-bs-target="#nav-presets" role="tab" aria-controls="nav-presets" aria-selected="true">Presets</button>
              <button type="button" class="nav-item nav-link" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" role="tab" aria-controls="nav-home" aria-selected="false">Delay</button>
              <button type="button" class="nav-item nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Exact</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent" style="padding:10px">
            <div class="tab-pane fade show active" id="nav-presets" role="tabpanel" aria-labelledby="nav-home-presets" tabindex="0">
              <select class="form-select my-1 mr-sm-2" id="time1" onchange="tselect=1">
                <option selected value="1">now (= 1 min)</option>
                <option value="5">in 5 min</option>
                <option value="15">in 15 min</option>
                <option value="30">in 30 min</option>
              </select>
            </div>
            <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
              <label for="time2">Delay by <b><span id="rinp">?</span></b> min</label>
              <input type="range" id="time2" class="custom-range" min="1" max="60" onchange="document.getElementById('rinp').innerHTML=this.value;tselect=2">
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
              <input id="time3" class="form-control" type="time" placeholder="Select exact time" onchange="tselect=3">
            </div>
          </div>
          <hr>
          <div id="pwrauth" class="form-group">
            <div class="input-group">
              <span class="input-group-text" id="myPsw2"><i class="bi bi-key"></i></span>
              <input type="password" id="inputPassword2" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="myPsw2">
              <div class="invalid-feedback">Invalid password!</div>
            </div>
          </div>
          <div id="pwrCheck" class='alert alert-info' role='alert'><i class='bi bi-chevron-double-right'></i>&nbsp;Checking authorization ...</div>
          <div id="pwrCheck2" class="hidden alert alert-success" role="alert"><i class="bi bi-check2-circle"></i>&nbsp;Authenticated</div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="confbtn" class="btn btn-primary" onclick="authorize();">Confirm identity</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-gear"></i>&nbsp;Options & About</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <h4 class="mb-0">Appearance</h4>
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" id="tempunit">
          <label class="custom-control-label" for="tempunit">Show Fahrenheit (°F) values</label>
        </div>
        <h4 class="mb-0">Threshold values</h4>
        <small class="text-muted">Throwing a warning (permanently saved)</small>
        <form id="settingsForm">
          <div class="form-row">
            <div class="col">
              <input type="number" id="warn_cpu_temp" class="form-control" placeholder="default: 65" aria-describedby="critCpuTempHelp" min="20" max="80" value="<?=$config->modified("thresholds.warn_cpu_temp")?>">
              <small id="critCpuTempHelp" class="form-text text-muted">CPU Temperature (°C) - default: 65°C</small>
            </div>
            <div class="col">
              <input type="number" id="warn_ram_space" class="form-control" placeholder="default: 80" aria-describedby="critRamSizeHelp" min="0" max="100" value="<?=$config->modified("thresholds.warn_ram_space")?>">
              <small id="critRamSizeHelp" class="form-text text-muted">RAM Load (%) - default: 80%</small>
            </div>
          </div>
          <div class="form-row">
            <div class="col-6">
              <input type="number" id="warn_loads_size" class="form-control" placeholder="default: 2" aria-describedby="critCpuLoadHelp" min="1" max="4" value="<?=$config->modified("thresholds.warn_loads_size")?>">
              <small id="critCpuLoadHelp" class="form-text text-muted">CPU workload (last min) - default: 2</small>
            </div>
          </div>
          <div class="form-row mb-2">
            <label for="upd_time_interval" class="col-sm-6 col-form-label">Refresh rate (sec)</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" placeholder="default: 15" id="upd_time_interval" aria-describedby="dbRefreshHelp" min="5" max="600" value="<?=$config->modified("thresholds.upd_time_interval")?>">
            </div>
            <small id="dbRefreshHelp" class="col form-text text-muted">Refresh interval of live data update section (recommended: 10 - 60 sec) - default: 15</small>
          </div>
          <h4 class="mb-0">Authentication</h4>
          <div class="form-row mb-2">
            <div class="col">
              <input type="password" id="pass" class="form-control" placeholder="default: root" aria-describedby="passHelp" value="<?=$passVal;?>">
              <small id="passHelp" class="form-text text-muted">Password - default: root</small>
            </div>
            <div class="col">
              <input type="password" id="pass2" class="form-control" placeholder="please repeat" aria-describedby="pass2Help" value="<?=$passVal;?>">
              <small id="pass2Help" class="form-text text-muted">Repeat password</small>
              <div class="invalid-feedback">Repeated password not correct!</div>
            </div>
          </div>
          <button type="button" id="applyBtn" class="btn btn-outline-success">Apply</button>
          <button type="button" id="discardBtn" class="btn btn-outline-secondary">Discard changes</button>
          <button type="button" id="defaultsBtn" class="btn btn-outline-primary">Defaults</button>
          <div id="sformFeedback"></div>
        </form>

        <hr />
        <div class="accordion" id="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                About this version of RPi Dashboard
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <h3><span class='text-success'>&#10003;</span> Version 1.1.2</h3>
                <ul><li>live page title with hostname + status of monitored RPi</li><li>overhauled project documentation / readme</li><li><a href='https://github.com/femto-code/Rasberry-Pi-Dashboard/releases'>Stay updated here</a></li><li><i><a href="CHANGELOG.md">All changes</a></i></li></ul>
                <small>most important changes since RPi Dashboard v1.0.1</small>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Customization
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <p>Your local dashboard project instance is located under: <code><?=$fol;?></code>. Look for a file called <kbd>local.config</kbd>. Within this config file you can customize a few things of RPi Dashboard (e.g. thresholds, password).</p><p>See the notes in <kbd>README.md</kbd> for instructions.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                What does 'CPU Load' mean?
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <i>Explanation of CPU Loads in Linux at <a href="http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages">http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages</a> (recommended article)</i><br>In case of Raspberry Pi a value of about 3~4 is critical, i.e. all kernel cores are busy.
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel"><i class="bi bi-shield-lock"></i>&nbsp;Authentication</h5>
      </div>
      <div class="modal-body">
        <div class='alert alert-info' role='alert'>Please enter password to access dashboard!</div>
        <form onkeydown="return event.key != 'Enter';">
          <div class="input-group">
            <span class="input-group-text" id="myPsw"><i class="bi bi-key"></i></span>
            <input type="password" id="lpwd" class="form-control" placeholder="" aria-label="Password" aria-describedby="myPsw" autofocus>
            <div class="invalid-feedback">Invalid password!</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-block btn-primary" onclick="loginToServer()" id="lbtn">Login</button>
      </div>
    </div>
  </div>
</div>

<!--End Modal(s)-->

<!-- Footer -->
<footer style="line-height: 40px; margin-top: 10px;" class="border-top py-1">
  <div class="container text-center">
    RPi Dashboard v1.1.3 <span class="text-muted">(Sept 2023)</span> <span id="dot">&middot;</span> <span id="notf" class="text-success">See the <a href="https://github.com/femto-code/Rasberry-Pi-Dashboard/releases">Github releases</a> for updates!</span><br />
    <button class="btn btn-secondary mb-2" onclick="$('#exampleModal').modal('show');"><i class="bi bi-gear"></i>&nbsp;Options</button>
    <hr style="margin-top: 0; margin-bottom: 0;">
    femto-code&nbsp;<a href="https://github.com/femto-code"><i class="bi bi-github"></i></a> &middot; <span class="text-muted">2018 - 2023</span>
  </div>
</footer>
<!-- End Footer -->

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap-5.3.2.bundle.min.js"></script>
<script src="js/Chart-2.9.3.min.js"></script>
<script src="js/mdtoast.min.js?v=2.0.2"></script>
<script src="js/radialIndicator-2.0.0.min.js"></script>

<script>
warn_cpu_temp = <?=$config->get("thresholds.warn_cpu_temp")?>;
warn_ram_space = <?=$config->get("thresholds.warn_ram_space")?>;
upd_time_interval = <?=$config->get("thresholds.upd_time_interval")?>;
warn_loads_size = <?=$config->get("thresholds.warn_loads_size")?>;
temp_unit = <?=$config->get("general.tempunit")?>;
(function() {
   // your page initialization code here
   // the DOM will be available here
  document.getElementById("tempunit").checked = temp_unit;
})();

console.log("Temp unit setting: ", temp_unit);
var settingsKeys=["warn_cpu_temp", "warn_ram_space", "warn_loads_size", "upd_time_interval", "pass", "tempunit"];
console.log("Custom user options: warncputemp="+warn_cpu_temp+" | warn_ram_space="+warn_ram_space+" | upd_time_interval="+upd_time_interval+" | warn_loads_size="+warn_loads_size);
var hostname = <?="'".$string."'";?>;
$('.modal').on('shown.bs.modal', function() {
  $(this).find('[autofocus]').focus();
});
</script>

<script src="js/main.js?v=1.1.0"></script>

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
      //easing: 'easeInExpo'
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
      borderWidth: 1,
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
