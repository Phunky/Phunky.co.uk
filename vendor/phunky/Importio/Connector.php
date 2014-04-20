<?php
namespace Importio;

use Guzzle\Http\Client;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;

class Connector {

  private $user;
  private $key;
  private $request;
  public $client;

  function __construct(Authenticator $Authenticator, $cache){
    $this->user = $Authenticator->user;
    $this->key = $Authenticator->key;
    $this->client = new Client();

    if( isset($cache) ){
      $this->cache($cache);
    }
  }

  function cache($path){
    $cache = new CachePlugin([
      'storage' => new DefaultCacheStorage(
        new DoctrineCacheAdapter(
          new FilesystemCache($path)
        )
      )
    ]);

    $this->client->addSubscriber($cache);
    return $this;
  }

  function guid($guid){
    $this->request = $this->client->get('https://api.import.io/store/connector/' . $guid . '/_query');
    return $this;
  }

  function send(){
    $this->request->getQuery()->set('_user', $this->user);
    $this->request->getQuery()->set('_apikey', $this->key);
    return $this->request->send();
  }

  function json(){
    $this->request->getQuery()->set('format', 'JSON');
    return $this->send()->json();
  }

  function xml(){
    $this->request->getQuery()->set('format', 'XML');
    return $this->send()->xml();
  }

  function html(){
    $this->request->getQuery()->set('format', 'HTML');
    return $this->send()->getBody();
  }

}
