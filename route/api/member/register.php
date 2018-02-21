<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : register.php [fGame]
 * Date   : 2018-01-31
 * Time   : 18:23
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . "/");
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}

if ($_SESSION['user_id']) {
  apiPrint(1, '已登录');
  return;
}

// verify user inputs
if (
  !array_key_exists('username', $_POST) || $_POST['username']=='' ||
  !array_key_exists('password', $_POST) || $_POST['password']==''
  ) {
  apiPrint(0, '提交的数据不正确');
  return;
}

$user_id = trim($_POST['username']);
$password = trim($_POST['password']);

$isExisted = DB::queryFirstRow('SELECT avatar FROM '.DBtable('users').' WHERE user_id=%s', $user_id);
if (!empty($isExisted)) {
  apiPrint(0, '用户名已被注册');
  return;
}

$password = password_hash($password, PASSWORD_DEFAULT);

DB::insert(DBtable('users'), [
    'user_id' => $user_id,
    'display_name' => $user_id,
    'login_pass' => $password,
    'register_date' => date(dtFormat['mysql']),
    'register_ip' => $_SERVER['REMOTE_ADDR'],
    'last_date' => date(dtFormat['mysql']),
    'last_ip' => $_SERVER['REMOTE_ADDR'],
  ]);

$userProfile = DB::queryFirstRow('SELECT * FROM '.DBtable('users').' WHERE user_id=%s', $user_id);
$_SESSION['user_id'] = $userProfile['user_id'];

apiPrint(1, [
  'user_id' => $userProfile['user_id'],
  'display_name' => $userProfile['display_name']
  ]);
return;
