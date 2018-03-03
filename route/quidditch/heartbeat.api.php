<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : heartbeat.api.php [HP-Union]
 * Date   : 2018-03-02
 * Time   : 23:59
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


apiPrint(1, "没有新消息");
