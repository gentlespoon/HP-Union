<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : hat.php [HP-Union]
 * Date   : 2018-02-26
 * Time   : 23:16
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


echo "The Sorting Hat";
