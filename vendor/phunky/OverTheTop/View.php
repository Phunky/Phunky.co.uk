<?php
namespace OTT;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_SimpleFunction;

class View {
  protected $twig;

  function __construct(){
    // Setup twig, I know this is tight dependancy but Twig is one so that justifies it for me.
    $this->views = new Twig_Loader_Filesystem(VIEW_PATH);
    $this->views->addPath(PUBLIC_DIR);

    $this->twig = new Twig_Environment($this->views, array(
      'debug' => true,
      'cache' => CACHE_PATH . 'twig',
      'strict_variables' => false,
    ));

    // Call any php function from within the templates
    $this->twig->addFunction(
      new Twig_SimpleFunction('php_*', function(){
        $arg_list = func_get_args();
        $function = array_shift($arg_list);
        return call_user_func_array($function, $arg_list);
      })
    );
  }

  public function render($template, $data){
    echo $this->twig->render(strtolower($template) . '.twig', $data);
  }

}
