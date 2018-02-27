<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : editProfile.api.php [localhost]
 * Date   : 2018-02-09
 * Time   : 05:17
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


if (!$_SESSION['uid']) {
  apiPrint(0, 'Not logged in.');
  return;
}

// get editable fields
$editableFields = DB::queryFirstField('SELECT content FROM '.DBtable('settings').' WHERE name=%s', 'editable_profile_fields');
$editableFields = json_decode($editableFields);

$toBeSaved = [];

foreach($_POST as $key => $value) {
  if (in_array($key, $editableFields)) {
    $toBeSaved[$key] = $value;
  }
}

DB::update(DBtable('users'), $toBeSaved, 'user_id=%s', $_SESSION['user_id']);

apiPrint(1, 'Profile Updated.');
return;
