<?php
header('Access-Control-Allow-Origin: *');
error_reporting (E_ALL);
ini_set ('display_errors','On');

session_start();

require "backend/Config.php";
$config = new Config;
$config->load("local.config", "defaults.php");

if(isset($_POST["complete"])){

  if($config->get("general.initialsetup")=="0"){

    if(isset($_POST["pw"])){
      $val=$_POST["pw"];
    }else{
      echo "Setup error - no password specified. Please create an issue <a href='https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/new' target='blank'>here</a>.";
      exit();
    }
    $edit=array('general' => array ());
    $edit["general"]["pass"]=md5($val);
    $edit["general"]["initialsetup"]="1";
    //print_r($edit);

  }else{
    $edit=$config->userconf;
  }
  

  $existing=$config->userconf;

  $combined=array_replace_recursive($existing, $edit);
  echo $config->save($combined);
  $_SESSION["setup"]="justfinished";
  exit();
}

shell_exec("sudo /sbin/shutdown -r +99999");
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
$return=getShutdownEventsInfo();
// print_r($return);
if($return["date"]=="0"){
  $permi2='<span class="text-danger"><i class="bi bi-x-circle"></i>&nbsp;Failed!</span>';
  $pclass2="danger";
}else{
  system('sudo /sbin/shutdown -c');
  $permi2='<span class="text-success"><i class="bi bi-check-circle"></i>&nbsp;Passed!</span>';
  $pclass2="success";
}

$permtest=shell_exec("vcgencmd measure_volts core");
if(strlen($permtest)<2){
  $permi1='<span class="text-danger"><i class="bi bi-x-circle"></i>&nbsp;Failed!</span>';
  $pclass1="danger";
}else{
  $permi1='<span class="text-success"><i class="bi bi-check-circle"></i>&nbsp;Passed!</span>';
  $pclass1="success";
}

function isFileWritable($path){
  $writable_file = (file_exists($path) && is_writable($path));
  $writable_directory = (!file_exists($path) && is_writable(dirname($path)));

  if ($writable_file || $writable_directory) {
      return true;
  }
  return false;
}
$perm0test=isFileWritable(__DIR__."/local.config");
if($perm0test){
  $permi0='<span class="text-success"><i class="bi bi-check-circle"></i>&nbsp;Passed!</span>';
  $pclass0="success";
  $p0help="It seems, that permissions are set correctly!";
}else{
  $permi0='<span class="text-danger"><i class="bi bi-x-circle"></i>&nbsp;Failed!</span>';
  $pclass0="danger";
  $p0help="Click for help on setting correct permissions!";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>RPi Dashboard Setup</title>

<link rel="stylesheet" href="css/bootstrap-5.3.2.min.css">
<link rel="stylesheet" href="css/bootstrap-icons-1.11.1.css">

<style>

.multisteps-form__progress {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
}

.multisteps-form__progress-btn {
  transition-property: all;
  transition-duration: 0.15s;
  transition-timing-function: linear;
  transition-delay: 0s;
  position: relative;
  padding-top: 20px;
  color: rgba(108, 117, 125, 0.7);
  text-indent: -9999px;
  border: none;
  background-color: transparent;
  outline: none !important;
  cursor: pointer;
}

@media (min-width: 500px) {
  .multisteps-form__progress-btn {
    text-indent: 0;
  }
}

.multisteps-form__progress-btn:before {
  position: absolute;
  top: 0;
  left: 50%;
  display: block;
  width: 13px;
  height: 13px;
  content: '';
  -webkit-transform: translateX(-50%);
          transform: translateX(-50%);
  transition: all 0.15s linear 0s, -webkit-transform 0.15s cubic-bezier(0.05, 1.09, 0.16, 1.4) 0s;
  transition: all 0.15s linear 0s, transform 0.15s cubic-bezier(0.05, 1.09, 0.16, 1.4) 0s;
  transition: all 0.15s linear 0s, transform 0.15s cubic-bezier(0.05, 1.09, 0.16, 1.4) 0s, -webkit-transform 0.15s cubic-bezier(0.05, 1.09, 0.16, 1.4) 0s;
  border: 2px solid currentColor;
  border-radius: 50%;
  background-color: #fff;
  box-sizing: border-box;
  z-index: 3;
}

.multisteps-form__progress-btn:after {
  position: absolute;
  top: 5px;
  left: calc(-50% - 13px / 2);
  transition-property: all;
  transition-duration: 0.15s;
  transition-timing-function: linear;
  transition-delay: 0s;
  display: block;
  width: 100%;
  height: 2px;
  content: '';
  background-color: currentColor;
  z-index: 1;
}

.multisteps-form__progress-btn:first-child:after {
  display: none;
}

.multisteps-form__progress-btn.js-active {
  color: #007bff;
}

.multisteps-form__progress-btn.js-active:before {
  -webkit-transform: translateX(-50%) scale(1.2);
          transform: translateX(-50%) scale(1.2);
  background-color: currentColor;
}

.multisteps-form__form {
  position: relative;
}

.multisteps-form__panel {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 0;
  opacity: 0;
  visibility: hidden;
}

.multisteps-form__panel.js-active {
  height: auto;
  opacity: 1;
  visibility: visible;
}
/* Define your own CSS3 animations in the CSS. */

.multisteps-form__panel[data-animation="scaleIn"] {
  -webkit-transform: scale(0.9);
          transform: scale(0.9);
}

.multisteps-form__panel[data-animation="scaleIn"].js-active {
  transition-property: all;
  transition-duration: 0.2s;
  transition-timing-function: linear;
  transition-delay: 0s;
  -webkit-transform: scale(1);
          transform: scale(1);
}

.multisteps-form__panel[data-animation=scaleOut]{
  -webkit-transform:scale(1.1);
  transform:scale(1.1);
}
.multisteps-form__panel[data-animation=scaleOut].js-active{
  transition-property:all;
  transition-duration:.2s;
  transition-timing-function:linear;
  transition-delay:0s;
  -webkit-transform:scale(1);
  transform:scale(1);
}
.multisteps-form__panel[data-animation=slideHorz]{
  left:50px;
}
.multisteps-form__panel[data-animation=slideHorz].js-active{
  transition-property:all;
  transition-duration:.25s;
  transition-timing-function:cubic-bezier(.2,1.13,.38,1.43);
  transition-delay:0s;left:0;
}
.multisteps-form__panel[data-animation=slideVert]{
  top:30px;
}
.multisteps-form__panel[data-animation=slideVert].js-active{
  transition-property:all;
  transition-duration:.2s;
  transition-timing-function:linear;
  transition-delay:0s;top:0;
}
.multisteps-form__panel[data-animation=fadeIn].js-active{
  transition-property:all;
  transition-duration:.3s;
  transition-timing-function:linear;
  transition-delay:0s;
}



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

<script src="js/color-modes.js"></script>

</head>
<body>

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

<div class="container">

  <div style="margin:auto;margin-top: 30px" class="text-center">
    <img class="mb-4" src="img/official_logo.svg" alt="" width="72" height="72">
    <h1 class="headline h3 mb-2 font-weight-normal">Welcome!</h1>
    <h3 class="headline h5 mb-3 font-weight-light">Raspberry Pi Dashboard</h3>
    <h5 class="headline text-secondary font-weight-normal">Setup</h5>
  </div>


  <form class="pick-animation my-4" style="display:none">
    <div class="form-row">
      <div class="col-5 m-auto">
        <select class="pick-animation__select form-control">
          <option value="scaleIn" selected="selected">ScaleIn</option>
          <option value="scaleOut">ScaleOut</option>
          <option value="slideHorz">SlideHorz</option>
          <option value="slideVert">SlideVert</option>
          <option value="fadeIn">FadeIn</option>
        </select>
      </div>
    </div>
  </form>

  <div class="multisteps-form">
    <!--progress bar-->
    <div class="row">
      <div class="col-12 col-lg-8 m-auto mb-4">
        <div class="multisteps-form__progress">
          <button class="multisteps-form__progress-btn js-active" type="button" title="Order Info">Appearance</button>
          <button class="multisteps-form__progress-btn" type="button" title="User Info" id="authbtn">Authorization</button>
          <button class="multisteps-form__progress-btn" type="button" title="Comments">Permissions</button>
        </div>
      </div>
    </div>
    <!--form panels-->
    <div class="row">
      <div class="col-12 col-lg-8 m-auto">
        <form class="multisteps-form__form" name="myForm">
          <!--single form panel-->
          <div class="multisteps-form__panel shadow p-4 rounded js-active" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Appearance</h3>
            <div class="multisteps-form__content pt-2">

              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="light" name="myRadios" class="custom-control-input" checked data-bs-theme-value="light">
                <label class="custom-control-label" for="false">Light Theme</label>
              </div>
              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="dark" name="myRadios" class="custom-control-input" data-bs-theme-value="dark">
                <label class="custom-control-label" for="true">Dark Theme</label>
              </div>
              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="auto" name="myRadios" class="custom-control-input" data-bs-theme-value="auto">
                <label class="custom-control-label" for="auto">Adjust to (local) system settings</label>
              </div>

              <div class="alert alert-info mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;Theme settings can always be adjusted later.</div>

              <div class="row">
                <div class="button-row d-flex mt-4 col-12">
                  <button class="btn btn-primary ms-auto js-btn-next" type="button" title="Next">Next&nbsp;<i class="bi bi-chevron-double-right"></i></button>
                </div>
              </div>
            </div>
          </div>
          <!--single form panel-->
          <div class="multisteps-form__panel shadow p-4 rounded" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Authorization</h3>
            <div class="multisteps-form__content">

              <?php
              if($config->get("general.initialsetup")=="0"){

              ?>

              <div class="form-row mt-4">
                <div class="col-12 col-sm-6">
                  <label for="pwinput1">Password</label>
                  <input class="multisteps-form__input form-control" type="password" id="pwinput1" />
                </div>
                <div class="col-12 col-sm-6 mt-4 mt-sm-0">
                  <label for="pwinput2">Password (repeat)</label>
                  <input class="multisteps-form__input form-control" type="password" id="pwinput2" onkeyup="checkPw()" />
                  <div class="invalid-feedback">Passwords are not equal!</div>
                </div>
              </div>

              <?php
              }else{
              ?>

              <div class="alert alert-danger mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;Custom password was already set (by you) using this setup. This form is now disabled for security reasons. Log-in to Dashboard first and open settings to change it.<hr>
              <p class="mb-0"><strong>Don't have access to it / forgot password?</strong><br>Alternatively, change your password manually by editing your <kbd>local.config</kbd> file.<br><a href="https://github.com/femto-code/Raspberry-Pi-Dashboard#configure-password-manually" target="blank" class="alert-link"><i class="bi bi-question-circle"></i>&nbsp;Help / Instructions</a></p></div>

              <?php
              }

              ?>

              <div class="alert alert-warning mt-3" role="alert">
                <h4 class="alert-heading">Please note</h4>
                
                Please keep in mind, that this password authentication <b>does not prevent</b> attackers to access your dashboard for 100%.
                <hr>
                <p class="mb-0"><b>In case you use remote shutdown functionality and/or have your Pi accessible over Internet</b> <i>or</i> if you (more securely) want to block unwanted users you should consider <i>HTTP Authentication</i> or other mechanisms!</p>

              </div>

              <div class="button-row d-flex mt-4">
                <button class="btn btn-primary js-btn-prev" type="button" title="Prev"><i class="bi bi-chevron-double-left"></i>&nbsp;Prev</button>
                <button class="btn btn-primary ms-auto js-btn-next" type="button" title="Next">Next&nbsp;<i class="bi bi-chevron-double-right"></i></button>
              </div>
            </div>
          </div>
          <!--single form panel-->
          <div class="multisteps-form__panel shadow p-4 rounded" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Permissions</h3>
            <div class="multisteps-form__content">
              <div class="form-row mt-4">

                <p>The dashboard attempts to retrieve/control some system information as listed below. In order to inspect/use them (from your dashboard instance) you can figure out if necessary permissions are set correctly.<br>In case of any insufficient configuration click on the item to get help.</p>

                <h4>Required</h4>
                <div class="list-group mb-3" style="width: 100%">
                  <a href="https://github.com/femto-code/Rasberry-Pi-Dashboard#valid-permissions" class="list-group-item list-group-item-action list-group-item-<?=$pclass0;?>">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">Basic directory access</h5>
                      <small><?=$permi0;?></small>
                    </div>
                    <p class="mb-1">Storing user settings (password etc.) and configuration options (thresholds etc.)</p>
                    <small><?=$p0help;?></small>
                  </a>
                </div>
                
                <h4>Optional</h4>
                <div class="alert alert-info" role="alert"><i class="bi bi-info-circle"></i>&nbsp;The following permission configurations are <b>optional</b> to be decided with respect to what you want the dashboard to tell/control and security aspects (e.g. in case RPi is accessible over Internet).<br>Click on the respective item to get information about what you cannot see/use when permission is missing.</div>
                <div class="list-group" style="width: 100%">
                  <a href="#" data-toggle="modal" data-target="#exampleModal" data-whatever="hwinfo" class="list-group-item list-group-item-action list-group-item-<?=$pclass1;?>">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">Advanced hardware information</h5>
                      <small><?=$permi1;?></small>
                    </div>
                    <p class="mb-1">Get core voltage, firmware version etc.</p>
                    <small>Click for more</small>
                  </a>
                  <a href="#" data-toggle="modal" data-target="#exampleModal" data-whatever="power" class="list-group-item list-group-item-action list-group-item-<?=$pclass2;?>" role="button">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">Shutdown / Reboot RPi remotely</h5>
                      <small class="text-info"><?=$permi2;?></small>
                    </div>
                    <p class="mb-1">Control power state of RPi from your local network. Also affects inspecting (viewing) upcoming/planned power events!</p>
                    <small>Click for more</small>
                  </a>
                </div>


              </div>
              <div class="button-row d-flex mt-4">
                <button class="btn btn-primary js-btn-prev" type="button" title="Prev"><i class="bi bi-chevron-double-left"></i>&nbsp;Prev</button>
                <button onclick="completeSetup()" id="submit" class="btn btn-success ms-auto" type="button" title="To Dashboard">To Dashboard&nbsp;<i class="bi bi-forward"></i></button>
              </div>
            </div>
          </div>
        </form>
       </div>
    </div>
  </div>

  <?php
  if($config->get("general.initialsetup")=="0"){
  ?>
  <div class="alert alert-warning mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;<b>Welcome!</b> You need to complete this setup first before entering dashboard for the first time!</div>
  <?php
  }
  ?>

</div>

<footer class="my-5 py-3 text-muted text-center text-small">
  <p class="mb-1"><a href="https://github.com/femto-code/Raspberry-Pi-Dashboard">RPi Dashboard</a> by <a href="https://github.com/femto-code">@femto-code</a></p>
  <ul class="list-inline">
    <li class="list-inline-item"><a href="https://github.com/femto-code/Raspberry-Pi-Dashboard/issues">Issues</a></li>
    <li class="list-inline-item"><a href="https://github.com/femto-code/Raspberry-Pi-Dashboard/blob/master/README.md">Readme</a></li>
    <li class="list-inline-item"><a href="https://github.com/femto-code/Raspberry-Pi-Dashboard/blob/master/CHANGELOG.md">Changelog</a></li>
  </ul>
</footer>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap-5.3.2.bundle.min.js"></script>
<script>

$('#exampleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var content = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var text, modal = $(this)
  
  if(content=="power"){
    modal.find('.modal-title').text('Shutdown / Reboot RPi remotely (optional)')
    text='<div class="alert alert-warning mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;This is <b>optional</b> and <b>recommended only</b> if your RPi is not accessible over the Internet!</div>In order to use the remote power functionality you have to give the user www-data advanced rights for running one specific command:<br><ul><li>Run <code>sudo visudo</code> to open the editor for adjusting user rights</li><li>Be careful what you change here! Just add the following at the end of the file: <code>www-data ALL=NOPASSWD: /sbin/shutdown</code></li><li>Restart your Pi and now shutdown from another device (connected to same local network like your Pi) is possible</li></ul><h5>Without this change</h5><ul><li>you cannot view any information about upcoming shutdown (e.g. planned via console)</li><li>you cannot shutdown your RPi remotely using the Dashboard</li></ul>';
  }else if(content=="hwinfo"){
    modal.find('.modal-title').text('Advanced hardware information (optional)');
    text='<ul><li>Run <code>sudo usermod -aG video www-data</code> in a terminal</li></ul><div class="alert alert-info mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;If you do not use Raspbian (or any other RasPi distro) like Ubuntu, you do have to install libraspberrypi-bin by running <code>sudo apt install libraspberrypi-bin</code>.</div><h4>Background</h4><p>The <code>vcgencmd</code> command (specifically dedicated to RPi firmware) is a system command that requires certain hardware rights. Therefore one has to grant this particular right (to read hardware info) to e.g. <code>www-data</code> (under which web server is running). This is achived by adding this user to a system group called video, which the standard user pi is part of by default.<br><br>In case of problems: please comment on <a href="https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/12" target="blank">#12</a> (or <a href="https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/new" target="blank">new issue</a>)</p>';
  }
  modal.find('.modal-body p').html(text)
});

// General XMLHttpRequest(s)
class ntwReq {
  constructor(url, successfct, timeoutfct, type="GET", encode=false, data=null) {
    if (!navigator.onLine) {
      $('#overallstate').html('<font class="text-danger"><i class="bi bi-question-circle"></i>&nbsp;You are offline ...</font>');
    }
    this.xmlhttp = new XMLHttpRequest();
    this.xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        successfct(this);
      }
    };
    this.xmlhttp.open(type, url, true);
    if(encode){
      this.xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    }
    this.xmlhttp.timeout = 4 * 1000;
    this.xmlhttp.ontimeout = timeoutfct;
    if(type=="POST"){
      console.log("POSTING...");
      this.xmlhttp.send(data);
    }else{
      this.xmlhttp.send();
    }

  }
}
function completeSetup() {
  <?php
  if($config->get("general.initialsetup")=="0"){
  ?>
  if(checkPw()==false){
    alert("Please check password fields.");
    $("#authbtn").click();
    return;
  }
  var value=document.getElementById("pwinput1").value;
  <?php
  }else{
  ?>
  var value="xyz"; // does not matter - server won't accept when setup done before
  //location.replace("index.php");
  <?php
  }
  ?>
  $("#submit").html("Processing...").prop("disabled", true);
  var vReq = new ntwReq("setup.php", function (data) {
    console.log(data.responseText);
    if(data.responseText=="1"){
      window.setTimeout(function(){
        $('#submit').html("<i class='bi bi-check-circle'></i>&nbsp;Successful");
        window.setTimeout(function(){
          location.replace("index.php");
        }, 1000);
      }, 1000);
    }else{
      if(data.responseText=="perm_error"){
        if (confirm(("Config file (local.config) exists but could not be modified. Required permissions are not set correctly.\nShow help?"))){
          window.open('https://github.com/femto-code/Raspberry-Pi-Dashboard#valid-permissions');
        }
      }
      
    }
  }, null, "POST", true, "complete=true&pw="+value);

}



function checkPw() {
  var pw1=document.getElementById("pwinput1").value;
  var pw2=document.getElementById("pwinput2").value;
  if(pw1==""){
    return false;
  }
  if(pw1!=pw2){
    document.getElementById("pwinput2").classList.add("is-invalid");
    document.getElementById("pwinput2").classList.remove("is-valid");
    return false;
  }else{
    document.getElementById("pwinput2").classList.add("is-valid");
    document.getElementById("pwinput2").classList.remove("is-invalid");
    return true;
  }
}

  //DOM elements
const DOMstrings = {
  stepsBtnClass: 'multisteps-form__progress-btn',
  stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
  stepsBar: document.querySelector('.multisteps-form__progress'),
  stepsForm: document.querySelector('.multisteps-form__form'),
  stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
  stepFormPanelClass: 'multisteps-form__panel',
  stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
  stepPrevBtnClass: 'js-btn-prev',
  stepNextBtnClass: 'js-btn-next' };


//remove class from a set of items
const removeClasses = (elemSet, className) => {

  elemSet.forEach(elem => {

    elem.classList.remove(className);

  });

};

//return exect parent node of the element
const findParent = (elem, parentClass) => {

  let currentNode = elem;

  while (!currentNode.classList.contains(parentClass)) {
    currentNode = currentNode.parentNode;
  }

  return currentNode;

};

//get active button step number
const getActiveStep = elem => {
  return Array.from(DOMstrings.stepsBtns).indexOf(elem);
};

//set all steps before clicked (and clicked too) to active
const setActiveStep = activeStepNum => {

  //remove active state from all the state
  removeClasses(DOMstrings.stepsBtns, 'js-active');

  //set picked items to active
  DOMstrings.stepsBtns.forEach((elem, index) => {

    if (index <= activeStepNum) {
      elem.classList.add('js-active');
    }

  });
};

//get active panel
const getActivePanel = () => {

  let activePanel;

  DOMstrings.stepFormPanels.forEach(elem => {

    if (elem.classList.contains('js-active')) {

      activePanel = elem;

    }

  });

  return activePanel;

};

//open active panel (and close unactive panels)
const setActivePanel = activePanelNum => {

  //remove active class from all the panels
  removeClasses(DOMstrings.stepFormPanels, 'js-active');

  //show active panel
  DOMstrings.stepFormPanels.forEach((elem, index) => {
    if (index === activePanelNum) {

      elem.classList.add('js-active');

      setFormHeight(elem);

    }
  });

};

//set form height equal to current panel height
const formHeight = activePanel => {

  const activePanelHeight = activePanel.offsetHeight;

  DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

};

const setFormHeight = () => {
  const activePanel = getActivePanel();

  formHeight(activePanel);
};

//STEPS BAR CLICK FUNCTION
DOMstrings.stepsBar.addEventListener('click', e => {

  //check if click target is a step button
  const eventTarget = e.target;

  if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
    return;
  }

  //get active button step number
  const activeStep = getActiveStep(eventTarget);

  //set all steps before clicked (and clicked too) to active
  setActiveStep(activeStep);

  //open active panel
  setActivePanel(activeStep);
});

//PREV/NEXT BTNS CLICK
DOMstrings.stepsForm.addEventListener('click', e => {

  const eventTarget = e.target;

  //check if we clicked on `PREV` or NEXT` buttons
  if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`)))
  {
    return;
  }

  //find active panel
  const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

  let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

  //set active step and active panel onclick
  if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
    activePanelNum--;

  } else {

    activePanelNum++;

  }

  setActiveStep(activePanelNum);
  setActivePanel(activePanelNum);

});

//SETTING PROPER FORM HEIGHT ONLOAD
window.addEventListener('load', setFormHeight, false);

//SETTING PROPER FORM HEIGHT ONRESIZE
window.addEventListener('resize', setFormHeight, false);

const setAnimationType=newType=>{DOMstrings.stepFormPanels.forEach(elem=>{elem.dataset.animation=newType;});};
const animationSelect=document.querySelector('.pick-animation__select');
animationSelect.addEventListener('change',()=>{
  const newAnimationType=animationSelect.value;
  setAnimationType(newAnimationType);
});
</script>


</body>
</html>
