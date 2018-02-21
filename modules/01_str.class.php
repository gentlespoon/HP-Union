<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : 00_string.php [GsMVC]
 * Date   : 2018-02-08
 * Time   : 19:47
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


class str {

  public static function before_first($delim, $string) {
    $segments = explode($delim, $string);
    return $segments[0];
  }

  public static function before_last($delim, $string) {
    $segments = explode($delim, $string);
    if (sizeof($segments) > 1) {
      array_pop($segments);
    }
    return implode($delim, $segments);
  }

  public static function after_first($delim, $string) {
    $segments = explode($delim, $string);
    unset($segments[0]);
    return implode($delim, $segments);
  }


  public static function after_last($delim, $string) {
    $segments = explode($delim, $string);
    return array_pop($segments);
  }


}
