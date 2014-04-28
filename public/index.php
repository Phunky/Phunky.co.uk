<?php
// Bring in anything from Composer
require_once '../vendor/autoload.php';

define('ROOT_DIR', __DIR__ . '/../');
define('VIEW_PATH', ROOT_DIR . 'views/');
define('PUBLIC_DIR', ROOT_DIR . 'public/');
define('CACHE_PATH', ROOT_DIR . 'cache/');

// Magic
new OTT\Response(
  new OTT\Request(),
  new OTT\View()
);
