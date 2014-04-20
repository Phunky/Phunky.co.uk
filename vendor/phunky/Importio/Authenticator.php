<?php
namespace Importio;

class Authenticator {

  public $user;
  public $key;

  function __construct($user, $key){
    $this->user = $user;
    $this->key = $key;
  }

}
