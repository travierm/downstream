<?php
namespace App;

use App\Media\YouTube;

class MediaResolver {

  private static $types = [];
  private static $routes = [];

  public static function init($userId)
  {
    self::$types = [
      "youtube" => new YouTube($userId)
    ];
  }

  public static function dispatch($type, $action, $params)
  {
    $typeClass = self::$types[$type];
    $methods = get_class_methods($typeClass);
    $methods = array_diff($methods, ['__construct']);

    if(in_array($action, $methods)) {
      return $typeClass->{$action}();
    }
  }
}
?>
