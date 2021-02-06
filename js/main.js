// General XMLHttpRequest(s) TODO: Compatibility check
class ntwReq {
  constructor(url, successfct, timeoutfct) {
    this.xmlhttp = new XMLHttpRequest();
    this.xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        successfct(this);
      }
    };
    this.xmlhttp.open("GET", url, true);
    this.xmlhttp.timeout = 4 * 1000;
    this.xmlhttp.ontimeout = timeoutfct;
    this.xmlhttp.send();
  }
}

function preload(){
	// Update of all data
	updatedb();
	if(window.location.search == "?live=disabled"){
		console.info("Live Update was disabled through site parameters.");
		document.getElementById("pctl").innerHTML='<i class="bi bi-play"></i>';
	}
  setTimeout(function(){ $(".preload-screen").fadeOut("slow"); }, 500);
  checkShutdown();
}

var tselect=1;
function authorize() {
  if(document.getElementById('inputPassword2')==null){
    pass="alreadyauthorized";
  }else{
    pass=document.getElementById('inputPassword2').value;
  }
  $("#confbtn").html("Checking...");
  $("#pwrform input, select").prop("disabled","true");
  $("#confbtn").prop("disabled","true");
  var e = document.getElementById("time"+tselect);
  var time;
  if( (tselect==1) || (tselect==2) ){
    
    if(tselect==1){
      time = parseInt(e.options[e.selectedIndex].value);
    }else{
      time = parseInt(e.value);
    }
    
    if( (!Number.isInteger(time)) || (time < 1) ){
		  alert("Invalid time input!");
		  return false;
	  }
  }else if(tselect==3){
    time = e.value;
  }
	
  console.log(time);
	
	var act=document.querySelector('input[name="pwrOptions"]:checked').value;
  if (pass.length == 0) { 
    document.getElementById("currentState").innerHTML = "<font class='text-danger'>Please enter a valid password!</font>";
    return;
  } else {
    var vReq = new ntwReq("backend/serv.php?p=" + pass+"&a="+act+"&time="+time, function (data) {
      console.log(data.responseText);
      if(data.responseText.indexOf("true") > -1){
        document.getElementById("currentState").innerHTML = "<div class='alert alert-success' role='alert'><i class='bi bi-check2-circle'></i>&nbsp;Authorization completed!</font>";
        $("#confbtn").html("<i class='bi bi-check2-circle'></i>&nbsp;Saved");
        var res=JSON.parse(data.responseText.split("true_")[1]);
        outputShutdown(res.date,res.act);
        setTimeout(function(){
          $("#exampleModalCenter").modal("hide");
          document.getElementById("pwrform").reset();
          $("#pwrform input, select").prop("disabled","");
          $("#confbtn").prop("disabled","");
          document.getElementById("currentState").innerHTML = "";
          $("#confbtn").html("Confirm identity");
        },3000);
      }else if(data.responseText=="wrongCredentials"){
        document.getElementById("currentState").innerHTML = "<div class='alert alert-danger' role='alert'><i class='bi bi-x-circle'></i>&nbsp;Authorization failed!</div>";
      }else{
        document.getElementById("currentState").innerHTML = "<div class='alert alert-success' role='alert'><i class='bi bi-x-circle'></i>&nbsp;Error!</div>";
      }
    }, function () {
      
    });
  }
}
function checkShutdown(callback) {
  document.getElementById("currentState").innerHTML='<div class="alert alert-info" role="alert"><i class="bi bi-chevron-double-right"></i>&nbsp;Checking for power events...</div>';
  var vReq = new ntwReq("backend/serv.php?checkShutdown", function (data) {
    console.log(data.responseText);
    var res=JSON.parse(data.responseText);
    if( (res.act=="") || (res.date==null) ){
      document.getElementById("sys2").innerHTML="";
      shutdownCurrent=false;
    }else{
      shutdownCurrent=true;
      outputShutdown(res.date,res.act);
    }
    if(callback !== undefined){
      callback();
    }else{
      if(shutdownCurrent){
        document.getElementById("currentState").innerHTML='<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i>&nbsp;Existing shutdown will be overwritten.&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="cancelShutdown();$(\'#exampleModalCenter\').modal(\'hide\');">Remove</button></div>';
      }else{
        document.getElementById("currentState").innerHTML='<div class="alert alert-success" role="alert"><i class="bi bi-check2-circle"></i>&nbsp;Currently there is no other power event planned.</div>';
      }
    }
  }, function () {
    
  });
}

function cancelShutdown(force) {
  if(force == undefined){
    mdtoast('<i class="bi bi-question-circle"></i>&nbsp;Confirm to cancel', { type: 'warning', interaction: true, actionText: "Confirm", action: function(){ cancelShutdown(true); }, duration: 3000});
    return;
  }
  var vReq = new ntwReq("backend/serv.php?cancelShutdown", function (data) {
    console.log(data.responseText);
    if(data.responseText==""){
      console.log("Cancel response is empty");
      mdtoast('<i class="bi bi-check2-circle"></i>&nbsp;Power event was cancelled!', { type: 'success'});
      checkShutdown();
      return;
    }
  }, function () {
    
  });
}

var dobj={Mon: "Monday", Tue: "Tuesday", Wed: "Wednesday", Thu: "Thursday", Fri: "Friday", Sat: "Saturday", Sun: "Sunday"};
function outputShutdown(data,act) {
  if(typeof data !== "number"){ // for compatibility reasons
    data=data.replace("\n","");
    console.log("Trying to process old info...");
    var day=data.substring(0,3);
    var s = data.replace(day,dobj[day]);
    console.log(s);
    s=s.split(" ")[0]+", "+s.split(" ")[3]+" "+s.split(" ")[1]+", "+s.split(" ")[6]+" "+s.split(" ")[4];
    console.log(s);
    scheduled=Date.parse(s);
    d = new Date(scheduled);
  }else{
    d = new Date(data);
  }
  
  
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
  if(str==""){ str="<font class='text-danger'>&lt; 1 min</font>"; }
  console.log(str);
  document.getElementById("sys2").innerHTML='<div class="alert alert-warning" role="alert"><button class="btn btn-sm btn-outline-danger" onclick="cancelShutdown()" style="float:right">Cancel</button>Scheduled power event: <kbd>'+act+'</kbd><br>Remaining time: <kbd>'+str+'</kbd><br></div>';
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


// General chart functions and globals
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
        }
        $('#overallstate').html('<font class="text-muted"><i class="bi bi-hourglass-split"></i>&nbsp;Waiting for authentication ...</font>');
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
				document.getElementById("tempstate").innerHTML="<i class='bi bi-thermometer-half'></i>&nbsp;Temperature <font class='text-success'>(OK)</font>";
			}else{
        document.getElementById("tempstate").innerHTML="<i class='bi bi-thermometer-half'></i>&nbsp;Temperature <font class='text-warning'>(WARNING)</font>";
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
				document.getElementById("overallstate").innerHTML="<font class='text-danger'><i class='bi bi-exclamation-circle'></i>&nbsp;"+warn+" problem"+s+" occured</font>";
				warnuser(warn);
			}else{
				document.getElementById("overallstate").innerHTML="<font class='text-success'><i class='bi bi-check2-circle'></i>&nbsp;System runs normally</font>";
			}
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
        checkShutdown();
				y=0;
			}
		}, (((upd_time_interval*1000)-1.5)/10));
		timer=true;
		console.log("Timer started");
		$('#overallstate').html('<font class="text-info"><i class="bi bi-chevron-double-right"></i>&nbsp;Will be updated ...</font>');
		return '<i class="bi bi-pause"></i>';
	}else{
		clearInterval(updinterval);
		timer=false;
		console.log("Timer gestoppt");
		$('#overallstate').html('<font class="text-muted"><i class="bi bi-clock"></i>&nbsp;Live Update disabled</font>');
		return '<i class="bi bi-play"></i>';
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
          $("#lock_section").html("<i class='bi bi-unlock' style='width: 100px;height:100px;color:#aaa'></i><br><font class='text-success'>You are authorized!<br><a href='javascript:location.reload()'>Reload</a> the page to load the full page content.</font>");
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
function checkLauth(){
  document.getElementById("pwrauth").innerHTML="<div class='alert alert-info' role='alert'><i class='bi bi-chevron-double-right'></i>&nbsp;Checking authorization ...</div>";
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      if(this.responseText=="invalid"){
        $("#pwrauth").html('<label for="inputPassword2" class="sr-only">Password</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="bi bi-key"></i></div></div><input type="password" class="form-control" id="inputPassword2" placeholder="Password"></div>');
      }else{
        $("#pwrauth").html('<div class="alert alert-success" role="alert"><i class="bi bi-check2-circle"></i>&nbsp;Authenticated</div>');
      }
    }
  };
  xmlhttp.open("POST", "backend/serv.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  setTimeout(() => {
    xmlhttp.send("check=true");
  }, 1000);
}

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
  mdtoast('<i class="bi bi-exclamation-circle"></i>&nbsp;There '+str+'&nbsp;<b>'+c+'</b>&nbsp;problem'+str2+', please check!', { type: 'error'});
}
function addWarning(problem, icon){
  document.getElementById("sys11").innerHTML+='<div class="bg-danger card text-white text-center shadow m-1" style="max-width: 18rem;"><h5 style="margin-top: .75rem;" class="card-title"><i class="bi bi-'+icon+'"></i>&nbsp;'+problem+'</h5></div>';
}

$('#exampleModalCenter').on('shown.bs.modal', function (e) {
  checkShutdown(function(){
    if(shutdownCurrent){
      document.getElementById("currentState").innerHTML='<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i>&nbsp;Existing shutdown will be overwritten.&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="cancelShutdown();$(\'#exampleModalCenter\').modal(\'hide\');">Remove</button></div>';
    }else{
      document.getElementById("currentState").innerHTML='<div class="alert alert-success" role="alert"><i class="bi bi-check2-circle"></i>&nbsp;Currently there is no other power event planned.</div>';
    }
  });
  checkLauth();
});

// Dark Mode Switch and Init
function toggleDarkMode() {
  var state = $("#dm").prop("checked");
  darkmode(state);
  $("#dmauto").prop("checked", false);
  localStorage.setItem("darkmode", state);
}
function toggleAutoDarkMode(predef) {
  var state;
  var save;
  if(predef!==undefined){
    state = predef;
    console.log("predef",predef);
  }else{
    state = $("#dmauto").prop("checked");
  }
  if(state){
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      darkmode(true);
    } else {
      darkmode(false);
    }
    window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
      if (e.matches) {
        darkmode(true);
      } else {
        darkmode(false);
      }
    });
    save = "auto";
  }else{
    save = $("#dm").prop("checked");
    darkmode(save);
  }
  localStorage.setItem("darkmode", save);
}
function darkmode(state) {
  if (state == undefined) {
    return !$("#dmcss").attr("disabled");
  }
  if (state == true) {
    $("#dmcss").prop("disabled", false);
    $("#webinfo").addClass("text-muted");
    $("#kernel").addClass("text-muted");
  } else if (state == false) {
    $("#dmcss").prop("disabled", true);
    $("#webinfo").removeClass("text-muted");
    $("#kernel").removeClass("text-muted");
  }
}
$("#dm").prop("checked", (localStorage.getItem("darkmode") == 'true'));
$("#dmauto").prop("checked", (localStorage.getItem("darkmode") == 'auto'));
darkmode( (localStorage.getItem("darkmode") != 'false' && localStorage.getItem("darkmode") != null) );
// Formerly: call to disable darkmode by setting disabled prop to (?) -> logical A + B -> now we need the opposite /(A+B) = /A * /B -> works :D
toggleAutoDarkMode((localStorage.getItem("darkmode")=="auto"));