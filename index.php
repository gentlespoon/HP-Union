<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include_once(ROOT."core/core.php");



$r = DB("SELECT uid, username, regdate FROM member ORDER BY uid ASC");
foreach ($r as $k => $v) {
  $r[$k]['regdate'] = toUserTime($r[$k]['regdate']);
}
foreach ($r as $v) {
  $body['text'] .= printv($v, true);
}

$title = $lang['site']['name'];
template("index");
