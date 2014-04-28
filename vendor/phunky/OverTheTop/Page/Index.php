<?php
namespace OTT\Page;

use OTT\Webpage as Webpage;
use Importio\Authenticator;
use Importio\Connector;

class Index extends Webpage  {

  public function get(){
    $auth = new Authenticator(
      "c4c27fbd-53b8-4216-a399-ba83eae1cfc7",
      "K0oLG5tbdmPlcl41i7+80i/z8zK8a+o6O3zMPLLWL+si2oH3Ts3TBmp28tCiNfNaudx2UEQsOTxVC62T6VgR3g=="
    );

    $connector = new Connector($auth, CACHE_PATH . 'guzzle');
    $connector->guid("109c94b2-9864-4d68-ad07-4227750270c5");

    $this->steam = $connector->json();
  }

}
