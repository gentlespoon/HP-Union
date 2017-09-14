<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");



$forum_hierarchy = [];
$forumlist = DB::query("SELECT * FROM forum_forum WHERE visible>0");

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
