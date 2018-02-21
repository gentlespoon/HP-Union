<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : login.php [fGame]
 * Date   : 2018-01-31
 * Time   : 18:22
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . "/");
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


if ($_SESSION['uid']) {
  apiPrint(1, 'Already logged in');
  return;
}

// verify user inputs
if (
  !array_key_exists('username', $_POST) || $_POST['username']=='' ||
  !array_key_exists('password', $_POST) || $_POST['password']==''
) {
  apiPrint(0, 'Invalid credentials.');
  return;
}

$user_id = trim($_POST['username']);
$password = trim($_POST['password']);

$attemptedUser = DB::queryFirstRow('SELECT user_id, display_name, login_pass, total_login FROM '.DBtable('users').' WHERE user_id=%s', $user_id);
if (empty($attemptedUser)) {
  apiPrint(0, 'Login name does not exist.');
  return;
}

if (!password_verify($password, $attemptedUser['login_pass'])) {
  apiPrint(0, 'Incorrect credentials.');
  return;
}

$_SESSION['user_id'] = $attemptedUser['user_id'];

DB::update(DBtable('users'), [
    'last_date' => date(dtFormat['mysql']),
    'last_ip' => $_SERVER['REMOTE_ADDR'],
    'total_login' => $attemptedUser['total_login']+1,
  ], "user_id=%s", $_SESSION['user_id']);

apiPrint(1, [
  'user_id' => $attemptedUser['user_id'],
  'display_name' => $attemptedUser['display_name'],
]);
return;
