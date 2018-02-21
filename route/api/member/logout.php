<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : logout.php [localhost]
 * Date   : 2018-02-09
 * Time   : 03:56
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


$_SESSION['user_id'] = '';
apiPrint(1, '已退出登录');
