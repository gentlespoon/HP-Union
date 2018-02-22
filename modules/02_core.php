<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : 01_core.php [localhost]
 * Date   : 2018-02-15
 * Time   : 00:41
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


if (!array_key_exists('user_id', $_SESSION)) {
  $_SESSION['user_id'] = '';
}


$currentUser = [];
if ($_SESSION['user_id']) {
  $currentUser = DB::queryFirstRow('SELECT user_id, user_groups, display_name, avatar FROM '.DBtable('users').' WHERE user_id=%s', $_SESSION['user_id']);
  $currentUser['user_groups'] = json_decode($currentUser['user_groups']);
}

$user_groups = DB::query("SELECT group_id, group_name, group_icon FROM user_groups ORDER BY display_order ASC");
foreach($user_groups as $k => $group) {
  $user_groups[$group['group_id']] = [ 'groupName' => $group['group_name'], 'groupIcon' => $group['group_icon'] ];
  unset($user_groups[$k]);
}
