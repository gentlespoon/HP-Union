<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");



$r = DB("SELECT uid, username, qq, email, regdate, lastlogin FROM member ORDER BY uid ASC");
foreach ($r as $k => $v) {
  $r[$k]['regdate'] = toUserTime($r[$k]['regdate']);
  $r[$k]['lastlogin'] = toUserTime($r[$k]['lastlogin']);
}


$forum_hierarchy = [];
$forumlist = DB("SELECT * FROM forum_forum WHERE visible>0");

// construct top level forum hierarchy
foreach ($forumlist as $k => $forum) {
  if ($forum['parent_fid'] == 0) {
    $forum_hierarchy[$forum['fid']] = $forum;
    $forum_hierarchy[$forum['fid']]['subforum'] = [];
  }
}

// construct second level forum hierarchy
foreach ($forumlist as $k => $forum) {
  if ($forum['parent_fid'] > 0) {
    $forum_hierarchy[$forum['parent_fid']]['subforum'][$forum['fid']] = $forum;
  }
}


$body['registered'] = $r;

$body['forumlist'] = $forum_hierarchy;


template("index");
