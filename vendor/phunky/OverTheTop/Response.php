<?php
namespace OTT;

class Response {
  protected $request;
  protected $view;
  protected $data;
  protected $page = null;

  function __construct(Request $request, View $view){
    $this->request = $request;
    $this->view = $view;

    // Instantiate the page class
    $this->page = new $this->request->namespaced_class();

    // Set the requested route
    $this->page->route = strtolower( $this->request->requested_class );
  }

  function __destruct(){
    // Handle 404 request straight away
    if( $this->request->is404() ){
      header("HTTP/1.0 404 Not Found");
    }

    // If a POST handle the request and redirect to a GET for same URI
    if( $this->request->isPost() ){
      if( !$this->request->is404() ){
        header("Location: " . $_SERVER['REQUEST_URI']);
        $this->page->post();
      }
      return;
    }

    // If we get this far then it's a GET
    $this->page->get();

    // If a request for JSON format handle appropriately
    if( $this->request->isJson() ){
      header('Content-Type: application/json; charset=utf8');

      $output = json_encode($this->page->data());

      if( isset($_GET['callback']) ){
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_HOST']);
        header('Access-Control-Max-Age: 3628800');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        $output = $_GET['callback'] . '(' . $output . ');';
      }

      echo $output;

      return;
    }

    // If we get here we must be a View
    $this->view->render($this->request->requested_class, $this->page->data());
  }

}
