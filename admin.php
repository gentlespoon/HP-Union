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


  case "navigation":
    if (isset($_POST['delete-nav'])) {
      DB::query("DELETE FROM common_navigation WHERE id=%i", $_POST['delete-nav']);
      break;
    }
    $newnavlist = [];
    foreach ($_POST as $key => $value) {
      $nav = explode("-", $key);
      $newnavlist[$nav[1]][$nav[0]] = $value;
    }
    if ($newnavlist['new']['name'] != "") {
      DB::query("INSERT INTO common_navigation (name, link, target, displayorder, category, filename) VALUES (%s, %s, %s, %i, %s, %s)",
          $newnavlist['new']['name'],
          $newnavlist['new']['link'],
          $newnavlist['new']['target'],
          $newnavlist['new']['displayorder'],
          $newnavlist['new']['category'],
          $newnavlist['new']['filename']
        );
    }
    unset($newnavlist['new']);
    foreach ($newnavlist as $id => $nav) {
      DB::query("UPDATE common_navigation SET name=%s, link=%s, target=%s, displayorder=%i, category=%s, filename=:%s WHERE id=%i",
        $id,
        $nav['name'],
        $nav['link'],
        $nav['target'],
        $nav['displayorder'],
        $nav['category'],
        $nav['filename']
      );
    }
    break;






  case "forumhierarchy":
    if (isset($_POST['delete-forum'])) {
      $threads = DB::query("SELECT tid FROM forum_thread WHERE forum_id=%i", $_POST['delete-forum']);
      if (empty($threads)) {
        DB::query("DELETE FROM forum_forum WHERE fid=%i", $_POST['delete-forum']);
      }
      break;
    }
    $newforumlist = [];
    foreach ($_POST as $key => $value) {
      $forum = explode("-", $key);
      $newforumlist[$forum[1]][$forum[0]] = $value;
    }
    if ($newforumlist['new']['name'] != "") {
      DB::query("INSERT INTO forum_forum (name, description, parent_fid) VALUES (%s, %s, %i)",
        $newforumlist['new']['name'],
        $newforumlist['new']['description'],
        $newforumlist['new']['parent_fid']
      );
    }
    unset($newforumlist['new']);
    foreach ($newforumlist as $fid => $forum) {
      DB::query("UPDATE forum_forum SET name=%s, description=%s, parent_fid=%i WHERE fid=%i",
        $forum['name'],
        $forum['description'],
        $forum['parent_fid'],
        $fid
      );
    }
    break;


  case "usermanage":

    break;

  case "globalsettings":
  default:
    foreach ($_POST as $k => $v) {
      DB::query("UPDATE common_settings SET data=%s WHERE name=%s", $v, $k);
    }
}








// retrieve info for new admin page


// globalsettings
$globalsettings = DB::query("SELECT * FROM common_settings");
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
$forumlist = DB::query("SELECT * FROM forum_forum WHERE visible>0");
array_push($forumlist, [
  "fid" => "new",
  "name" => "",
  "parent_fid" => "0",
  "description" => "",
]);








// get navigation item
$nav = getNavItem();
$nav['new'] = [];
array_push($nav['new'], [
  "id" => "new",
  "name" => "",
  "link" => "",
  "target" => "_self",
  "displayorder" => "0",
  "category" => "main",
  "filename" => "",
]);



// usermanage
$memberlist = DB::query("SELECT uid, avatar, username, qq, email, regdate, lastlogin FROM member ORDER BY uid ASC");
foreach ($memberlist as $k => $v) {
  $memberlist[$k]['regdate'] = toUserTime($v['regdate']);
  $memberlist[$k]['lastlogin'] = toUserTime($v['lastlogin']);
  if ($v['uid'] == 0) {
    unset($memberlist[$k]);
  }
}


include_once(ROOT."templates/admin.htm");
