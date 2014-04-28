<?php
namespace OTT;

class Request {
  public $namespaced_class;
  public $requested_class;

  function __construct(){

    if( $this->isPage() ){
      $uri = explode( '.' , $_SERVER['REQUEST_URI'] );
      $this->requested_class = preg_replace( '/[^\da-z]/i', '', $uri[0] );
    }

    if( $this->isIndex() ){
      $this->requested_class = 'Index';
    }

    if( !$this->checkRequestedClass() ){
      $this->requested_class = 'Error';
      $this->is404 = true;
    }

    $this->namespaced_class = __NAMESPACE__ . '\\Page\\' . $this->requested_class;
  }

  public function isGet(){
    return $_SERVER['REQUEST_METHOD'] === 'GET';
  }

  public function isPost(){
    return $_SERVER['REQUEST_METHOD'] === 'POST';
  }

  public function isJson(){
    if( isset($_SERVER["CONTENT_TYPE"]) === 'json' ){
      return true;
    }

    return pathinfo( explode('?', $_SERVER['REQUEST_URI'])[0], PATHINFO_EXTENSION ) === 'json';
  }

  public function is404(){
    return isset($this->is404);
  }

  protected function checkRequestedClass(){
    return class_exists( __NAMESPACE__ . '\\Page\\' . $this->requested_class );
  }

  protected function isIndex(){
    return $_SERVER['REQUEST_URI'] === '/';
  }

  protected function isPage(){
    return $_SERVER['REQUEST_URI'] !== '';
  }

}
