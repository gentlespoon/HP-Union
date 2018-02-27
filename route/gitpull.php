<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : gitpull.php [app]
 * Date   : 2018-02-21
 * Time   : 00:47
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


echo shell_exec('cd '.ROOT.'/app; git pull 2>&1');
