<?php
header('Access-Control-Allow-Origin: *');
error_reporting (E_ALL);
ini_set ('display_errors','On');

session_start();

require "backend/Config.php";
$config = new Config;
$config->load("local.config", "defaults.php");

if(isset($_POST["complete"])){
  if(isset($_POST["pw"])){
    $val=$_POST["pw"];
  }else{
    echo "error";
    exit();
  }

  $existing=$config->userconf;

  $edit=array('general' => array ());
  $edit["general"]["pass"]=md5($val);
  $edit["general"]["initsetup"]="1";
  //print_r($edit);

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
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>RPi Dashboard Setup</title>

<link rel="stylesheet" href="css/bootstrap-4.6.0.min.css">
<link rel="stylesheet" href="css/bootstrap-icons.css?v=1.4.0">
<link rel="stylesheet" href="css/darkmode.css" id="dmcss" type="text/css" disabled>


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

</head>
<body style="background-color: #f5f5f5;">

<div class="container">

  <div style="margin:auto;margin-top: 30px" class="text-center">
    <img class="mb-4" src="../login/official_logo.svg" alt="" width="72" height="72">
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
      <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
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
          <div class="multisteps-form__panel shadow p-4 rounded bg-white js-active" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Appearance</h3>
            <div class="multisteps-form__content pt-2">

              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="false" name="myRadios" class="custom-control-input" checked>
                <label class="custom-control-label" for="false">Light Theme</label>
              </div>
              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="true" name="myRadios" class="custom-control-input">
                <label class="custom-control-label" for="true">Dark Theme</label>
              </div>
              <div class="custom-control custom-radio ml-3">
                <input type="radio" id="auto" name="myRadios" class="custom-control-input">
                <label class="custom-control-label" for="auto">Adjust to (local) system settings</label>
              </div>

              <div class="alert alert-info mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;Theme settings can also be changed later using dashboard options modal.</div>

              <div class="row">
                <div class="button-row d-flex mt-4 col-12">
                  <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next&nbsp;<i class="bi bi-chevron-double-right"></i></button>
                </div>
              </div>
            </div>
          </div>
          <!--single form panel-->
          <div class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Authorization</h3>
            <div class="multisteps-form__content">

              <?php
              if($config->get("general.initsetup")=="0"){

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

              <div class="alert alert-warning mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;Custom password was already set and this form now disabled for security reasons. Log-in to Dashboard and use options to change it.<hr>
              <p class="mb-0"><strong>Don't have access to it?</strong> Alternatively, reset setup state by editing your <kbd>local.config</kbd> file. <a href="" class="alert-link"><i class="bi bi-question-circle"></i>&nbsp;Help</a></p></div>

              <?php
              }

              ?>

              <div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Please note</h4>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                Please keep in mind, that this password authentication <b>does not prevent</b> attackers to access your dashboard for 100%.
                <hr>
                <p class="mb-0">If you certainly want to block unwanted users you should consider HTTP Authentication or other mechanisms.</p>

              </div>

              <div class="button-row d-flex mt-4">
                <button class="btn btn-primary js-btn-prev" type="button" title="Prev"><i class="bi bi-chevron-double-left"></i>&nbsp;Prev</button>
                <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next&nbsp;<i class="bi bi-chevron-double-right"></i></button>
              </div>
            </div>
          </div>
          <!--single form panel-->
          <div class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleOut">
            <h3 class="multisteps-form__title">Permissions</h3>
            <div class="multisteps-form__content">
              <div class="form-row mt-4">

                <p>The dashboard attempts to retrieve the following information. Therefore it will figure out if permissions are set correctly. Click on the item to get help.</p>

                <div class="list-group" style="width: 100%">
                  <a href="#" class="list-group-item list-group-item-action list-group-item-<?=$pclass1;?>">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">Hardware information</h5>
                      <small><?=$permi1;?></small>
                    </div>
                    <p class="mb-1">Get voltage, firmware version etc. by vcgencmd command</p>
                    <small>Click for more</small>
                  </a>
                  <a href="#" class="list-group-item list-group-item-action list-group-item-<?=$pclass2;?>">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">Shutdown / Reboot RPi</h5>
                      <small class="text-info"><?=$permi2;?></small>
                    </div>
                    <p class="mb-1">Control power state of RPi from your local network</p>
                    <small>Click for more</small>
                  </a>
                </div>


              </div>
              <div class="button-row d-flex mt-4">
                <button class="btn btn-primary js-btn-prev" type="button" title="Prev"><i class="bi bi-chevron-double-left"></i>&nbsp;Prev</button>
                <button onclick="completeSetup()" id="submit" class="btn btn-success ml-auto" type="button" title="To Dashboard">To Dashboard&nbsp;<i class="bi bi-forward"></i></button>
              </div>
            </div>
          </div>
        </form>
       </div>
    </div>
  </div>

  <?php
  if($config->get("general.initsetup")=="0"){
  ?>
  <div class="alert alert-info mt-3" role="alert"><i class="bi bi-info-circle"></i>&nbsp;You need to complete setup first before entering dashboard!</div>
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

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/popper-1.16.1.min.js"></script>
<script src="js/bootstrap-4.6.0.min.js"></script>
<script>

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
  if($config->get("general.initsetup")=="0"){
  ?>
  if(checkPw()==false){
    alert("Please check password fields.");
    $("#authbtn").click();
    return;
  }
  var value=document.getElementById("pwinput1").value;
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
      alert("Error occured.");
    }
  }, null, "POST", true, "complete=true&pw="+value);
  <?php
  }else{
  ?>
  location.replace("index.php");
  <?php
  }
  ?>


}


var rad = document.myForm.myRadios;
var prev = null;
for (var i = 0; i < rad.length; i++) {
  rad[i].addEventListener('click', function() {
    adjustTheme(this.id);
  });
}

function adjustTheme(chose) {

  localStorage.setItem("darkmode", chose);
  darkmode(chose);
}

function darkmode(state) {
  if (state == undefined) {
    return !$("#dmcss").attr("disabled");
  }
  if (state == "true") {
    $("#dmcss").prop("disabled", false);
  } else if (state == "false") {
    $("#dmcss").prop("disabled", true);
  }else if(state=="auto"){
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      darkmode("true");
    } else {
      darkmode("false");
    }
    window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
      if (e.matches) {
        darkmode("true");
      } else {
        darkmode("false");
      }
    });
  }
}
if(localStorage.getItem("darkmode") != null){
  darkmode(localStorage.getItem("darkmode"));
}

document.getElementById(localStorage.getItem("darkmode")).checked="true";

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
