<?php
namespace App;

use Auth;
use App\Media\YouTube;

class MediaResolver {

  private $userId;
  private $types = [];

  public function __construct($userId)
  {
    $this->types = [
      "youtube" => new YouTube($userId)
    ];
  }

  public function dispatch($type, $action, $input)
  {
    $typeClass = $this->types[$type];
    $methods = get_class_methods($typeClass);
    $methods = array_diff($methods, ['__construct']);

    //@TODO make sure my meta logic with arguments vs params is correct
    if(in_array($action, $methods)) {
      $arguments = $this->matchArguments($typeClass, $action, $input);
      return $typeClass->{$action}(...$arguments);
    }
  }

  private function matchArguments($class, $method, $arguments)
  {
    $params = [];

    $ref = new \ReflectionMethod($class, $method);
    foreach($ref->getParameters() as $param) {
      $params[] = $param->name;
    }

    $data = [];
    //match param to available arguments
    foreach($params as $param) {
      //fill array will correct param order instead of key => value
      $data[] = $arguments[$param];
    }

    return $data;
  }
}
?>
