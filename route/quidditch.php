<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : quidditch.php [HP-Union]
 * Date   : 2018-02-28
 * Time   : 23:37
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


include_once(ROOT.'app/view/quidditch.html');
