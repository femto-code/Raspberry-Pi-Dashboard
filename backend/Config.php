<?php

class Config{

  protected $data;
  public $userconf;
  public $defconf;


  protected $file;

  public function load($file, $defaultfile){
    if(!(file_exists($file))){
      $myfile = fopen($file, "w") or die("<br><br>Unable to open file! This error happens in the situation where the user responsible for web server (e.g. www-data) does not have rights to create/modify the local config file for saving your dashboard adjustments (your custom thresholds, password etc.).<br><br>THIS IS NECESSARY FOR THE DASHBOARD TO WORK!<br><br>Please see <a href='https://github.com/femto-code/Raspberry-Pi-Dashboard#valid-permissions' target='blank'>installation instructions</a> (and the permission prequisites) for setting correct permissions on your dashboard folder.");
      fwrite($myfile, "<?php\nreturn array();\n?>");
      fclose($myfile);
      chmod($file, 0664);
    }
    $this->file=$file;
    $userconf=require $file;
    $defaults=require $defaultfile;
    $this->data = array_replace_recursive($defaults, $userconf); // not array_merge($defaults, $userconf)! Use recursive replace!
    $this->userconf = $userconf;
    $this->defconf = $defaults;
    //echo "<pre>",print_r($this->data),"</pre>";
    //$this->save($this->data);
  }

  public function get($key, $source=false){

    $segments = explode(".", $key);

    $data = ($source!==false) ? $this->{$source} : $this->data;

    foreach ($segments as $segment){
      if (isset($data[$segment])){
        $data = $data[$segment];
      }else{
        $data = "";
        break;
      }
    }
    return $data;
  }

  public function modified($key){
    return $this->get($key, "userconf");
  }
  public function defaults($key){
    return $this->get($key, "defconf");
  }

  public function save($dat){
    // return ".json_encode($dat).";";
    if (is_writable($this->file)) {
      $res=file_put_contents($this->file, "<?php\nreturn ".var_export($dat, true).";\n?>");
      if($res===false){
        return "write_error";
      }else{
        return true;
      }
    }else{
      return "perm_error";
    }
  }

}

?>
