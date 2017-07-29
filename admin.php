<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");

// handle submitted requests first
switch ($_GET['act']) {

  case "usermanage":

    break;
  case "sitesettings":
  default:


}








// retrieve info for new admin page


// globalsettings
$globalsettings = DB("SELECT * FROM common_settings");
$settings = [];
foreach($globalsettings as $k => $v) {
  $settings[$v['key']] = $v['value'];
}



// forumhierarchy
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


// usermanage
$memberlist = DB("SELECT uid, avatar, username, qq, email, regdate, lastlogin FROM member ORDER BY uid ASC");
foreach ($memberlist as $k => $v) {
  $memberlist[$k]['regdate'] = toUserTime($v['regdate']);
  $memberlist[$k]['lastlogin'] = toUserTime($v['lastlogin']);
  if ($v['uid'] == 0) {
    unset($memberlist[$k]);
  }
}


include_once(ROOT."templates/admin.htm");
