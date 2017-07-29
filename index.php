<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");




/*
$memberlist = DB("SELECT uid, avatar, username, qq, email, regdate, lastlogin FROM member ORDER BY uid ASC");
foreach ($memberlist as $k => $v) {
  $memberlist[$k]['regdate'] = toUserTime($v['regdate']);
  $memberlist[$k]['lastlogin'] = toUserTime($v['lastlogin']);
  if ($v['uid'] == 0) {
    unset($memberlist[$k]);
  }
}
$body['registered'] = $memberlist;
*/

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



$body['forumlist'] = $forum_hierarchy;


template("index");
