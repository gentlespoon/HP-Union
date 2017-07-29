<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");


if ($_SESSION['uid'] > 0) {
  if ($member['adminback'] != "1") {
    exit($lang['permission-denied']);
  }
} else {
  exit($lang['not-logged-in']);
}

// handle submitted requests first
switch ($_GET['act']) {

  case "forumhierarchy":
    if (isset($_POST['delete-forum'])) {
      DB("DELETE FROM forum_forum WHERE fid=:fid", [":fid" => $_POST['delete-forum']]);
      break;
    }
    $newforumlist = [];
    foreach ($_POST as $key => $value) {
      $forum = explode("-", $key);
      $newforumlist[$forum[1]][$forum[0]] = $value;
    }
    if ($newforumlist['new']['name'] != "") {
      DB("INSERT INTO forum_forum (name, description, parent_fid) VALUES (:name, :description, :parent_fid)", [
        ":name" => $newforumlist['new']['name'],
        ":description" => $newforumlist['new']['description'],
        ":parent_fid" => $newforumlist['new']['parent_fid'],
      ]);
    }
    unset($newforumlist['new']);
    foreach ($newforumlist as $fid => $forum) {
      DB("UPDATE forum_forum SET name=:name, description=:description, parent_fid=:parent_fid WHERE fid=:fid", [
        ":name" => $forum['name'],
        ":description" => $forum['description'],
        ":parent_fid" => $forum['parent_fid'],
        ":fid" => $fid,
      ]);
    }
    break;


  case "usermanage":

    break;

  case "globalsettings":
  default:
    foreach ($_POST as $k => $v) {
      DB("UPDATE common_settings SET data=:v WHERE name=:k", [":v" => $v, ":k" => $k]);
    }
}








// retrieve info for new admin page


// globalsettings
$globalsettings = DB("SELECT * FROM common_settings");
$settings = [];
foreach($globalsettings as $k => $v) {
  $settings[$v['name']] = $v['data'];
}
$tplt = $settings['template'];
$settings['template'] = ["current" => $tplt, "others" => []];
// get templates
$templatedirs = glob(ROOT."templates/*" , GLOB_ONLYDIR);
foreach ($templatedirs as $v) {
  if (isset($templateName)) {
    unset($templateName);
  }
  include_once($v."/info.php");
  if (isset($templateName)) {
    array_push($settings['template']['others'], $templateName);
  }
}





// forumhierarchy
$forum_hierarchy = [];
$forumlist = DB("SELECT * FROM forum_forum WHERE visible>0");
array_push($forumlist, [
  "fid" => "new",
  "name" => "",
  "parent_fid" => "0",
  "description" => "",
]);








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
