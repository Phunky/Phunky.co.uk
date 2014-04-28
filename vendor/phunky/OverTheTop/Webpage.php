<?php
namespace OTT;

class Webpage {

  protected $data = [];

  function __construct(){
  }

  public function data(){
    return $this->data;
  }

  public function __set($name, $value){
    $this->data[$name] = $value;
  }

  public function __get($name){
    // Do we have this stored?
    if( array_key_exists($name, $this->data) ){
      return $this->data[$name];
    }
    return null;
  }

}
