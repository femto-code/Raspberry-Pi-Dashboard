<?php

class Config{

  protected $data;

  protected $default = null;

  protected $file;

  public function load($file, $defaultfile){
    if(!(file_exists($file))){
      $myfile = fopen($file, "w") or die("Unable to open file!");
      fwrite($myfile, "<?php\nreturn array();\n?>");
      fclose($myfile);
    }
    $this->file=$file;
    $userconf=require $file;
    $defaults=require $defaultfile;
    $this->data = array_merge($defaults, $userconf);
    //echo "<pre>",print_r($this->data),"</pre>";
    //$this->save($this->data);
  }

  public function get($key, $default = null){
    $this->default = $default;

    $segments = explode(".", $key);

    $data = $this->data;

    foreach ($segments as $segment){
      if (isset($data[$segment])){
        $data = $data[$segment];
      }else{
        $data = $this->default;
        break;
      }
    }
    return $data;
  }

  public function exists($key){
    return $this->get($key) !== $this->default;
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
