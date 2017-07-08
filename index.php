<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include_once(ROOT."core/core.php");



$r = DB("SELECT uid, username, qq, email, regdate, lastlogin FROM member ORDER BY uid ASC");
foreach ($r as $k => $v) {
  $r[$k]['regdate'] = toUserTime($r[$k]['regdate']);
  $r[$k]['lastlogin'] = toUserTime($r[$k]['lastlogin']);
}

$body['text'] = "<br>".$lang['register'].$lang['user'].": ";
$body['registered'] = $r;

template("index");
