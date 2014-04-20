<?php
// Bring in anything from Composer
require_once '../vendor/autoload.php';

define('ROOT_DIR', __DIR__ . '/../');
define('VIEW_PATH', ROOT_DIR . 'views/');
define('PUBLIC_DIR', ROOT_DIR . 'public/');
define('CACHE_PATH', ROOT_DIR . 'cache/');

// Now lets sort out twig
$views = new Twig_Loader_Filesystem(VIEW_PATH);

// Add public dir for grabing assets
$views->addPath(PUBLIC_DIR);

$twig = new Twig_Environment($views, array(
  'debug' => true,
  'cache' => CACHE_PATH . 'twig',
  'auto_reload' => true,
  'strict_variables' => false,
));

// Debug
$twig->addExtension(new Twig_Extension_Debug());

// Call any php function from within the templates
$twig->addFunction(
  new Twig_SimpleFunction('php_*', function(){
    $arg_list = func_get_args();
    $function = array_shift($arg_list);
    return call_user_func_array($function, $arg_list);
  })
);

use Importio\Authenticator;
use Importio\Connector;

$auth = new Authenticator(
  "c4c27fbd-53b8-4216-a399-ba83eae1cfc7",
  "K0oLG5tbdmPlcl41i7+80i/z8zK8a+o6O3zMPLLWL+si2oH3Ts3TBmp28tCiNfNaudx2UEQsOTxVC62T6VgR3g=="
);

$connector = new Connector($auth, CACHE_PATH . 'guzzle');
$connector->guid("109c94b2-9864-4d68-ad07-4227750270c5");

echo $twig->render('index.twig', ['steam' => $connector->json()]);
