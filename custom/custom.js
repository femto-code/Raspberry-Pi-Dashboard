
/* Custom part
 * Here, you can make specific settings to adjust dashboard behaviour.
 * 
 */


// Temperature in °C, which is the critical value and throws a warning - default: 60
warn_cpu_temp = 60;

// Usage of working memory in %, which is the critical value and throws a warning - default: 80
warn_ram_space = 80;

// time interval of update in seconds (recommended: 10 - 60 sec) - Pay attention: Do not set too low. - default: 15
upd_time_interval = 15;

// CPU workload of last minute, which is the critical value and throws a warning - default: 2
warn_loads_size = 2;


//***************************
// DO NOT CHANGE
console.log("Übernehme Userdaten: warncputemp="+warn_cpu_temp+" | warn_ram_space="+warn_ram_space+" | upd_time_interval="+upd_time_interval+" | warn_loads_size="+warn_loads_size);
